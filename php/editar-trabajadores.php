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
    //El id es el número del trabajador seleccionado y es necesario por si se debe actualizar el número de trabajador
    $id=$_GET['id'];
    $_SESSION['anterior_num']=$id;//

    //Seleccionar el tipo de trabajador
    $sql="select tipo_tipo from trabajador where numero_trabajador = '".$id."'";
    $query= mysqli_query($con, $sql);
    if(!$query)
    {
      die("<br>" . "Error en la línea 24: " . mysqli_errno($con) . " : " . mysqli_error($con). ", verifique con el administrador de sistemas");
    }
    else
    { 
        $resul=mysqli_fetch_array($query);
        $tipo=$resul[0];
        require('buscar_info_trabajador.php');
        //Si el tipo de empleado del que se seleccionó es comisionado foráneo
        if($tipo==4)
        {
            $id2=consultaTrabajador($id);
            $id3=consultaCumple($id);
            $id4=consultaAcceso($id);
            $id5=consultaTServicio($id);
            $especial=consultaEspecial($id);
            //FECHA INICIO, FECHA FIN, EMPRESAS
            $fecha_inicio = $especial[1];
            $fecha_fin = $especial[2];
            $empresa = $especial[8];
            $genero=consultaGenero($id);   
        }
        else
        {
            $id2=consultaTrabajador($id);
            $id3=consultaCumple($id);
            $id4=consultaAcceso($id);
            $id5=consultaTServicio($id);  
            $genero=consultaGenero($id);  
        }       
    }   
?>

