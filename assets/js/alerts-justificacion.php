<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Estas rutas son correctas, pues recordemos que este archivo se usa en el archivo justificacion que se 
        encuentra en la carpeta php
    -->
    <link rel="stylesheet" href="../../assets/css/alertify.core.css" />
    <link rel="stylesheet" href="../../assets/css/alertify.default.css" />
    <script src="../../assets/js/alertify.min.js"></script>
</head>
<body>
</body>
</html>

<script type="text/javascript">
    function No_Existe(numero,fecha)
    {
        alertify.alert("No hay una incidencia en la fecha "+fecha+" para el número de trabajador "+numero, function(e)
        {
            if(e)
            {
                history.back();
            }
        }); 
    }

    function Ya(numero,fecha)
    {
        alertify.alert("Esta incidencia ya fue justificada antes", function(e)
        {
            if(e)
            {
                history.back();
            }
        }); 
    }

    function Correcto()
    {
        alertify.alert("Justificacion agregada Correctamente", function(e)
        {
            if(e)
            {
                location.href="../../ht/aprobaciones.php";
            }
        }); 
    }

    function Error()
    {
        alertify.alert("Algo salió mal", function(e)
        {
            if(e)
            {
                history.back();
            }
        }); 
    }

    function no()
    {
        alertify.alert("Ya posee 2 justificaciones o  2 omisiones o 1 omisión+ 1 justificación. Sustento: Art. 46 CGT", function(e)
        {
            if(e)
            {
                history.back();
            }
        }); 
    }

    function noMaxComision(fecha1, fecha2)
    {
        alertify.alert("El periodo entre las fechas "+fecha1+" y "+fecha2+" es superior a 5 meses y medio. NO ES POSIBLE TENER UNA COMISIÓN QUE DURE ESE TIEMPO.", function(e)
        {
            if(e)
            {
                history.back();
            }
        }); 
    }

    function noComision(numero)
    {
        alertify.alert("El trabajador con número "+numero+ " Ya posee una comisión activa. NO ES POSIBLE TENER 2 COMISIONES A LA VEZ", function(e)
        {
            if(e)
            {
                history.back();
            }
        }); 
    }

    function siComision()
    {
        alertify.alert("La comisión se agregó correctamente", function(e)
        {
            if(e)
            {
                location.href="../../ht/aprobaciones.php";
            }
        }); 
    }

    function noOmision()
    {
        alertify.alert("Ya posee 2 omisiones o 2 faltas o 1 omisión + 1 justifiación. Sustento: Art. 46 CGT", function(e)
        {
            if(e)
            {
                history.back();
            }
        }); 
    }

    function antesOmision()
    {
        alertify.alert("Esta omisión ya fue justificada antes.", function(e)
        {
            if(e)
            {
                history.back();
            }
        });
    }

    function omisionNoExiste(numero,fecha)
    {
        alertify.alert("No hay una omisión en la fecha "+fecha+" para el número de trabajador "+numero, function(e)
        {
            if(e)
            {
                history.back();
            }
        });
    }

    function omisionCorrecta()
    {
        alertify.alert("Omision justificada correctamente.", function(e)
        {
            if(e)
            {
                location.href="../../ht/aprobaciones.php";
            }
        });
    }

    function imprime(texto)
    {
        alertify.alert(texto, function(e)
        {
            if(e)
            {
                history.back();
            }
        });
    }

    function todoCorrecto(texto)
    {
        alertify.alert(texto, function(e)
        {
            if(e)
            {
                location.href="../../ht/aprobaciones.php";
            }
        });
    }
</script>