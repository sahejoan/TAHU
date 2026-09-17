@extends('errors::illustrated-layout')

@section('code', '403')
@section('title', __('Acceso Negado'))

@section('image')
<style>
    #apartado-derecho{
        text-align:center;
    }
    ul{
        text-decoration: none !important;
        list-style: none;
        color: black;
        font-weight: bold;
    }
</style>
<div id="apartado-derecho" style="background-color: #F5716C;" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    <h2>Volver a atras:</h2>
    <div style="background-image: url('image/qrcode/negado.png');" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
    <ul>
        {{-- <li><a href="/">Inicio</a></li>
        <li><a href="/">Blog</a></li>
        <li><a href="/">Dónde estamos</a></li> --}}

    </ul>
</div>
@endsection

@section('message', __('No tiene permisos para acceder a esta página.'))