<div class="space-y-6">
    <div
        x-data="{ show: false, message: '' }"
        x-on:notify.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-slate-800 text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-xl"
        style="display: none;">
        <i class="fas fa-check-circle text-green-400"></i>
        <span x-text="message"></span>
    </div>

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Pengguna</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola hak akses dan informasi akun administrator serta petugas lapangan.</p>
        </div>
        <a href="{{ route('users.create') }}"
            class="inline-flex items-center justify-center gap-2 bg-secondary hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-900/20 transition-all active:scale-95">
            <i class="fas fa-user-plus text-xs"></i> Tambah User Baru
        </a>
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    {{-- SEARCH --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
        <div class="relative group max-w-md">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                <i class="fas fa-search text-sm"></i>
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Cari nama atau NIK petugas..."
                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Informasi Pengguna</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Kontak & NIK</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Level Akses</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" wire:loading.class="opacity-50">
                    @forelse($users as $u)
                    <tr class="hover:bg-slate-50/50 transition-colors group">

                        {{-- INFORMASI PENGGUNA --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-inner font-bold text-sm">
                                    {{ substr($u->nama, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700 group-hover:text-primary transition-colors">{{ $u->nama }}</span>
                                    <span class="text-[11px] text-slate-400">ID Petugas: {{ str_pad($u->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- KONTAK & NIK --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-center">
                                <span class="text-[13px] font-mono text-slate-600 tracking-tighter">{{ $u->nik }}</span>
                                <span class="text-[11px] text-secondary font-semibold mt-1">
                                    <i class="fas fa-phone-alt text-[9px] mr-1"></i> {{ $u->no_hp }}
                                </span>
                            </div>
                        </td>

                        {{-- LEVEL AKSES --}}
                        <td class="px-6 py-4 text-center">
                            @php
                            $roleClass = $u->role === 'admin'
                            ? 'bg-blue-50 text-primary border-blue-100'
                            : 'bg-emerald-50 text-secondary border-emerald-100';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest border {{ $roleClass }}">
                                <i class="fas fa-shield-alt mr-1.5 text-[9px]"></i> {{ $u->role }}
                            </span>
                        </td>

                        {{-- TINDAKAN --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('users.edit', $u->id) }}"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 hover:bg-primary hover:text-white transition-all shadow-sm"
                                    title="Edit Pengguna">
                                    <i class="fas fa-user-edit text-xs"></i>
                                </a>
                                <button
                                    wire:click="deleteUser('{{ $u->id }}')"
                                    wire:confirm="Hapus pengguna ini? Akses akan dicabut permanen."
                                    wire:loading.attr="disabled"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm"
                                    title="Hapus Pengguna">
                                    <span wire:loading wire:target="deleteUser('{{ $u->id }}')">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                    <span wire:loading.remove wire:target="deleteUser('{{ $u->id }}')">
                                        <i class="fas fa-user-minus text-xs"></i>
                                    </span>   
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                    <i class="fas fa-users-slash text-2xl"></i>
                                </div>
                                <p class="text-slate-400 font-medium italic">Data pengguna tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER / PAGINATION --}}
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
            <p class="text-xs text-slate-500 font-medium">Menampilkan data petugas aktif sistem</p>
            <div>{{ $users->links() }}</div>
        </div>
    </div>

</div>

<style>
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
</style>