function confirmacion(compt) {
   let id = compt.id;
   var idform = $("#"+id).data("id");

    if (id=='') {
    } else {
      var confirmar = confirm("¿Desea Realmente Eliminar Este Registro?");
          if (confirmar) { document.getElementById("frmeliminar"+idform).submit(); }
      }   
}
