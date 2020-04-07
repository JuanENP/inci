<?php 
session_start();
$nombre=$_SESSION['name'];
$contra=$_SESSION['con'];
//si la variable de sesión no existe, entonces no es posible entrar al panel. 
//Lo redirigimos al index.html para que inicie sesión
if($nombre==null || $nombre=='')
{
    header("Location: ../index.html");
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
        location.href="./../ht/categoria.php";
    }
</script>
<?php
  //obtener el id que se mandó acá
  $id=$_GET['id'];
  
  $nombre=$_SESSION['name'];
  $contra=$_SESSION['con'];
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
