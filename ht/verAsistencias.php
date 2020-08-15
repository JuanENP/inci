<?php
session_start();
date_default_timezone_set('America/Mexico_City'); 
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php"); 
        $ubicacion='../php/update/modificarPass.php';//sirve para indicar la ruta del form modalCambiarPass
        $dia_actual=date('Y-m-d');
        $diaHoy=date('d-m-Y');
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
           Asistencias del <?php echo $diaHoy; ?> 
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

    </head>

    <body>

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

                <?php
                    /*Barra izquierda de navegación*/
                    $saltos="../";
                    require("../php/insert/moverse.php");
                ?>
                <!-- /.navbar-collapse -->
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
                                <a class="nav-link" href="#" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#mimodal"  name="boton"><i class="fa fa-key"></i> Cambiar contraseña</a>
                                <a class="nav-link" href="../php/logout.php"><i class="fa fa-power-off"></i> Salir</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- /header -->
            <div class="breadcrumbs">
                <div class="col-sm-5">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Asistencias de hoy <?php echo $diaHoy; ?> </h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content mt-3">
                <div class="animated fadeIn">
                    <!--  
                        <div class="row">
                            <form method="post" action="./../ht/inserta-concepto.php">
                                <div class="col-lg-12">
                                </div>
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <span id="MainContent_lbtitulo">Nueva Incidencia</span>
                                        </div>
                                        <div class="card-body card-block">
                                            <div class="form-group col-lg-6">
                                                <span id="MainContent_lbConcepto">Clave</span><input name="ctl00$MainContent$txtConcepto" type="text" maxlength="3" id="MainContent_txtConcepto" class="form-control" required="" />
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <span id="MainContent_lbNombre">Nombre</span><input name="ctl00$MainContent$txtNombre" type="text" maxlength="100" id="MainContent_txtNombre" class="form-control" required="" />
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <input type="submit" name="ctl00$MainContent$btnAgregar" value="Agregar" id="MainContent_btnAgregar" class="btn btn-primary btn-sm" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                               <!--<div class="card-header">
                                    <strong class="card-title">Información</strong>
                                </div>-->
                                <div class="card-body">
                                    <span id="MainContent_DataTable">
                                        <table id='' class='table table-striped table-bordered display'>
                                            <thead>
                                                <th>Trabajador</th>
                                                <th>Nombre</th>
                                                <th>A Paterno</th>
                                                <th>A Materno</th>
                                                <th>Depto</th>
                                                <th>Categoría</th>
                                                <th>Entrada</th>
                                                <th>Salida</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $sql="SELECT a.numero_trabajador,a.nombre,a.apellido_paterno,a.apellido_materno,a.depto_depto,a.categoria_categoria, b.fecha_entrada,b.fecha_salida FROM trabajador a 
                                                    inner join asistencia b where a.numero_trabajador=b.trabajador_trabajador and (fecha_entrada like '$dia_actual%' or fecha_salida like '$dia_actual%')
                                                    order by fecha_entrada, fecha_salida,depto_depto;";
                                                    $query= mysqli_query($con, $sql);
                                                    if(!$query)
                                                    {
                                                        die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con).", línea 219, no hay datos en la tabla clave incidencia, verifique con el administrador de sistemas.");
                                                    }
                                                    else
                                                    {
                                                        while($resul=mysqli_fetch_array($query))
                                                        {
                                                            echo "<tr>";
                                                            echo "<td>" . $resul[0] . "</td>";
                                                            echo "<td>" . $resul[1] . "</td>";
                                                            echo "<td>" . $resul[2] . "</td>";
                                                            echo "<td>" . $resul[3] . "</td>";
                                                            echo "<td>" . $resul[4] . "</td>";
                                                            echo "<td>" . $resul[5] . "</td>";
                                                            echo "<td>" . $resul[6] . "</td>";
                                                            echo "<td>" . $resul[7] . "</td>";
                                                            echo "</tr>";
                                                        }
                                                    }
                                                ?> <!--FIN PHP -->
                                            </tbody>
                                        </table>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- .animated -->
            </div><!-- .content -->

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
    <?php require("../ht/modalCambiarPass.php"); ?>
</html>
