<?php
    function guardarMail($miMail,$miUsuario)
    {
        global $con;
        $sql="INSERT INTO mail (mail,trabajador_trabajador) VALUES ('$miMail', '$miUsuario');";
        $query=mysqli_query($con, $sql);
        if($query)
        {
            return true;
        }
        else
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='5';
            error($er1,$er2,$línea);
        }
    }

    function error($er1,$er2,$numLinea)
    {
        $error="";
        $err1="$er1";
        $err2="$er2";
        //Hacer UN EXPLODE DE ERR2
        $divide=explode("'",$err2);
        $tamDivide=count($divide);//saber el tamaño del array
        if($tamDivide>0)//si el array posee datos
        {
            $err2="";
            for($i=0;$i<$tamDivide;$i++)
            {
                $err2.=$divide[$i];
            }
        }
        $error="$err1 : $err2. Línea de error: $numLinea. Verifique con el administrador de sistemas";
        echo"<script>error('$error'); </script>";
        exit();
    }
?>
<script type="text/javascript">
    function error(cadena)
    {
        alert(cadena);
        history.back();
        exit();
    }
</script>