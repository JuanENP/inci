<?php
session_start();
    //obtener el id que se mandó acá
    $id=$_GET['id'];
    require("../Acceso/global.php");
        $sql="select tipo_tipo from trabajador where numero_trabajador = '".$id."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
          die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        { 
             $resul=mysqli_fetch_array($query);
             //Si el tipo de empleado del que se seleccionó es comisionado foráneo
            if($resul[0]==4)
            {
                //Función que busca la categoría con el ID
                function consulta($myid)
                {
                    require("../Acceso/global.php");
                    $sql="select * from trabajador where numero_trabajador = '".$myid."'";
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
                    $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5]
                    ];
                    }
                }
                //guardar el array que retornó la función consulta
                $id2=consulta($id);
                echo"COMISIONADO FORÁNEO";
            }
            else
            {
                //Función que busca la categoría con el ID
                function consulta($myid)
                {
                    require("../Acceso/global.php");
                    $sql="select * from trabajador where numero_trabajador = '".$myid."'";
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
                    $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5]
                    ];
                    }
                }
                //guardar el array que retornó la función consulta
                $id2=consulta($id);
                echo"NORMAL";

            }
                
        }
    
    
?>

<!doctype html>
 
    <html class="no-js" lang="">
    <!--<![endif]-->

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>
            Personal
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
                                <li><i class="fa fa-check-square-o"></i><a href="aprobaciones.php">Aprobaciones</a></li>
                                <li><i class="fa fa-files-o"></i><a href="reportes.html">Reportes</a></li>
                                <li><i class="fa fa-shield"></i><a href="../ht/conceptos.php">Tipo de Incidencias</a></li>
                                <li><i class="fa fa-chain"></i><a href="especiales.php" title="comisiones, lactancia, estancia">Especiales</a></li>
                            </ul>
                        </li>
                        <li id="Menu_Sistema" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Sistema</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-users"></i><a href="../ht/usuarios.php">Usuarios</a></li>
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
                                <img class="user-avatar rounded-circle" src="images/admin.png" alt="User">
                            </a>
                            <div class="user-menu dropdown-menu">
                                <a class="nav-link" href="updatePassword.php"><i class="fa fa-key"></i> Cambiar Contraseña</a>
                                <a class="nav-link" href="Logout.php"><i class="fa fa-power-off"></i> Salir</a>
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
                            <h1>Trabajador</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Personal</a></li>
                                <li class="active">Trabajador</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content mt-3">
                <div class="animated fadeIn">
                   <div class="row"> 
                   <form method="POST" action="../php/update/tabajadores.php"> 
                        <div class="row">  
                            <div class="col-lg-12">                        
                              <div class="card">
                                    <div class="card-header">
                                        <span id="MainContent_lbtitulo"> Datos personales</span>
                                    </div>
                                    <div class="card-body card-block">
                                      
                                       <div class="form-group col-lg-12">
                                            <span id="MainContent_numero">Número de empleado</span><input name="num" type="number" id="MainContent_txtCategoria" class="form-control" value="<?php echo $id2[0]?>" required/>
                                       </div>
                                        <div class="form-group col-lg-12">
                                            <span id="MainContent_nombre">Nombre</span><input name="nom" type="text" id="MainContent_txtNombre" class="form-control" pattern="[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]{2,48}"  title="Ingrese solo letras" required value="<?php echo $id2[1]; ?>" />
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <span id="MainContent_a_pat">Apellido paterno</span><input name="a_pat" type="text" id="MainContent_txtNombre" class="form-control" pattern="[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]{2,48}" title="Ingrese solo letras" required value="<?php  echo$id2[2]; ?>" />
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <span id="MainContent_a_mat">Apellido materno</span><input name="a_mat" type="text" id="MainContent_txtNombre" class="form-control" pattern="[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]{2,48}" title="Ingrese solo letras" required value="<?php echo $id2[3]; ?>" />
                                        </div>
                                       
                                        <div class="form-group col-lg-12">
                                            <span id="MainContent_f_nac">Fecha de nacimiento</span><input name="cumple" type="date" id="MainContent_txtNombre" class="form-control" required="" min="1930-01-01"/>
                                        </div>

                                    </div>
                               </div>
                           </div> 
                     </div> <!--fin del row datos personales --> 
                     <div class="row">  
                            <div class="col-lg-12">                        
                              <div class="card">
                                    <div class="card-header">
                                        <span id="MainContent_lbtitulo"> Puesto de trabajo</span>
                                    </div>
                                    <div class="card-body card-block">

                                       <div class="form-group col-lg-12">
                                            <span id="MainContent_lbCategoria">Departamento</span>
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
                                                        echo "<select name='depto' class='form-control' value='<?php echo $id2[4];?>' >";
                                                        while($fila=mysqli_fetch_array($query)){
                                                            echo "<option value='".$fila[0]."'>". $fila[0] . " " .$fila[1]."</option>";
                                                        }
                                                        echo "</select>";
                                                    }
                                                
                                                        mysqli_close($con);
                                                ?> <!--FIN PHP -->
                                       </div>
                                        <div class="form-group col-lg-12">
                                            <span id="MainContent_lbNombre">Categoría</span>
                                                <?php
                                                    require("../Acceso/global.php");  
        
                                                    $sql="select * from categoria";
                                                    $query= mysqli_query($con, $sql);
                                                    if(!$query)
                                                    {
                                                    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                                                    }
                                                    else
                                                    {
                                                        echo "<select name='cat' class='form-control' value='<?php echo $id2[5];?>' >";
                                                        while($fila=mysqli_fetch_array($query)){
                                                            echo "<option value='".$fila[0]."'>". $fila[0] . " " .$fila[1]."</option>";
                                                        }
                                                        echo "</select>";
                                                    }
                                                    
                                                        mysqli_close($con);
                                                ?> <!--FIN PHP -->

                                        </div>
                                        <div class="form-group col-lg-12">
                                            <span id="MainContent_lbNombre">Tipo de empleado</span>
                                                <?php
                                                    require("../Acceso/global.php");  
        
                                                    $sql="select * from tipo";
                                                    $query= mysqli_query($con, $sql);
                                                    if(!$query)
                                                    {
                                                    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                                                    }
                                                    else
                                                    {

                                                        while($fila=mysqli_fetch_array($query))
                                                        {
                                                            if($fila[1]=="COMISIONADO FORÁNEO")
                                                            {
                                                                echo "<br> <input type='radio' name='tipo'  value='".$fila[0]."' onclick='oculta(0)'>". $fila[1] . " ". "</input>";
                                                            }
                                                            else
                                                            {   if($fila[1]=="EVENTUAL")
                                                                {  
                                                                    echo "<br> <input type='radio' name='tipo'  value='".$fila[0]."' onclick='oculta(1)' checked>". $fila[1] . " ". "</input>";
                                                                }
                                                                else 
                                                                {  
                                                                echo "<br> <input type='radio' name='tipo'  value='".$fila[0]."' onclick='oculta(1)'>". $fila[1] . " ". "</input>";
                                                                }
                                                            }

                                                        }
                                                        echo  //El id= empresa del div sirve para ocultar estos elementos
                                                        "<div id='empresa' class='form-group col-lg-12'>
                                                        <br> <span>Empresa de origen: </span> <br> <input type='text' class='form-control' name='emp' pattern='[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð&,''.0-9]{2,48}' title='Ingresar letras, números, solo caracteres: &, '', . ' >
                                                        <br> <span>Fecha de inicio de la comisión: </span> <br> <input type='date' id='f_ini' class='form-control' name='f_ini' min='2020-01-01'>
                                                        <br> <span>Fecha de fin de la comisión: </span> <br> <input type='date' id='f_fin'  class='form-control' name='f_fin'min='2020-01-01'>
                                                        </div>";
                                                    
                                                    }
                                                        mysqli_close($con);
                                                ?> <!--FIN PHP -->
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <span id="MainContent_lbNombre">Días de trabajo</span>
                                            <br>
												<input type="checkbox" name="dia[]" value="lunes"/>Lunes<br />
												<input type="checkbox" name="dia[]" value="martes"/>Martes<br />
												<input type="checkbox" name="dia[]" value="miercoles" />Miercoles<br />
												<input type="checkbox" name="dia[]" value="jueves" />Jueves<br />
												<input type="checkbox" name="dia[]" value="viernes" />Viernes<br />
												<input type="checkbox" name="dia[]" value="sabado" />Sábado<br />
												<input type="checkbox" name="dia[]" value="domingo" />Domingo<br />
                                                <input type="checkbox" name="dia[]" value="dias_festivos" />Días festivos<br />
                                            <br>
                                        <div class="form-group col-lg-12">
                                            <span id="MainContent_lbNombre">Turno</span>
                                            <?php
                                                        require("../Acceso/global.php");  
      
                                                        $sql="select * from turno";
                                                        $query= mysqli_query($con, $sql);
                                                        if(!$query)
                                                        {
                                                          die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                                                        }
                                                        else
                                                        {
                                                            echo "<select name='turno' class='form-control' >";
                                                            while($fila=mysqli_fetch_array($query)){
                                                                echo "<option value='".$fila[0]."'>". $fila[0] . " " .$fila[1]. " - " .$fila[2]."</option>";
                                                            }
                                                            echo "</select>";
                                                        }
                                                
                                                            mysqli_close($con);
                                                    ?> <!--FIN PHP -->
                                        </div>
                                       
                                    </div>
                                    <div class="row">  
                            <div class="col-lg-12">                        
                              <div class="card">
                                    <div class="card-header">
                                        <span id="MainContent_lbtitulo">Inicio de servicio laboral </span>
                                    </div>
                                    <div class="card-body card-block">
                                    <div class="form-group col-lg-12">
                                            <span id="MainContent_lbCategoria">Fecha de alta del trabajador(Según su FM1) </span>
                                            <input name="fecha_alta" type="date" id="MainContent_txtCategoria" class="form-control" required  min="1930-01-01" />
                                       </div>    
                                     
                                    </div>
                               </div>
                           </div> 
                     </div> <!--fin del row datos personales --> 
                                    <div class="card-footer">
                                            <input type="submit" name="guardar" value="Guardar" id="MainContent_btnAgregar" class="btn btn-primary btn-sm" />
                                    </div>
                               </div>
                           </div> 
                     </div> <!--fin del row datos personales --> 
                   </form>
                   </div>  

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <strong class="card-title">Información</strong>
                                        </div>
                                            <div class="card-body">
                                                <table id='' class='table table-striped table-bordered display'>
                                                    <thead>
                                                    <th>Número de trabajador</th>
													<th>Nombre</th>
													<th>Apellido Paterno</th>
													<th>Apellido Materno</th> 
                                                    <th>Departamento</th> 
													<th>Categoría</th> 
													<th>Tipo de empleado</th>  
                                                    </thead>
                                                    <tbody>
                                                            <?php
                                                                require("../Acceso/global.php");  
                            
                                                                $sql="SELECT a.numero_trabajador,a.nombre,a.apellido_paterno,a.apellido_materno,a.depto_depto,a.categoria_categoria,b.descripcion FROM trabajador a 
                                                                inner join tipo b on b.idtipo = a.tipo_tipo";
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
                                                                        echo "<td>" . $resul[0] . "</td>"; 
																		echo "<td>" . $resul[1] . "</td>";
																		echo "<td>" . $resul[2] . "</td>";
																		echo "<td>" . $resul[3] . "</td>";
																		echo "<td>" . $resul[4] . "</td>";
                                                                        echo "<td>" . $resul[5] . "</td>";
                                                                        echo "<td>" . $resul[6] . "</td>";
                                                                        echo "<td> <button class='btn btn-danger'> <a href='../php/eliminar-depto.php?id=".$resul[0]."'>Eliminar</a> </button> ";
                                                                        echo "<button class='btn btn-success'> <a href='../php/editar-depto.php?id=".$resul[0]."'>Editar</a> </button> </td>";
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

    