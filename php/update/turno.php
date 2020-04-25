<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
    }
    else
    {
        header("Location: ../../index.html");
        die();
    }
    //obtener el id que se mandó acá
    $id=$_GET['id'];
    //Función que busca la categoría con el ID
    function consulta($myid)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php");
        $sql="select * from turno where idturno = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
          die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        { 
          $resul=mysqli_fetch_array($query);
          //retornar este array
          return[
          $resul[0],$resul[1],$resul[2],$resul[3]
          ];
        }
    }
    //guardar el array que retornó la función consulta
    $id2=consulta($id);
?>

<!doctype html>
<!--[if gt IE 8]><!-->
<html class="no-js" lang="">
    <!--<![endif]-->

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>
            MODIFICAR TURNO
        </title>
        <meta name="description" content="Sistema de Control de Asistencia" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="apple-touch-icon" href="apple-icon.png" />
        <link rel="shortcut icon" href="favicon.ico" />
        <link rel="stylesheet" href="../../assets/css/normalize.css" />
        <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="../../assets/css/font-awesome.min.css" />
        <link rel="stylesheet" href="../../assets/css/themify-icons.css" />
        <link rel="stylesheet" href="../../assets/css/flag-icon.min.css" />
        <link rel="stylesheet" href="../../assets/css/cs-skin-elastic.css" />
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.css"
        />
        <link rel="stylesheet" href="../../assets/scss/style.css" />
        <link href="../../assets/css/lib/vector-map/jqvmap.min.css" rel="stylesheet" />
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

                <div id="main-menu" class="main-menu collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="../../panel_control.php"> <i class="menu-icon fa fa-dashboard"></i>Panel de Control </a>
                        </li>
                        <li id="Menu_Personal" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-users"></i>Personal</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-crosshairs"></i><a href="../../ht/categoria.php">Categorias</a></li>
                                <li><i class="fa fa-sitemap"></i><a href="../../ht/departamentos.php">Departamentos</a></li>
                                <li><i class="fa fa-male"></i><a href="../../ht/tipoempleado.php">Tipo Empleado</a></li>
                                <li><i class="fa fa-users"></i><a href="../../ht/trabajadores.php">Personal</a></li>
                            </ul>
                        </li>
                        <li id="Menu_Dispositivo" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-desktop"></i>Dispositivo</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-plus-circle"></i><a href="../../ht/dispositivos.php">Dispositivo</a></li>
                            </ul>
                        </li>
                        <li id="Menu_Asistencia" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-clock-o"></i>Asistencia</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-calendar"></i><a href="../../ht/turnos.php">Turnos</a></li>
                                <li><i class="fa fa-check-square-o"></i><a href="aprobaciones.php">Aprobaciones</a></li>
                                <li><i class="fa fa-files-o"></i><a href="reportes.html">Reportes</a></li>
                                <li><i class="fa fa-shield"></i><a href="../../ht/conceptos.php">Tipo de Incidencias</a></li>
                                <li><i class="fa fa-chain"></i><a href="especiales.php" title="comisiones, lactancia, estancia">Especiales</a></li>
                            </ul>
                        </li>
                        <li id="Menu_Sistema" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Sistema</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-users"></i><a href="../../ht/usuarios.php">Usuarios</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </nav>
        </aside>
        <!-- /#left-panel -->

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
                                <img class="user-avatar rounded-circle" src="../../images/admin.png" alt="User">
                            </a>
                            <div class="user-menu dropdown-menu">
                                <a class="nav-link" href="../php/update/password.php"><i class="fa fa-key"></i> Cambiar Contraseña</a>
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
                            <h1> MODIFICAR TURNO</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Catálogos</a></li>
                                <li class="active">Turnos</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="content mt-3">
                <div class="animated fadeIn">
                   <div class="row"> 
                       <form method="post" action="_turno.php" id="form2">   
                            <div class="col-lg-12">                        
                              <div class="card">
                                    <div class="card-header">
                                        <span id="MainContent_lbtitulo">Turno existente</span>
                                    </div>
                                    <input type="hidden" name="old_id" value="<?php echo $id?>">
                                    <div class="card-body card-block">                          
                                        <div class="form-group col-lg-6">
                                            <span id="MainContent_lbTurno">Turno</span>
                                            <input type="text" id="id-turno" name="id-turno" value="<?php echo $id2[0]?>" class="form-control" required/>
                                        </div>

                                        <div class="form-group col-lg-6">
                                            <span id="MainContent_lbEntrada">Hora de entrada</span>                              
                                            <input type="time" id="hora-ini" name="fec-ini" value="<?php echo $id2[1]?>"class="form-control" required/>
                                        </div>
                                    
                                        <div class="form-group col-lg-6">
                                            <span >
                                            </span>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <span id="MainContent_lbSalida">Hora de salida</span>
                                            <input type="time" id="hora-fin" name="fec-fin" value="<?php echo $id2[2]?>" class="form-control" required="" />
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                            <input type="submit" name="guardar" value="Guardar" id="MainContent_btnAgregar" class="btn btn-primary btn-sm" />
                                    </div>
                                </div>
                            </div> 
                        </form>
                   </div>  

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <strong class="card-title">Información</strong>
                                </div>
                                <div class="card-body">
                                    <span id="MainContent_DataTable">
                                        <table id='' class="table table-striped table-bordered display">
                                            <thead>
                                            <th>Turno</th>
                                            <th>Hora entrada</th>
                                            <th>Hora salida</th>
                                            <th>Total de horas</th>
                                            <th>Acciones</th>
                                            </thead>
                
                                            <tbody>
                                                <!--PONER AQUÍ EL CONTENIDO DE LA TABLA-->
                                                <?php
                                                    $sql="select * from turno";
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
                                                        echo utf8_encode("<td>" . $resul[2] . "</td>");
                                                        echo utf8_encode("<td>" . $resul[3] . "</td>");
                                                       // echo "<td> <button class='btn btn-danger btn-sm'><i class='fa fa-trash-o'><a href='../delete/turno.php?id=".$resul[0]."'>Eliminar</a></i>  </button> ";
                                                        echo "<td><a href='../delete/turno.php?id=".$resul[0]."'><button class='btn btn-danger btn-sm'><i class='fa fa-trash-o'></i>Eliminar </button></a> ";
                                                        echo " <a href='../update/turno.php?id=".$resul[0]."'><button class='btn btn-success btn-sm'><i class='fa fa-pencil-square-o'></i>Editar </button></a> </td>";
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
            </div>
            <!-- .content -->

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
        <script src="../../assets/js/plugins.js"></script>
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
