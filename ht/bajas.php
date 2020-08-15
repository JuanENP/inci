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
        Bajas
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
            });
        </script> 
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
                            <h1>Bajas</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Asistencia</a></li>
                                <li class="active">Bajas</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="content mt-3">
                <div class="animated fadeIn">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <span id="MainContent_lbtitulo">
                                        Mostrando bajas:
                                    </span>
                                </div>
                                
                                <div class="card-body card-block">
                                    <div class="form-1-2">
                                        <?php
                                            $sql="SELECT * FROM bajas";
                                            $query=mysqli_query($con, $sql);
                                            if(!$query)
                                            {
                                                echo "Error al recuperar los datos de baja";
                                            }
                                            else
                                            {
                                                if(mysqli_num_rows($query)>0)
                                                {
                                                    $tabla="";
                                                    $tabla.="<table class='table table-striped table-bordered display' style='text-align: center; font-size:18px; background: white; table-layout:fixed;'>
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha en que se agregó</th>
                                                            <th>Tot. Días</th>
                                                            <th>Motivo/Clave</th>
                                                            <th>Trabajador</th>
                                                            <th>Quincena</th>
                                                            <th>Baja definitiva</th>
                                                            <th>¿Dar de baja de forma definitiva?</th>
                                                        </tr>
                                                    </thead>     
                                                    <tbody>";
                                            
                                                    while ($fila = mysqli_fetch_array($query)) 
                                                    {
                                                        $id=$fila[0];
                                                        $tabla.="<tr>
                                                                    <td>".$fila[1]."</td>
                                                                    <td>".$fila[2]."</td>
                                                                    <td>".$fila[3]."</td>
                                                                    <td>".$fila[4]."</td>
                                                                    <td>".$fila[5]."</td>
                                                                    <td>".$fila[7]."</td>";
                                                                    if($fila[7]=="0")
                                                                    {
                                                                        $tabla.="<td><a><button class='btn btn-danger btn-sm' id='$id' onclick='preguntar(this);'><i class='fa fa-trash-o'></i>SI </button> </a> </td>";
                                                                    }
                                                                    else
                                                                    {
                                                                        $tabla.="<td> </td>";
                                                                    }
                                                                $tabla.="</tr>";
                                                    }
                                                    $tabla.="</tbody></table>";
                                                    echo $tabla;
                                                }
                                                else
                                                {
                                                    echo "No hay datos";
                                                }
                                            }
                                        ?>
                                    </div> 
                                </div> 
                                    
                            </div>
                        </div>
                    </div>
                </div> <!--FIN DIV animated fadeIn-->
            </div> <!--FIN DIV content mt-3--> 
        </div> <!-- FIN right-panel -->  

        <script type="text/javascript">
            
            function preguntar(elemento)
            {
                var miID=elemento.id;
                alertify.confirm("¿Desea dar de baja de forma DEFINITIVA a este empleado?", function(e)
                {
                    if(e)
                    {
                        window.location.href="../php/update/baja.php?4Plkksd7="+miID+"";
                    }
                });
            }
        </script>     
    </body>
    <?php require("../ht/modalCambiarPass.php"); ?>
</html>