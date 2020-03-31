<script type="text/javascript">
    function Correcto()
    {
        alert("Correcto");
        location.href="./../ht/turno.php";
    }
</script>
<?php

    session_start();
        //obtener el id que se mandó acá
        $id=$_GET['id'];
        
        require("../Acceso/global.php");
        $sql="DELETE FROM turno WHERE idturno = '".$id."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
          die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        {
          echo"<script>Correcto();</script>";
        }
        mysqli_close($con);   
    
?>
