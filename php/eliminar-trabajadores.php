<?php 
session_start();
  if (($_SESSION["name"]) && ($_SESSION["con"]))
  {
    $nombre=$_SESSION['name'];
    $contra=$_SESSION['con'];
    require("../Acceso/global.php"); 
    $nombre_host= gethostname();
    require('buscar_info_trabajador.php');
  }
  else
  {
    header("Location: ../index.php");
    die();
  }
  //obtener el id que se mandó acá
  $encriptado=$_GET['jhgtp09'];
  $id=base64_decode($encriptado);
  //Obtener la información para la bitacora de trabajador
  $trabajador=consultaTrabajador($id);
  $descripcionTipo=describeTipoTrabajador($id,$trabajador[6]);

  mysqli_autocommit($con, FALSE);
  if(!(mysqli_query($con,"DELETE FROM trabajador WHERE numero_trabajador = '$id'")))
  {
    //die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
    $error="Error al eliminar al trabajador, contacte al administrador. Líneas de error: 19.";
    echo "<script> imprime('$error'); </script>";
    mysqli_rollback($con);
    mysqli_autocommit($con, TRUE); 
  }
  else
  {
    $correcto=bitacoraTrabajador('Eliminado',$id, $trabajador[1], $trabajador[2], $trabajador[3], $trabajador[4], $trabajador[5], $descripcionTipo ,$trabajador[7], $nombre_host);
    if($correcto== true)
    {
      mysqli_commit($con);
      mysqli_autocommit($con, TRUE);
      echo"<script>alert('Eliminado correctamente'); location.href='../ht/trabajadores.php';</script>";
    }
  } 

  function bitacoraTrabajador($accion,$numero_anterior, $nombre_anterior, $a_pat_anterior, $a_mat_anterior, $depto_anterior, $cat_anterior, $tipo_anterior, $genero_anterior, $nombre_host)
  {
    global $con;
    
    $sql="call inserta_bitacora_trabajador('$accion', '-', '-', '-', '-', '-', '-', '-', '-', '$numero_anterior', '$nombre_anterior', '$a_pat_anterior', '$a_mat_anterior', '$depto_anterior', '$cat_anterior', '$tipo_anterior', '$genero_anterior', '$nombre_host');"; 
    mysqli_autocommit($con, FALSE);//quitar el autocommit hasta que todo haya resultado correcto
    if(!(mysqli_query($con,$sql)))
    {
      $error="Error al insertar datos en la bitacóra trabajador, verifique. En caso de que no sea ese el problema contacte al administrador. Líneas de error: 33.";
      echo "<script> imprime('$error'); </script>";
      mysqli_rollback($con);
      mysqli_autocommit($con, TRUE); 
    }
    else
    {
      return true;
    } 
  }
?>