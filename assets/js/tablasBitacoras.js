function creaTabla(elementoSeleccionado, filtro, fi, ff) {
    $.ajax({
            url: '_bitacoras.php',
            type: 'POST',
            dataType: 'html',
            data: { opcion: elementoSeleccionado, filtroComun: filtro, fini: fi, ffin: ff },
        })
        .done(function(respuesta) {
            $("#info").html(respuesta);
        })
        .fail(function() {
            alert("Ha ocurrido un error en la petición. Reintente.");
        });
}

$(document).ready(function() {

    //radio buttons
    $(".bt").click(function() {
        //value del elemento input llamado opcion seleccionado actualmente
        var rad = $("input[name='opcion']:checked").val();
        var filtro = "no";
        var micheckbox = document.getElementById("si"); //obtener el elemento checkBox y ver si está seleccionado
        if ($(micheckbox).is(':checked')) {
            // Hacer algo si el checkbox ha sido seleccionado
            filtro = "si";
        }

        var mibox = document.getElementById("fife"); //obtener el elemento checkBox y ver si está seleccionado
        if ($(mibox).is(':checked')) {
            // Hacer algo si el checkbox ha sido seleccionado
            //fecha de inicio
            var date = $('#fechaI').val().split("-");
            day = date[2];
            month = date[1];
            year = date[0];
            fInicio = day + "/" + month + "/" + year;

            //fecha de fin
            var date = $('#fechaF').val().split("-");
            day = date[2];
            month = date[1];
            year = date[0];
            fFin = day + "/" + month + "/" + year;
        } else {
            // Hacer algo si el checkbox ha sido deseleccionado
            fInicio = "undefined/undefined/";
            fFin = "undefined/undefined/";
        }

        creaTabla(rad, filtro, fInicio, fFin);
    });

    //checkBox: eventos comunes y fechas, inputs tipo date
    $(".bt2").on('change', function() {
        var rad = $("input[name='opcion']:checked").val();

        var mibox = document.getElementById("fife"); //obtener el elemento checkBox de fechas y ver si está seleccionado
        if ($(mibox).is(':checked')) {
            // Hacer algo si el checkbox ha sido seleccionado
            //fecha de inicio
            var date = $('#fechaI').val().split("-");
            day = date[2];
            month = date[1];
            year = date[0];
            fInicio = day + "/" + month + "/" + year;

            //fecha de fin
            var date = $('#fechaF').val().split("-");
            day = date[2];
            month = date[1];
            year = date[0];
            fFin = day + "/" + month + "/" + year;
        } else {
            // Hacer algo si el checkbox ha sido deseleccionado
            fInicio = "undefined/undefined/";
            fFin = "undefined/undefined/";
        }

        var micheckbox = document.getElementById("si"); //obtener el elemento checkBox de eventos comunes y ver si está seleccionado
        var filtro = "no";
        if ($(micheckbox).is(':checked')) {
            // Hacer algo si el checkbox ha sido seleccionado
            filtro = "si";
            creaTabla(rad, filtro, fInicio, fFin);
        } else {
            // Hacer algo si el checkbox ha sido deseleccionado
            creaTabla(rad, filtro, fInicio, fFin);
        }
    });
});