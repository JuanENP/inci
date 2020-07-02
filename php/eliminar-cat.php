<?php 
session_start();
  $nombre=$_SESSION['name'];
  $contra=$_SESSION['con'];
  //si la variable de sesión no existe, entonces no es posible entrar al panel. 
  //Lo redirigimos al index.php para que inicie sesión
  if($nombre==null || $nombre=='')
  {
    header("Location: ../index.php");
    die();
  }
  else
  {
      //mandar el nom de usuario
      $_SESSION['name']=$nombre;
      $_SESSION['con']=$contra;
      
  }
?>
<script type="text/javascript">
    function Correcto()
    {
        alert("Eliminado correctamente");
        location.href="../ht/categoria.php";
    }
</script>
<?php
  //obtener el id que se mandó acá
  $id=$_GET['ff0_lo'];
  $cadena = base64_decode($id); // Decode
  
  $nombre=$_SESSION['name'];
  $contra=$_SESSION['con'];
  require("../Acceso/global.php");
  //SIRVE PARA SELECCIONAR EL NOMBRE LA CATEGORIA QUE SE VA A eliminar
  $sql="select * from categoria where idcategoria='$cadena'";
  $query= mysqli_query($con, $sql) or die();
  while($resul=mysqli_fetch_array($query))
  {
    $nombre_cat=$resul[1];
  }

  $sql="DELETE FROM categoria WHERE idcategoria = '".$cadena."'";
  $query= mysqli_query($con, $sql);
  if(!$query)
  {
    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
  }
  else
  {
    $nombre_host= gethostname();
    $sql="call inserta_bitacora_categoria('Eliminado','-','-','$cadena', '$nombre_cat ', '$nombre_host')";
    $query= mysqli_query($con, $sql) or die();
            
    echo"<script>Correcto();</script>";
  }

?>
