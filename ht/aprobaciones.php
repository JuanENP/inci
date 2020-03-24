<!doctype html>

    <script type="text/javascript">
        var theForm = document.forms['form1'];
        if (!theForm) {
            theForm = document.form1;
        }

        function __doPostBack(eventTarget, eventArgument) {
            if (!theForm.onsubmit || (theForm.onsubmit() != false)) 
            {
                theForm.__EVENTTARGET.value = eventTarget;
                theForm.__EVENTARGUMENT.value = eventArgument;
                theForm.submit();
            }
        }
    </script>
    <!--[if gt IE 8]><!-->
    <html class="no-js" lang="">
    <!--<![endif]-->

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>
        Aprobaciones
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
                    document.getElementById('fecf').style.display="none";
                    document.getElementById('he').style.display="none";
                    document.getElementById('hs').style.display="none";
                }
                else
                {
                    document.getElementById('fecf').style.display="block";
                    document.getElementById('he').style.display="block";
                    document.getElementById('hs').style.display="block";
                }
            }

            function inicio()
            {
                document.getElementById('fecf').style.display="none";
                document.getElementById('he').style.display="none";
                document.getElementById('hs').style.display="none";
            }
        </script>
    </head>

    <body onload="inicio()">
    <script src="../assets/js/jquery.min.js"></script>
        <script src="../assets/js/main.js"></script>
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
                            <a href="default.aspx"> <i class="menu-icon fa fa-dashboard"></i>Panel de Control</a>
                        </li>
                        <li id="Menu_Personal" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-users"></i>Personal</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-crosshairs"></i><a href="categoria.php">Categorias</a></li>
                                <li><i class="fa fa-sitemap"></i><a href="departamentos.html">Departamentos</a></li>
                                <li><i class="fa fa-male"></i><a href="tipoempleado.html">Tipo Empleado</a></li>
                                <li><i class="fa fa-users"></i><a href="personal.html">Personal</a></li>
                            </ul>
                        </li>
                        <li id="Menu_Dispositivo" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-desktop"></i>Dispositivo</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-plus-circle"></i><a href="dispositivos.html">Dispositivo</a></li>
                            </ul>
                        </li>
                        <li id="Menu_Asistencia" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-clock-o"></i>Asistencia</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-calendar"></i><a href="turnos.html">Turnos</a></li>
                                <li><i class="fa fa-check-square-o"></i><a href="#">Aprobaciones</a></li>
                                <li><i class="fa fa-files-o"></i><a href="reportes.php">Reportes</a></li>
                                <li><i class="fa fa-shield"></i><a href="conceptos.html">Tipo de Incidencias</a></li>
                            </ul>
                        </li>
                        <li id="Menu_Sistema" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Sistema</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-users"></i><a href="usuarios.html">Usuarios</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
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
                                <a class="nav-link" href="updatePassword.php"><i class="fa fa-key"></i> Cambiar Contraseña</a>
                                <a class="nav-link" href="../php/logout.php"><i class="fa fa-power-off"></i> Salir</a>
                            </div>
                        </div>
                    </div>
                </div>

            </header>
            <!-- /header -->
            <!-- Header-->

            <div class="breadcrumbs">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Aprobaciones</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Asistencia</a></li>
                                <li class="active">Aprobaciones</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="../php/insert/justificacion.php">
                <div class="content mt-3">
                    <div class="animated fadeIn">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <span id="MainContent_lbtitulo">
                                            Opciones
                                        </span>
                                    </div>
                                    <div class="card-body card-block">
                                        <div class="row col-md-12">
                                            <div class="form-group col-lg-3">
                                                <div class="form-1-2">
                                                    <input type="radio" name="opcion" value="justificar" onclick="oculta(0)" checked>Justificar retardo
                                                </div>
                                                <div class="form-1-2">
                                                    <!--<label for="caja_busqueda" id="MainContent_lbTrabajador">Buscar:</label>-->
                                                    <input type="radio" name="opcion" value="omision" onclick="oculta(0)">Justificar omisión
                                                </div>
                                                <div class="form-1-2">
                                                    <input type="radio" name="opcion" value="comision" onclick="oculta(1)">Comisiones
                                                </div>

                                                <div class="form-1-2">
                                                    <input type="radio" name="opcion" value="licencia" onclick="oculta(2)">Licencias
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                            </div>
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <span id="MainContent_lbtitulo">Nueva Aprobación</span>
                                    </div>

                                    <div class="card-body card-block">
                                        <div class="row col-md-12">
                                            <div class="form-group col-lg-3">
                                                <span id="MainContent_lbTrabajador">Trabajador</span>
                                                <div class="form-1-2">
                                                    <!--<label for="caja_busqueda" id="MainContent_lbTrabajador">Buscar:</label>-->
                                                    <input type="text" name="caja_busqueda" id="caja_busqueda">
                                                </div>

                                                <div id="datos">
                                                    <!--aquí se cargan los trabajadores-->
                                                </div>
                                            </div>

                                            <div class="form-group col-lg-3">
                                                <span id="MainContent_lbfcinicial">Fecha Inicial</span>
                                                <input name="fec" type="date" id="" class="form-control" required/>
                                            </div>

                                            <div class="form-group col-lg-3" id="fecf">
                                                <span id="MainContent_lbfcinicial">Fecha Final</span>
                                                <input name="fecf" type="date" id="" class="form-control"/>
                                            </div>

                                            <div class="form-group col-lg-3">
                                            </div>

                                            <div class="form-group col-lg-3" id="he">
                                                <span id="MainContent_lbfcinicial">Hora Entrada</span>
                                                <input name="he" type="time" id="" class="form-control"/>
                                            </div>

                                            <div class="form-group col-lg-3" id="hs">
                                                <span id="MainContent_lbfcinicial">Hora Salida</span>
                                                <input name="hs" type="time" id="" class="form-control"/>
                                            </div>

                                            <!--
                                            <div class="form-group col-lg-3">
                                                <span id="MainContent_lbfcinicial">Fecha Inicial</span>
                                                <input name="ctl00$MainContent$txtFechaInicial" type="date" id="MainContent_txtFechaInicial" class="form-control" required/>
                                            </div>
                                            -->
                                            <!--
                                            <div class="form-group col-lg-3">
                                                <span id="MainContent_lblfcfinal">Fecha Final</span>
                                                <input name="ctl00$MainContent$txtFechaFinal" type="date" id="MainContent_txtFechaFinal" class="form-control" required/>
                                            </div>
                                            -->
                                        </div>
                                        <div class="row col-md-12">
                                            <!--
                                            <div class="form-group col-lg-3">
                                                <span id="MainContent_lbComentarioReporte">Comentario - Corto</span>
                                                <input name="ctl00$MainContent$txtComentarioCorto" type="text" id="MainContent_txtComentarioCorto" class="form-control" maxlength="13" />
                                            </div>
                                            -->
                                            <!--
                                            <div class="form-group col-lg-9">
                                                <span id="MainContent_Label1">Comentario - Largo</span>
                                                <input name="ctl00$MainContent$txtComentarioLargo" type="text" id="MainContent_txtComentarioLargo" class="form-control" maxlength="250" />
                                            </div>
                                            -->
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="dropdown">
                                            <input type="submit" name="Aceptar" value="Guardar" class="btn btn-primary btn-sm" />
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
            </form>  <!-- FIN DEL FORM -->

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <strong class="card-title">Información</strong>
                                    </div>
                                    <div class="card-body">

                                        <select name="ctl00$MainContent$ddlMes" style="margin-bottom:10px">
                                            <!--Los meses del año-->
                                        </select>
                                        <select name="ctl00$MainContent$ddlanio" onchange="" id="MainContent_ddlanio">
                                            <!--Años 2018, 20191 y 2020-->
                                        </select>
                                        <span id="MainContent_DataTable">      
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- .animated -->
                </div>
            <!-- .content -->

            <script type="text/javascript">
                $(document).ready(function() {
                    setTimeout(function() {
                        $(".alert").fadeOut(1500);
                    }, 4000);
                    $('table.display').DataTable({
                        "columnDefs": [{
                            "width": "70px",
                            "targets": 1
                        }],
                        "order": [
                            [1, "asc"]
                        ],
                        "autoWidth": false,
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
                            //'pdf',
                            //{ extend: 'print', text: 'Imprimir' },
                        ]
                    });
                    $('.select2').select2();
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