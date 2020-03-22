<?php

    session_start();
        //obtener el id que se mandó acá
        $id=$_GET['id'];
        
        require("../Acceso/global.php");
        $sql="DELETE FROM depto WHERE depto = '".$id."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
          die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        {
          echo"<script>alert('Eliminado correctamente')</script>";
        }
        mysqli_close($con);   
    
?>
