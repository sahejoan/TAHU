@extends('layouts.app')

@section('content')
  <section class="section">
    <div class="section-header">
      <h3 class="page__heading">Inicio</h3>
    </div>
    <div class="section-body">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body justify-content-center "> 
              <div class="visible-print text-center ">
                <div class="container">
                  <h3>SCAN QR CODE FOR MORE LARAVEL TUTORIALS.</h3>                        
                  <br><br>                        
                  <div class="mb-3">                                                                                                                       
                    <img src="data:image/png;base64,{!! DNS2D::getBarcodepng($link, 'QRCODE', 5, 5,) !!}" alt="barcode"/>
                    {{-- <img src="data:image/png;base64,{!! DNS2D::getBarcodepng($link, "QRCODE", 1, 25, '#2A3239') !!}" alt="barcode"/> --}}
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