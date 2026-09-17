@extends('layouts.app')

@section('css')

@stop



@section('content')
                                        
<section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="timeline">
              <div class="time-label">
                <span class="bg-green">Región</span>
              </div>              
              <div>
                <i class="fas fa-phone bg-blue"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fas fa-clock"></i></span>
                  <h3 class="timeline-header"><a href="#">Teléfono</a> telefnoaqui </h3>
                  <div class="timeline-body">
                    escribir region aqui
                  </div>
                  <div class="timeline-footer">
                  </div>
                </div>
              </div>
              <div>
                <i class="fas fa-map-marker-alt bg-green"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fas fa-clock"></i></span>
                  <h3 class="timeline-header no-border"><a href="#">Coordenadas</a> escribir coordenadas aqui</h3>
                </div>
              </div>
              <div>
                <i class="fas fa-map-signs bg-yellow"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fas fa-clock"></i></span>
                  <h3 class="timeline-header"><a href="#">Dirección</a></h3>
                  <div class="timeline-body">
                    escribir la direccion aqui
                  </div>
                  <div class="timeline-footer">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>                                        
@endsection 



@section("page_js")

@endsection 

@section("scripts")

@stop