<?php

    function guardarMail($mail)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
        $sql="INSERT INTO mail VALUES ('', '$mail', '$nombre');";
        $query= mysqli_query($con, $sql) or die();
        return 0;
    }
?>
