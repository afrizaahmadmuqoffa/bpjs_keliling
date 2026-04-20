<?php

namespace App\Livewire;

use App\Services\WhatsAppService;
use App\Models\ParticipantModel;
use App\Models\ServiceModel;
use App\Models\SegmentModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    // Reset pagination ketika filter/search berubah
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => '', 'as' => 'status'],
    ];

    public function sendWa(string $id)
    {
        $p = ParticipantModel::with(['service', 'segment'])->findOrFail($id);

        if ($p->status === 'selesai') {
            $this->addError('msg', 'Sudah diproses');
            return;
        }

        if (!$p->layanan_id || !$p->segment_id) {
            $this->addError('msg', 'Layanan dan segment wajib diisi sebelum menghubungi peserta!');
            return;
        }

        $message = "Halo {$p->nama},\n\n"
            . "Kami menerima pengajuan layanan BPJS Keliling atas nama Anda. Mohon konfirmasi apakah data di bawah ini sudah benar:\n\n"
            . "NIK: {$p->nik}\n"
            . "Layanan: " . ($p->service->nama ?? '-') . "\n"
            . "Segment: " . ($p->segment->nama ?? '-') . "\n\n"
            . "Jika data sudah sesuai, mohon balas dengan 'YA' agar dapat segera kami proses. Terima kasih.";

        WhatsAppService::send($p->no_hp, $message);

        $this->dispatch('notify', message: 'WA berhasil dikirim');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Update layanan_id atau segment_id secara parsial (tanpa reload)
     */
    public function updateField(string $id, string $field, $value): void
    {
        // Hanya admin & super_admin
        if (!in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            $this->addError('auth', 'Tidak punya akses');
            return;
        }

        $participant = ParticipantModel::findOrFail($id);

        if ($participant->status === 'selesai') {
            $this->addError('msg', 'Data sudah selesai, tidak bisa diubah');
            return;
        }

        if (!in_array($field, ['layanan_id', 'segment_id'])) {
            return;
        }

        // Validasi value
        if ($field === 'layanan_id' && $value) {
            $exists = \App\Models\ServiceModel::where('id', $value)->exists();
            if (!$exists) return;
        }

        if ($field === 'segment_id' && $value) {
            $exists = \App\Models\SegmentModel::where('id', $value)->exists();
            if (!$exists) return;
        }

        $participant->$field = $value ?: null;
        $participant->save();

        $this->dispatch('notify', message: 'Berhasil diupdate');
    }

    public function process(string $id): void
    {
        if (!in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            $this->addError('auth', 'Tidak punya akses');
            return;
        }

        $participant = ParticipantModel::with(['service', 'segment'])->findOrFail($id);

        if ($participant->status === 'selesai') {
            $this->addError('msg', 'Sudah diproses');
            return;
        }

        if (!$participant->layanan_id || !$participant->segment_id) {
            $this->addError('msg', 'Layanan dan segment wajib diisi sebelum diproses');
            return;
        }

        $participant->update([
            'status'          => 'selesai',
            'processed_by'    => Auth::id(),
            'tanggal_selesai' => now(),
        ]);


        $message = "Halo {$participant->nama},\n\n"
            . "Pengajuan BPJS Anda telah SELESAI diproses.\n\n"
            . "Layanan: {$participant->service->nama}\n"
            . "Segment: {$participant->segment->nama}\n\n"
            . "Terima kasih.";

        WhatsAppService::send($participant->no_hp, $message);

        $this->dispatch('notify', message: 'Diproses & WA terkirim');
    }


    /**
     * Download data peserta sebagai Excel
     * Filter mengikuti search & statusFilter yang aktif
     */
    public function downloadExcel(): StreamedResponse
    {
        $query = ParticipantModel::with(['region', 'service', 'segment', 'creator']);

        if (Auth::user()->role === 'pic') {
            $query->where('created_by', Auth::id());
        }

        if ($this->search) {
            $search = strtolower($this->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"])
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhereHas('region', function ($r) use ($search) {
                        $r->whereRaw('LOWER(kabupaten) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(kecamatan) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(kelurahan) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $participants = $query->latest()->get();

        $rows = $participants->map(fn($p) => [
            'Nama'           => $p->nama,
            'NIK'            => $p->nik,
            'No HP'          => $p->no_hp,
            'Alamat'         => $p->alamat,
            'Provinsi'       => ucwords($p->region->provinsi ?? ''),
            'Kabupaten'      => ucfirst($p->region->kabupaten ?? ''),
            'Kecamatan'      => ucfirst($p->region->kecamatan ?? ''),
            'Kelurahan'      => ucfirst($p->region->kelurahan ?? ''),
            'Layanan'        => $p->service->nama ?? '-',
            'Segment'        => $p->segment->nama ?? '-',
            'Status'         => ucfirst($p->status),
            'Tanggal Selesai'=> $p->tanggal_selesai?->format('d/m/Y') ?? '-',
            'Diinput Oleh'   => $p->creator->nama ?? '-',
            'Diproses Oleh'   => $p->processor->nama ?? '-',
        ]);

        $filename = 'peserta_' . now()->format('Ymd_His') . '.xlsx';

        return (new FastExcel($rows))->download($filename);
    }
    public function delete(string $id): void
    {
        if (!in_array(Auth::user()->role, ['admin', 'super_admin', 'pic'])) {
            $this->addError('auth', 'Tidak punya akses');
            return;
        }

        $participant = ParticipantModel::findOrFail($id);

        // PIC hanya bisa hapus data miliknya sendiri
        if (Auth::user()->role === 'pic' && $participant->created_by !== Auth::id()) {
            $this->addError('auth', 'Tidak punya akses terhadap data ini');
            return;
        }

        if ($participant->status === 'selesai') {
            $this->addError('msg', 'Data sudah selesai, tidak bisa dihapus');
            return;
        }

        $participant->delete();

        $this->dispatch('notify', message: 'Data peserta berhasil dihapus');
    }

    public function render()
    {
        $query = ParticipantModel::with(['region', 'service', 'segment']);

        if (Auth::user()->role === 'pic') {
            $query->where('created_by', Auth::id());
        }

        if ($this->search) {
            $search = strtolower($this->search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"])
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhereHas('region', function ($r) use ($search) {
                        $r->whereRaw('LOWER(kabupaten) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(kecamatan) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(kelurahan) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $participants = $query->latest()->paginate(10);
        $services     = ServiceModel::all();
        $segments     = SegmentModel::all();

        return view('livewire.participant-table', compact('participants', 'services', 'segments'));
    }
}
