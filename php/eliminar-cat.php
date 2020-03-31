<?php 
session_start();
?>
<script type="text/javascript">
    function Correcto()
    {
        alert("Eliminado correctamente");
        location.href="./../ht/categoria.php";
    }
</script>
<?php
  //obtener el id que se mandó acá
  $id=$_GET['id'];
  
  require("../Acceso/global.php");
  $sql="DELETE FROM categoria WHERE idcategoria = '".$id."'";
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
