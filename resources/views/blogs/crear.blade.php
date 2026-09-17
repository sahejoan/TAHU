@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Crear Blog</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">     
                                                                      
                        @if ($errors->any())                                                
                            <div class="alert alert-dark alert-dismissible fade show" role="alert">
                                <strong>¡Revise los campos!</strong>                        
                                @foreach ($errors->all() as $error)                                    
                                    <span class="badge badge-danger">{{ $error }}</span>
                                @endforeach                        

                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>

                            </div>
                        @endif

                        {!!Form::open(array('name' => 'form', 'route' => array('blogs.store'), 'method' => 'post', 'files' => true))!!}     
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">

                                    {!!Form::label('Título')!!}
                                    {!!Form::text('titulo', array('id' => 'titulo', 'class' => 'form-control')!!}

                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                                    
                            <div class="form-floating">
                                {!!Form::label('Contenido')!!}
                                {!!Form::textarea('contenido', array('id' => 'contenido', 'class' => 'form-control', 'style' => 'height: 100px')!!}
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Guardar</button>                            
                        </div>
                        {!!Form::close()!!}
                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection