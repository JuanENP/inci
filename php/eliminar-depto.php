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
        location.href="../ht/departamentos.php";
    }
</script>

<?php
    //obtener el id que se mandó acá
  $id=$_GET['id'];
  
  $nombre=$_SESSION['name'];
  $contra=$_SESSION['con'];
  require("../Acceso/global.php");
  //SIRVE PARA SELECCIONAR EL NOMBRE LA CATEGORIA QUE SE VA A ACTUALIZAR
  $sql="select * from depto where iddepto='$id'";
  $query= mysqli_query($con, $sql) or die();
  while($resul=mysqli_fetch_array($query))
  {
    $nombre_depto=$resul[1];
  }
   $sql="DELETE FROM depto WHERE iddepto = '".$id."'";
   $query= mysqli_query($con, $sql);
   if(!$query)
   {
     die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
   }
   else
   {
    $nombre_host= gethostname();
    $sql="call inserta_bitacora_depto('Eliminado','-','-','$id ', '$nombre_depto ', '$nombre_host')";
    $query= mysqli_query($con, $sql) or die();
            
    echo"<script>Correcto();</script>";
   }
   mysqli_close($con);   
    
?>
