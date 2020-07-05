function creaTabla(elementoSeleccionado) {
    $.ajax({
            url: '_bitacoras.php',
            type: 'POST',
            dataType: 'html',
            data: { opcion: elementoSeleccionado },
        })
        .done(function(respuesta) {
            $("#info").html(respuesta);
        })
        .fail(function() {
            alert("Ha ocurrido un error en la petición. Reintente.");
        });
}

$(document).ready(function() {
    $(".bt").click(function() {
        //value del elemento input llamado opcion seleccionado actualmente
        var rad = $("input[name='opcion']:checked").val();
        creaTabla(rad);
    })
});