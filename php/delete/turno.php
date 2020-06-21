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
    alert("Turno eliminado correctamente");
    location.href="../../ht/turnos.php";
  }
</script>

<?php
  //obtener el id que se mandó acá
  $id=$_GET['id'];
  mysqli_autocommit($con, FALSE);
  $nombre_host= gethostname();

  $ejecu="select * from turno where idturno = '$id'";
  $codigo=mysqli_query($con,$ejecu);
  $resul=mysqli_fetch_array($codigo);
  $hora_ent=$resul[1];
  $hora_sal=$resul[2];
  $total_tiempo=$resul[3];

  if(!(mysqli_query($con,"DELETE FROM turno WHERE idturno = '".$id."'")))
  {
    mysqli_rollback($con);
    mysqli_autocommit($con, TRUE); 
    echo "alert('Datos incorrectos en la tabla turno'); history.back();";
  }
  else
  {
    if(!(mysqli_query($con,"call inserta_bitacora_turno('Eliminado','-','','','','$id','$hora_ent','$hora_sal','$total_tiempo', '$nombre_host')")))
    {
      mysqli_rollback($con);
      mysqli_autocommit($con, TRUE); 
      echo "alert('Datos incorrectos en bitacora turno); history.back();";
    }
    else
    {
      mysqli_commit($con);
      mysqli_autocommit($con, TRUE);
      //Guardado correcto
      echo "<script> Correcto(); </script>";
    }
  }
  mysqli_close($con);   
?>
