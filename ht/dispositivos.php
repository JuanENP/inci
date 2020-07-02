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

 <html class="no-js" lang=""> 
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>
        Catalogo de Dispositivos
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
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.css"/>
        <link rel="stylesheet" href="../assets/scss/style.css" />
        <link href="../assets/css/lib/vector-map/jqvmap.min.css" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.full.min.js"></script>    
    </head>
    <body>
        <!-- Left Panel -->
        <aside id="left-panel" class="left-panel">
            <nav class="navbar navbar-expand-sm navbar-default">

                <div class="navbar-header">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand" href="#">Control de Asistecia</a>
                    <a class="navbar-brand hidden" href="#"></a>
                </div>

                <div id="main-menu" class="main-menu collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="../panel_control.php"> <i class="menu-icon fa fa-dashboard"></i>Panel de Control </a>
                        </li>
                        <li id="Menu_Personal" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-users"></i>Personal</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-crosshairs"></i><a href="../ht/categoria.php">Categorias</a></li>
                                <li><i class="fa fa-sitemap"></i><a href="../ht/departamentos.php">Departamentos</a></li>
                                <li><i class="fa fa-male"></i><a href="../ht/tipoempleado.php">Tipo Empleado</a></li>
                                <li><i class="fa fa-users"></i><a href="../ht/trabajadores.php">Personal</a></li>
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
                                 <li><a class="nav-link" href="#" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#mimodal"  name="boton"><i class="fa fa-key"></i> Cambiar contraseña</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </nav>
        </aside><!-- /#left-panel -->

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
                                <a class="nav-link" href="#" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#mimodal"  name="boton"><i class="fa fa-key"></i> Cambiar contraseña</a>
                                <a class="nav-link" href="../php/logout.php"><i class="fa fa-power-off"></i> Salir</a>
                            </div>
                        </div>
                    </div>
                </div>

            </header><!-- /header -->
            <!-- Header-->

        

            <div class="breadcrumbs">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Catálogo de Dispositivos</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Catálogos</a></li>                            
                                <li class="active">Dispositivos</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>        

            <div class="content mt-3">
                <div class="animated fadeIn">

                    <div class="row"> 
                        <div class="col-lg-12">
                                                    
                            <div id="MainContent_success" class="sufee-alert alert with-close alert-success alert-dismissible fade show">  
                                                <span id="MainContent_lbmsgsuccess"></span>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                        </div>                      
                        <div class="col-lg-6">                        
                        <div class="card">
                        <div class="card-header">
                            <span id="MainContent_lbtitulo"><strong>Nuevo Dispositivo</strong></span>
                        </div>
                        <div class="card-body card-block">
                            <div class="row form-group">
                                <div class="col col-md-4"><label for="txtDireccionIP" class=" form-control-label">Dirección IP:</label></div>
                                <div class="col-12 col-md-6"><input name="ctl00$MainContent$txtDireccionIP" type="text" id="MainContent_txtDireccionIP" placeholder="" class="form-control" />
                                    
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-4"><label for="txtNPuerto" class=" form-control-label">N° de Puerto:</label></div>
                                <div class="col-12 col-md-4"><input name="ctl00$MainContent$txtNPuerto" type="text" id="MainContent_txtNPuerto" placeholder="" class="form-control" />
                                    
                                </div>
                            </div>                                           
                        </div>
                        <div class="card-footer">
                            <input type="submit" name="ctl00$MainContent$btnAgregar" value="Agregar" id="MainContent_btnAgregar" class="btn btn-primary btn-sm" />
                        </div>
                        </div>                    
                    </div>                            
                    </div>          

                    <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Información</strong>
                            </div>
                            <div class="card-body">
                                <span id="MainContent_DataTable"><table id='' class='table table-striped table-bordered display'><thead><tr><th>Terminal</th><th>Descripcion</th><th>DireccionIP</th><th>PuertoIP</th><th>NumeroSerie</th><th>Activa</th><th>TFT</th><th>VersionFirmware</th><th>Plataforma</th><th>Acciones</th></tr></thead><tbody><tr><td>1</td><td>SFace900/ID</td><td>172.16.0.50</td><td>4370</td><td>AF5B174260391</td><td><span class='badge badge-success'>Activo</span></td><td>Pantalla a Color</td><td>SFace900/ID</td><td>SFace900/ID</td><td><a href='cat-dispositivos.aspx?f=delete&id=1'><button type='button' class='btn btn-danger btn-sm'><i class='fa fa-trash-o'></i>&nbsp;Eliminar</button></a></td></tr><tr><td>2</td><td>SFace900/ID</td><td>172.16.0.75</td><td>4370</td><td>AF5B174260628</td><td><span class='badge badge-success'>Activo</span></td><td>Pantalla a Color</td><td>SFace900/ID</td><td>SFace900/ID</td><td><a href='cat-dispositivos.aspx?f=delete&id=2'><button type='button' class='btn btn-danger btn-sm'><i class='fa fa-trash-o'></i>&nbsp;Eliminar</button></a></td></tr><tr><td>3</td><td>SFace900/ID</td><td>172.16.0.100</td><td>4370</td><td>AF5B174260650</td><td><span class='badge badge-success'>Activo</span></td><td>Pantalla a Color</td><td>SFace900/ID</td><td>SFace900/ID</td><td><a href='cat-dispositivos.aspx?f=delete&id=3'><button type='button' class='btn btn-danger btn-sm'><i class='fa fa-trash-o'></i>&nbsp;Eliminar</button></a></td></tr><tr><td>4</td><td>SFace900/ID</td><td>172.16.0.125</td><td>4370</td><td>AF5B174260661</td><td><span class='badge badge-success'>Activo</span></td><td>Pantalla a Color</td><td>SFace900/ID</td><td>SFace900/ID</td><td><a href='cat-dispositivos.aspx?f=delete&id=4'><button type='button' class='btn btn-danger btn-sm'><i class='fa fa-trash-o'></i>&nbsp;Eliminar</button></a></td></tr></tbody></table></span>
                            </div>
                        </div>
                    </div>


                    </div>
                </div><!-- .animated -->
            </div><!-- .content -->

    <script type="text/javascript">
        $(document).ready(function () {

            $('body').toggleClass('open');

            setTimeout(function () {
                $(".alert-danger").fadeOut(1500);
            }, 4000);

            setTimeout(function () {
                $(".alert-success").fadeIn(0);
                $(".alert-success").html("Introduzca la información del dispositivo, Dirección IP y Número de Puerto son campos obligatorios. <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>");
            }, 6000);

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
                buttons: [
                    { extend: 'copy', text: 'Copiar' },
                    //'csv',
                    'excel',
                    //'pdf',
                    //{ extend: 'print', text: 'Imprimir' },
                ]
            });
        });
        </script>

    
            
        </div><!-- /#right-panel -->

        <!-- Right Panel -->  
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
        <script src="../assets/js/plugins.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                setTimeout(function () {
                    $(".alert").fadeOut(1500);
                }, 4000);
                $('#menuToggle').on('click', function(event) {
                    $('body').toggleClass('open');
                });
            });
        </script>
    </body>
    <?php require("../ht/modalCambiarPass.php"); ?>
</html>




