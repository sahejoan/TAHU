<script>
    let modalTimeout;
   
   document.addEventListener('DOMContentLoaded', function() {
       const calendarEl = document.getElementById('calendar');
       const calendar = new FullCalendar.Calendar(calendarEl, {
           initialView: 'dayGridMonth',
           events: @json($eventos), // Pasar los eventos desde el controlador
           locale: 'es',
           eventDisplay: 'block', // Mostrar el evento como un bloque
           eventDidMount: function(info) {
               // Habilitar HTML en el título del evento
               info.el.querySelector('.fc-event-title').innerHTML = info.event.title;
               // Agregar eventos de mouseenter y mouseleave al evento
               info.el.addEventListener('mouseenter', function() {
                   clearTimeout(modalTimeout); // Cancelar el retraso si existe
                   openModal(info.event); // Mostrar el modal al pasar el puntero
               });
   
               info.el.addEventListener('mouseleave', function() {
                   // Ocultar el modal después de un pequeño retraso
                   modalTimeout = setTimeout(() => {
                       closeModal();
                   }, 300); // Retraso de 300ms
               });
           }
       });
       calendar.render();
   });
   
   // Función para abrir el modal
   function openModal(event) {
       const modal = document.getElementById('eventModal');
       const modalImage = document.getElementById('modalImage');
       const modalName = document.getElementById('modalName');
   
       // Mostrar la foto y el nombre del funcionario en el modal
       if (event.extendedProps.foto) {
           modalImage.src = event.extendedProps.foto;
       } else {
           modalImage.src = ''; // Si no hay foto, dejar el src vacío
       }
        // Usar innerHTML para interpretar el salto de línea
        modalName.innerHTML = event.title;
   
       // Mostrar el modal y el fondo oscuro
       modal.style.display = 'block';
       document.getElementById('modalOverlay').style.display = 'block';
   
       // Evitar que el modal se cierre si el puntero está sobre él
       modal.addEventListener('mouseenter', function() {
           clearTimeout(modalTimeout); // Cancelar el retraso si el puntero entra en la modal
       });
   
       modal.addEventListener('mouseleave', function() {
           // Cerrar el modal si el puntero sale de la modal
           modalTimeout = setTimeout(() => {
               closeModal();
           }, 400); // Retraso de 300ms
       });
   }
   
   // Función para cerrar el modal
   function closeModal() {
       const modal = document.getElementById('eventModal');
       const modalOverlay = document.getElementById('modalOverlay');
   
       // Ocultar el modal y el fondo oscuro
       modal.style.display = 'none';
       modalOverlay.style.display = 'none';
   }
   </script>