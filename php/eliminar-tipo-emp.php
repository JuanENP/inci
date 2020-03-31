<?php
session_start();
?>
<script type="text/javascript">
    function Correcto()
    {
        alert("Eliminado correctamente");
        location.href="./../ht/tipoempleado.php";
        //window.close();
        //Si quieres usar instrucciones php, salte del script y coloca la apertura y cierre de php, escribe dentro de ellas de forma normal
    }
</script>
<?php
  //obtener el id que se mandó acá
  $id=$_GET['id'];
    
  require("../Acceso/global.php");
  $sql="DELETE FROM tipo WHERE idtipo = '".$id."'";
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
