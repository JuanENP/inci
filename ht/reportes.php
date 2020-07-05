<?php
session_start();
date_default_timezone_set('America/Mexico_City'); 
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php"); 
        $anio_actual=date("Y");//guardar la fecha actual
        $id="";
        $ubicacion='../php/update/modificarPass.php';//sirve para indicar la ruta del form modalCambiarPass
    }
    else
    {
        header("Location: ../index.php");
        die();
    }
    $sql="select idquincena from quincena where (validez=1);";
    $query= mysqli_query($con, $sql);
    $filas=mysqli_num_rows($query);
    if($filas==1)
    {
        $resul=mysqli_fetch_array($query);
        $idactual= $resul[0];
    }
    else
    {
        $salida="La tabla quincena no posee una quincena válida, verifique con el administrador de sistemas. Error: línea 20. Revise la tarea obtener_quincena y verifique que la tabla quincena posea solo una quincena con valor 1.";
        echo "<script type=\"text/javascript\">alert('$salida'); history.back(); </script>";
    }

?>

<!doctype html>
    <html class="no-js" lang="es">
    <head>
        <meta meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>
           Reportes
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
        <link rel="stylesheet" href="../assets/scss/style.css"/>
        <link href="../assets/css/lib/vector-map/jqvmap.min.css" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.full.min.js"></script>
        <script type="text/javascript">
            function oculta(x) 
            {
                if (x == 1) 
                {
                    document.getElementById('rangos').style.display = "block";//ver
                    document.getElementById('todos-q').style.display = "none";//no ver
                    document.getElementById('numeros').style.display = "none";
                } 
                else 
                { 
                    if (x == 2) 
                    {   
                        document.getElementById('todos-q').style.display = "block";//ver
                        document.getElementById('rangos').style.display = "none";//no ver
                        document.getElementById('numeros').style.display = "none";
                    } 
                    else
                    {
                        if (x == 3) 
                        {  
                            document.getElementById('numeros').style.display = "block";//ver
                            document.getElementById('rangos').style.display = "none";//no ver
                            document.getElementById('todos-q').style.display = "none";
                        } 
                    }
                }
            }//Fin function

            //COMISIONES
            function ocultacomision(x)
            {
                if(x==1)
                {
                    document.getElementById('boton-descargar').style.display = "none";//ver
                    document.getElementById('buscar-comision').style.display = "block";//ver
                    //Sirve para que las subopciones muestren  el boton de descargar
                    var subrad = $("input[name='sub-opc']:checked").val();
                    if(subrad=="activas" || subrad=="inactivas")
                    {
                        ocultadescargar(1);//Ver el botón descargar
                    }        
                }
                else
                {
                    if(x==2)
                    {
                        document.getElementById('boton-descargar').style.display = "none";//ver
                        document.getElementById('buscar-comision').style.display = "block";//ver        
                        //Sirve para que las subopciones muestren  el boton de descargar
                        var subrad = $("input[name='sub-opc']:checked").val();
                        if(subrad=="activas" || subrad=="inactivas")
                        {
                            ocultadescargar(1);//Ver el botón descargar
                        }  
                    }
                    else
                    {
                        if(x==3)
                        {
                            document.getElementById('boton-descargar').style.display = "none";//ver
                            document.getElementById('buscar-comision').style.display = "block";//ver 
                            //Sirve para que las subopciones muestren  el boton de descargar
                            var subrad = $("input[name='sub-opc']:checked").val();
                            if(subrad=="activas" || subrad=="inactivas")
                            {
                                ocultadescargar(1);//Ver el botón descargar
                            }  
                        }
                        else
                        {
                            if(x==4)
                            {
                                document.getElementById('boton-descargar').style.display = "block";//ver
                                document.getElementById('buscar-comision').style.display = "none";      
                            }
                        }
                    }
                }
            }

            function ocultadescargar(x)
            {
                if(x==1)
                {
                    document.getElementById('boton-descargar').style.display = "block";//ver        
                }
            }
            //COMISIONES

            function ocultavinieron(x)
            {
                if(x==1)
                {
                    document.getElementById('numero-vinieron').style.display = "none";//no ver  
                    document.getElementById('quincena-vinieron').style.display = "none";//no ver  
                    document.getElementById('rangos-vinieron').style.display = "block";//ver              
                }
                else
                {
                    if(x==2)
                    {
                        document.getElementById('rangos-vinieron').style.display = "none";// no ver 
                        document.getElementById('quincena-vinieron').style.display = "none";//no ver 
                        document.getElementById('numero-vinieron').style.display = "block";//ver        
                    }
                    else
                    {
                        if(x==3)
                        {
                            document.getElementById('rangos-vinieron').style.display = "none";// no ver 
                            document.getElementById('numero-vinieron').style.display = "none";//    
                            document.getElementById('quincena-vinieron').style.display = "block";//ver   
                        }
                    }
                }
            }

            function ocultafaltaron(x)
            {
                if(x==1)
                {
                    document.getElementById('numero-faltaron').style.display = "none";//no ver  
                    document.getElementById('quincena-faltaron').style.display = "none";//no ver  
                    document.getElementById('rangos-faltaron').style.display = "block";//ver              
                }
                else
                {
                    if(x==2)
                    {
                        document.getElementById('rangos-faltaron').style.display = "none";// no ver 
                        document.getElementById('quincena-faltaron').style.display = "none";//no ver 
                        document.getElementById('numero-faltaron').style.display = "block";//ver        
                    }
                    else
                    {
                        if(x==3)
                        {
                            document.getElementById('rangos-faltaron').style.display = "none";// no ver 
                            document.getElementById('numero-faltaron').style.display = "none";//    
                            document.getElementById('quincena-faltaron').style.display = "block";//ver   
                        }
                    }
                }
            }

            function ocultaguardias(x)
            {
                if(x==1)
                {
                    document.getElementById('numero-guardias').style.display = "none";//no ver  
                    document.getElementById('quincena-guardias').style.display = "none";//no ver  
                    document.getElementById('rangos-guardias').style.display = "block";//ver              
                }
                else
                {
                    if(x==2)
                    {
                        document.getElementById('rangos-guardias').style.display = "none";// no ver 
                        document.getElementById('quincena-guardias').style.display = "none";//no ver 
                        document.getElementById('numero-guardias').style.display = "block";//ver        
                    }
                    else
                    {
                        if(x==3)
                        {
                            document.getElementById('rangos-guardias').style.display = "none";// no ver 
                            document.getElementById('numero-guardias').style.display = "none";//    
                            document.getElementById('quincena-guardias').style.display = "block";//ver   
                        }
                    }
                }
            }

            function ocultaSubElementos()
            {
                //Quienes tienen cumpleaños u onomasticos
                document.getElementById('rangos-guardias').style.display = "none";// no ver 
                document.getElementById('numero-guardias').style.display = "none";//    
                document.getElementById('quincena-guardias').style.display = "none";//ver  
                //Quienes faltaron
                document.getElementById('rangos-faltaron').style.display = "none";// no ver
                document.getElementById('numero-faltaron').style.display = "none";//no ver 
                document.getElementById('quincena-faltaron').style.display = "none";//no ver 
                //Quienes vinieron
                document.getElementById('rangos-vinieron').style.display = "none";// no ver
                document.getElementById('numero-vinieron').style.display = "none";//no ver 
                document.getElementById('quincena-vinieron').style.display = "none";//no ver 
                //Vacaciones
                document.getElementById('rangos').style.display = "none";// no ver
                document.getElementById('todos-q').style.display = "none";//no ver
                document.getElementById('numeros').style.display = "none";
                //comisiones
                document.getElementById('buscar-comision').style.display = "none";//no ver   

            }

            // <!--Funcion que sirve para mostrar u ocultar los divs del modal  --> 
            function recibir(numero)
            {
                var valor = document.getElementById(numero).value;/*Es el valor del value del botón*/
                if(valor=="UNICO")
                {
                    $('input:hidden[name=id]').val("unico");
                    ocultaSubElementos();
                    document.getElementById('asistencia').style.display = "none";/*no ver div*/
                    document.getElementById('vacaciones').style.display = "none";/*no ver div*/
                    document.getElementById('comisionados').style.display = "none";/*No ver div*/
                    document.getElementById('buscar-comision').style.display = "none";/*No ver div*/    
                    document.getElementById('vinieron').style.display = "none";  
                    document.getElementById('faltaron').style.display = "none";
                    document.getElementById('cumpleOno').style.display = "none"; 
                    document.getElementById('guardias').style.display = "none"; 
                    document.getElementById('sextas').style.display = "none";
                    document.getElementById('licencias').style.display = "none";
                    document.getElementById('pases').style.display = "none";
                    document.getElementById('unico').style.display = "block";/*Ver div*/
                    document.getElementById('boton-descargar').style.display = "block"; 
                }
                else
                {  
                    if(valor=="VACACIONES")
                    {   
                        $('input:hidden[name=id]').val("vacaciones");
                        ocultaSubElementos();
                        document.getElementById('unico').style.display = "none";/*No ver div*/   
                        document.getElementById('asistencia').style.display = "none";/*No ver div*/ 
                        document.getElementById('comisionados').style.display = "none";/*No ver div*/     
                        document.getElementById('buscar-comision').style.display = "none";/*No ver div*/
                        document.getElementById('vinieron').style.display = "none";  
                        document.getElementById('faltaron').style.display = "none";
                        document.getElementById('cumpleOno').style.display = "none";
                        document.getElementById('guardias').style.display = "none"; 
                        document.getElementById('sextas').style.display = "none";
                        document.getElementById('licencias').style.display = "none"; 
                        document.getElementById('pases').style.display = "none";
                        document.getElementById('vacaciones').style.display = "block";/*Ver div*/
                        document.getElementById('boton-descargar').style.display = "block"; 
                        var rad = $("input[name='opcion']:checked").val();
                        if(rad=="rango")
                        {
                            oculta(1);//Solo ver el div de rango
                            
                        }
                        else
                        {
                            if(rad=="todos")
                            {
                                oculta(2);
                            }
                            else
                            {
                                if(rad=="numero")
                                {
                                    oculta(3);
                                }
                            }
                        }
                    }
                    else
                    {
                        if(valor=="ASISTENCIA")
                        {   $('input:hidden[name=id]').val("asistencia");
                                ocultaSubElementos();
                                document.getElementById('unico').style.display = "none";/*No ver div*/  
                                document.getElementById('vacaciones').style.display = "none";/*No ver div*/
                                document.getElementById('comisionados').style.display = "none";/*No ver div*/ 
                                document.getElementById('buscar-comision').style.display = "none";/*No ver div*/
                                document.getElementById('vinieron').style.display = "none";  
                                document.getElementById('faltaron').style.display = "none";
                                document.getElementById('cumpleOno').style.display = "none"; 
                                document.getElementById('guardias').style.display = "none"; 
                                document.getElementById('sextas').style.display = "none";
                                document.getElementById('licencias').style.display = "none";
                                document.getElementById('pases').style.display = "none";
                                // document.getElementById('rangos2').style.display = "none";/*No ver div*/  
                                // document.getElementById('fecha').style.display = "none";/*No ver div*/ 
                                document.getElementById('asistencia').style.display = "block";/*Ver div*/ 
                                document.getElementById('boton-descargar').style.display = "block"; 
                                
                        }
                        else
                        {
                                if(valor=="COMISIONADOS")
                                {   $('input:hidden[name=id]').val("comisionados");
                                    ocultaSubElementos();
                                    document.getElementById('unico').style.display = "none";/*No ver div*/  
                                    document.getElementById('vacaciones').style.display = "none";/*No ver div*/ 
                                    document.getElementById('asistencia').style.display = "none";  
                                    document.getElementById('buscar-comision').style.display = "none";/*No ver div*/
                                    document.getElementById('boton-descargar').style.display = "none";/*No ver div*/
                                    document.getElementById('vinieron').style.display = "none";
                                    document.getElementById('faltaron').style.display = "none";
                                    document.getElementById('cumpleOno').style.display = "none";
                                    document.getElementById('guardias').style.display = "none"; 
                                    document.getElementById('sextas').style.display = "none";
                                    document.getElementById('licencias').style.display = "none";
                                    document.getElementById('pases').style.display = "none";
                                    document.getElementById('comisionados').style.display = "block";/*Ver div*/ 
                                    
                                    var rad = $("input[name='opc']:checked").val();
                                    //Sirve para que las subopciones muestren  el boton de descargar
                                    var subrad = $("input[name='sub-opc']:checked").val();
                                    
                                    if(rad=="fora")
                                    {
                                        ocultacomision(1);
                                        if(subrad=="activas" || subrad=="inactivas")
                                        {
                                            ocultadescargar(1);
                                        }
                                    }
                                    else
                                    {
                                        if(rad=="int")
                                        {
                                            ocultacomision(2);
                                            if(subrad=="activas" || subrad=="inactivas")
                                            {
                                                ocultadescargar(1);
                                            }
                                        }
                                        else
                                        {
                                            if(rad=="ext")
                                            {
                                                ocultacomision(3);
                                                if(subrad=="activas" || subrad=="inactivas")
                                                {
                                                    ocultadescargar(1);
                                                }
                                            }
                                            else
                                            {
                                                if(rad=="vence")
                                                {
                                                    ocultacomision(4);
                                                }
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    
                                    if(valor=="VINIERON")
                                    {   $('input:hidden[name=id]').val("vinieron");//id
                                        ocultaSubElementos();
                                    
                                        document.getElementById('unico').style.display = "none";/*No ver div*/  
                                        document.getElementById('vacaciones').style.display = "none";/*No ver div*/ 
                                        document.getElementById('asistencia').style.display = "none";  
                                        document.getElementById('buscar-comision').style.display = "none";/*No ver div*/
                                        document.getElementById('comisionados').style.display = "none";
                                        document.getElementById('faltaron').style.display = "none";
                                        document.getElementById('cumpleOno').style.display = "none";
                                        document.getElementById('guardias').style.display = "none"; 
                                        document.getElementById('sextas').style.display = "none";
                                        document.getElementById('licencias').style.display = "none";
                                        document.getElementById('pases').style.display = "none";
                                        document.getElementById('vinieron').style.display = "block";
                                        document.getElementById('boton-descargar').style.display = "block";/*ver div*/
                                        
                                        var rad = $("input[name='opcion-v']:checked").val();
                                        if(rad=="rango-v")
                                        {
                                            ocultavinieron(1);
                                        }
                                        else
                                        {
                                            if(rad=="numero-v")
                                            {
                                                ocultavinieron(2);
                                            }
                                            else
                                            {    if(rad=="quincena-v")
                                                {
                                                    ocultavinieron(3);
                                                }
                                                
                                            }
                                        }
                                    }//fin if
                                    else
                                    {
                                        if(valor=="FALTARON")
                                        {  
                                            $('input:hidden[name=id]').val("faltaron");//id
                                            ocultaSubElementos();
                                        
                                            document.getElementById('unico').style.display = "none";/*No ver div*/  
                                            document.getElementById('vacaciones').style.display = "none";/*No ver div*/ 
                                            document.getElementById('asistencia').style.display = "none";  
                                            document.getElementById('buscar-comision').style.display = "none";/*No ver div*/
                                            document.getElementById('comisionados').style.display = "none";
                                            document.getElementById('vinieron').style.display = "none";
                                            document.getElementById('cumpleOno').style.display = "none"; 
                                            document.getElementById('guardias').style.display = "none";
                                            document.getElementById('sextas').style.display = "none";
                                            document.getElementById('licencias').style.display = "none";
                                            document.getElementById('pases').style.display = "none";
                                            document.getElementById('faltaron').style.display = "block";/*ver div*/
                                            document.getElementById('boton-descargar').style.display = "block";/*ver div*/
                                            var rad = $("input[name='opcion-f']:checked").val();
                                            if(rad=="rango-f")
                                            {
                                                ocultafaltaron(1);
                                            }
                                            else
                                            {
                                                if(rad=="numero-f")
                                                {
                                                    ocultafaltaron(2);
                                                }
                                                else
                                                {   if(rad=="quincena-f")
                                                    {
                                                        ocultafaltaron(3);
                                                    }
                                                    
                                                }
                                            }
                                        }//fin if
                                        else
                                        {
                                            if(valor=="CUMPLEONO")
                                            {   
                                                $('input:hidden[name=id]').val("cumpleOno");
                                                ocultaSubElementos();
                                                document.getElementById('unico').style.display = "none";/*No ver div*/  
                                                document.getElementById('vacaciones').style.display = "none";/*No ver div*/
                                                document.getElementById('comisionados').style.display = "none";/*No ver div*/ 
                                                document.getElementById('buscar-comision').style.display = "none";/*No ver div*/
                                                document.getElementById('vinieron').style.display = "none";  
                                                document.getElementById('faltaron').style.display = "none";
                                                // document.getElementById('rangos2').style.display = "none";/*No ver div*/  
                                                // document.getElementById('fecha').style.display = "none";/*No ver div*/ 
                                                document.getElementById('asistencia').style.display = "none";/*No ver div*/ 
                                                document.getElementById('guardias').style.display = "none";
                                                document.getElementById('sextas').style.display = "none";
                                                document.getElementById('licencias').style.display = "none";
                                                document.getElementById('pases').style.display = "none";
                                                document.getElementById('cumpleOno').style.display = "block"; 
                                                document.getElementById('boton-descargar').style.display = "block"; 
                                                
                                            }//fin if
                                            else
                                            {
                                                if(valor=="GUARDIAS")
                                                {   $('input:hidden[name=id]').val("guardias");//id
                                                    ocultaSubElementos();
                                                
                                                    document.getElementById('unico').style.display = "none";/*No ver div*/  
                                                    document.getElementById('vacaciones').style.display = "none";/*No ver div*/ 
                                                    document.getElementById('asistencia').style.display = "none";  
                                                    document.getElementById('buscar-comision').style.display = "none";/*No ver div*/
                                                    document.getElementById('comisionados').style.display = "none";
                                                    document.getElementById('faltaron').style.display = "none";
                                                    document.getElementById('cumpleOno').style.display = "none"; 
                                                    document.getElementById('vinieron').style.display = "none";
                                                    document.getElementById('sextas').style.display = "none";
                                                    document.getElementById('licencias').style.display = "none";
                                                    document.getElementById('pases').style.display = "none";
                                                    document.getElementById('guardias').style.display = "block";/*ver div*/
                                                    document.getElementById('boton-descargar').style.display = "block";/*ver div*/
                                                    
                                                    var rad = $("input[name='opcion-g']:checked").val();
                                                    if(rad=="rango-g")
                                                    {
                                                        ocultaguardias(1);
                                                    }
                                                    else
                                                    {
                                                        if(rad=="numero-g")
                                                        {
                                                            ocultaguardias(2);
                                                        }
                                                        else
                                                        {    if(rad=="quincena-g")
                                                            {
                                                                ocultaguardias(3);
                                                            }
                                                            
                                                        }
                                                    }//fin else
                                                }//fin if
                                                else
                                                {
                                                    if(valor=="SEXTAS")
                                                    {
                                                        $('input:hidden[name=id]').val("sextas");
                                                        ocultaSubElementos();
                                                        document.getElementById('unico').style.display = "none";
                                                        document.getElementById('asistencia').style.display = "none";/*no ver div*/
                                                        document.getElementById('vacaciones').style.display = "none";/*no ver div*/
                                                        document.getElementById('comisionados').style.display = "none";/*No ver div*/
                                                        document.getElementById('buscar-comision').style.display = "none";/*No ver div*/    
                                                        document.getElementById('vinieron').style.display = "none";  
                                                        document.getElementById('faltaron').style.display = "none";
                                                        document.getElementById('cumpleOno').style.display = "none"; 
                                                        document.getElementById('guardias').style.display = "none"; 
                                                        document.getElementById('licencias').style.display = "none";
                                                        document.getElementById('pases').style.display = "none";
                                                        document.getElementById('sextas').style.display = "block";/*Ver div*/
                                                        document.getElementById('boton-descargar').style.display = "block"; 
                                                    }
                                                    else
                                                    {
                                                        if(valor=="LICENCIAS")
                                                        {
                                                            $('input:hidden[name=id]').val("licencias");
                                                            ocultaSubElementos();
                                                            document.getElementById('unico').style.display = "none";
                                                            document.getElementById('asistencia').style.display = "none";/*no ver div*/
                                                            document.getElementById('vacaciones').style.display = "none";/*no ver div*/
                                                            document.getElementById('comisionados').style.display = "none";/*No ver div*/
                                                            document.getElementById('buscar-comision').style.display = "none";/*No ver div*/    
                                                            document.getElementById('vinieron').style.display = "none";  
                                                            document.getElementById('faltaron').style.display = "none";
                                                            document.getElementById('cumpleOno').style.display = "none"; 
                                                            document.getElementById('guardias').style.display = "none"; 
                                                            document.getElementById('sextas').style.display = "none";
                                                            document.getElementById('pases').style.display = "none";
                                                            document.getElementById('licencias').style.display = "block";/*Ver div*/
                                                            document.getElementById('boton-descargar').style.display = "block"; 
                                                        }
                                                        else
                                                        {
                                                            if(valor=="PASES")
                                                            {
                                                                $('input:hidden[name=id]').val("pases");
                                                                ocultaSubElementos();
                                                                document.getElementById('unico').style.display = "none";
                                                                document.getElementById('asistencia').style.display = "none";/*no ver div*/
                                                                document.getElementById('vacaciones').style.display = "none";/*no ver div*/
                                                                document.getElementById('comisionados').style.display = "none";/*No ver div*/
                                                                document.getElementById('buscar-comision').style.display = "none";/*No ver div*/    
                                                                document.getElementById('vinieron').style.display = "none";  
                                                                document.getElementById('faltaron').style.display = "none";
                                                                document.getElementById('cumpleOno').style.display = "none"; 
                                                                document.getElementById('guardias').style.display = "none"; 
                                                                document.getElementById('sextas').style.display = "none";
                                                                document.getElementById('licencias').style.display = "none";
                                                                document.getElementById('pases').style.display = "block";
                                                                document.getElementById('boton-descargar').style.display = "block"; 
                                                            }

                                                        }

                                                    }//fin else sextas
                                                }//fin else guardias
                                            }//fin else cumpleono
                                        }//fin else faltaron
                                    }
                                }
                        }
                    } 
                }
            } 
            // <!--Funcion que sirve para mostrar u ocultar los divs del modal  -->
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
                    <a class="navbar-brand" href="#">Control de Asistencia
                <a class="navbar-brand hidden" href="#"></a>
                </div>
                <div id="main-menu" class="main-menu collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="../panel_control.php" title="Volver al panel principal"> <i class="menu-icon fa fa-dashboard"></i>Panel de Control </a>
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
                        <!-- SIRVE PARA CAMBIAR LAS OPCIONES DEL MENÚ REPOSITORIO DEL EMPLEADO -->
                        <!--  <li id="Menu_Sistema" class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Repositorio</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-users"></i><a href="ht/repositorio.php">Formatos</a></li>
                            </ul>
                        </li>
                        -->
                        <!-- -------------------------------------------- -->
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </nav>
        </aside>

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
                            <h1>REPORTES</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li class="active">Inicio</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content ">
                <div class="animated fadeIn">
                    <div class="row">
                        <div class="contenedor">
                            <div class="col-xl-5">
                                <button class="btn-primary btn-sm bt-report" data-toggle="modal" data-target="#mimodalejemplo"  name="boton"id="1"   value="UNICO" onclick="recibir(1);"><i class="fa fa-file-pdf-o"></i> REPORTE ÚNICO DE INCIDENCIAS </button>
                            </div>

                            <div class="col-xl-5">
                                <button class="btn-primary btn-sm bt-report" data-toggle="modal" data-target="#mimodalejemplo"  name="boton"id="2"  value="VACACIONES" onclick="recibir(2);"><i class="fa fa-file-pdf-o"></i>VACACIONES</button>
                            </div>
                            
                            <div class="col-xl-5" >
                                <button class="btn-primary btn-sm bt-report" data-toggle="modal" data-target="#mimodalejemplo"  name="boton" id="3" value="ASISTENCIA" onclick="recibir(3);"><i class="fa fa-file-pdf-o"></i> ¿QUIÉN DEBE ASISTIR?</button> 
                            </div>

                            <div class="col-xl-5">
                                <button class="btn-primary btn-sm bt-report" data-toggle="modal" data-target="#mimodalejemplo"  name="boton" id="4" value="COMISIONADOS" onclick="recibir(4);"><i class="fa fa-file-pdf-o"></i> COMISIONADOS</button>
                            </div>

                            <div class="col-xl-5">
                                <button class="btn-primary btn-sm bt-report" data-toggle="modal" data-target="#mimodalejemplo"  name="boton" id="5" value="VINIERON" onclick="recibir(5);"><i class="fa fa-file-pdf-o"></i> ¿QUIÉNES VINIERON?</button>
                            </div>
                            
                            <div class="col-xl-5">
                                <button class="btn-primary btn-sm bt-report" data-toggle="modal" data-target="#mimodalejemplo"  name="boton" id="6" value="FALTARON" onclick="recibir(6);"><i class="fa fa-file-pdf-o"></i> ¿QUIÉNES FALTARON?</button>
                            </div>

                            <div class="col-xl-5">
                                <button class="btn-primary btn-sm bt-report"  data-toggle="modal" data-target="#mimodalejemplo"   name="boton" id="7" value="CUMPLEONO" onclick="recibir(7);"><i class="fa fa-file-pdf-o"></i> CUMPLEAÑOS U ONOMÁSTICO</button>
                            </div>

                            <div class="col-xl-5">
                                <button class="btn-primary btn-sm bt-report" data-toggle="modal" data-target="#mimodalejemplo"  name="boton" id="8" value="GUARDIAS" onclick="recibir(8);"><i class="fa fa-file-pdf-o"></i> GUARDIAS</button>
                            </div>

                            <div class="col-xl-5">
                                <button class="btn-primary btn-sm bt-report" data-toggle="modal" data-target="#mimodalejemplo"  name="boton" id="9" value="SEXTAS" onclick="recibir(9);"><i class="fa fa-file-pdf-o"></i>SEXTAS</button>
                            </div>

                            <div class="col-xl-5">
                                <button class="btn-primary btn-sm bt-report" data-toggle="modal" data-target="#mimodalejemplo"  name="boton" id="10" value="LICENCIAS" onclick="recibir(10);"><i class="fa fa-file-pdf-o"></i>LICENCIAS Y PERMISOS</button>
                            </div>

                            <div class="col-xl-5">
                                <button class="btn-primary btn-sm bt-report" data-toggle="modal" data-target="#mimodalejemplo"  name="boton" id="11" value="PASES" onclick="recibir(11);"><i class="fa fa-file-pdf-o"></i>PASES DE SALIDA</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="mimodalejemplo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content" id="modal" >
                        <div class="modal-header">
                            <!-- <h4 class="modal-title" id="myModalLabel">REPORTE</h4> -->
                        </div>
                        <form method="post" action="../rep-asistencia/crearPdf-reportes.php" id="form2" class="form-modal">
                            <!-- <p>Cuerpo del modal</p> -->
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div id="unico">
                                            <div class="col-md-12 label">
                                                <label>REPORTE ÚNICO DE INCIDENCIAS</label><br><br>
                                                <span>Seleccione la quincena: </span>
                                                <?php 
                                                    $sql2="select idquincena,fecha_inicio,fecha_fin from quincena where idquincena<=$idactual";
                                                    $query2= mysqli_query($con, $sql2);
                                                    if(!$query2)
                                                    {
                                                        die("<br>" . "Error no hay datos en la tabla quincena, verifique con el administrador de sistemas. Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                                                    }
                                                    else
                                                    {   
                                                        echo "<select name='quincena' class='form-control'>";
                                                        while($fila=mysqli_fetch_array($query2))
                                                        {   ///Divido la fecha de inicio y fin de la quincena
                                                            $f_ini=explode('-',$fila[1]);
                                                            $f_fin=explode('-',$fila[2]);
                                                            // Concateno el año actual a las fechas
                                                            $fila[1]=$anio_actual.'-'.$f_ini[1].'-'.$f_ini[2];
                                                            $fila[2]=$anio_actual.'-'.$f_fin[1].'-'.$f_fin[2];

                                                            echo "<option value='". $fila[0] . " " .$fila[1]. " " .$fila[2]."'>Quincena ". $fila[0] . " " .$fila[1]. " al  " .$fila[2]."</option>";
                                                        }
                                                        echo "</select>";
                                                    }
                                                ?> <!--FIN PHP -->
                                            </div> <!--fin col-md5 bn-3 label -->
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="unico" >                                                    
                                        </div><!--fin div unico-->

                                        <div id="vacaciones">
                                            <div class="col-md-12 label ">
                                                <div class="radios">
                                                <label for="">REPORTE DE VACACIONES</label><br><br>
                                                <label for="">Seleccione cómo desea que busquemos:</label>
                                                <p></p>
                                                <label for="rango"> Por rango de fechas
                                                <input type="radio" name="opcion" value="rango" id="rango" onclick="oculta(1)"></label>
                                                <p></p>
                                                <label for="todos"> Todos los empleados en una quincena
                                                <input type="radio" name="opcion" value="todos" id="todos" onclick="oculta(2)"></label>
                                                <p></p>
                                                <label for="numero"> Por un número de empleado en específico 
                                                <input type="radio" name="opcion" value="numero" id="numero" onclick="oculta(3)"></label>
                                                <p></p>
                                                </div>
                                                <div class="form-1-2">
                                                    <div id=rangos> 
                    
                                                        <label for="">Fecha inicio</label>
                                                        <input type="date" class="form-control" name="f_ini">
                                                        <label for="">Fecha fin</label>
                                                        <input type="date" class="form-control" name="f_fin">
                                                    
                                                    </div>
                                                </div> 

                                                <div class="form-1-2" >
                                                
                                                    <div id="todos-q">
                                                        <label for="">Seleccione la quincena: </label>
                                                        <?php
                                                        $sql2="select idquincena,fecha_inicio,fecha_fin from quincena where idquincena<=$idactual";
                                                        $query2= mysqli_query($con, $sql2);
                                                        if(!$query2)
                                                        {
                                                            die("<br>" . "Error no hay datos en la tabla quincena, verifique con el administrador de sistemas. Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                                                        }
                                                        else
                                                        {   
                                                            echo "<select name='quincena2' class='form-control' >";
                                                            while($fila=mysqli_fetch_array($query2))
                                                            {   ///Divido la fecha de inicio y fin de la quincena
                                                                $f_ini=explode('-',$fila[1]);
                                                                $f_fin=explode('-',$fila[2]);
                                                                // Concateno el año actual a las fechas
                                                                $fila[1]=$anio_actual.'-'.$f_ini[1].'-'.$f_ini[2];
                                                                $fila[2]=$anio_actual.'-'.$f_fin[1].'-'.$f_fin[2];
                                                            
                                                                echo "<option value='". $fila[0] . " " .$fila[1]. " " .$fila[2]."'>". Quincena. " ". $fila[0] . " " .$fila[1]. " al  " .$fila[2]."</option>";
                                                            }
                                                            echo "</select>";
                                                        }
                                                        ?> <!--FIN PHP -->
                                                        <!--Sirve para enviar que queremos buscar en el reporte-->
                                                        <input type="hidden" name="enviar" value="todos-q" >  
                                                    </div>
                                                </div>
                                                
                                                <div class="form-1-2" >
                                                    <div id=numeros>
                                                        <label for="">Ingrese un numéro de trabajador: </label>
                                                        <input type="text" class="form-control" name="num">
                                                    
                                                    </div>
                                                </div>
                                            </div>  
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="vacaciones" >                                                    
                                        </div><!--fin div-->

                                        <div id="asistencia">

                                            <div class="col-md-12 label">
                                            <label >REPORTE DE QUIÉN DEBE ASISTIR</label><br><br>
                                                <!--<label for="">Seleccione cómo desea que busquemos:</label>
                                                <p></p>
                                                <label for="rango2">Por rango de fechas <input type="radio" name="opcion2" value="rango2" id="rango2" onclick="oculta2(1)"></label>
                                                <p></p>
                                                <label for="fecha2">Por fecha en específico<input type="radio" name="opcion2" value="fecha2" id="fecha2" onclick="oculta2(2)"></label>
                                                <p></p>
                                                <div class="form-1-2" >
                                                    <div id=rangos2> 
                                                        <label for="">Fecha inicio</label>
                                                        <input type="date" class="form-control" name="ini">
                                                        <label for="">Fecha fin</label>
                                                        <input type="date" class="form-control" name="fin">
                                                    </div>
                                                </div> -->
                                                <div class="form-1-2"> 
                                                    <div id=fecha> 
                                                        <label for="">Seleccione una fecha:</label>
                                                        <input type="date" class="form-control" name="fecha">
                                                    </div>
                                                </div> 
                                            </div>
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="asistencia" >                                                    
                                        </div><!--fin div-->
                                        
                                        <div id="comisionados">
                                            <div class="col-md5 bn-3 ">
                                            <label for="">REPORTE DE COMISIONES</label><br><br>
                                                <div class="radios">
                                                <p>Seleccione una opción:</p>
                                                <p></p>
                                                
                                                <input type="radio" name="opc" value="fora" id="fora" onclick="ocultacomision(1)">
                                                <label for="fora"> Comisionados foráneos </label>
                                                <p></p>
                                                
                                                <input type="radio" name="opc" value="int" id="int" onclick="ocultacomision(2)">
                                                <label for="int"> Comisionados internos</label>
                                                <p></p>
                                                
                                                <input type="radio" name="opc" value="ext" id="ext" onclick="ocultacomision(3)">
                                                <label for="ext"> Comisionados externos</label>
                                                <p></p>
                                                
                                                <input type="radio" name="opc" value="vence" id="vence" onclick="ocultacomision(4)">
                                                <label for="vence"> Comisiones por vencer</label>
                                                <p></p>
                                                </div>
                                                <div class="form-1-2">
                                                    <div id=buscar-comision> 
                                                        <p> Seleccione una opción:</p>
                                                        <input type="radio" name="sub-opc" value="activas" id="act" onclick="ocultadescargar(1)">
                                                        <label for="act"> Comisiones activas</label>  
                                                        <p></p>
                                                        <input type="radio" name="sub-opc" value="inactivas" id="ina" onclick="ocultadescargar(1)">
                                                        <label for="ina"> Comisiones inactivas</label>
                                                        <p></p>                                                                
                                                    </div>
                                                </div> 
                                            </div>  
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="comisionados" >                                                    
                                        </div><!--fin div -->

                                        <div id="vinieron">
                                            <div class="col-md5 bn-3 ">
                                            <label for="">REPORTE DE ASISTENCIAS</label><br><br>
                                                <div class="radios">
                                                    <p>Seleccione cómo desea buscar:</p>
                                                    <p></p>
                                                    <label for="rango-v"> Por rango de fechas
                                                    <input type="radio" name="opcion-v" value="rango-v" id="rango-v" onclick="ocultavinieron(1)"></label>
                                                    <p></p>
                                                    <label for="numero-v"> Por un número de empleado y rango de fechas
                                                    <input type="radio" name="opcion-v" value="numero-v" id="numero-v" onclick="ocultavinieron(2)"></label>
                                                    <p></p>
                                                    <label for="quincena-v"> Por quincena
                                                    <input type="radio" name="opcion-v" value="quincena-v" id="quincena-v" onclick="ocultavinieron(3)"></label>
                                                    <p></p>
                                                </div>
                                                <div class="form-1-2">
                                                    <div id=rangos-vinieron> 
                
                                                        <label for="">Fecha inicio</label>
                                                        <input type="date" class="form-control" name="f_ini-v">
                                                        <label for="">Fecha fin</label>
                                                        <input type="date" class="form-control" name="f_fin-v">
                                                    
                                                    </div>
                                                </div> 

                                                <div class="form-1-2" >
                                                    <div id=numero-vinieron>
                                                        <label for="">Ingrese un numéro de trabajador: </label>
                                                        <input type="text" class="form-control" name="num-v">
                                                        <label for="">Fecha inicio</label>
                                                        <input type="date" class="form-control" name="ini-v">
                                                        <label for="">Fecha fin</label>
                                                        <input type="date" class="form-control" name="fin-v">
                                                    </div>
                                                </div>
                                                <div class="form-1-2" >
                                                <div id="quincena-vinieron">
                                                    <label for="">Seleccione la quincena: </label>
                                                    <?php
                                                    $sql2="select idquincena,fecha_inicio,fecha_fin from quincena where idquincena<=$idactual";
                                                    $query2= mysqli_query($con, $sql2);
                                                    if(!$query2)
                                                    {
                                                        die("<br>" . "Error no hay datos en la tabla quincena, verifique con el administrador de sistemas. Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                                                    }
                                                    else
                                                    {   
                                                        echo "<select name='quincena3' class='form-control' >";
                                                        while($fila=mysqli_fetch_array($query2))
                                                        {   ///Divido la fecha de inicio y fin de la quincena
                                                            $f_ini=explode('-',$fila[1]);
                                                            $f_fin=explode('-',$fila[2]);
                                                            // Concateno el año actual a las fechas
                                                            $fila[1]=$anio_actual.'-'.$f_ini[1].'-'.$f_ini[2];
                                                            $fila[2]=$anio_actual.'-'.$f_fin[1].'-'.$f_fin[2];
                                                            
                                                            echo "<option value='". $fila[0] . " " .$fila[1]. " " .$fila[2]."'>Quincena ". $fila[0] . " " .$fila[1]. " al  " .$fila[2]."</option>";
    
                                                        }
                                                        echo "</select>";
                                                    }
                                                    ?> <!--FIN PHP -->
                                                    </div>    
                                                </div> 
                                            </div>  
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="vinieron" >                                                    
                                        </div><!--fin div-->

                                        <div id="faltaron">
                                            <div class="col-md5 bn-3 ">
                                                <div class="radios">
                                                    <label for="">REPORTE DE FALTAS</label><br><br>
                                                    <p>Seleccione cómo desea buscar:</p>
                                                    <p></p>
                                                    <label for="rango-f"> Por rango de fechas
                                                    <input type="radio" name="opcion-f" value="rango-f" id="rango-f" onclick="ocultafaltaron(1)"></label>
                                                    <p></p>
                                                    <label for="numero-f"> Por un número de empleado y rango de fechas
                                                    <input type="radio" name="opcion-f" value="numero-f" id="numero-f" onclick="ocultafaltaron(2)"></label>
                                                    <p></p>
                                                    <label for="numero-f"> Por quincena
                                                    <input type="radio" name="opcion-f" value="quincena-f" id="quincena-f" onclick="ocultafaltaron(3)"></label>
                                                    <p></p>
                                                </div>
                                                <div class="form-1-2">
                                                    <div id=rangos-faltaron> 
                
                                                        <label for="">Fecha inicio</label>
                                                        <input type="date" class="form-control" name="ini-f">
                                                        <label for="">Fecha fin</label>
                                                        <input type="date" class="form-control" name="fin-f">
                                                    
                                                    </div>
                                                </div> 

                                                <div class="form-1-2" >
                                                    <div id=numero-faltaron>
                                                        <label for="">Ingrese un numéro de trabajador: </label>
                                                        <input type="text" class="form-control" name="num-f">
                                                        <label for="">Fecha inicio</label>
                                                        <input type="date" class="form-control" name="ini-f">
                                                        <label for="">Fecha fin</label>
                                                        <input type="date" class="form-control" name="fin-f">
                                                    </div>
                                                </div>
                                                <div class="form-1-2" >
                                                <div id="quincena-faltaron">
                                                    <label for="">Seleccione la quincena: </label>
                                                    <?php
                                                    $sql2="select idquincena,fecha_inicio,fecha_fin from quincena where idquincena<=$idactual";
                                                    $query2= mysqli_query($con, $sql2);
                                                    if(!$query2)
                                                    {
                                                        die("<br>" . "Error no hay datos en la tabla quincena, verifique con el administrador de sistemas. Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                                                    }
                                                    else
                                                    {   
                                                        echo "<select name='quincena4' class='form-control' >";
                                                        while($fila=mysqli_fetch_array($query2))
                                                        {   ///Divido la fecha de inicio y fin de la quincena
                                                            $f_ini=explode('-',$fila[1]);
                                                            $f_fin=explode('-',$fila[2]);
                                                            // Concateno el año actual a las fechas
                                                            $fila[1]=$anio_actual.'-'.$f_ini[1].'-'.$f_ini[2];
                                                            $fila[2]=$anio_actual.'-'.$f_fin[1].'-'.$f_fin[2];
                                                            
                                                            echo "<option value='". $fila[0] . " " .$fila[1]. " " .$fila[2]."'>Quincena ". $fila[0] . " " .$fila[1]. " al  " .$fila[2]."</option>";
    
                                                        }
                                                        echo "</select>";
                                                    }
                                                    ?> <!--FIN PHP -->
                                                    </div>    
                                                </div> 
                                            </div>  
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="faltaron" >                                                    
                                        </div><!--fin div-->
                                    
                                        <div id="cumpleOno">
                                            <div class="col-md5 bn-3 ">
                                                <div class="radios">
                                                    <label for="">REPORTE DE CUMPLEAÑOS U ONOMÁSTICOS </label><br><br>
                                                    <label for="">Fecha inicio</label>
                                                    <input type="date" class="form-control" name="ini-c">
                                                    <label for="">Fecha fin</label>
                                                    <input type="date" class="form-control" name="fin-c">
                                                </div>
                                            </div> 
                                    
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="cumpleOno" >                                                    
                                        </div><!--fin div-->

                                        <div id="guardias">
                                            <div class="col-md5 bn-3 ">
                                                <div class="radios">
                                                    <label for="">REPORTE DE GUARDIAS </label><br><br>
                                                    <p>Seleccione cómo desea buscar:</p>
                                                    
                                                    <label for="rango-g"> Por rango de fechas
                                                    <input type="radio" name="opcion-g" value="rango-g" id="rango-g" onclick="ocultaguardias(1)">
                                                    </label> <p></p>
                                                    
                                                    <label for="numero-g"> Por un número de empleado y rango de fechas
                                                    <input type="radio" name="opcion-g" value="numero-g" id="numero-g" onclick="ocultaguardias(2)">
                                                    </label><p></p>
                                                    
                                                    <label for="quincena-g"> Por quincena
                                                    <input type="radio" name="opcion-g" value="quincena-g" id="quincena-g" onclick="ocultaguardias(3)">
                                                    </label><p></p>
                                                    
                                                </div>
                                                <div class="form-1-2">
                                                    <div id=rangos-guardias> 
                
                                                        <label for="">Fecha inicio</label>
                                                        <input type="date" class="form-control" name="ini-g">
                                                        <label for="">Fecha fin</label>
                                                        <input type="date" class="form-control" name="fin-g">
                                                    
                                                    </div>
                                                </div> 
                                                <div class="form-1-2" >
                                                    <div id=numero-guardias>
                                                        <label for="">Ingrese un numéro de trabajador: </label>
                                                        <input type="text" class="form-control" name="num-g">
                                                        <label for="">Fecha inicio</label>
                                                        <input type="date" class="form-control" name="in-g">
                                                        <label for="">Fecha fin</label>
                                                        <input type="date" class="form-control" name="fi-g">
                                                    </div>
                                                </div>
                                                <div class="form-1-2" >
                                                <div id="quincena-guardias">
                                                    <label for="">Seleccione la quincena: </label>
                                                    <?php
                                                        $sql2="select idquincena,fecha_inicio,fecha_fin from quincena where idquincena<=$idactual";
                                                        $query2= mysqli_query($con, $sql2);
                                                        if(!$query2)
                                                        {
                                                            die("<br>" . "Error no hay datos en la tabla quincena, verifique con el administrador de sistemas. Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                                                        }
                                                        else
                                                        {   
                                                            echo "<select name='quin-g' class='form-control' >";
                                                            while($fila=mysqli_fetch_array($query2))
                                                            {   ///Divido la fecha de inicio y fin de la quincena
                                                                $f_ini=explode('-',$fila[1]);
                                                                $f_fin=explode('-',$fila[2]);
                                                                // Concateno el año actual a las fechas
                                                                $fila[1]=$anio_actual.'-'.$f_ini[1].'-'.$f_ini[2];
                                                                $fila[2]=$anio_actual.'-'.$f_fin[1].'-'.$f_fin[2];
                                                                
                                                                echo "<option value='". $fila[0] . " " .$fila[1]. " " .$fila[2]."'>Quincena ". $fila[0] . " " .$fila[1]. " al  " .$fila[2]."</option>";
        
                                                            }
                                                            echo "</select>";
                                                        }
                                                    ?> <!--FIN PHP -->
                                                    </div>    
                                                </div> 
                                            </div>  
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="guardias" >                                                    
                                        </div><!--fin div-->

                                        <div id="sextas">
                                            <div class="col-md5 bn-3 ">
                                                <div class="radios">
                                                    <label for="">REPORTE DE SEXTAS</label><br><br>
                                                    <p>Seleccione qué desea buscar:</p>
                                                    <p></p>
                                                    <label for="todos-sexta">
                                                    <input type="radio" name="opcion-s" value="todos-sexta" id="todos-sexta" >
                                                    Todos los empleados con sexta</label>
                                                    <p></p>

                                                    <label for="viene-sexta">
                                                    <input type="radio" name="opcion-s" value="viene-sexta" id="viene-sexta" >
                                                    ¿Quién viene hoy y tiene sexta?</label>  
                                                    <p></p>
                                                </div>
                                            </div>  
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="sextas" >                                                    
                                        </div><!--fin div-->

                                        <div id="licencias">
                                            <div class="col-md5 bn-3 ">
                                                <div class="radios">
                                                <label for="">REPORTE DE LICENCIAS Y PERMISOS </label><br><br>
                                                <p>Seleccione una opción:</p>
                                                <p></p>
                                                <label for="noempiezan">
                                                <input type="radio" name="opcion-l" value="noempiezan" id="noempiezan" >
                                                Licencias y permisos que aún no empiezan </label>
                                                <p></p>
                                                <label for="xvencer">
                                                <input type="radio" name="opcion-l" value="xvencer" id="xvencer" >
                                                Licencias y permisos por vencer</label>
                                                <p></p>
                                                <label for="vencida">
                                                <input type="radio" name="opcion-l" value="vencida" id="vencida" >
                                                Licencias y permisos vencidos</label>
                                                <p></p>
                                                <label for="activa"> 
                                                <input type="radio" name="opcion-l" value="activa" id="activa" >
                                                Licencias y permisos activos</label>
                                                <p></p>
                                                </div>
                                            </div>  
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="licencias" >                                                    
                                        </div><!--fin div -->

                                        <div id="pases">
                                            <div class="col-md5 bn-3 ">
                                                <div class="radios">
                                                    <label for="">REPORTE DE PASES DE SALIDA </label><br><br>
                                                    <p>Seleccione una opción:</p>
                                                    <p></p>
                                                    <label for="hoy"> 
                                                    <input type="radio" name="opcion-p" value="hoy" id="hoy" >
                                                    Pases de salida de hoy </label>
                                                    <p></p>
                                                    <label for="vencido">
                                                    <input type="radio" name="opcion-p" value="vencido" id="vencido" >
                                                    Pases de salida vencidos</label>
                                                    <p></p>
                                                    <label for="antes"> 
                                                    <input type="radio" name="opcion-p" value="antes" id="antes" >
                                                    Pases de salida próximos </label>
                                                    <p></p>

                                                </div>
                                            </div>  
                                            <!--Sirve para enviar que reporte queremos-->
                                            <input type="hidden" name="id" value="pases" >                                                    
                                        </div><!--fin div -->
                                        
                                        <div class="modal-footer">
                                            <div id="boton-descargar">                       
                                                <button type="submit" id="descargar" class="btn btn-primary">Descargar</button>
                                            </div>  
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                        </div><!--fin modal-footer-->
                                    </div><!--fin form-row-->
                                </div>
                            </div> <!--fin modal -->
                        </form>
                    </div>
                </div>
            </div>
            <!-- Fin-Modal -->    

            <script type="text/javascript">
                $(document).ready(function() {
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
                    $('.select2').select2();
                });
            </script>
        </div>
        <!-- /#right-panel -->    
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
