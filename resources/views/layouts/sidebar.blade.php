@php
    $role = Auth::user()->role->name;
@endphp

@if($role == 'admin')
    @include('layouts.sidebars.admin')
@elseif($role == 'kaprodi')
    @include('layouts.sidebars.kaprodi')
@elseif($role == 'supplier')  {{-- Ubah dari vendor ke supplier --}}
    @include('layouts.sidebars.supplier')
@endif