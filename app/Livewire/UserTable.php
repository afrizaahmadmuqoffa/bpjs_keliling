<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class UserTable extends Component
{
    use WithPagination;

    public string $search = '';

    // Reset halaman saat search berubah
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deleteUser(string $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'Tidak bisa hapus akun sendiri.');
            return;
        }

        if ($user->role === 'super_admin') {
            session()->flash('error', 'Tidak bisa menghapus akun super admin.');
            return;
        }

        $user->delete();
        $this->dispatch('notify', message: 'User berhasil dihapus!');
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $search = strtolower($this->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"])
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);

        return view('livewire.user-table', compact('users'));
    }
}
