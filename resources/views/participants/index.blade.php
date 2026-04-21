{{-- resources/views/participants/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="space-y-8 px-4 py-14 lg:px-10 lg:py-10">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Data Peserta</h2>
            <p class="text-sm text-slate-500">Kelola, filter, dan proses antrean peserta BPJS Keliling.</p>
        </div>
        {{-- Tombol Desktop Only --}}
        <a href="{{ route('participants.create') }}"
            class="hidden lg:inline-flex items-center justify-center gap-2 bg-secondary hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-900/20 transition-all active:scale-95">
            <i class="fas fa-plus"></i> Tambah Peserta
        </a>
    </div>

    {{-- LIVEWIRE COMPONENT --}}
    <livewire:participant-table />

</div>
@endsection