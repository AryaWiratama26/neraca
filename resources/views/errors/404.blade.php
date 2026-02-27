@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan - 404')
@section('code', '404')
@section('message', 'Halaman Tidak Ditemukan')
@section('description', 'Maaf, halaman yang Anda cari mungkin telah dihapus, namanya diubah, atau sementara tidak tersedia. Periksa kembali URL Anda.')
@section('icon')
    <i class="icon-file-question relative z-10" style="font-size: 48px; color: #f59e0b;"></i>
@endsection
