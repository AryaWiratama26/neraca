@extends('errors.layout')

@section('title', 'Server Error - 500')
@section('code', '500')
@section('message', 'Terjadi Kesalahan Server')
@section('description', 'Sistem kami mengalami gangguan internal (seperti database yang terputus atau masalah koneksi). Tim teknis kami telah disiagakan. Silakan coba lagi beberapa saat.')
@section('icon')
    <i class="icon-server-crash relative z-10" style="font-size: 48px; color: #ef4444;"></i>
@endsection
