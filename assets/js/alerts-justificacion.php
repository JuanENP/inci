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
</script>