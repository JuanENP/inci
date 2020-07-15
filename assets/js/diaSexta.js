function actualiza(consulta,consulta2,consulta3) {
    $.ajax({
            url: ruta,
            type: 'POST',
            dataType: 'html',
            data: { consulta: consulta,consulta2: consulta2,consulta3: consulta3 },
        })
        .done(function(respuesta) {
            $("#dias_sexta").html(respuesta);
        })
        .fail(function() {
            console.log("error");
        });
}

$(document).ready(function()
{
    $("#turno").click (function() {

        var valor = document.getElementById('turno').value;
        var valor2 = document.getElementById('sexta').value;//numero trabajador
        var valor3 = $("input[name='tipo']:checked").val();//nomForm= nombre del formulario; tipo = nombre de los elementos radiobuton
        actualiza(valor,valor2,valor3);
    })
});

