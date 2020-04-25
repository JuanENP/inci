<script type="text/javascript">
    function No_Existe(numero,fecha)
    {
        alert("No hay una incidencia en la fecha "+fecha+" para el número de trabajador "+numero);
        history.back();
    }

    function Ya(numero,fecha)
    {
        alert("Esta incidencia ya fue justificada antes");
        history.back();
    }

    function Correcto()
    {
        alert("Justificacion agregada Correctamente");
        location.href="../../ht/aprobaciones.php";
    }

    function Error()
    {
        alert("Algo salió mal");
        history.back();
    }

    function no()
    {
        alert("Ya posee 2 justificaciones o  2 omisiones o 1 omisión+ 1 justificación. Sustento: Art. 46 CGT");
        history.back();
        //window.close();
    }

    function noMaxComision(fecha1, fecha2)
    {
        alert("El periodo entre las fechas "+fecha1+" y "+fecha2+" es superior a 5 meses y medio. NO ES POSIBLE TENER UNA COMISIÓN QUE DURE ESE TIEMPO.");
        history.back();
    }

    function noComision(numero)
    {
        alert("El trabajador con número "+numero+ " Ya posee una comisión activa. NO ES POSIBLE TENER 2 COMISIONES A LA VEZ");
        history.back();
    }

    function siComision()
    {
        alert("la comisión se agregó correctamente");
        location.href="../../ht/aprobaciones.php";
    }

    function noOmision()
    {
        alert("Ya posee 2 omisiones o 2 faltas o 1 omisión + 1 justifiación");
        history.back();
    }

    function antesOmision()
    {
        alert("Esta omisión ya fue justificada antes.");
        history.back();
    }

    function omisionNoExiste(numero,fecha)
    {
        alert("No hay una omisión en la fecha "+fecha+" para el número de trabajador "+numero);
        history.back();
    }

    function omisionCorrecta()
    {
        alert("Omision justificada correctamente.");
        location.href="../../ht/aprobaciones.php";
    }

    function imprime(texto)
    {
        alert(texto);
        history.back();
    }
</script>