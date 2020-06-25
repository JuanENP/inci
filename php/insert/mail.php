<?php
    function guardarMail($miMail)
    {
        global $con;
        global $nombre;
        $sql="INSERT INTO mail(mail,trabajador_trabajador) VALUES ('$miMail', '$nombre');";
        if(mysqli_query($con, $sql))
        {
            return 0;
        }
        else
        {
            echo mysqli_errno($con).": ".mysqli_error($con);
            exit();
        }
    }
?>