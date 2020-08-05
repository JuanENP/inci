function dibujarTabla(texto) {
    $.ajax({
            url: rutaparaTablaTrab,
            type: 'POST',
            dataType: 'html',
            data: { textoEnviar: texto },
        })
        .done(function(respuesta) {
            //div con id datos, agregamos su html con la respuesta del servidor
            $("#trabajadores").html(respuesta);
            //Sirve para que aparezcan los botones pdf, excel, etc. para descargar
            $('#datos').DataTable( {
                "searching": false, //false para desactivar el botón de búsqueda
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5',
                ]
            } );
        })
        .fail(function() {
            console.log("error");
        });
}
//Evento para la tecla enter
$(document).on('keydown','#buscador', function(e) {
    if (e.which == 13) {
        e.preventDefault();
        valor=document.getElementById("buscador").value;
        if(valor == "")
        {
            dibujarTabla("#.#"); 
        }
        else
        {
            dibujarTabla(valor);
        }
    }
});
//Evento para cuando queda vació el input buscador
$(document).on('keyup', '#buscador', function() {
    var valor = $(this).val();
    if (valor == "") 
    {
        dibujarTabla("#.#");
    }
    /*else 
    {
        dibujarTabla(valor);
    }*/
});