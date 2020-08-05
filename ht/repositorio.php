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
    require("../Acceso/global.php");  
    //$numero= $_SESSION['num_emp']; //NÚMERO DE EMPLEADO
    //$numero=$nombre;
    $sql="select mail from mail where trabajador_trabajador='$nombre';";
    $query= mysqli_query($con, $sql);
    $resul=mysqli_num_rows($query);
    if($resul>0)
    {
      $resul=mysqli_fetch_array($query);
      $email=$resul[0];
    }

?>
<!doctype html>

<!--[if gt IE 8]><!-->
<html class="no-js" lang="">
    <!--<![endif]-->


    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>
           Repositorio de formatos
        </title>
        <meta name="description" content="Sistema de Control de Asistencia" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="../assets/css/reportes.css" />
        <link rel="apple-touch-icon" href="apple-icon.png" />
        <link rel="shortcut icon" href="favicon.ico" />
        <link rel="stylesheet" href="../assets/css/normalize.css" />
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="../assets/css/font-awesome.min.css" />
        <link rel="stylesheet" href="../assets/css/themify-icons.css" />
        <link rel="stylesheet" href="../assets/css/flag-icon.min.css" />
        <link rel="stylesheet" href="../assets/css/cs-skin-elastic.css" />
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.css"
        />
        <link rel="stylesheet" href="../assets/scss/style.css" />
        <link href="../assets/css/lib/vector-map/jqvmap.min.css" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.full.min.js"></script>
        <script>
        function oculta(x)
            {
                if(x==0)
                {
                    document.getElementById('fecha').style.display="none";//ver
                    document.getElementById('causa').style.display="block";//no ver
                }
                else
                {
                    document.getElementById('causa').style.display="none";//ocultar
                    document.getElementById('fecha').style.display="block";//ver
                }
   
            }
            function inicio()
            {
                document.getElementById('causa').style.display="none";//ocultar
                document.getElementById('fecha').style.display="none";//ocultar   
            }

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

    </head>

    <body onload="inicio()">
        <!-- Left Panel -->
        <aside id="left-panel" class="left-panel">
            <nav class="navbar navbar-expand-sm navbar-default">
                <div class="navbar-header">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                    <a class="navbar-brand" href="#">EMPLEADO <?php echo$nombre?></a>
                    <a class="navbar-brand hidden" href="#"></a>
                </div>
             <!-- SIRVE PARA CAMBIAR LAS OPCIONES DEL MENÚ DEL EMPLEADO -->
                <div id="main-menu" class="main-menu collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <!-- <a href="../panel_control.php"> <i class="menu-icon fa fa-dashboard"></i>Panel de Control </a> -->
                        </li>
                    
                        <li id="Menu_Sistema" ><!-- class="menu-item-has-children dropdown" -->
                            <a href="#" class="dropdown-toggle" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#mimodalejemplo"  name="boton"><i class="menu-icon fa fa-key"></i>Cambiar contraseña</a>
                            <!--<ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-users"></i><a href="../ht/usuarios.php">Mi usuario</a></li>
                            </ul>-->
                        </li>
                    </ul>
                </div>
             <!-------------------------------------------------------------------- -->
            </nav>
        </aside>
        <!-- /#left-panel -->

        <!-- Left Panel -->

        <!-- Right Panel -->

        <div id="right-panel" class="right-panel">

            <!-- Header-->
            <header id="header" class="header">

                <div class="header-menu">

                    <div class="col-sm-7">
                        <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
                        <div class="header-left">
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="user-area dropdown float-right">

                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="user-avatar rounded-circle" src="../images/admin.png" alt="User">
                            </a>
                            <div class="user-menu dropdown-menu">
                                <a class="nav-link" href="#" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#mimodalejemplo"  name="boton"><i class="fa fa-key"></i> Cambiar contraseña</a>
                                <a class="nav-link" href="../php/logout.php"><i class="fa fa-power-off"></i> Salir</a>
                            </div>
                        </div>
                    </div>
                </div>

            </header>
            <!-- /header -->

            <div class="breadcrumbs">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>FORMATOS</h1>
                        </div>
                    </div>
                </div>
               
            </div>

            <div class="content mt-3">
                <div class="animated fadeIn">

                    <div class="row">
                        <div class="col-lg-12">
                        </div>
                        <div class="col-lg-12">
                            <form method="post" action="../pdf/crearPdf.php" id="form2">
                                <div class="card">
                                    <div class="card-header">
                                       <b><span id="MainContent_lbtitulo">Seleccione el documento que desee: </span></b>
                                    </div>
                                    <div class="card-body card-block"> 
                                        <div>                               
                                            <label for="jus-omi"><input type="radio" id="jus-omi" name="formato" class="form-input" value="1" onclick='oculta(1)'/> Justificación de omisión de entrada &nbsp</label>
                                            <label for="jus-omi-sal"><input type="radio" id="jus-omi-sal" name="formato" class="form-input" value="2" onclick='oculta(1)'/> Justificación de omisión de salida &nbsp</label>
                                            <label for="jus-ret"><input type="radio" id="jus-ret" name="formato" class="form-input" value="3" onclick='oculta(1)' /> Justificación de retardo &nbsp</label>
                                            <label for="ps"><input type="radio" id="ps" name="formato" class="form-input" value="4" onclick='oculta(0)' /> Pase de salida</label>
                                        </div>   
                                        <div class="col-mg-10">
                                            <div id="causa">
                                               <b><span>Deseo solicitar un pase de salida para: </span></b><br><input type='text' class='form-control' name='motivo'>
                                                <!-- <span>Fecha en la que deseo usar mi pase de salida: </span> <br> <input type='date' class='form-control' name='fecha'><br> -->
                                            </div>
                                            <div id="fecha">
                                               <b><span>Fecha que deseo justificar: </span></b><br><input type='date' class='form-control' name='f-justifica'>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="descargar">
                                    <input class="btn btn-primary black-background white" name="descargar" type="submit" value="Descargar" />
                                </div>
                            </form>
                        </div>
                    </div><!-- .animated --> 
                </div><!-- .content -->
            </div>

            <!-- inicio de modal -->
            <div class="modal fade" id="mimodalejemplo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content" id="modal" >
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">MI USUARIO</h4>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <form method="post" action="../php/update/modificarPass.php" autocomplete="off" >
                                        <label> Usuario</label>
                                        <div class="input-group">
                                            <input type="text" name="numControl" Class="form-control" value="<?php echo$nombre;?>" disabled>
                                        </div>
                                        <label>Correo electrónico</label>
                                        <div class="input-group">
                                            <input type="email" name="email" Class="form-control" value="<?php echo$email;?>">
                                        </div>
                                        
                                        <label> Ingrese contraseña actual</label>
                                        <div class="input-group">
                                            <input id="txtPassword" type="Password" Class="form-control" name="contraActual" required maxlength=4 minlength=4 pattern="[0-9]{4}"  title="Ingrese exactamente 4 números">
                                            <div class="input-group-append">
                                                <button id="show_password" class="btn btn-primary" type="button" onclick="mostrarPasswordActual();"> <span class="fa fa-eye-slash icon" id="A" ></span> </button>
                                            </div>
                                        </div>
                                        
                                        <label> Ingrese nueva contraseña</label>
                                        <div class="input-group">
                                            <input id="txtPassword2" type="Password" Class="form-control" name="nuevaContra" required maxlength=4 minlength=4 pattern="[0-9]{4}"  title="Ingrese exactamente 4 números">
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
            <!-- fin modal -->

            <script type="text/javascript">
                $(document).ready(function() {
                    setTimeout(function() {
                        $(".alert").fadeOut(1500);
                    }, 4000);
                    $('table.display').DataTable({
                        "language": {
                            "emptyTable": "<i>No hay datos disponibles en la tabla.</i>",
                            "info": "Mostrando del _START_ al _END_ de _TOTAL_ ",
                            "infoEmpty": "Mostrando 0 registros de un total de 0",
                            "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                            "loadingRecords": "Cargando...",
                            "processing": "Procesando...",
                            "search": "<span style='font-size:15px;'>Buscar:</span>",
                            "searchPlaceholder": "Dato para buscar",
                            "zeroRecords": "No se han encontrado coincidencias.",
                            "paginate": {
                                "first": "Primera",
                                "last": "Última",
                                "next": "Siguiente",
                                "previous": "Anterior"
                            },
                            "aria": {
                                "sortAscending": "Ordenación ascendente",
                                "sortDescending": "Ordenación descendente"
                            }
                        },
                        responsive: true,
                        dom: 'Bfrtip',
                        buttons: [{
                                extend: 'copy',
                                text: 'Copiar'
                            },
                            //'csv',
                            'excel',
                            'pdf',
                            //{ extend: 'print', text: 'Imprimir' },
                        ]
                    });
                });
            </script>
        </div>
        <!-- /#right-panel -->

        <!-- Right Panel -->

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
        <script src="../assets/js/plugins.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                setTimeout(function() {
                    $(".alert").fadeOut(1500);
                }, 4000);
                $('#menuToggle').on('click', function(event) {
                    $('body').toggleClass('open');
                });
            });
        </script>
    </body>
  
 </html>
