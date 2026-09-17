@extends('layouts.app')

@section('css')
    {!! Html::style('/css/in/estilotablaseniat.min.css') !!}
    <style>
    .dataTables_wrapper .dataTables_processing {
      position: absolute;
      top: 30%;
      left: 50%;
      width: 30%;
      height: 40px;
      margin-left: -20%;
      margin-top: -25px;
      padding-top: 20px;
      text-align: center;
      font-size: 1.2em;
      background:none;
    } 
    </style>
@stop

@section('content')
    {!!Form::open(array('name' => 'form'))!!} 
      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">        
    {!!Form::close()!!}
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Usuarios</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                             
                            <a class="btn btn-warning" href="{{route('usuarios.create')}}">Nuevo</a>
                            
                            <table class="redTable yajra-datatable nowrap" style="width: 100%">                           
                            <!--<table class="table table-striped mt-2">-->
                              <thead style="background-color:#6777ef;">

                                 <th style="display: none;">ID</th>
                                 <th style="color:#fff;">Nombre</th>
                                 <th style="color:#fff;">Email</th>
                                 <th style="color:#fff;">Rol</th>
                                 <th style="color:#fff;" class="text-center">Editar</th>                               
                                 <th style="color:#fff;" class="text-center">Borrar</th>                                
                              </thead>
                              <tbody>


                              </tbody>
                              

                            </table>
                                                  
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section("scripts")

  {!! Html::script('/js/in/confirmaraccionv2.js') !!} 
  @include('usuarios.jsindex')  

@endsection 
