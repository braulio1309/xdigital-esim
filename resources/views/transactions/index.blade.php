@extends('layouts.app')

@section('title', 'Transactions')

@section('contents')
    <app-transactions :is-admin-user='@json(auth()->check() && auth()->user()->user_type === "admin")'></app-transactions>
@endsection
