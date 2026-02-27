@extends('errors.layout')

@section('title', 'Akses Dilarang - 403')
@section('code', '403')
@section('message', 'Akses Dilarang')
@section('description', 'Maaf, Anda tidak memiliki izin (hak akses) untuk melihat atau mengakses halaman ini.')
@section('icon')
    <i class="icon-lock relative z-10" style="font-size: 48px; color: #6366f1;"></i>
@endsection
