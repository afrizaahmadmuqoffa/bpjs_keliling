@extends('layouts.app')

@section('content')
<livewire:user-edit :id="$user->id" />
@endsection