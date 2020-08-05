<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];// numero del trabajador
        $contra=$_SESSION['con'];
        require("../Acceso/global.php");

        $ejecu=mysqli_query($con,"Select CONCAT(a.nombre, ' ', a.apellido_paterno) as n from trabajador a where a.numero_trabajador = '$nombre';");
        $resul=mysqli_num_rows($ejecu);
        if($resul>0)
        {
            $resul=mysqli_fetch_array($ejecu);
            $nom_apellido=$resul[0];
        }
        else
        {
            $nom_apellido=$nombre;   
        }

        if(!empty($_SESSION['verCampo']))
        {
            $div=$_SESSION['verCampo'];
            // echo"<script language= javascript type= text/javascript>alert('$div');</script >";
        }
    }
    else
    {
        header("Location: ../../index.php");
        die();
    }

?>
<!doctype html>
<html class="no-js" lang="">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Control de Asistencia</title>
        <meta name="description" content="Sistema de Control de Asistencia">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-icon.png">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" href="../assets/css/inicio.css">
        <link rel="stylesheet" href="../assets/css/normalize.css">
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="../assets/css/themify-icons.css">
        <link rel="stylesheet" href="../assets/css/flag-icon.min.css">
        <link rel="stylesheet" href="../assets/css/cs-skin-elastic.css">

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.css"
        />

        <link rel="stylesheet" href="../assets/scss/style.css">
        <link href="../assets/css/lib/vector-map/jqvmap.min.css" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>
        <script>  
            
            function mostrarPasswordActual()
            {
                var cambio = document.getElementById("txtPassword");
                if(cambio.type == "password")
                {
                    cambio.type = "text";
                    $('#A').removeClass('fa fa-eye-slash').addClass('fa fa-eye');//El ide del icono es A
                }
                else
                {
                    cambio.type = "password";
                    $('#A').removeClass('fa fa-eye').addClass('fa fa-eye-slash');//El ide del icono es N
                }
            }
            function mostrarPasswordNueva()
            {
                
                var cambio2 = document.getElementById("txtPassword2");
                if(cambio2.type == "password")
                {
                    cambio2.type = "text";
                    $('#N').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
                }
                else
                {
                    cambio2.type = "password";
                    $('#N').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
                }  
	        }

            function oculta(miID)
            {
                var numero=miID;
                if(numero==1)
                {
                    document.getElementById('cont').style.display="block";//ver div de contrasena
                    document.getElementById('mail').style.display="none";//no ver
                }
                if(numero==2)
                {
                    document.getElementById('cont').style.display="none";
                    document.getElementById('mail').style.display="block";//ver div mail
                }
                if(numero==3)
                {
                    document.getElementById('cont').style.display="block";
                    document.getElementById('mail').style.display="block";
                }
            } 
        </script>
    </head>
    <body class="bg-dark" >
        <div class="sufee-login d-flex align-content-center flex-wrap">
            <div class="container">
                <div class="login-content">
                    <div class="login-logo">
                        <img src="../images/LOGO_ISSSTE.png"></img>
                        <a href="#">
                            <span>¡Bienvenido(a) <?php echo $nom_apellido?> !</span>
                        </a>
                    </div>
                    <div class="login-form">
                        <form name="formulario" method="post" action="../php/update/modificarPassPrimeraVez.php">
                            <div id="mail"> 
                                <div class="form-group">
                                    <label>Ingrese su dirección de correo electrónico </label>
                                    <input name="mail"  type="email" class="form-control" autofocus/>
                                </div>
                            </div>
                            <div id="cont">
                                <label> Ingrese una nueva contraseña</label>
                                <div class="input-group">
                                    <input id="txtPassword" type="Password" Class="form-control" name="nuevaContra" maxlength=4 minlength=4 pattern="[0-9]{4}"  title="Ingrese exactamente 4 números">
                                    <div class="input-group-append">
                                        <button id="show_password" class="btn btn-primary" type="button" onclick="mostrarPasswordActual();"> <span class="fa fa-eye-slash icon" id="A" ></span> </button>
                                    </div>
                                </div> 
                                <label> Confirmar nueva contraseña</label>
                                <div class="input-group">
                                    <input id="txtPassword2" type="Password" Class="form-control" name="confirmaContra" maxlength=4 minlength=4 pattern="[0-9]{4}"  title="Ingrese exactamente 4 números">
                                    <div class="input-group-append">
                                        <button id="show_password2" class="btn btn-primary" type="button" onclick="mostrarPasswordNueva();"> <span class="fa fa-eye-slash icon" id="N"></span> </button>
                                    </div>
                                </div>
                            </div><br>
                            <div class="form-group">
                                <input type="submit" name="guardar" value="Guardar" id="guardar" class="btn btn-success btn-flat m-b-30 m-t-30" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                setTimeout(function() {
                    $(".alert").fadeOut(1500);
                }, 4000);
            });
        </script>
    </body>

</html>
<?php
echo"<script language= javascript type= text/javascript> alert('Consejo de seguridad: No comparta este correo con ningún otro empleado, para evitar pérdida de información.');</script>";
 
    if($div=="pass")
    {
        echo"<script language= javascript type= text/javascript> oculta(1);</script >";
    }
    else
    {
        if($div=="correo.")
        {
            echo"<script language= javascript type= text/javascript> oculta(2);</script >";
        }
        else
        {
            if($div=="correo.pass")
            {
                echo"<script language= javascript type= text/javascript> oculta(3);</script >";
            }
        }
    }
?>