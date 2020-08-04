<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php");
        $ubicacion='../php/update/modificarPass.php';//sirve para indicar la ruta del form modalCambiarPass
    }
    else
    {
        header("Location: ../index.php");
        die();
    }
?>
<!doctype html>
    <html class="no-js" lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>
        Vacaciones
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

        <link rel="stylesheet" href="../assets/css/alertify.core.css" />
        <link rel="stylesheet" href="../assets/css/alertify.default.css" />
        <link rel="stylesheet" href="../assets/css/jquery-ui.css" />

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
        <script src="../assets/js/jquery.min.js"></script>
        <script src="../assets/js/main.js"></script>
        <script src="../assets/js/alertify.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
        <script src="../assets/js/plugins.js"></script>
        <script src="../assets/js/jquery-ui.js"></script>

        <script type="text/javascript">
            $(document).ready(function() 
            {
                setTimeout(function() 
                {
                    $(".alert").fadeOut(1500);
                }, 4000);

                $('#menuToggle').on
                ('click', function(event) 
                    {
                        $('body').toggleClass('open');
                    }
                );

                $('.btn-sm').on('click', function() {
                    //Añadimos la imagen de carga en el contenedor
                    $('#img-loading').html('<div class="loading"><img src="../images/loader.gif"/><br/>Estoy Procesando la información, sea paciente :)...</div>');
                });
            });

            function oculta(x) 
            {
                if(x==0)
                {
                    document.getElementById('lote').style.display = "block";
                    document.getElementById('individual').style.display = "none";
                }
                else
                {
                    if(x==1)
                    {
                        document.getElementById('lote').style.display = "none";
                        document.getElementById('individual').style.display = "block";
                    }
                }
            }

            function inicio()
            {
                var rad = $("input[name='opcion']:checked").val();
                if(rad=="lote")
                {
                    document.getElementById('lote').style.display = "block";
                    document.getElementById('individual').style.display = "none";
                }
                else
                {
                    if(rad=="indiv")
                    {
                        document.getElementById('lote').style.display = "none";
                        document.getElementById('individual').style.display = "block";
                    }
                } 
            }

            function buttonDisabled(idbutton) 
            {
                document.getElementById(idbutton).style.display = "none";
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
                    <a class="navbar-brand" href="#">Control de Asistencia</a>
                    <a class="navbar-brand hidden" href="#"></a>
                </div>

                <div id="main-menu" class="main-menu collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="../panel_control.php"> <i class="menu-icon fa fa-dashboard"></i>Panel de Control</a>
                        </li>
                        <li id="Menu_Personal" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-users"></i>Personal</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-crosshairs"></i><a href="categoria.php">Categorias</a></li>
                                <li><i class="fa fa-sitemap"></i><a href="departamentos.html">Departamentos</a></li>
                                <li><i class="fa fa-male"></i><a href="tipoempleado.html">Tipo Empleado</a></li>
                                <li><i class="fa fa-users"></i><a href="trabajadores.php">Personal</a></li>
                            </ul>
                        </li>
                        <li id="Menu_Dispositivo" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-desktop"></i>Dispositivo</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-plus-circle"></i><a href="../ht/dispositivos.php">Dispositivo</a></li>
                            </ul>
                        </li>
                        <li id="Menu_Asistencia" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-clock-o"></i>Asistencia</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-calendar"></i><a href="../ht/turnos.php">Turnos</a></li>
                                <li><i class="fa fa-check-square-o"></i><a href="../ht/aprobaciones.php">Aprobaciones</a></li>
                                <li><i class="fa fa-files-o"></i><a href="../ht/reportes.php">Reportes</a></li>
                                <li><i class="fa fa-shield"></i><a href="../ht/conceptos.html">Tipo de Incidencias</a></li>
                            </ul>
                        </li>
                        <li id="Menu_Sistema" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Sistema</a>
                            <ul class="sub-menu children dropdown-menu">
                                <?php 
                                    if($nombre=="AdministradorGod")
                                    {
                                        echo "<li><i class='fa fa-users'></i><a href='../ht/usuarios.php'>Usuarios</a></li>";
                                        
                                    }
                                ?>
                                 <li><a class="nav-link" href="#" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#mimodalejemplo"  name="boton"><i class="fa fa-key"></i> Cambiar contraseña</a></li>
                            </ul>
                        </li>
                    </ul>
                </div> <!--FIN menu-principal-->
                
            </nav> <!-- FIN navbar-collapse -->
        </aside> <!-- FIN DE ASIDE_left-panel -->

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
                                <a class="nav-link" href="#" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#mimodal"  name="boton"><i class="fa fa-key"></i> Cambiar contraseña</a>
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
                            <h1>Vacacaciones normales</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Vacaciones</a></li>
                                <li class="active">Normales</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <form id="f1" method="POST" action="../php/insert/vac.php" enctype="multipart/form-data">
                <div class="content mt-3">
                    <div class="animated fadeIn">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <span id="lbl_lote">
                                            Opciones
                                        </span>
                                    </div>
                                    
                                    <div class="card-body card-block">
                                        <div class="form-1-2">
                                            <input type="radio" name="opcion" value="lote" id="lot" onclick="oculta(0)"> <label for="lot">Insertar por lote a través de excel (Solo 1 vez por año)</label>
                                        </div> 
                                        <div class="form-1-2">
                                            <input type="radio" name="opcion" value="indiv" id="ind" checked onclick="oculta(1)"> <label for="ind">Insertar días de vacaciones a un trabajador en específico</label>  
                                        </div> 
                                    </div> 
                                        
                                </div>
                            </div>
                        </div>

                        <div class="row" id="lote">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <span id="lbl_lote">
                                            Inserción por lote
                                        </span>
                                    </div>
                                    
                                    <div class="card-body card-block">
                                        <span id="">Excel a subir:</span>
                                        <input type="file" name="archivo[]" id="myfile">
                                    </div>   
                                </div>
                            </div>
                        </div>

                        <div class="row" id="individual">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <span id="lbl_lote">
                                            Inserción individual
                                        </span>
                                    </div>
                                    
                                    <div class="card-body card-block">
                                        <span>Trabajador</span>
                                        <div class="form-1-2">
                                            <input type="text" name="caja_busqueda" id="caja_busqueda" autocomplete="off" class="form-control">
                                        </div>
                                        <div class="form-1-2" id="datos">
                                            <!--aquí se cargan los trabajadores-->
                                        </div>
                                    </div> 
                                        
                                </div>
                            </div>
                        </div>
                        <br><br><br><br><br><br><br><br><br>
                        <div class="card-footer">
                            <input type="submit" name="guardar" value="GUARDAR" class="btn btn-primary btn-sm" id="final" onclick="buttonDisabled(this.id)"/>

                            <div id="img-loading" class="col-lg-12">
                            </div>
                        </div>

                    </div> <!--FIN DIV animated fadeIn-->
                </div> <!--FIN DIV content mt-3--> 
            </form>  <!-- FIN DEL FORM -->
        </div> <!-- FIN right-panel -->       
    </body>
    <?php require("../ht/modalCambiarPass.php"); ?>
</html>