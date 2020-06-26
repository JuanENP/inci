function buscar_datos(consulta) {
    $.ajax({
            url: 'buscar-trabajador.php',
            type: 'POST',
            dataType: 'html',
            data: { consulta: consulta },
        })
        .done(function(respuesta) {
            //div con id datos, agregamos su html con la respuesta del servidor
            $("#datos").html(respuesta);
        })
        .fail(function() {
            console.log("error");
        });
}

$(document).on('keyup', '#caja_busqueda', function() {
    var valor = $(this).val();
    if (valor !== "") {
        buscar_datos(valor);
    }
});