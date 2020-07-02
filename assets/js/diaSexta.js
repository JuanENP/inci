function actualiza(consulta,consulta2,consulta3) {
    $.ajax({
            url: '_clickSexta.php',
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
        // var valor = $(this).val();
        var valor = document.getElementById('turno').value;
        var valor2 = document.getElementById('sexta').value;
        var valor3= document.mio.tipo.value;
        actualiza(valor,valor2,valor3);
    })

    $("#radio_confianza").click (function() {
        // var valor = $(this).val();
        var valor = document.getElementById('turno').value;
        var valor2 = document.getElementById('sexta').value;
        var valor3= document.mio.tipo.value;
        actualiza(valor,valor2,valor3);
    })

    $("#radio_base").click (function() {
        // var valor = $(this).val();
        var valor = document.getElementById('turno').value;
        var valor2 = document.getElementById('sexta').value;
        var valor3= document.mio.tipo.value;
        actualiza(valor,valor2,valor3);
    })

    $("#radio_eventual").click (function() {
        // var valor = $(this).val();
        var valor = document.getElementById('turno').value;
        var valor2 = document.getElementById('sexta').value;
        var valor3= document.mio.tipo.value;
        actualiza(valor,valor2,valor3);
    })

    $("#radio_foraneo").click (function() {
        // var valor = $(this).val();
        var valor = document.getElementById('turno').value;
        var valor2 = document.getElementById('sexta').value;
        var valor3= document.mio.tipo.value;
        actualiza(valor,valor2,valor3);
    })

    
});

