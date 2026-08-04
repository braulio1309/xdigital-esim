@extends('layouts.app')

@section('title', 'Clientes')

@section('contents')
    @php
        $authUser = auth()->check()
            ? auth()->user()->only(['id', 'user_type', 'user_sub_type', 'super_partner_id', 'beneficiario_id'])
            : null;
    @endphp

    <app-clientes :auth-user='@json($authUser)'></app-clientes>
@endsection
