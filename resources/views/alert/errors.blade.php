@if(Session::has('message-error'))
    <script>
        var messageerror = "{{ Session::get('message-error') }}";        
        function MostrarMensajeError(messageerror) {       
            var vistamen = Swal.mixin({
                position: 'center',
                showConfirmButton: true,
                timer: 4000,
                background : '#9999cc',  
            });

            vistamen.fire({
                title: "<center><h5 style='color:black'>Transacción</h5></center>",    
                icon: 'warning',
                html: "<h6 style='color:black'>"+messageerror+"</h6>",
                showCloseButton: true,
                confirmButtonText: '<center><i class="fa fa-thumbs-up"></i> Continuar!</center>',
                confirmButtonColor: '#1f13df',
            }).then((result) => {  });
            /*
            vistamen.fire({
                title: "<center><h5 style='color:black'>Transacción</h5></center>",    
                icon: 'success',
                html: "<h6 style='color:black'>"+message+"</h6>",
                showCloseButton: true,
                showCancelButton: false,
                cancelButtonText: '<i class="fa fa-thumbs-down">Cancelar</i>',
                cancelButtonAriaLabel: 'Thumbs down',
                confirmButtonText: '<center><i class="fa fa-thumbs-up"></i> Continuar!</center>',
                confirmButtonAriaLabel: 'Thumbs up, great!',
                confirmButtonColor: '#1f13df',
                reverseButtons:  false
            }).then((result) => {  });
            */
        }
    </script>

    <!--    
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
    	{{ Session::get('message-error') }}		
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    		<span aria-hidden="true">&times;</span>
  		</button>                
    </div>
    -->
@endif