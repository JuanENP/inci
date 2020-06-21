<?php  
    $sql="select mail from mail where trabajador_trabajador='$nombre';";
    $query= mysqli_query($con, $sql);
    $resul=mysqli_num_rows($query);
    if($resul>0)
    {
      $resul=mysqli_fetch_array($query);
      $email=$resul[0];
    }
    else
    {
        $email='';
    }
?>
<script>
    function mostrarPasswordActual($x)
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
            $('#N').removeClass('fa fa-eye-slash N').addClass('fa fa-eye');
        }
        else
        {
            cambio2.type = "password";
            $('#N').removeClass('fa fa-eye N').addClass('fa fa-eye-slash');
        }
        
        
	} 

</script>
<html>
    <!-- ESTA MODAL SOLO ESE MUESTRA A USUARIOS LOS USUARIOS JEFES -->
    <head>
        <!-- <link rel="stylesheet" href="../assets/css/reportes.css" /> -->
    </head>
    <body>  
        <div class="modal fade" id="mimodalejemplo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" id="modal" >
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">MI USUARIO</h4>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <form method="post" action="php/update/modificarPass.php" >
                                    <label> Usuario</label>
                                    <div class="input-group">
                                        <input type="text" name="numControl" Class="form-control" disabled="disabled" value="<?php echo$nombre;?>">
                                    </div>
                                    <label>Correo electrónico</label>
                                    <div class="input-group">
                                        <input type="email" name="email" Class="form-control" value="<?php echo $email;?>">
                                    </div>
                                    
                                    <label> Ingrese contraseña actual</label>
                                    <div class="input-group">
                                        <input id="txtPassword" type="Password" Class="form-control" name="contraActual" required  minlength=4>
                                        <div class="input-group-append">
                                            <button id="show_password" class="btn btn-primary" type="button" onclick="mostrarPasswordActual();"> <span class="fa fa-eye-slash icon" id="A" ></span> </button>
                                        </div>
                                    </div>
                                    
                                    <label> Ingrese nueva contraseña</label>
                                    <div class="input-group">
                                        <input id="txtPassword2" type="Password" Class="form-control" name="nuevaContra" required minlength=4>
                                        <div class="input-group-append">
                                            <button id="show_password2" class="btn btn-primary" type="button" onclick="mostrarPasswordNueva();"> <span class="fa fa-eye-slash icon" id="N"></span> </button>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">            
                                        <button type="submit" id="guardar" class="btn btn-primary">Guardar</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    </div><!--fin modal-footer-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>