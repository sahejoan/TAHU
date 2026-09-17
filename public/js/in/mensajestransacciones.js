    function MostrarMensajeSuccess(message) { 
        var vistamen = Swal.mixin({
          toast: true,
          position: 'center',
          showConfirmButton: true,
          timer: 3000,
          background : '#69c1c6',  
        });

        vistamen.fire({
          title: "<h5 style='color:black'>Transacción</h5>",    
          icon: 'success',
          html: "<h6 style='color:black'>"+message+"</h6>",
          showCloseButton: true,
          showCancelButton: false,
          cancelButtonText: '<i class="fa fa-thumbs-down">Cancelar</i>',
          cancelButtonAriaLabel: 'Thumbs down',
          confirmButtonText: '<i class="fa fa-thumbs-up"></i> Continuar!',
          confirmButtonAriaLabel: 'Thumbs up, great!',
          confirmButtonColor: '#1f13df',
          reverseButtons:  false
        }).then((result) => {  });
    }

    function MostrarMensajeError(message) { 
        var vistamen = Swal.mixin({
          toast: true,
          position: 'center',
          showConfirmButton: true,
          timer: 3000,
          background : '#69c1c6',  
        });

        em = '';
        for(var i=0; i < message.length;i++) {
          em = em + "<li style='color:#000000;'>"+message[i]+"</li>";
        }

        vistamen.fire({
          title: "<h5 style='color:black'>Transacción</h5>",    
          icon: 'success',
          html: "<h6 style='color:black'>"+em+"</h6>",
          showCloseButton: true,
          showCancelButton: false,
          cancelButtonText: '<i class="fa fa-thumbs-down">Cancelar</i>',
          cancelButtonAriaLabel: 'Thumbs down',
          confirmButtonText: '<i class="fa fa-thumbs-up"></i> Continuar!',
          confirmButtonAriaLabel: 'Thumbs up, great!',
          confirmButtonColor: '#1f13df',
          reverseButtons:  false
        }).then((result) => {  });
    }
