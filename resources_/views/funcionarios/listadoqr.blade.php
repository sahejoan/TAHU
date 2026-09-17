@extends('layouts.app')

@section('content')
    @include('alert.succes')
    @include('alert.errors')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Listado QR</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">                     
                                 
                                               
                                                             
                                                               
                                <div class="container mt-1 p-4">
                               
                                  <h3 class="text-center">
                                    Listado General QR Funcionarios 
                                    
                                  </h3>
                                  <hr>
                                
                                  <div class="row text-center" style="background-color: #f7f8f8">
                                
                                    @foreach($funcionarios as $funcionario)
                                        <div class="col-md-3 mt-2 mb-2"> 
                                          <label for="nombre" id="nombre">{{ $funcionario->nombre }}</label>
                                          <label for="nombre" id="nombre">{{ $funcionario->apellido }}</label>
                                          <br>
                                          <div class="mb-3">{!! DNS2D::getBarcodehtml("$funcionario->cedula", 'QRCODE', 5,5) !!}</div>
                                        </div>
                                
                                    @endforeach
                                
                                  </div>
                                </div>                              
                              
                                
                                                                  

                        </div>
                    </div>
                </div>
            </div>
        </div>
           <div class="row">                
                        <div>
                            <a class="btn btn-success" href="{{route('funcionarios.index')}}">Atras</a>
                        </div>              
                                   
                        <div>
                            <a class="btn btn-info" href="{{route('qrcodes.pdf')}}">Imprimir QR</a>
                        </div>               
            
          
    </section>
@endsection

@section("scripts")
  <script>
    if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
      //Es seguro ejectura la función
      MostrarMensajeSuccess(message);
    }

    if( typeof MostrarMensajeError !== 'undefined' && jQuery.isFunction( MostrarMensajeError ) ) {
      //Es seguro ejectura la función
      MostrarMensajeError(messageerror);
    }
  </script>  
@stop 