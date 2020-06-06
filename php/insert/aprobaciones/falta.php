<?php
    if((!empty($_POST["num"])) && (!empty($_POST["fec"])) && (!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
    {
        $num = $_POST['num'];
        $fecha=$_POST['fec'];
        //Validar las fechas
        $validarfechas=RevisarFechas(3,$fecha,"","de la falta","una falta","",1);
        $sql="SELECT idfalta from falta where fecha='$fecha' and trabajador_trabajador='$num' 
        and quincena='$quincena'";
        $filas=obtenerFilas($sql);
        echo "falta";
    }
    else //fin if posts vacíos
    {
        //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
        $error="Faltan los siguientes datos:"."<br>";
        if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
        if (empty($_POST["fec"])){$error.="La fecha de inicio de la falta a justificar."."<br>";} 
        if (empty($_FILES["archivo"]["name"][0])){$error.="El archivo escaneado"."<br>";}
        echo "<script> imprime('$error'); </script>";
    }
?>