<!doctype html> 
<html class="no-js" lang="es">
    <head>
        <meta meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>
            Personal
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
        <script>
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
                }
                else
                {
                    document.getElementById('empresa').style.display="none";//ocultar
                }

                var valor = document.getElementById('turno').value;
                var valor2 = document.getElementById('sexta').value;
                 var valor3 = $("input[name='tipo']:checked").val();//nomForm= nombre del formulario; tipo = nombre de los elementos radiobuton
                actualiza(valor,valor2,valor3);
            }

            function inicial(consulta) {
            $.ajax({
                    url: '_loadSexta.php',
                    type: 'POST',
                    dataType: 'html',
                    data: { consulta: consulta },
                })
                .done(function(respuesta) {
                 $("#dias_sexta").html(respuesta);
                })
                .fail(function() {
                    console.log("error");
                });
            }


            function inicio()
            {
                ruta='_clickSexta.php';
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

                    var numTrabajador=document.getElementById("sexta").value;
                    inicial(numTrabajador);
                }); 
            }
        
            function mayus(e) 
            {
                e.value = e.value.toUpperCase();
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
                                <li><i class="fa fa-files-o"></i><a href="../ht/reportes.html">Reportes</a></li>
                                <li><i class="fa fa-shield"></i><a href="../ht/conceptos.php">Tipo de Incidencias</a></li>
                           
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
                </div><!--fin div main-menu -->
            </nav> <!--fin navbar -->
        </aside><!--fin de aside -->
        
        <!-- Right Panel ------------------------------------------------------------------------------------------------------>
        <div id="right-panel" class="right-panel">
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
            <div class="breadcrumbs">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>MODIFICAR EMPLEADO</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="../ht/trabajadores.php">Regresar al catálogo de personal</a></li>
                                <!-- <li class="active">Nuevo trabajador</li> -->
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content mt-3">
                <div class="animated fadeIn">
                    <form id="f1" name="nomForm" method="POST" action="../php/update/trabajadores.php">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                   <div class="card-header">
                                        <span>
                                            Datos personales
                                        </span>
                                   </div>
                                   <div class="card-body card-block">
                                        <div class="form-group col-lg-3">
                                            <span>
                                                Número de empleado
                                            </span>
                                            <input name="num" type="number"  class="form-control" value="<?php echo $id2[0]?>" min="0" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" required/>
                                        </div>
                                        
                                        <div class="form-group col-lg-3">
                                            <span>
                                                Nombre
                                            </span>
                                            <input name="nom" type="text"  class="form-control" pattern="[a-zA-ZàáâäãåacceèéêëeiìíîïlnòóôöõøùúûüuuÿýzzñçcÀÁÂÄÃÅACCEEÈÉÊËÌÍÎÏILNÒÓÔÖÕØÙÚÛÜUUÝZZÑßÇÆC?ð ]{2,48}"  title="Ingrese solo letras" required value="<?php echo $id2[1]; ?>" onkeyup="mayus(this);" />
                                        </div>
                                        
                                        <div class="form-group col-lg-3">
                                            <span>
                                                Apellido paterno
                                            </span>
                                            <input name="a_pat" type="text"   class="form-control" pattern="[a-zA-ZàáâäãåacceèéêëeiìíîïlnòóôöõøùúûüuuÿýzzñçcÀÁÂÄÃÅACCEEÈÉÊËÌÍÎÏILNÒÓÔÖÕØÙÚÛÜUUÝZZÑßÇÆC?ð ]{2,48}" title="Ingrese solo letras" required value="<?php  echo$id2[2]; ?>" onkeyup="mayus(this);" />
                                        </div>
                                        
                                        <div class="form-group col-lg-3">
                                            <span>
                                                Apellido materno
                                            </span>
                                            <input name="a_mat" type="text"   class="form-control" pattern="[a-zA-ZàáâäãåacceèéêëeiìíîïlnòóôöõøùúûüuuÿýzzñçcÀÁÂÄÃÅACCEEÈÉÊËÌÍÎÏILNÒÓÔÖÕØÙÚÛÜUUÝZZÑßÇÆC?ð ]{2,48}" title="Ingrese solo letras" required value="<?php echo $id2[3]; ?>" onkeyup="mayus(this);" />
                                        </div>
                                        
                                        <div class="form-group col-lg-4">
                                            <span>
                                                Fecha de nacimiento
                                            </span>
                                            <input name="cumple" type="date" value="<?php echo $id3[0]?>"   class="form-control" required="" min="1930-01-01"/>
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <span>
                                                Fecha de onomástico
                                            </span>
                                            <input name="ono" type="date" value="<?php echo $id3[1]?>"   class="form-control"  min="1930-01-01"/>
                                        </div>
                                        <?php
                                            //validez de cumpleOno
                                            if($id3[3]==0)
                                            {
                                                echo "<div class='form-group col-lg-4'>
                                                        <span>Día de descanso del trabajador:</span><br>
                                                        <input type='radio' name='cumpleOno' value='cum' id='cumple'checked> 
                                                        <label for='cumple'>Cumpleaños &nbsp</label>
                                                        <input type='radio' name='cumpleOno' value='ono' id='ono'> 
                                                        <label for='ono'>Onomástico</label><br>
                                                </div>";
                                            }
                                            else
                                            {
                                                if($id3[3]==1)
                                                {
                                                    echo "<div class='form-group col-lg-4'>
                                                            <span>Día de descanso del trabajador:</span><br>
                                                            <input type='radio' name='cumpleOno' value='cum' id='cumple'> 
                                                            <label for='cumple'>Cumpleaños &nbsp</label>
                                                            <input type='radio' name='cumpleOno' value='ono' id='ono' checked> 
                                                            <label for='ono'>Onomástico</label><br>
                                                        </label> 
                                                    </div>";
                                                }
                                            }

                                            if($genero[0]=='M')
                                            {
                                                echo"
                                                <div class='form-group col-lg-5'>
                                                    <span>Género:</span><br>
                                                    <label class='radio-inline'>
                                                        <input type='radio' id='h' name='genero' value='M' checked>
                                                        <label for='h'>Masculino </label><br>
                                                    </label>
                                                    <label class='radio-inline'>
                                                        <input type='radio' id='m' name='genero' value='F'>
                                                        <label for='m'> Femenino</label><br>
                                                    </label> 
                                                </div>";
                                            }
                                            else
                                            {
                                                if($genero[0]=='F')
                                                {
                                                    echo"
                                                    <div class='form-group col-lg-5'>
                                                        <span>Género:</span><br>
                                                        <label class='radio-inline'>
                                                            <input type='radio' id='h' name='genero' value='M'>
                                                            <label for='h'>Masculino </label><br>
                                                        </label>
                                                        <label class='radio-inline'>
                                                            <input type='radio' id='m' name='genero' value='F' checked>
                                                            <label for='m'> Femenino</label><br>
                                                        </label> 
                                                    </div>";
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <span>
                                            Puesto de trabajo
                                        </span>
                                    </div>
                                    <div class="card-body card-block">
                                        <div class="form-group col-lg-5">
                                            <span >
                                                Departamento
                                            </span>
                                            <?php
                                                $sql="select * from depto";
                                                $query= mysqli_query($con, $sql);
                                                if(!$query)
                                                {
                                                    die("<br>" . "Error, línea 415: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla departamento, verifique con el administrador de sistemas.");
                                                }
                                                else
                                                {   
                                                    echo "<select name='depto' class='form-control' >";
                                                    while($fila=mysqli_fetch_array($query))
                                                    {
                                                        if($fila[0]==$id2[4])
                                                        {
                                                            echo "<option value='".$fila[0]."' selected>". $fila[0] . " " .$fila[1]."</option>";
                                                        }
                                                        else
                                                        {
                                                            echo "<option value='".$fila[0]."'>". $fila[0] . " " .$fila[1]."</option>";
                                                        }
                                                    }
                                                    echo "</select>";
                                                }    
                                            ?> <!--FIN PHP -->
                                        </div>

                                        <div class="form-group col-lg-7">
                                            <span >
                                                Categoría
                                            </span>
                                            <?php
                                                $sql="select * from categoria";
                                                $query= mysqli_query($con, $sql);
                                                if(!$query)
                                                {
                                                    die("<br>" . "Error, línea 445: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla categoría, verifique con el administrador de sistemas. ");
                                                }
                                                else
                                                {
                                                    echo "<select name='cat' class='form-control' value='<?php echo $id2[5];?>' >";
                                                    while($fila=mysqli_fetch_array($query))
                                                    {
                                                        if($id2[5]==$fila[0])
                                                        {
                                                            echo "<option value='".$fila[0]."' selected >". $fila[0] . " " .$fila[1]."</option>";  
                                                        }
                                                        else
                                                        {
                                                            echo "<option value='".$fila[0]."'>". $fila[0] . " " .$fila[1]."</option>"; 
                                                        }
                                                    }
                                                    echo "</select>";
                                                }
                                            ?> <!--FIN PHP -->
                                        </div>

                                        <div class="form-group col-lg-5">
                                            <span>
                                                Tipo de empleado
                                            </span>
                                            <?php
                                                if($id2[6]==4)
                                                {
                                                    echo "<br> <input type='radio' name='tipo' id='4' value='4' onclick='oculta(0)'checked></input><label for='4'>COMISIONADO FORÁNEO</label>";
                                                }
                                                else
                                                {
                                                    $sql="select * from tipo";
                                                    $query= mysqli_query($con, $sql);
                                                    if(!$query)
                                                    {
                                                        die("<br>" . "Error, línea 485: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla tipo, verifique con el administrador de sistemas.");
                                                    }
                                                    else
                                                    {
                                                        while($fila=mysqli_fetch_array($query))
                                                        {                                                        
                                                            if($id2[6]==$fila[0])
                                                            {  
                                                                
                                                                if($id2[6]==1)
                                                                {
                                                                    echo "<br> <input type='radio' name='tipo' id='".$fila[0]."' value='".$fila[0]."' onclick='oculta(1)' checked></input><label for='".$fila[0]."'>".$fila[1]."</label>";
                                                                }
                                                                if($id2[6]==2)
                                                                {
                                                                    echo "<br> <input type='radio' name='tipo' id='".$fila[0]."' value='".$fila[0]."' onclick='oculta(1)' checked></input><label for='".$fila[0]."'>".$fila[1]."</label>";
                                                                }
                                                                if($id2[6]==3)
                                                                {
                                                                    echo "<br> <input type='radio' name='tipo' id='".$fila[0]."' value='".$fila[0]."' onclick='oculta(1)' checked></input><label for='".$fila[0]."'> ".$fila[1]."</label>";
                                                                }
                                                            }
                                                            else
                                                            {  
                                                                if($fila[0]==1)
                                                                {
                                                                    echo "<br> <input type='radio' name='tipo' id='".$fila[0]."' value='".$fila[0]."' onclick='oculta(1)'></input><label for='".$fila[0]."'>".$fila[1]."</label>";
                                                                }
                                                                if($fila[0]==2)
                                                                {
                                                                    echo "<br> <input type='radio' name='tipo' id='".$fila[0]."' value='".$fila[0]."' onclick='oculta(1)'></input><label for='".$fila[0]."'>".$fila[1]."</label>";
                                                                }
                                                                if($fila[0]==3)
                                                                {
                                                                    echo "<br> <input type='radio' name='tipo' id='".$fila[0]."' value='".$fila[0]."' onclick='oculta(1)'></input><label for='".$fila[0]."'> ".$fila[1]."</label>";
                                                                }
                                                            }
                                                            
                                                        }
                                                        
                                                    }//fin del while
                                                    
                                                }
                                                if(!empty($empresa))
                                                {
                                                    echo  //El id= empresa del div sirve para ocultar estos elementos
                                                    "<div id='empresa' class='form-group col-lg-12'>
                                                    <br> <span>Empresa de origen: </span> <br> <input type='text' class='form-control' name='emp' value='$empresa' pattern='[a-zA-ZàáâäãåacceèéêëeiìíîïlnòóôöõøùúûüuuÿýzzñçcÀÁÂÄÃÅACCEEÈÉÊËÌÍÎÏILNÒÓÔÖÕØÙÚÛÜUUÝZZÑßÇÆC?ð& ,''.0-9]{2,48}' title='Ingresar letras, números, solo caracteres: &, '', . ' >
                                                    <br> <span>Fecha de inicio de la comisión: </span> <br> <input type='date' id='f_ini'value='$fecha_inicio'  class='form-control' name='f_ini' min='2020-01-01'>
                                                    <br> <span>Fecha de fin de la comisión: </span> <br> <input type='date' id='f_fin' value='$fecha_fin'  class='form-control' name='f_fin'min='2020-01-01'>
                                                    </div>";
                                                }
                                                else
                                                {
                                                    echo  //El id= empresa del div sirve para ocultar estos elementos
                                                    "<div id='empresa' class='form-group col-lg-12'>
                                                    <br> <span>Empresa de origen: </span> <br> <input type='text' class='form-control' name='emp' pattern='[a-zA-ZàáâäãåacceèéêëeiìíîïlnòóôöõøùúûüuuÿýzzñçcÀÁÂÄÃÅACCEEÈÉÊËÌÍÎÏILNÒÓÔÖÕØÙÚÛÜUUÝZZÑßÇÆC?ð& ,''.0-9]{2,48}' title='Ingresar letras, números, solo caracteres: &, '', . ' >
                                                    <br> <span>Fecha de inicio de la comisión: </span> <br> <input type='date' id='f_ini' class='form-control' name='f_ini' min='2020-01-01'>
                                                    <br> <span>Fecha de fin de la comisión: </span> <br> <input type='date' id='f_fin'  class='form-control' name='f_fin'min='2020-01-01'>
                                                    </div>";
                                                }
                                                

                                            
                                            ?> <!--FIN PHP -->
                                        </div>

                                        <div class="form-group col-lg-7">
                                            <?php 
                                                $semana = array('lunes','martes','miercoles','jueves','viernes','sabado','domingo','dias_festivos');//campos de la bd
                                                $semana2 = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo','Días festivos'); //dias de la semana
                                                echo"<div class='form-group col-lg-4'>
                                                <span> Días de trabajo</span><br>";
                                                for ($i=0;$i<8;$i++)
                                                {
                                                    if($id4[$i]==1)
                                                    {
                                                        echo "<input type='checkbox' name='dia[]' id='$semana[$i]' value='$semana[$i]' checked/> <label for='$semana[$i]'> $semana2[$i]</label><br/>";  
                                                    }
                                                    else
                                                    {
                                                        echo "<input type='checkbox' name='dia[]' id='$semana[$i]' value='$semana[$i]'/> <label for='$semana[$i]'> $semana2[$i]</label><br/>";
                                                    }  
                                                }
                                                echo "</div";
                                            ?> 
                                        </div>

                                        <div id='dias_sexta' class='form-group col-lg-3'>
                                            
                                        </div>
                                        <input type="hidden" id='sexta' value="<?php echo $id; ?>"><!--número de trabajador-->

                                        <div class="form-group col-lg-5">
                                            <span >
                                                Turno
                                            </span>
                                            <?php
                                                $sql="select * from turno";
                                                $query= mysqli_query($con, $sql);
                                                if(!$query)
                                                {
                                                    die("<br>" . "Error, línea 583: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla turno, verifique con el administrador de sistemas. ");
                                                }
                                                else
                                                {
                                                    echo "<select name='turno' id='turno' class='form-control'  >";
                                                    while($fila=mysqli_fetch_array($query))
                                                    {
                                                        if($id4[8]==$fila[0])
                                                        {
                                                            echo "<option value='".$fila[0]." " .$fila[3]."'selected>". $fila[0] . " " .$fila[1]. " - " .$fila[2]."</option>";
                                                        }
                                                        else
                                                        {
                                                            echo "<option value='".$fila[0]." " .$fila[3]."'>". $fila[0] . " " .$fila[1]. " - " .$fila[2]."</option>";
                                                        }
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
                                       <div class="form-group col-lg-12">
                                             <span>Fecha de alta del trabajador (Según su FM1) </span>
                                             <input name="fecha_alta" type="date" class="form-control" value="<?php echo $id5[0]?>" required  min="1930-01-01" />
                                       </div>      
                                    </div>
                                </div>

                                </div>
                                <div class="card-footer">
                                     <input type="submit" name="Aceptar" value="Guardar" class="btn btn-primary btn-sm" />
                                </div>
                            </div>
                        </div>  
                    </form>

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
                                        <th>Acción</th>  
                                        </thead>
                                        <tbody>
                                            <?php
                                                require("../ht/_encript.php");
                                                $sql="SELECT a.numero_trabajador,a.nombre,a.apellido_paterno,a.apellido_materno,a.depto_depto,a.categoria_categoria,b.descripcion FROM trabajador a 
                                                inner join tipo b on b.idtipo = a.tipo_tipo";
                                                $query= mysqli_query($con, $sql);
                                                if(!$query)
                                                {
                                                    die("<br>" . "Error, línea 651: " . mysqli_errno($con) . " : " . mysqli_error($con).", verifique con el administrador de sistemas.");
                                                }
                                                else
                                                {
                                                    while($resul=mysqli_fetch_array($query))
                                                    {
                                                        $encript=generaURL($resul[0]);
                                                        echo "<tr>";
                                                        echo "<td>" . $resul[0] . "</td>"; 
                                                        echo "<td>" . $resul[1] . "</td>";
                                                        echo "<td>" . $resul[2] . "</td>";
                                                        echo "<td>" . $resul[3] . "</td>";
                                                        echo "<td>" . $resul[4] . "</td>";
                                                        echo "<td>" . $resul[5] . "</td>";
                                                        echo "<td>" . $resul[6] . "</td>";
                                                        echo "<td><a><button class='btn btn-danger btn-sm' id='$encript' onclick='preguntar(this);'><i class='fa fa-trash-o'></i>Eliminar </button></a> ";
                                                        echo " <a href='../php/editar-trabajadores.php?id=".$resul[0]."'><button class='btn btn-success btn-sm'><i class='fa fa-pencil-square-o'></i>Editar </button></a> </td>";
                                                        echo "</tr>";
                                                    }
                                                }
                                            ?> <!--FIN PHP -->
                                        </tbody> <!--/tbody-->
                                    </table> <!--/table-->
                                </div><!--/card body-->
                            </div><!--/card-->
                        </div><!--/col- md-12-->
                    </div><!-- FIN ROW-->
                </div> <!--FIN DIV animated fadeIn-->
            </div> <!--FIN DIV content mt-3--> 

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
        <!-- FIN-Right Panel ------------------------------------------------------------------------------------------------>
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

                $(function() {
                    $('#turno').on('keyup', function(e) {
                        if (e.which == 38 || e.which == 40)
                        {
                            var valor = document.getElementById('turno').value;
                            var valor2 = document.getElementById('sexta').value;
                            var valor3 = $("input[name='tipo']:checked").val();//nomForm= nombre del formulario; tipo = nombre de los elementos radiobuton
                            actualiza(valor,valor2,valor3);
                        }
                    });

                });

            });

            function preguntar(elemento,ruta,id)
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
    </body>
    <?php require("../ht/modalCambiarPass.php"); ?>
</html>