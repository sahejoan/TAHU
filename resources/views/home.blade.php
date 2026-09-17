@extends('layouts.app')

@section('content')
    @include('alert.succes')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Inicio</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">                          
                                <div class="row">
                                    <div class="col-md-4 col-xl-4">
                                    
                                    <div class="card bg-c-red order-card">
                                            <div class="card-block">
                                            <h5>Usuarios</h5>                                               
                                                @php
                                                  use App\Models\User;
                                                  $cant_usuarios = User::count();                                                
                                                @endphp
                                                <h2 class="text-right"><i class="fa fa-users f-left"></i><span>{{$cant_usuarios}}</span></h2>
                                                <p class="m-b-0 text-right"><a href="#" class="text-red">Ver más</a></p>
                                            </div>                                            
                                        </div>                                    
                                    </div>
                                    
                                    <div class="col-md-4 col-xl-4">
                                        <div class="card bg-c-green order-card">
                                            <div class="card-block">
                                            <h5>Funcionarios</h5>                                               
                                                @php
                                                    use App\Models\funcionarios;
                                                    $cant_funcionarios = Funcionarios::count(); 
                                                @endphp
                                                <h2 class="text-right"><i class="fa fa-user-lock f-left"></i><span>{{$cant_funcionarios}}</span></h2>
                                                <p class="m-b-0 text-right"><a href="/tahu/public/funcionarios" class="text-red">Ver más</a></p>
                                            </div>
                                        </div>
                                    </div>                                                                
                                    
                                    <div class="col-md-4 col-xl-4">
                                        <div class="card bg-c-pink order-card">
                                            <div class="card-block">
                                                <h5>Blogs</h5>                                               
                                                @php
                                                   use App\Models\Blog;
                                                   $cant_blogs = Blog::count();                                                
                                                @endphp
                                                <h2 class="text-right"><i class="fa fa-blog f-left"></i><span>{{$cant_blogs}}</span></h2>
                                                <p class="m-b-0 text-right"><a href="/blogs" class="text-red">Ver más</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section("scripts")
<script>
    $(document).ready(function(){ 
        if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
        //Es seguro ejectura la función
        MostrarMensajeSuccess(message);
        }

        if( typeof MostrarMensajeError !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
        //Es seguro ejectura la función
        MostrarMensajeError(messageerror);
        }        
    });
</script>
@endsection