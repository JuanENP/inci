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
        <meta meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>
            Catálogo de personal
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
        <script src="../assets/js/diaSexta.js"></script>
       <!-- <link href="https://code.jquery.com/jquery-3.3.1.js" rel="stylesheet"></script> -->
       <!-- <link href="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" rel="stylesheet"></script> -->
       <!-- <link href="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js" rel="stylesheet"></script> -->
       <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js" rel="stylesheet"></script> -->
       <link href="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js" rel="stylesheet"></script>
       <script src="../assets/js/tabla-trabajador.js"></script>
      <script>
            function inicio()
            {
                ruta='../php/_clickSexta.php';
                rutaparaTablaTrab='../php/generaTablaTrab.php'; //ruta para archivo tabla-trabajador.js
                $(document).ready(function()
                {   
                    //Guarda el valor del radio de tipo trabajador
                    var rad= $("input[name='tipo']:checked").val();
                    //Si el tipo de trabajador es 1.confianza, 2.base, 3.eventual
                    if(rad==1 || rad==2 || rad==3)
                    {
                        document.getElementById('empresa').style.display="none";//ocultar empresa
                    }
                    else
                    {
                        document.getElementById('empresa').style.display="block";//ver empresa
                    }

                    if(rad==1 || rad==2 || rad==4)
                    {
                        document.getElementById('tOp').style.display="block";
                    }
                    else
                    {
                        document.getElementById('tOp').style.display="none";
                    }

                    var valor = document.getElementById('turno').value;
                    var valor2 = document.getElementById('sexta').value;//numero trabajador
                    var valor3 = $("input[name='tipo']:checked").val();//nomForm= nombre del formulario; tipo = nombre de los elementos radiobuton
                    actualiza(valor,valor2,valor3);   
                }); 
                dibujarTabla("#.#");
            }

            function buscarInfoTrabajador() 
            {
                valor=document.getElementById("buscador").value;
                dibujarTabla(valor);
            }   

            function noCopy()
            {
                var myInput = document.getElementById('MainContent_txtNomEmpl');
                var myInput2 = document.getElementById('MainContent_txtNom');
                var myInput3 = document.getElementById('MainContent_txtPat');
                var myInput4 = document.getElementById('MainContent_txtMat');
                myInput.onpaste = function(e) 
                {
                    e.preventDefault();
                    alert("Evite pegar aquí");
                }
                myInput2.onpaste = function(e) 
                {
                    e.preventDefault();
                    alert("Evite pegar aquí");
                }

                myInput3.onpaste = function(e) 
                {
                    e.preventDefault();
                    alert("Evite pegar aquí");
                }

                myInput4.onpaste = function(e) 
                {
                    e.preventDefault();
                    alert("Evite pegar aquí");
                }
            }

            function oculta(x)
            {
                if(x==0)
                {
                    document.getElementById('empresa').style.display="block";//ver
                    document.getElementById('tOp').style.display="block";//Ver el div de turno opcional
                }
                else
                {   if(x==1)
                    {
                        document.getElementById('empresa').style.display="none";//ocultar
                        document.getElementById('tOp').style.display="block";
                    }
                    else
                    {
                        if(x==2)
                        {
                            document.getElementById('empresa').style.display="none";//ocultar
                            document.getElementById('tOp').style.display="none";//ocultar
                        }
                    } 
                }
                var valor = document.getElementById('turno').value;
                var valor2 = document.getElementById('sexta').value;//numero trabajador
                var valor3 = $("input[name='tipo']:checked").val();//nomForm= nombre del formulario; tipo = nombre de los elementos radiobuton
                actualiza(valor,valor2,valor3);  
            }

            function PasarValorParaNip()
            {   //Esta función asigna el nip tomando en cuenta los ultimos cuatro digitos del número de trabajador
                var numeroEmpleado=document.getElementById("MainContent_txtNomEmpl").value;
                var nip=numeroEmpleado.substr(-4);
                document.getElementById("MainContent_txtNip").value = nip;
            }
    
            function mayus(e) 
            {
                e.value = e.value.toUpperCase();
            }

        </script>
    </head>

    <body onload="inicio();">

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
            </nav>
        </aside>
        <!-- /#left-panel -->

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
                            <h1>Catálogo de empleados</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="trabajadores.php">Nuevo</a></li>
                                <li class="active">Catálogo de empleados</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="content mt-3">
                <div class="animated fadeIn">
                    <form method="post" name="nomForm" action="inserta-trabajadores.php" id="form2"> 
                        <div class="row">     
                            <div class="col-lg-12">  

                                <div class="card">
                                    <div class="card-header">
                                        <span> Datos personales</span>
                                    </div>

                                    <div class="card-body card-block">
                                        <div>
                                            <div class="form-group col-lg-3">
                                                <span>Número de empleado</span><input name="num" type="number" id="MainContent_txtNomEmpl" class="form-control" min="0" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" onkeyup="PasarValorParaNip();" required/>
                                            </div>
                                            <div class="form-group col-lg-3">
                                                <span >NIP</span><input name="nip" type="number" id="MainContent_txtNip" class="form-control" min="0" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" placeholder="Necesario para biométrico" required/>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <span >Nombre</span><input name="nom" type="text" id="MainContent_txtNom" class="form-control" pattern="[a-zA-ZàáâäãåacceèéêëeiìíîïlnòóôöõøùúûüuuÿýzzñçcšžÀÁÂÄÃÅACCEEÈÉÊËÌÍÎÏILNÒÓÔÖÕØÙÚÛÜUUŸÝZZÑßÇŒÆCŠŽ?ð ]{2,48}"  title="Ingrese solo letras"  required />
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <span >Apellido paterno</span><input name="a_pat" type="text" id="MainContent_txtPat" class="form-control" pattern="[a-zA-ZàáâäãåacceèéêëeiìíîïlnòóôöõøùúûüuuÿýzzñçcšžÀÁÂÄÃÅACCEEÈÉÊËÌÍÎÏILNÒÓÔÖÕØÙÚÛÜUUŸÝZZÑßÇŒÆCŠŽ?ð ]{2,48}" title="Ingrese solo letras"  required />
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <span >Apellido materno</span><input name="a_mat" type="text" id="MainContent_txtMat" class="form-control" pattern="[a-zA-ZàáâäãåacceèéêëeiìíîïlnòóôöõøùúûüuuÿýzzñçcšžÀÁÂÄÃÅACCEEÈÉÊËÌÍÎÏILNÒÓÔÖÕØÙÚÛÜUUŸÝZZÑßÇŒÆCŠŽ?ð ]{2,48}" title="Ingrese solo letras"  required />
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <span >Fecha de nacimiento</span><input name="cumple" type="date" id="MainContent_txtCum" class="form-control" required="" min="1930-01-01"/>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span>Fecha de onomástico (opcional)</span><input name="ono" type="date" id="MainContent_txtOno" class="form-control"  min="1930-01-01"/>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span>Día de descanso del trabajador:</span><br>
                                            <input type="radio" name="cumpleOno" value="cum" id="cumple" checked> <label for="cumple">Cumpleaños &nbsp</label> <!--&nbsp sirve para dar espacios entre palabras  -->
                                            <input type="radio" name="cumpleOno" value="ono" id="ono"> <label for="ono">Onomástico</label><br>
                                        </div>
                                        
                                        <div class="form-group  col-lg-5">
                                            <span>Género:</span><br>
                                            <label class="radio-inline">
                                                <input type="radio" id="h" name="genero" value="M">
                                                <label for="h">Masculino &nbsp</label>
                                            </label> 
                                            <label class="radio-inline">
                                                <input type="radio" id="m" name="genero" value="F" >
                                                <label for="m"> Femenino</label>
                                            </label> 
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <span> Puesto de trabajo</span>
                                    </div>
                                    <div class="card-body card-block">
                                        <div id="tOp" class="form-group col-lg-12">
                                            <span>Turno opcional:</span><br>
                                            <label class="radio-inline">
                                                <input type="radio" id="si" name="t_opc" value="si">
                                                <label for="si">Si &nbsp &nbsp &nbsp</label>
                                            </label> 
                                            <label class="radio-inline">
                                                <input type="radio" id="no" name="t_opc" value="no" checked>
                                                <label for="no"> No &nbsp</label>
                                            </label> 
                                        </div>

                                       <div class="form-group col-lg-5">
                                            <span>Departamento</span>
                                            <?php 
                                                $sql="select * from depto";
                                                $query= mysqli_query($con, $sql);
                                                if(!$query)
                                                {
                                                    die("<br>" . "Error, línea 293: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla departamento, verifique con el administrador de sistemas.");
                                                }
                                                else
                                                {
                                                    echo "<select name='depto' class='form-control' >";
                                                    while($fila=mysqli_fetch_array($query)){
                                                        echo "<option value='".$fila[0]."'>". $fila[0] . " " . $fila[1]."</option>";
                                                    }
                                                    echo "</select>";
                                                }
                                            ?> <!--FIN PHP -->
                                       </div>

                                        <div class="form-group col-lg-5">
                                            <span>Categoría</span>
                                            <?php
                                                $sql="select * from categoria";
                                                $query= mysqli_query($con, $sql);
                                                if(!$query)
                                                {
                                                    die("<br>" . "Error, línea 313: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla categoría, verifique con el administrador de sistemas.");
                                                }
                                                else
                                                {
                                                    echo "<select name='cat' class='form-control' >";
                                                    while($fila=mysqli_fetch_array($query))
                                                    {
                                                        echo "<option value='".$fila[0]."'>". $fila[0] . " " .$fila[1]."</option>";
                                                    }
                                                    echo "</select>";
                                                }
                                            ?> <!--FIN PHP -->
                                        </div>
                                        <div class="form-group col-lg-5">
                                            <span>Tipo de empleado</span>
                                            <?php
                                                $sql="select * from tipo";
                                                $query= mysqli_query($con, $sql);
                                                if(!$query)
                                                {
                                                    die("<br>" . "Error, línea 333: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla tipo de empleado, verifique con el adminsitrador de sistemas.");
                                                }
                                                else
                                                {
                                                    while($fila=mysqli_fetch_array($query))
                                                    {   //se valida el id del tipo: 1.Confianza, 2.Base, 3.Eventual, 4.Comisionao foráneo
                                                        if($fila[0]=="4")
                                                        {
                                                            echo "<br> <input type='radio' name='tipo' id='".$fila[0]."' value='".$fila[0]."' onclick='oculta(0)'></input><label for='".$fila[0]."'> ".$fila[1]."</label>";
                                                        }
                                                        else
                                                        {  
                                                            if($fila[0]=="3")
                                                            {  
                                                                echo "<br><input type='radio' name='tipo' id='".$fila[0]."'  value='".$fila[0]."' onclick='oculta(2)' checked></input><label for='".$fila[0]."'> ".$fila[1]."</label>";
                                                            }
                                                            else 
                                                            {  
                                                                echo "<br><input type='radio' name='tipo' id='".$fila[0]."' value='".$fila[0]."' onclick='oculta(1)'></input><label for='".$fila[0]."'> ".$fila[1]."</label>";
                                                            }
                                                        }
                                                    }
                                                    echo  //El id= empresa del div sirve para ocultar estos elementos
                                                    "<div id='empresa' class='form-group col-lg-12'>
                                                    <br> <span>Empresa de origen: </span> <br> <input type='text' class='form-control' name='emp' pattern='[a-zA-ZàáâäãåacceèéêëeiìíîïlnòóôöõøùúûüuuÿýzzñçcšžÀÁÂÄÃÅACCEEÈÉÊËÌÍÎÏILNÒÓÔÖÕØÙÚÛÜUUŸÝZZÑßÇŒÆCŠŽ?ð&,''.0-9]{2,48}' title='Ingresar letras, números, solo caracteres: &, '', . ' >
                                                    <br> <span>Fecha de inicio de la comisión: </span> <br> <input type='date' id='f_ini' class='form-control' name='f_ini' min='2020-01-01'>
                                                    <br> <span>Fecha de fin de la comisión: </span> <br> <input type='date' id='f_fin'  class='form-control' name='f_fin'min='2020-01-01'>
                                                    </div>";
                                                }
                                            ?> <!--FIN PHP -->
                                        </div>
                                        <div class="form-group col-lg-7">
                                            <div class="form-group col-lg-4">
                                                <span> Días de trabajo</span><br>
                                                <input type="checkbox" name="dia[]" value="lunes" id="lu"/> <label for="lu">Lunes</label><br>
                                                <input type="checkbox" name="dia[]" value="martes" id="ma"/> <label for="ma">Martes</label><br>
                                                <input type="checkbox" name="dia[]" value="miercoles"id="mi"/> <label for="mi">Miércoles</label><br>
                                                <input type="checkbox" name="dia[]" value="jueves" id="ju"/> <label for="ju">Jueves</label><br>
                                                <input type="checkbox" name="dia[]" value="viernes"id="vi"/> <label for="vi">Viernes</label><br>
                                                <input type="checkbox" name="dia[]" value="sabado" id="sa"/> <label for="sa">Sábado</label><br>
                                                <input type="checkbox" name="dia[]" value="domingo" id="do"/> <label for="do">Domingo</label><br>
                                                <input type="checkbox" name="dia[]" value="dias_festivos" id="df"/> <label for="df">Días festivos</label> <br>
                                            </div>
                                            <div id='dias_sexta' class='form-group col-lg-3'>
                                            </div>
                                            <input type="hidden" id='sexta' value="<?php echo $numero; ?>"><!--número de trabajador-->
                                        </div>
                                                    
                                        <div class="form-group col-lg-5">
                                            <span>Turno</span>
                                            <?php  
                                                $sql="select * from turno";
                                                $query= mysqli_query($con, $sql);
                                                if(!$query)
                                                {
                                                  die("<br>" . "Error, línea 388: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla turno, verifique con el administrador de sistemas.");
                                                }
                                                else
                                                {
                                                    echo "<select name='turno' id='turno' class='form-control'>";
                                                    while($fila=mysqli_fetch_array($query)){ 
                                                        echo "<option value='".$fila[0]." " .$fila[3]."'>". $fila[0] . " " .$fila[1]. " - " .$fila[2]."</option>";
                                                    }
                                                    echo "</select>";
                                                }
                                            ?> <!--FIN PHP -->
                                        </div>   
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <span>Inicio de servicio laboral </span>
                                    </div>
                                    <div class="card-body card-block">
                                       <div class="form-group col-lg-5">
                                            <span>Fecha de alta del trabajador (Según su FM1) </span>
                                            <input name="fecha_alta" type="date" class="form-control" required  min="1930-01-01" />
                                       </div>      
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <input type="submit" name="guardar" value="Guardar" id="MainContent_btnAgregar" class="btn btn-primary btn-sm" />
                                </div>
                            </div> 
                        </div>
                    </form>
                    <br><br>
                    <div class="row"> 
                        <div class="form-group col-lg-5">
                            <input name="buc-tr"  id="buscador" type="text" class="form-control" placeholder="Buscar (todos los empleados escriba: todos)"/>
                        </div>      
                        <div class="card">
                            <input type="submit" name="buscar" id="botonBuscar" value="Buscar" class="btn btn-sm btn-info" onclick="buscarInfoTrabajador()"/>
                        </div>
                       <!-- 
                            <div class="card">
                                <button id="generaExcel" class="btn btn-sm btn-success ">Descargar Excel</button>
                            </div>
                        -->
                    </div> 
                    <div class="row" >
                        <div class='col-md-12'>
                            <div class='card'>
                                <div class='card-header'>
                                    <strong class='card-title'>Información</strong>
                                </div>
                                <div  id="trabajadores" class='card-body'>
                                 <!-- Aquí se muestra la  tabla de trabajadores -->
                                </div>
                            </div>
                        </div>    
                    </div>

                </div><!-- .animated -->
            </div><!-- .content -->

            <script type="text/javascript">
                $(document).ready(function() {
                    $('#datos').DataTable( {
                        dom: 'Bfrtip',
                        buttons: [
                            'copyHtml5',
                            'excelHtml5',
                            'csvHtml5',
                            'pdfHtml5'
                        ]
                    });
                });
                function preguntar(elemento)
                {
                    var miID=elemento.id;
                    eliminar=confirm("¿Deseas eliminar este registro?");
                    if (eliminar)
                    //Redireccionamos si das a aceptar
                    {
                        window.location.href='../php/eliminar-trabajadores.php?jhgtp09='+miID+'';
                    }
                    else
                    {
                        exit();
                    }
                }
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
