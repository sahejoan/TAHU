<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
  <h1 class=" ">
    {{-- -------Emision de Errores---------------}}
    @if ($errors->any())
    <div class="alert alert-dark alert-dismissible fade show" role="alert">
           <strong>¡Revise los Campos!</strong>
          @foreach ($errors->all() as $error)
              <span class="badge badge-danger" >{{$error}}</span>                                      
          @endforeach
         <button type="button" class="close" data-dismiss="alert" aria-label="close">
               <span aria-hidden="true">&times;</span>
         </button> 
     </div>                                
    @endif
     
   {{-- ----------------------}}
    
    <h1 class="bg-white text-left text-primary"></h1>
    <div style="width:50%">
      <form action="/funcionarios/{{$funcionarios->id}}" method="POST" enctype="multipart/form-data">
        @csrf
       @method('PUT')

       <div class= class="mb-4 font-medium text-sm text-green-600" >
          <label for="" class="form-label">Nombre</label>
         <input id="nombre" name="nombre"  type="text" class="form-control" tabindex="1" value="{{$funcionarios->nombre}}">
       </div>

       <div class="mb-3">
          <label for="" class="form-label">Apellido</label>
          <input id="apellido" name="apellido"  type="text" class="form-control" tabindex="2" value="{{$funcionarios->apellido}}">
      </div>

       <div class="mb-3">
         <label for="" class="form-label">Cedula</label>
         <input id="cedula" name="cedula"  type="number" class="form-control" tabindex="3" value="{{$funcionarios->cedula}}">
       </div>

       <div class="mb-3">
         <label for="" class="form-label">Fecha de Ingreso</label>
         <input id="fecha_in" name="fecha_in"  type="" step="any"  class="form-control" tabindex="4" value="{{$funcionarios->fecha_in}}">
       </div>

       <div class="mb-3">
        <label for="" class="form-label">Ubicacion</label>
        <input id="ubicacion" name="ubicacion"  type="" step="any"  class="form-control" tabindex="4" value="{{$funcionarios->ubicacion}}">
      </div>

      <a href="/funcionarios" class="btn btn-warning" tabindex="5" >Cancelar</a>
      <button type="submit" class="btn btn-primary" tabindex="4">Guardar</button>
     
    </form>
  </h1>
</body>
</html>