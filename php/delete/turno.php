<?php 
session_start();
  if (($_SESSION["name"]) && ($_SESSION["con"]))
  {
    $nombre=$_SESSION['name'];
    $contra=$_SESSION['con'];
    require("../../Acceso/global.php"); 
  }
  else
  {
    header("Location: ../../index.html");
    die();
  }
?>

<script type="text/javascript">
  function Correcto()
  {
    alert("Correcto");
    location.href="../../ht/turnos.php";
  }
</script>

<?php
  //obtener el id que se mandó acá
  $id=$_GET['id'];
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
