<?php
session_start();
  if (($_SESSION["name"]) && ($_SESSION["con"]))
  {
    $nombre=$_SESSION['name'];
    $contra=$_SESSION['con'];
    require("../Acceso/global.php"); 
  }
  else
  {
      header("Location: ../index.html");
      die();
  }
  //obtener el id que se mandó acá
  $id=$_GET['id'];
  $sql="DELETE FROM trabajador WHERE numero_trabajador = '".$id."'";
  $query= mysqli_query($con, $sql);
  if(!$query)
  {
    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
  }
  else
  {
    mysqli_close($con); 
    echo"<script>alert('Eliminado correctamente'); location.href='../ht/trabajadores.php';</script>";
  }  
?>