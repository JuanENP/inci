function buscar_datos2(consulta2) {
    $.ajax({
            url: 'buscar-trabajador2.php',
            type: 'POST',
            dataType: 'html',
            data: { consulta2: consulta2 },
        })
        .done(function(respuesta2) {
            //div con id datos, agregamos su html con la respuesta del servidor
            $("#datosSuplente").html(respuesta2);
        })
        .fail(function() {
            console.log("error");
        });
}


$(document).on('keyup', '#caja_busqueda2', function() {
    var valor2 = $(this).val();
    if (valor2 != "") {
        buscar_datos2(valor2);
    }
});