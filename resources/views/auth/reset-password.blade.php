@extends('layouts.auth_app')
@section('title')
    Reset Password
@endsection
@section('content')
    <div class="card card-primary">
        <div class="card-header"><h4>Nueva clave</h4></div>

        <div class="card-body">
            {!!Form::open(array('name' => 'form', 'route' => array('actualizarclave'), 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
                @csrf
                @if(Session::has('message-error'))
                    <div class="alert alert-danger p-0">
                        <ul>
                          <li>{{ Session::get('message-error') }} </li>
                        </ul>
                    </div>
                @endif
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                           name="email" tabindex="1" value="{{ old('email') }}" autofocus>
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input id="password" type="password"
                           class="form-control{{ $errors->has('password') ? ' is-invalid': '' }}" name="password"
                           tabindex="2">
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="control-label">Confirm Password</label>
                    <input id="password_confirmation" type="password"
                           class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid': '' }}"
                           name="password_confirmation" tabindex="2">
                    <div class="invalid-feedback">
                        {{ $errors->first('password_confirmation') }}
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                        Enviar
                    </button>
                </div>
            {!!Form::close()!!}
        </div>
    </div>
    <div class="mt-5 text-muted text-center">        
    </div>
@endsection
