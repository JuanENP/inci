
<?php 

session_start();
$num=$_POST['num'];
$nombre=$_POST['nom'];
$a_pat=$_POST['a_pat'];
$a_mat=$_POST['a_mat'];
$cat=$_POST['cat'];
$dep=$_POST['depto'];
$tipo=$_POST['tipo'];
$turno=$_POST['turno'];

$semana = array(0,0,0,0,0,0,0);
$dias=$_POST['dia'];
$num=count($dias);
for($n=0;$n<$num;$n++)
{
    if($dias[$n]=="lunes")
    {  
         $semana[0]=1;
    }
    if($dias[$n]=="martes")
    {  
         $semana[1]=1;
    }
    if($dias[$n]=="miercoles")
    {  
         $semana[2]=1;
    }
    if($dias[$n]=="jueves")
    {  
         $semana[3]=1;
    }
    if($dias[$n]=="viernes")
    {  
         $semana[4]=1;
    }
    if($dias[$n]=="sabado")
    {  
         $semana[5]=1;
    }
    if($dias[$n]=="domingo")
    {  
         $semana[6]=1;
    }
}
for($i=0; $i<7; $i++)
{
    echo($semana[$i]);
    echo"<br>";
}


//-------------------------AQUI INSERTO PRIMERO A LA TABLA ACCESO Y LUEGO  AL TRABAJADOR------------------------------------//
     //Aqui consulto los datos 
     require("../Acceso/global.php");  
        if(!(mysqli_query($con,"Insert into acceso values ('','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$turno)")))
        {
         //Ocurrió algún error
         echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
         die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
         else
        {

            //Aqui consulto si existe una categoria igual a la que se va a guardar
            require("../Acceso/global.php");  
            $ejecu="select * from trabajador where numero_trabajador = '$num'";
            $codigo=mysqli_query($con,$ejecu);
            $consultar=mysqli_num_rows($codigo);
            echo $consultar;
            if($consultar>0)
            {
                    echo"<script>alert('Datos ya registrados')</script>";
            }
            elseif ($consultar<=0) 
            {
                if(!(mysqli_query($con,"Insert into trabajador values ('$nom','$nombre','$a_pat','$a_mat','$cat','$dep',$tipo,$acceso)")))
                {
                //Ocurrió algún error
                echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
                die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                }
                else
                {
                //Guardado correcto
                echo "<script type=\"text/javascript\">alert(\"Empleado guardado correctamente\");</script>";
                }
                mysqli_close($con);   

            }
        }

     
//-------------------------------------------------------------------------------------------------//





?>
