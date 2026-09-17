function confirma(compt) {  
  let id = compt.id;
  var idform = $("#"+id).data("id");
  if (id=='') {
  } else {
    Swal.fire({
        title: '¿ Desea realmente eliminar este registro ?',
        text: "No podrá revertir esto!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: '<i class="fa fa-thumbs-down">Cancelar</i>',
        confirmButtonText: 'Si, borrarlo!'
    }).then((result) => {      
        if (result.isConfirmed) {           
          document.getElementById("frmeliminar"+idform).submit();
        }
    });
  }
}
