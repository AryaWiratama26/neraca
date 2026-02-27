@extends('errors.layout')

@section('title', 'Sedang Maintenance - 503')
@section('code', '503')
@section('message', 'Layanan Tidak Tersedia')
@section('description', 'Neraca saat ini sedang dalam pemeliharaan rutin atau pembaharuan sistem. Kami akan segera kembali, terima kasih atas kesabaran Anda.')
@section('icon')
    <i class="icon-wrench relative z-10" style="font-size: 48px; color: #8b5cf6;"></i>
@endsection
