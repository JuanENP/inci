function actualiza(consulta,consulta2) {
    $.ajax({
            url: '_clickSexta.php',
            type: 'POST',
            dataType: 'html',
            data: { consulta: consulta,consulta2: consulta2 },
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
    // var valor = $(this).val();
    var valor = document.getElementById('turno').value;
    var valor2 = document.getElementById('sexta').value;
    actualiza(valor,valor2);
 })
});
