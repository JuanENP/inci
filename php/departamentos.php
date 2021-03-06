<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<form method="post" action="./cat-departamentos" id="form1"></form>

<div class="aspNetHidden">

    <input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="F1105A88" />
    <input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION"
        value="2IWzTZy++tA+YCARSF54Pj1Tmz8eeBCB4RtDBuAhn5UMzzeaXHoULbefce1DplPqXpsYZ58TJMnvmkn9y0fSumdFA6NEmOoAVWHaCyXtnHG22/w4kT2Guw2bU1hOiXQ2cjw36/OTtowhqakxT83HnTW3jahR+9cMOFVzpXunO+8=" />
</div>
<!--[if gt IE 8]><!-->
<html class="no-js" lang="">
<!--<![endif]-->

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>
        Catalogo de Departamentos
    </title>
    <meta name="description" content="Sistema de Control de Asistencia" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="apple-touch-icon" href="apple-icon.png" />
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="stylesheet" href="../assets/css/normalize.css" />
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="../assets/css/themify-icons.css" />
    <link rel="stylesheet" href="../assets/css/flag-icon.min.css" />
    <link rel="stylesheet" href="../assets/css/cs-skin-elastic.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.css" />
    <link rel="stylesheet" href="../assets/scss/style.css" />
    <link href="../assets/css/lib/vector-map/jqvmap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.full.min.js"></script>

</head>

<body>

    <!-- Left Panel -->
    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu"
                    aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="#">Control de Asistecia</a>
                <a class="navbar-brand hidden" href="#"></a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="../panel_control"> <i class="menu-icon fa fa-dashboard"></i>Panel de Control </a>
                    </li>
                    <li id="Menu_Personal" class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"> <i class="menu-icon fa fa-users"></i>Personal</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-crosshairs"></i><a href="categoria.php">Categorias</a></li>
                            <li><i class="fa fa-sitemap"></i><a href="departamentos.php">Departamentos</a></li>
                            <li><i class="fa fa-male"></i><a href="tipoempleado.html">Tipo Empleado</a></li>
                            <li><i class="fa fa-users"></i><a href="trabajadores.html">Personal</a></li>
                        </ul>
                    </li>
                    <li id="Menu_Dispositivo" class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"> <i class="menu-icon fa fa-desktop"></i>Dispositivo</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-plus-circle"></i><a href="dispositivos.html">Dispositivo</a></li>
                        </ul>
                    </li>
                    <li id="Menu_Asistencia" class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"> <i class="menu-icon fa fa-clock-o"></i>Asistencia</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-calendar"></i><a href="turnos.html">Turnos</a></li>
                            <li><i class="fa fa-check-square-o"></i><a href="aprobaciones.php">Aprobaciones</a></li>
                            <li><i class="fa fa-files-o"></i><a href="">Reportes</a></li>
                            <li><i class="fa fa-shield"></i><a href="conceptos.html">Tipo de Incidencias</a></li>
                        </ul>
                    </li>
                    <li id="Menu_Sistema" class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Sistema</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-users"></i><a href="usuarios.html">Usuarios</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
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

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="images/admin.png" alt="User">
                        </a>
                        <div class="user-menu dropdown-menu">
                            <a class="nav-link" href="updatePassword.aspx"><i class="fa fa-key"></i> Cambiar
                                Contraseña</a>
                            <a class="nav-link" href="Logout.aspx"><i class="fa fa-power-off"></i> Salir</a>
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
                        <h1>Catálogo de Departamentos</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Catálogos</a></li>
                            <li class="active">Departamentos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">

                <div class="row">
                    <div class="col-lg-12">


                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <span id="MainContent_lbtitulo">Nuevo Departamento</span>
                            </div>

                            <div class="card-body card-block">
                                <div class="form-group col-lg-6">
                                    <span id="MainContent_lbDepto">Departamento</span><input
                                        name="ctl00$MainContent$txtDepto" type="text" id="MainContent_txtDepto"
                                        class="form-control" required="" />
                                </div>
                                <div class="form-group col-lg-6">
                                    <span id="MainContent_lbNombre">Nombre</span><input
                                        name="ctl00$MainContent$txtNombre" type="text" id="MainContent_txtNombre"
                                        class="form-control" required="" />
                                </div>
                            </div>
                            <div class="card-footer">
                                <input type="submit" name="ctl00$MainContent$btnAgregar" value="Agregar"
                                    id="MainContent_btnAgregar" class="btn btn-primary btn-sm" />

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
                                <span id="MainContent_DataTable">
                                    <table id='' class='table table-striped table-bordered display'>
                                        <?php
                                             require("../Acceso/global.php");  
        
                                             $sql="select * from depto";
                                             $query= mysqli_query($con, $sql);
                                                if(!$query)
                                                {
                                                    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                                                }
                                                else
                                                {
                                                while($resul=mysqli_fetch_array($query))
                                                {
                                                    echo "<tr>";
                                                    echo utf8_encode("<td>" . $resul[0] . "</td>");
                                                    echo utf8_encode("<td>" . $resul[1] . "</td>");
                                                    echo "<td> <button class='btn btn-danger'> <a href='../php/eliminar-depto.php?id=".$resul[0]."'>Eliminar</a> </button> ";
                                                    echo "<button class='btn btn-success'> <a href='../php/editar-depto.php?id=".$resul[0]."'>Editar</a> </button> </td>";
                                                    echo "</tr>";
                                                }
                                             }
                                        ?> <!--FIN PHP -->
                                    </table>
                                </span>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->

        <script type="text/javascript">
            $(document).ready(function () {
                setTimeout(function () {
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
    <script src="assets/js/plugins.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $(".alert").fadeOut(1500);
            }, 4000);
            $('#menuToggle').on('click', function (event) {
                $('body').toggleClass('open');
            });
        });
    </script>
</body>

</html>
</form>