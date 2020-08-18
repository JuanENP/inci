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
        <link rel="stylesheet" href="../assets/css/mdtimepicker.min.css"/>

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
        <script src="../assets/js/main.js"></script>
        <script src="../assets/js/main2.js"></script>
        <script src="../assets/js/alertify.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
        <script src="../assets/js/plugins.js"></script>
        <script src="../assets/js/jquery-ui.js"></script>
        <script src="../assets/js/mdtimepicker.min.js"></script>

        <script type="text/javascript">
            $( function() 
            {
                $(".datepicker").datepicker({
                    dateFormat: "yy-mm-dd",
                    changeMonth: true,
                    changeYear: true
                });

                $('.hour').mdtimepicker({
                    timeFormat:'hh:mm:ss',

                    format:'hh:mm tt',   

                    // 'red', 'purple', 'indigo', 'teal', 'green'
                    theme: 'blue',        

                    // determines if input is readonly
                    readOnly: true,       

                    // determines if display value has zero padding for hour value less than 10 (i.e. 05:30 PM); 24-hour format has padding by default
                    hourPadding: false     
                });
            });

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

                //
                $("#radio-justificacion").mouseover(function()
                {
                    $("#radio-justificacion").css("background-color", "rgb(202, 250, 240)");
                });

                $("#radio-justificacion").mouseleave(function()
                {
                    $("#radio-justificacion").css("background-color", "transparent");
                });

                $("#radio-justificacion").click(function()
                {
                    $("#jus-ret").prop("checked", true);
                    oculta(0);
                });
                //

                //
                $("#radio-omision").mouseover(function()
                {
                    $("#radio-omision").css("background-color", "rgb(202, 250, 240)");
                });

                $("#radio-omision").mouseleave(function()
                {
                    $("#radio-omision").css("background-color", "transparent");
                });
                $("#radio-omision").click(function()
                {
                    $("#jus-omi").prop("checked", true);
                    oculta(3);
                });
                //

                //
                $("#radio-falta").mouseover(function()
                {
                    $("#radio-falta").css("background-color", "rgb(202, 250, 240)");
                });

                $("#radio-falta").mouseleave(function()
                {
                    $("#radio-falta").css("background-color", "transparent");
                });
                $("#radio-falta").click(function()
                {
                    $("#jus-falt").prop("checked", true);
                    oculta(7);
                });
                //

                //
                $("#radio-comision").mouseover(function()
                {
                    $("#radio-comision").css("background-color", "rgb(202, 250, 240)");
                });

                $("#radio-comision").mouseleave(function()
                {
                    $("#radio-comision").css("background-color", "transparent");
                });
                $("#radio-comision").click(function()
                {
                    $("#comi").prop("checked", true);
                    oculta(1);
                    //Para el tipo de comision igual a un día
                    var cod = document.getElementById("ti-c").value;
                    //alert (cod);
                    if(cod=="co1")
                    {
                        document.getElementById('fecf').style.display = "none";
                        document.getElementById('he').style.display = "none";
                        document.getElementById('hs').style.display = "none";
                    }
                    else
                    {
                        if(cod=="cse")
                        {
                            document.getElementById('fecf').style.display = "block";
                            document.getElementById('he').style.display = "block";
                            document.getElementById('hs').style.display = "block";
                        }
                        else
                        {
                            if(cod=="com1")
                            {
                                document.getElementById('fecf').style.display = "none";
                                document.getElementById('he').style.display = "block";
                                document.getElementById('hs').style.display = "block";
                            }
                        }
                    }
                    //fin de comision igual a un dia
                });
                //

                //
                $("#radio-licencia").mouseover(function()
                {
                    $("#radio-licencia").css("background-color", "rgb(202, 250, 240)");
                });

                $("#radio-licencia").mouseleave(function()
                {
                    $("#radio-licencia").css("background-color", "transparent");
                });

                $("#radio-licencia").click(function()
                {
                    $("#lice").prop("checked", true);//seleccionar el radio de las licencias
                    oculta(2);
                    //las licencias
                    var cod = document.getElementById("Selectlicencias").value;
                    if(cod=="92") //Para la tolerancia de lactancia
                    {
                        $("#spanFechaInicial").text("");//cambiar el texto del span de la fecha inicial
                        document.getElementById('imagen').style.display = "none";
                        document.getElementById('fec').style.display = "none";
                        document.getElementById('fecf').style.display = "none";
                        document.getElementById('vacio_').style.display = "none";
                        document.getElementById('div-tol-est').style.display = "none";
                        document.getElementById('div-perm-go').style.display = "none";
                        document.getElementById('licenciaHastaUnAnio').style.display = "none";
                        document.getElementById('doct').style.display = "none";
                        document.getElementById('div-tol-lac').style.display = "block";
                    }
                    else
                    {
                        if(cod=="93")//Para la tolerancia de estancia
                        {
                            $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                            document.getElementById('vacio_').style.display = "none";
                            document.getElementById('div-tol-lac').style.display = "none";
                            document.getElementById('div-perm-go').style.display = "none";
                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                            document.getElementById('imagen').style.display = "none";
                            document.getElementById('doct').style.display = "none";
                            document.getElementById('fecf').style.display = "block";
                            document.getElementById('fec').style.display = "block";
                            document.getElementById('div-tol-est').style.display = "block";
                        }
                        else
                        {
                            if(cod=="41")//Para licencia goce antiguedad
                            {
                                $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                document.getElementById('div-tol-lac').style.display = "none";
                                document.getElementById('div-tol-est').style.display = "none";
                                document.getElementById('vacio_').style.display = "none";
                                document.getElementById('fecf').style.display = "none";
                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                document.getElementById('imagen').style.display = "none";
                                document.getElementById('doct').style.display = "none";
                                document.getElementById('fec').style.display = "block";
                                document.getElementById('div-perm-go').style.display = "block";

                                //ver que opción está seleccionada
                                var cod = document.getElementById("pergo").value;
                                if(cod=="1")
                                {
                                    
                                    document.getElementById('fecf').style.display = "block";
                                }
                                else
                                {
                                    if(cod=="2")
                                    {
                                        document.getElementById('fecf').style.display = "none";
                                    }
                                }
                            }
                            else
                            {
                                if(cod=="48" || cod=="49") //licencia matrimonio y fallecimiento
                                {
                                    $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                    document.getElementById('fecf').style.display = "none";
                                    document.getElementById('div-tol-lac').style.display = "none";
                                    document.getElementById('div-tol-est').style.display = "none";
                                    document.getElementById('vacio_').style.display = "none";
                                    document.getElementById('div-perm-go').style.display = "none";
                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                    document.getElementById('imagen').style.display = "none";
                                    document.getElementById('doct').style.display = "none";
                                    document.getElementById('fec').style.display = "block";
                                }
                                else
                                {
                                    if(cod=="LSG")//licencia sin goce
                                    {
                                        $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                        document.getElementById('div-tol-lac').style.display = "none";
                                        document.getElementById('div-tol-est').style.display = "none";
                                        document.getElementById('div-perm-go').style.display = "none";
                                        document.getElementById('doct').style.display = "none";
                                        document.getElementById('vacio_').style.display = "none";
                                        document.getElementById('fecf').style.display = "block";
                                        document.getElementById('fec').style.display = "block";
                                        document.getElementById('licenciaHastaUnAnio').style.display = "block";
                                        document.getElementById('imagen').style.display = "block";
                                    }
                                    else
                                    {
                                        if(cod=="LSGSS")//licencia sin goce para servicio social
                                        {
                                            $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                            document.getElementById('div-tol-lac').style.display = "none";
                                            document.getElementById('div-tol-est').style.display = "none";
                                            document.getElementById('div-perm-go').style.display = "none";
                                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                            document.getElementById('vacio_').style.display = "none";
                                            document.getElementById('doct').style.display = "none";
                                            document.getElementById('fecf').style.display = "block";
                                            document.getElementById('fec').style.display = "block";
                                            document.getElementById('imagen').style.display = "block";
                                        }
                                        else
                                        {
                                            if(cod=="53" || cod=="54" || cod=="47" || cod=="51")
                                            {
                                                $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                                document.getElementById('div-tol-lac').style.display = "none";
                                                document.getElementById('div-tol-est').style.display = "none";
                                                document.getElementById('div-perm-go').style.display = "none";
                                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                document.getElementById('vacio_').style.display = "none";
                                                document.getElementById('doct').style.display = "none";
                                                document.getElementById('imagen').style.display = "block";
                                                document.getElementById('fecf').style.display = "block";
                                                document.getElementById('fec').style.display = "block";
                                            }
                                            else//todas las demas
                                            {
                                                if(cod=="55" || cod=="62")
                                                {
                                                    $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                                    document.getElementById('div-tol-lac').style.display = "none";
                                                    document.getElementById('div-tol-est').style.display = "none";
                                                    document.getElementById('div-perm-go').style.display = "none";
                                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                    document.getElementById('vacio_').style.display = "none";
                                                    document.getElementById('fecf').style.display = "block";
                                                    document.getElementById('fec').style.display = "block";
                                                    document.getElementById('doct').style.display = "block";
                                                    document.getElementById('imagen').style.display = "block";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //Fin licencias

                    //licencia 41 fuerza mayor o no
                    var cod = document.getElementById("pergo").value;
                    if(cod=="1")
                    {
                        document.getElementById('fecf').style.display = "block";
                    }
                    else
                    {
                        if(cod=="2")
                        {
                            document.getElementById('fecf').style.display = "none";
                        }
                    }
                    //Fin fuerza mayor o no
                });
                //

                //
                $("#radio-permiso").mouseover(function()
                {
                    $("#radio-permiso").css("background-color", "rgb(202, 250, 240)");
                });

                $("#radio-permiso").mouseleave(function()
                {
                    $("#radio-permiso").css("background-color", "transparent");
                });
                $("#radio-permiso").click(function()
                {
                    $("#perm").prop("checked", true);
                    oculta(4);
                });
                //

                //
                $("#radio-guardia").mouseover(function()
                {
                    $("#radio-guardia").css("background-color", "rgb(202, 250, 240)");
                });

                $("#radio-guardia").mouseleave(function()
                {
                    $("#radio-guardia").css("background-color", "transparent");
                });
                $("#radio-guardia").click(function()
                {
                    $("#guard").prop("checked", true);
                    oculta(5);
                });
                //

                //
                $("#radio-pt").mouseover(function()
                {
                    $("#radio-pt").css("background-color", "rgb(202, 250, 240)");
                });

                $("#radio-pt").mouseleave(function()
                {
                    $("#radio-pt").css("background-color", "transparent");
                });
                $("#radio-pt").click(function()
                {
                    $("#pati").prop("checked", true);
                    oculta(6);
                });
                //

                //
                $("#radio-curso").mouseover(function()
                {
                    $("#radio-curso").css("background-color", "rgb(202, 250, 240)");
                });

                $("#radio-curso").mouseleave(function()
                {
                    $("#radio-curso").css("background-color", "transparent");
                });
                $("#radio-curso").click(function()
                {
                    $("#cur").prop("checked", true);
                    oculta(9);
                    //curso capacitacion opcion
                    var cod = document.getElementById("cucap").value;
                    if(cod=="o")
                    {
                        document.getElementById('he').style.display = "none";
                        document.getElementById('hs').style.display = "none";
                    }
                    else
                    {
                        if(cod=="d")
                        {
                            document.getElementById('he').style.display = "block";
                            document.getElementById('hs').style.display = "block";
                        }
                    }
                });

                //
                $("#radio-otros").mouseover(function()
                {
                    $("#radio-otros").css("background-color", "rgb(202, 250, 240)");
                });

                $("#radio-otros").mouseleave(function()
                {
                    $("#radio-otros").css("background-color", "transparent");
                });

                $("#radio-otros").click(function()
                {
                    $("#otr").prop("checked", true);
                    oculta(10);
                });
                //

                $("#cucap").click(function()
                {
                    var cod = document.getElementById("cucap").value;
                    if(cod=="o")
                    {
                        document.getElementById('he').style.display = "none";
                        document.getElementById('hs').style.display = "none";
                    }
                    if(cod=="d")
                    {
                        document.getElementById('he').style.display = "block";
                        document.getElementById('hs').style.display = "block";
                    }
                });

                //Para el tipo de comision igual a un dia
                $("#ti-c").click(function()
                {
                    var cod = document.getElementById("ti-c").value;
                    if(cod=="co1")
                    {
                        document.getElementById('fecf').style.display = "none";
                        document.getElementById('he').style.display = "none";
                        document.getElementById('hs').style.display = "none";
                    }
                    else
                    {
                        if(cod=="csi" || cod=="cse")
                        {
                            document.getElementById('fecf').style.display = "block";
                            document.getElementById('he').style.display = "block";
                            document.getElementById('hs').style.display = "block";
                        }
                        else
                        {
                            if(cod=="com1")
                            {
                                document.getElementById('fecf').style.display = "none";
                                document.getElementById('he').style.display = "block";
                                document.getElementById('hs').style.display = "block";
                            }
                        }
                    }
                });

                
                $("#Selectlicencias").click(function()
                {
                    document.getElementById("myfile").value = "";
                    var cod = document.getElementById("Selectlicencias").value;
                    if(cod=="92") //Para la tolerancia de lactancia
                    {
                        $("#spanFechaInicial").text("");//cambiar el texto del span de la fecha inicial
                        document.getElementById('imagen').style.display = "none";
                        document.getElementById('fec').style.display = "none";
                        document.getElementById('fecf').style.display = "none";
                        document.getElementById('vacio_').style.display = "none";
                        document.getElementById('div-tol-est').style.display = "none";
                        document.getElementById('div-perm-go').style.display = "none";
                        document.getElementById('doct').style.display = "none";
                        document.getElementById('licenciaHastaUnAnio').style.display = "none";
                        document.getElementById('div-tol-lac').style.display = "block";
                    }
                    else
                    {
                        if(cod=="93")//Para la tolerancia de estancia
                        {
                            $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                            document.getElementById('vacio_').style.display = "none";
                            document.getElementById('div-tol-lac').style.display = "none";
                            document.getElementById('div-perm-go').style.display = "none";
                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                            document.getElementById('imagen').style.display = "none";
                            document.getElementById('doct').style.display = "none";
                            document.getElementById('fecf').style.display = "block";
                            document.getElementById('fec').style.display = "block";
                            document.getElementById('div-tol-est').style.display = "block";
                        }
                        else
                        {
                            if(cod=="41")//Para licencia goce antiguedad
                            {
                                $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                document.getElementById('div-tol-lac').style.display = "none";
                                document.getElementById('div-tol-est').style.display = "none";
                                document.getElementById('vacio_').style.display = "none";
                                document.getElementById('fecf').style.display = "none";
                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                document.getElementById('imagen').style.display = "none";
                                document.getElementById('doct').style.display = "none";
                                document.getElementById('fec').style.display = "block";
                                document.getElementById('div-perm-go').style.display = "block";

                                //ver que opción está seleccionada
                                var cod = document.getElementById("pergo").value;
                                if(cod=="1")
                                {
                                    
                                    document.getElementById('fecf').style.display = "block";
                                }
                                else
                                {
                                    if(cod=="2")
                                    {
                                        document.getElementById('fecf').style.display = "none";
                                    }
                                }
                            }
                            else
                            {
                                if(cod=="48" || cod=="49") //licencia matrimonio y fallecimiento
                                {
                                    $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                    document.getElementById('fecf').style.display = "none";
                                    document.getElementById('div-tol-lac').style.display = "none";
                                    document.getElementById('div-tol-est').style.display = "none";
                                    document.getElementById('vacio_').style.display = "none";
                                    document.getElementById('div-perm-go').style.display = "none";
                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                    document.getElementById('imagen').style.display = "none";
                                    document.getElementById('doct').style.display = "none";
                                    document.getElementById('fec').style.display = "block";
                                }
                                else
                                {
                                    if(cod=="LSG")//licencia sin goce
                                    {
                                        $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                        document.getElementById('div-tol-lac').style.display = "none";
                                        document.getElementById('div-tol-est').style.display = "none";
                                        document.getElementById('div-perm-go').style.display = "none";
                                        document.getElementById('vacio_').style.display = "none";
                                        document.getElementById('doct').style.display = "none";
                                        document.getElementById('fecf').style.display = "block";
                                        document.getElementById('fec').style.display = "block";
                                        document.getElementById('licenciaHastaUnAnio').style.display = "block";
                                        document.getElementById('imagen').style.display = "block";
                                    }
                                    else
                                    {
                                        if(cod=="LSGSS")//licencia sin goce para servicio social
                                        {
                                            $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                            document.getElementById('div-tol-lac').style.display = "none";
                                            document.getElementById('div-tol-est').style.display = "none";
                                            document.getElementById('div-perm-go').style.display = "none";
                                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                            document.getElementById('vacio_').style.display = "none";
                                            document.getElementById('doct').style.display = "none";
                                            document.getElementById('fecf').style.display = "block";
                                            document.getElementById('fec').style.display = "block";
                                            document.getElementById('imagen').style.display = "block";
                                        }
                                        else
                                        {
                                            if(cod=="53" || cod=="54" || cod=="47" || cod=="51")
                                            {
                                                $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                                document.getElementById('div-tol-lac').style.display = "none";
                                                document.getElementById('div-tol-est').style.display = "none";
                                                document.getElementById('div-perm-go').style.display = "none";
                                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                document.getElementById('vacio_').style.display = "none";
                                                document.getElementById('doct').style.display = "none";
                                                document.getElementById('imagen').style.display = "block";
                                                document.getElementById('fecf').style.display = "block";
                                                document.getElementById('fec').style.display = "block";
                                            }
                                            else//todas las demas
                                            {
                                                if(cod=="55" || cod=="62")
                                                {
                                                    $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                                    document.getElementById('div-tol-lac').style.display = "none";
                                                    document.getElementById('div-tol-est').style.display = "none";
                                                    document.getElementById('div-perm-go').style.display = "none";
                                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                    document.getElementById('vacio_').style.display = "none";
                                                    document.getElementById('fecf').style.display = "block";
                                                    document.getElementById('fec').style.display = "block";
                                                    document.getElementById('doct').style.display = "block";
                                                    document.getElementById('imagen').style.display = "block";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                });

                //para licencias 41 fuerza mayor o no
                $("#pergo").click(function()
                {
                    var cod = document.getElementById("pergo").value;
                    if(cod=="1")
                    {
                        
                        document.getElementById('fecf').style.display = "block";
                    }
                    else
                    {
                        if(cod=="2")
                        {
                            document.getElementById('fecf').style.display = "none";
                        }
                    }
                });

            });
        </script>  

        <script>
            function oculta(x) {
                if (x == 0) 
                {
                    $("#MainContent_lbTrabajador").text("Trabajador");//cambiar el texto del span del trabajador
                    $("#spanFechaInicial").text("Fecha del retardo");//cambiar el texto del span de la fecha inicial
                    document.getElementById('tipo-com').style.display = "none";
                    document.getElementById('fecf').style.display = "none";
                    document.getElementById('he').style.display = "none";
                    document.getElementById('hs').style.display = "none";
                    document.getElementById('empresa').style.display = "none";
                    document.getElementById('clavelicencia').style.display = "none";
                    document.getElementById('prioridad').style.display = "none";
                    document.getElementById('div-curso1').style.display = "none";
                    document.getElementById('div-tol-lac').style.display = "none";
                    document.getElementById('div-tol-est').style.display = "none";
                    document.getElementById('div-perm-go').style.display = "none";
                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                    document.getElementById('suplente').style.display = "none";
                    document.getElementById('imagen').style.display = "none";
                    document.getElementById('div-otro').style.display = "none";
                    document.getElementById('doct').style.display = "none";
                    document.getElementById('fec').style.display = "block";
                } 
                else 
                {
                    if(x==1)
                    {
                        document.getElementById("myfile").value = "";
                        $("#MainContent_lbTrabajador").text("Trabajador");//cambiar el texto del span del trabajador
                        $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                        document.getElementById('clavelicencia').style.display = "none";
                        document.getElementById('vacio_').style.display = "none";
                        document.getElementById('div-curso1').style.display = "none";
                        document.getElementById('div-tol-lac').style.display = "none";
                        document.getElementById('div-tol-est').style.display = "none";
                        document.getElementById('div-perm-go').style.display = "none";
                        document.getElementById('licenciaHastaUnAnio').style.display = "none";
                        document.getElementById('suplente').style.display = "none";
                        document.getElementById('imagen').style.display = "none";
                        document.getElementById('div-otro').style.display = "none";
                        document.getElementById('doct').style.display = "none";
                        document.getElementById('tipo-com').style.display = "block";
                        document.getElementById('fecf').style.display = "block";
                        document.getElementById('he').style.display = "block";
                        document.getElementById('hs').style.display = "block";
                        document.getElementById('empresa').style.display = "block";
                        document.getElementById('prioridad').style.display = "block";
                        document.getElementById('fec').style.display = "block";
                        $("#p_NA").val("n");//cambiar el option de la prioridad de la comisión a normal
                    }
                    else
                    {
                        if (x == 2) 
                        {
                            document.getElementById("myfile").value = "";
                            $("#MainContent_lbTrabajador").text("Trabajador");//cambiar el texto del span del trabajador
                            $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                            document.getElementById('tipo-com').style.display = "none";
                            document.getElementById('he').style.display = "none";
                            document.getElementById('hs').style.display = "none";
                            document.getElementById('empresa').style.display = "none";
                            document.getElementById('prioridad').style.display = "none";
                            document.getElementById('div-curso1').style.display = "none";
                            document.getElementById('div-tol-lac').style.display = "none";
                            document.getElementById('div-tol-est').style.display = "none";
                            document.getElementById('div-perm-go').style.display = "none";
                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                            document.getElementById('suplente').style.display = "none";
                            document.getElementById('imagen').style.display = "none";
                            document.getElementById('div-otro').style.display = "none";
                            document.getElementById('doct').style.display = "none";
                            document.getElementById('clavelicencia').style.display = "block";
                            document.getElementById('fecf').style.display = "block";
                            document.getElementById('fec').style.display = "block";
                            document.getElementById('vacio_').style.display = "block";
                        } 
                        else
                        {
                            if (x == 3) 
                            {
                                $("#MainContent_lbTrabajador").text("Trabajador");//cambiar el texto del span del trabajador
                                $("#spanFechaInicial").text("Fecha de la omisión");//cambiar el texto del span de la fecha inicial
                                document.getElementById('tipo-com').style.display = "none";
                                document.getElementById('fecf').style.display = "none";
                                document.getElementById('he').style.display = "none";
                                document.getElementById('hs').style.display = "none";
                                document.getElementById('empresa').style.display = "none";
                                document.getElementById('clavelicencia').style.display = "none";
                                document.getElementById('prioridad').style.display = "none";
                                document.getElementById('div-curso1').style.display = "none";
                                document.getElementById('div-tol-lac').style.display = "none";
                                document.getElementById('div-tol-est').style.display = "none";
                                document.getElementById('div-perm-go').style.display = "none";
                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                document.getElementById('suplente').style.display = "none";
                                document.getElementById('imagen').style.display = "none";
                                document.getElementById('div-otro').style.display = "none";
                                document.getElementById('doct').style.display = "none";
                                document.getElementById('fec').style.display = "block";
                            }

                            else
                            {
                                if(x == 4)
                                {
                                    $("#MainContent_lbTrabajador").text("Trabajador");//cambiar el texto del span del trabajador
                                    $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                    document.getElementById('tipo-com').style.display = "none";
                                    document.getElementById('he').style.display = "none";
                                    document.getElementById('hs').style.display = "none";
                                    document.getElementById('empresa').style.display = "none";
                                    document.getElementById('clavelicencia').style.display = "none";
                                    document.getElementById('prioridad').style.display = "none";
                                    document.getElementById('div-curso1').style.display = "none";
                                    document.getElementById('div-tol-lac').style.display = "none";
                                    document.getElementById('div-tol-est').style.display = "none";
                                    document.getElementById('div-perm-go').style.display = "none";
                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                    document.getElementById('suplente').style.display = "none";
                                    document.getElementById('div-otro').style.display = "none";
                                    document.getElementById('doct').style.display = "none";
                                    document.getElementById('imagen').style.display = "block";
                                    document.getElementById('fecf').style.display = "block";
                                    document.getElementById('fec').style.display = "block";
                                }
                                else
                                {
                                    if(x == 5)
                                    {
                                        document.getElementById('tipo-com').style.display = "none";
                                        document.getElementById('he').style.display = "none";
                                        document.getElementById('hs').style.display = "none";
                                        document.getElementById('empresa').style.display = "none";
                                        document.getElementById('clavelicencia').style.display = "none";
                                        document.getElementById('prioridad').style.display = "none";
                                        document.getElementById('div-curso1').style.display = "none";
                                        document.getElementById('div-tol-lac').style.display = "none";
                                        document.getElementById('div-tol-est').style.display = "none";
                                        document.getElementById('div-perm-go').style.display = "none";
                                        document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                        document.getElementById('fecf').style.display = "none";
                                        document.getElementById('imagen').style.display = "none";
                                        document.getElementById('div-otro').style.display = "none";
                                        document.getElementById('doct').style.display = "none";
                                        document.getElementById('suplente').style.display = "block";
                                        document.getElementById('fec').style.display = "block";

                                        $("#MainContent_lbTrabajador").text("Trabajador solicitante");//cambiar el texto del span del trabajador
                                        $("#spanFechaInicial").text("Fecha de la guardia");//cambiar el texto del span de la fecha inicial
                                    }
                                    else
                                    {
                                        if (x == 6) 
                                        {
                                            $("#MainContent_lbTrabajador").text("Trabajador");//cambiar el texto del span del trabajador
                                            $("#spanFechaInicial").text("*La fecha siempre será la actual*");//cambiar el texto del span de la fecha inicial
                                            document.getElementById('fec').style.display = "none";
                                            document.getElementById('tipo-com').style.display = "none";
                                            document.getElementById('fecf').style.display = "none";
                                            document.getElementById('he').style.display = "none";
                                            document.getElementById('hs').style.display = "none";
                                            document.getElementById('empresa').style.display = "none";
                                            document.getElementById('clavelicencia').style.display = "none";
                                            document.getElementById('prioridad').style.display = "none";
                                            document.getElementById('vacio_').style.display = "none";
                                            document.getElementById('div-tol-lac').style.display = "none";
                                            document.getElementById('div-tol-est').style.display = "none";
                                            document.getElementById('div-perm-go').style.display = "none";
                                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                            document.getElementById('suplente').style.display = "none";
                                            document.getElementById('div-curso1').style.display = "none";
                                            document.getElementById('imagen').style.display = "none";
                                            document.getElementById('div-otro').style.display = "none";
                                            document.getElementById('doct').style.display = "none";
                                        }
                                        else
                                        {
                                            if(x == 7)
                                            {
                                                document.getElementById("myfile").value = "";
                                                $("#MainContent_lbTrabajador").text("Trabajador");//cambiar el texto del span del trabajador
                                                $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                                document.getElementById('tipo-com').style.display = "none";
                                                document.getElementById('he').style.display = "none";
                                                document.getElementById('hs').style.display = "none";
                                                document.getElementById('empresa').style.display = "none";
                                                document.getElementById('clavelicencia').style.display = "none";
                                                document.getElementById('prioridad').style.display = "none";
                                                document.getElementById('vacio_').style.display = "none";
                                                document.getElementById('div-tol-lac').style.display = "none";
                                                document.getElementById('div-tol-est').style.display = "none";
                                                document.getElementById('div-perm-go').style.display = "none";
                                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                document.getElementById('suplente').style.display = "none";
                                                document.getElementById('div-curso1').style.display = "none";
                                                document.getElementById('fecf').style.display = "none";
                                                document.getElementById('div-otro').style.display = "none";
                                                document.getElementById('doct').style.display = "none";
                                                document.getElementById('imagen').style.display = "block";
                                                document.getElementById('fec').style.display = "block";
                                            }
                                            else
                                            {
                                                if (x == 9) 
                                                {
                                                    document.getElementById("myfile").value = "";
                                                    $("#MainContent_lbTrabajador").text("Trabajador");//cambiar el texto del span del trabajador
                                                    $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                                    document.getElementById('tipo-com').style.display = "none";
                                                    document.getElementById('he').style.display = "none";
                                                    document.getElementById('hs').style.display = "none";
                                                    document.getElementById('empresa').style.display = "none";
                                                    document.getElementById('clavelicencia').style.display = "none";
                                                    document.getElementById('prioridad').style.display = "none";
                                                    document.getElementById('vacio_').style.display = "none";
                                                    document.getElementById('div-tol-lac').style.display = "none";
                                                    document.getElementById('div-tol-est').style.display = "none";
                                                    document.getElementById('div-perm-go').style.display = "none";
                                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                    document.getElementById('suplente').style.display = "none";
                                                    document.getElementById('div-otro').style.display = "none";
                                                    document.getElementById('doct').style.display = "none";
                                                    document.getElementById('imagen').style.display = "block";
                                                    document.getElementById('fecf').style.display = "block";
                                                    document.getElementById('div-curso1').style.display = "block";
                                                    document.getElementById('fec').style.display = "block";
                                                }
                                                else
                                                {
                                                    if (x == 10) 
                                                    {
                                                        document.getElementById("myfile").value = "";
                                                        $("#MainContent_lbTrabajador").text("Trabajador");//cambiar el texto del span del trabajador
                                                        $("#spanFechaInicial").text("*La fecha y quincena serán las actuales*");//cambiar el texto del span de la fecha inicial
                                                        document.getElementById('tipo-com').style.display = "none";
                                                        document.getElementById('he').style.display = "none";
                                                        document.getElementById('hs').style.display = "none";
                                                        document.getElementById('empresa').style.display = "none";
                                                        document.getElementById('clavelicencia').style.display = "none";
                                                        document.getElementById('prioridad').style.display = "none";
                                                        document.getElementById('vacio_').style.display = "none";
                                                        document.getElementById('div-tol-lac').style.display = "none";
                                                        document.getElementById('div-tol-est').style.display = "none";
                                                        document.getElementById('div-perm-go').style.display = "none";
                                                        document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                        document.getElementById('suplente').style.display = "none";
                                                        document.getElementById('div-curso1').style.display = "none";
                                                        document.getElementById('imagen').style.display = "none";
                                                        document.getElementById('fecf').style.display = "none";
                                                        document.getElementById('fec').style.display = "none";
                                                        document.getElementById('doct').style.display = "none";
                                                        document.getElementById('div-otro').style.display = "block";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            function seguro()
            {
                /* Para obtener el valor de la prioridad de la comisión*/
                var cod = document.getElementById("p_NA").value;
                if(cod=="a")
                {
                    alertify.confirm("ATENCIÓN. ESTA OPCIÓN ES SOLO EN CASOS DE *EXTREMA PRIORIDAD*. ESTA OPERACIÓN SERÁ REGISTRADA EN LA BITÁCORA CON SU NOMBRE DE USUARIO. GUARDE EL DOCUMENTO QUE COMPRUEBE ESTA PRIORIDAD ALTA PARA POSIBLES ACLARACIONES FUTURAS.", function(e)
                    {
                        if(e)
                        {
                            alertify.alert("¡Condición Aceptada!");
                            document.getElementById('imagen').style.display = "block";

                        }
                        else
                        {    
                            $(document).ready(function()
                            {
                                alertify.alert("Prioridad Normal");
                                $("#p_NA").val("n");//cambiar el option a normal
                                document.getElementById('imagen').style.display = "none";
                            });
                        }
                    });
                }
                else
                {
                    if(cod=="n")
                    {
                        document.getElementById('imagen').style.display = "none";
                    }
                }
            }

            //Para licencia sin goce para incapacidad médica
            function unAnio()
            {
                if( $("#li1Anio").prop('checked') ) 
                {
                    alertify.confirm("ATENCIÓN. Esta opción está basada en el artículo 37 de la Ley del ISSSTE (ENFERMEDAD NO PROFESIONAL)"+
                        " y es exclusivamente para el caso siguiente: Si al vencer la licencia con medio sueldo continúa la imposibilidad del Trabajador para desempeñar su "+
                        "labor, se concederá al Trabajador licencia sin goce de sueldo mientras dure la incapacidad, HASTA POR "+
                        "CINCUENTA Y DOS SEMANAS contadas desde que se inició ésta, o a partir de que se expida la primera "+
                        "licencia médica. Este opción deberá ser marcada SOLO EN EL CASO DESCRITO.", function(e)
                        {
                            if(e)
                            {
                                alertify.alert("¡Condición Aceptada!");
                            }
                            else
                            {    
                                $(document).ready(function()
                                {
                                    alertify.alert("OPCIÓN DESMARCADA");
                                    $("#li1Anio").prop("checked", false); //desmarcar el checkbox
                                });
                            }
                        });
                }    
            }//Fin de unAnio

            function inicio()
            {
                $(document).ready(function()
                {
                    var rad = $("input[name='opcion']:checked").val();
                    if(rad=="justificar")
                    {
                        oculta(0);
                    }
                    if(rad=="omision")
                    {
                        oculta(3);
                    }
                    if(rad=="falta")
                    {
                        oculta(7);
                    }
                    if(rad=="comision")
                    {
                        oculta(1);
                        //Para el tipo de comision igual a un día
                        var cod = document.getElementById("ti-c").value;
                        //alert (cod);
                        if(cod=="co1")
                        {
                            document.getElementById('fecf').style.display = "none";
                            document.getElementById('he').style.display = "none";
                            document.getElementById('hs').style.display = "none";
                        }
                        else
                        {
                            if(cod=="cse")
                            {
                                document.getElementById('fecf').style.display = "block";
                                document.getElementById('he').style.display = "block";
                                document.getElementById('hs').style.display = "block";
                            }
                            else
                            {
                                if(cod=="com1")
                                {
                                    document.getElementById('fecf').style.display = "none";
                                    document.getElementById('he').style.display = "block";
                                    document.getElementById('hs').style.display = "block";
                                }
                            }
                        }
                        //fin de comision igual a un dia
                    }
                    if(rad=="licencia")
                    {
                        oculta(2);
                        //las licencias
                        var cod = document.getElementById("Selectlicencias").value;
                        if(cod=="92") //Para la tolerancia de lactancia
                        {
                            $("#spanFechaInicial").text("");//cambiar el texto del span de la fecha inicial
                            document.getElementById('imagen').style.display = "none";
                            document.getElementById('fec').style.display = "none";
                            document.getElementById('fecf').style.display = "none";
                            document.getElementById('vacio_').style.display = "none";
                            document.getElementById('div-tol-est').style.display = "none";
                            document.getElementById('div-perm-go').style.display = "none";
                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                            document.getElementById('doct').style.display = "none";
                            document.getElementById('div-tol-lac').style.display = "block";
                        }
                        else
                        {
                            if(cod=="93")//Para la tolerancia de estancia
                            {
                                $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                document.getElementById('vacio_').style.display = "none";
                                document.getElementById('div-tol-lac').style.display = "none";
                                document.getElementById('div-perm-go').style.display = "none";
                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                document.getElementById('imagen').style.display = "none";
                                document.getElementById('doct').style.display = "none";
                                document.getElementById('fecf').style.display = "block";
                                document.getElementById('fec').style.display = "block";
                                document.getElementById('div-tol-est').style.display = "block";
                            }
                            else
                            {
                                if(cod=="41")//Para licencia goce antiguedad
                                {
                                    $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                    document.getElementById('div-tol-lac').style.display = "none";
                                    document.getElementById('div-tol-est').style.display = "none";
                                    document.getElementById('vacio_').style.display = "none";
                                    document.getElementById('fecf').style.display = "none";
                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                    document.getElementById('imagen').style.display = "none";
                                    document.getElementById('doct').style.display = "none";
                                    document.getElementById('fec').style.display = "block";
                                    document.getElementById('div-perm-go').style.display = "block";

                                    //ver que opción está seleccionada
                                    var cod = document.getElementById("pergo").value;
                                    if(cod=="1")
                                    {
                                        
                                        document.getElementById('fecf').style.display = "block";
                                    }
                                    else
                                    {
                                        if(cod=="2")
                                        {
                                            document.getElementById('fecf').style.display = "none";
                                        }
                                    }
                                }
                                else
                                {
                                    if(cod=="48" || cod=="49") //licencia matrimonio y fallecimiento
                                    {
                                        $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                        document.getElementById('fecf').style.display = "none";
                                        document.getElementById('div-tol-lac').style.display = "none";
                                        document.getElementById('div-tol-est').style.display = "none";
                                        document.getElementById('vacio_').style.display = "none";
                                        document.getElementById('div-perm-go').style.display = "none";
                                        document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                        document.getElementById('imagen').style.display = "none";
                                        document.getElementById('doct').style.display = "none";
                                        document.getElementById('fec').style.display = "block";
                                    }
                                    else
                                    {
                                        if(cod=="LSG")//licencia sin goce
                                        {
                                            $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                            document.getElementById('div-tol-lac').style.display = "none";
                                            document.getElementById('div-tol-est').style.display = "none";
                                            document.getElementById('div-perm-go').style.display = "none";
                                            document.getElementById('vacio_').style.display = "none";
                                            document.getElementById('doct').style.display = "none";
                                            document.getElementById('fecf').style.display = "block";
                                            document.getElementById('fec').style.display = "block";
                                            document.getElementById('licenciaHastaUnAnio').style.display = "block";
                                            document.getElementById('imagen').style.display = "block";
                                        }
                                        else
                                        {
                                            if(cod=="LSGSS")//licencia sin goce para servicio social
                                            {
                                                $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                                document.getElementById('div-tol-lac').style.display = "none";
                                                document.getElementById('div-tol-est').style.display = "none";
                                                document.getElementById('div-perm-go').style.display = "none";
                                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                document.getElementById('vacio_').style.display = "none";
                                                document.getElementById('doct').style.display = "none";
                                                document.getElementById('fecf').style.display = "block";
                                                document.getElementById('fec').style.display = "block";
                                                document.getElementById('imagen').style.display = "block";
                                            }
                                            else
                                            {
                                                if(cod=="53" || cod=="54" || cod=="47" || cod=="51")
                                                {
                                                    $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                                    document.getElementById('div-tol-lac').style.display = "none";
                                                    document.getElementById('div-tol-est').style.display = "none";
                                                    document.getElementById('div-perm-go').style.display = "none";
                                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                    document.getElementById('vacio_').style.display = "none";
                                                    document.getElementById('doct').style.display = "none";
                                                    document.getElementById('imagen').style.display = "block";
                                                    document.getElementById('fecf').style.display = "block";
                                                    document.getElementById('fec').style.display = "block";
                                                }
                                                else//todas las demas
                                                {
                                                    if(cod=="55" || cod=="62")
                                                    {
                                                        $("#spanFechaInicial").text("Fecha Inicial");//cambiar el texto del span de la fecha inicial
                                                        document.getElementById('div-tol-lac').style.display = "none";
                                                        document.getElementById('div-tol-est').style.display = "none";
                                                        document.getElementById('div-perm-go').style.display = "none";
                                                        document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                        document.getElementById('vacio_').style.display = "none";
                                                        document.getElementById('fecf').style.display = "block";
                                                        document.getElementById('fec').style.display = "block";
                                                        document.getElementById('doct').style.display = "block";
                                                        document.getElementById('imagen').style.display = "block";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //Fin licencias

                        //licencia 41 fuerza mayor o no
                        var cod = document.getElementById("pergo").value;
                        if(cod=="1")
                        {
                            document.getElementById('fecf').style.display = "block";
                        }
                        else
                        {
                            if(cod=="2")
                            {
                                document.getElementById('fecf').style.display = "none";
                            }
                        }
                        //Fin fuerza mayor o no

                    }//FIN RADIO LICENCIA

                    if(rad=="permiso")
                    {
                        oculta(4);
                    }

                    if(rad=="guardia")
                    {
                        oculta(5);
                    }

                    if(rad=="pt")
                    {
                        oculta(6);
                    }

                    if(rad=="curso")
                    {
                        oculta(9);

                        //curso capacitacion opcion
                        var cod = document.getElementById("cucap").value;
                        if(cod=="o")
                        {
                            document.getElementById('he').style.display = "none";
                            document.getElementById('hs').style.display = "none";
                        }
                        else
                        {
                            if(cod=="d")
                            {
                                document.getElementById('he').style.display = "block";
                                document.getElementById('hs').style.display = "block";
                            }
                        }
                    }

                    if(rad=="otros")
                    {
                        oculta(10);
                    }
                });
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

            <form id="f1" method="POST" action="../php/insert/aprobacion.php" enctype="multipart/form-data">
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
                                            <div class="form-group col-lg-4">

                                                <div class="form-1-2" id="radio-justificacion">
                                                    <input type="radio" name="opcion" value="justificar" id="jus-ret" onclick="oculta(0)" checked> <label>Justificar retardo</label> 
                                                </div> 
                                                <div class="form-1-2" id="radio-omision">
                                                    <!--<label for="caja_busqueda" id="MainContent_lbTrabajador">Buscar:</label>-->
                                                    <input type="radio" name="opcion" value="omision" id="jus-omi" onclick="oculta(3)"> <label> Justificar omisión</label>
                                                </div>
                                                <div class="form-1-2" id="radio-falta">
                                                    <!--<label for="caja_busqueda" id="MainContent_lbTrabajador">Buscar:</label>-->
                                                    <input type="radio" name="opcion" value="falta" id="jus-falt" onclick="oculta(7)"> <label> Justificar falta</label>
                                                </div>
                                                <div class="form-1-2" id="radio-comision">
                                                    <input type="radio" name="opcion" value="comision" id="comi" onclick="oculta(1)"> <label> Comisiones</label>
                                                </div>

                                                <div class="form-1-2" id="radio-licencia">
                                                    <input type="radio" name="opcion" value="licencia" id="lice" onclick="oculta(2)"> <label> Licencias</label>
                                                </div>

                                                <div class="form-1-2" id="radio-permiso">
                                                    <input type="radio" name="opcion" value="permiso" id="perm" onclick="oculta(4)"> <label> Permiso 1-3 días</label>
                                                </div>

                                                <div class="form-1-2" id="radio-guardia">
                                                    <input type="radio" name="opcion" value="guardia" id="guard" onclick="oculta(5)"> <label> Guardias</label>
                                                </div>

                                                <div class="form-1-2" id="radio-pt">
                                                    <input type="radio" name="opcion" value="pt" id="pati" onclick="oculta(6)"> <label> Pase de Salida</label>
                                                </div>

                                                <div class="form-1-2" id="radio-curso">
                                                    <input type="radio" name="opcion" value="curso" id="cur" onclick="oculta(9)"> <label> Curso capacitación </label>
                                                </div>

                                                <div class="form-1-2" id="radio-otros">
                                                    <input type="radio" name="opcion" value="otros" id="otr" onclick="oculta(10)"> <label> Otros (04, 30, 31, suspensiones) </label>
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

                                            <div class="form-group col-lg-3" id="tipo-com">
                                                <span id="">Tipo de comisión</span>
                                                <br> 
                                                <select name="tl" id="ti-c" class="form-control">
                                                <option value="csi">Comisión Sindical (CS) Interna</option>
                                                <option value="cse">Comisión Sindical (CS) Externa</option>
                                                <option value="com1">Comisión Oficial Menor a un Día - 61</option>
                                                <option value="co1">Comisión Sindical equivalente a un Día - 17</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-lg-3" id="clavelicencia">
                                                <span id="">Tipo Licencia</span>
                                                <!--select licencias-->
                                                <select name="lic" id="Selectlicencias" class="form-control">
                                                    <option value="" disabled selected>Elija:</option>
                                                    <?php 
                                                        $sql="SELECT * from clave_especial where 
                                                        (descripcion like 'LICENCIA%' and idclave_especial!='50')
                                                        or idclave_especial='54' 
                                                        or idclave_especial='55'
                                                        or descripcion like 'PERMISOS CON GOCE DE SUELDO POR ANTI%'
                                                        or descripcion like '%LACTANCIA%'
                                                        or descripcion like '%ESTANCIA%'";
                                                        $query= mysqli_query($con, $sql);
                                                        if(!$query)
                                                        {
                                                          die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                                                        }
                                                        else
                                                        {
                                                          while($resul2=mysqli_fetch_array($query))
                                                          {
                                                            echo "<option value='".$resul2[0]."'>".$resul2[0]."-".$resul2[1]."</option>";   
                                                          }
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-lg-3" id="div-tol-lac">
                                                <span id="">Opción</span> <br>
                                                <select name="to-la" id="tolac" class="form-control">
                                                    <option value="1" selected>30 minutos al inicio y 30 minutos al final de su turno</option>
                                                    <option value="2">1 hora al inicio de su turno</option>
                                                    <option value="3">1 hora al final de su turno</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-lg-3" id="div-tol-est">
                                                <span id="">Opción</span> <br>
                                                <select name="to-es" id="toest" class="form-control">
                                                    <option value="1">30 minutos al inicio de su turno</option>
                                                    <option value="2">30 minutos al final de su turno</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-lg-3" id="div-curso1">
                                                <span id="">Opción</span> <br>
                                                <select name="cucap" id="cucap" class="form-control">
                                                    <option value="o" selected>Omitir registros de asistencia</option>
                                                    <option value="d">Registrar asistencia con horario distinto</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-lg-3" id="div-perm-go">
                                                <span id="">Motivo</span> <br>
                                                <select name="per-go" id="pergo" class="form-control">
                                                    <option value=""selected disabled>Elija:</option>
                                                    <option value="1">Por fuerza mayor</option>
                                                    <option value="2">Tramites para obtener pensión por jubilación, de retiro por edad, cesantía en edad avanzada</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-lg-3">
                                                <span id="MainContent_lbTrabajador">Trabajador</span>
                                                <div class="form-1-2">
                                                    <!--<label for="caja_busqueda" id="MainContent_lbTrabajador">Buscar:</label>-->
                                                    <input type="text" name="caja_busqueda" id="caja_busqueda" autocomplete="off" class="form-control" required>
                                                </div>

                                                <div id="datos">
                                                    <!--aquí se cargan los trabajadores-->
                                                </div>
                                            </div>

                                            <div class="form-group col-lg-3" id="suplente">
                                                <span id="MainContent_lbSuplente">Trabajador Suplente</span>
                                                <div class="form-1-2">
                                                    <input type="text" name="caja_busqueda2" id="caja_busqueda2" autocomplete="off" class="form-control">
                                                </div>

                                                <div id="datosSuplente">
                                                    <!--aquí se cargan los trabajadores-->
                                                </div>
                                            </div>

                                            <div class="form-group col-lg-3">
                                                <span id="spanFechaInicial">Fecha Inicial</span>
                                                <input name="fec" id="fec" type="text" class="form-control datepicker" autocomplete="off" readonly="readonly" style="background:white"/>
                                            </div>

                                            <!-- <div class="form-group col-lg-3" id="div-es">
                                                <span id="">Omisión de:</span> <br>
                                                <select name="eOs" id="eOs" class="form-control">
                                                    <option value="e" selected>Entrada</option>
                                                    <option value="s">Salida</option>
                                                </select>
                                            </div> -->

                                            <div class="form-group col-lg-3" id="fecf">
                                                <span id="">Fecha Final</span>
                                                <input name="fecf" type="text" class="form-control datepicker" readonly="readonly" style="background:white" autocomplete="off"/>
                                            </div>

                                            <div class="form-group col-lg-3" id="vacio_">
                                            </div>

                                            <div class="form-group col-lg-3" id="he">
                                                <span id="">Hora Entrada</span>
                                                <input name="he" type="text" id="" class="form-control hour" style="background:white"/>
                                            </div>

                                            <div class="form-group col-lg-3" id="hs">
                                                <span id="">Hora Salida</span>
                                                <input name="hs" type="text" id="" class="form-control hour" style="background:white"/>
                                            </div>

                                            <div class="form-group col-lg-3" id="empresa">
                                                <span id="">Empresa Destino</span>
                                                <input type="text" name="emp" id="" class="form-control" autocomplete="off" placeholder="Empresa destino">
                                            </div>

                                            <div class="form-group col-lg-3" id="prioridad">
                                                <span id="">Prioridad</span>
                                                <br>
                                                <select name="priority" id="p_NA" class="form-control" onchange="seguro()">
                                                <option value="n" selected>Normal</option>
                                                <option value="a">ALTA</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group col-lg-3" id="licenciaHastaUnAnio">
                                                <span id="">¿Es una licencia para enfermedad no profesional?</span> <br>
                                                <input type="checkbox" name="licUnAnio" value="1" id="li1Anio" onclick="unAnio()"> SI
                                            </div>

                                            <div class="form-group col-lg-3" id="imagen">
                                                <span id="">Escaneo del documento</span>
                                                <input type="file" name="archivo[]" id="myfile">
                                            </div>

                                            <div class="form-group col-lg-3" id="div-otro">
                                                <span id="">Otras incidencias</span>
                                                <br>
                                                <select name="select-otro" id="other" class="form-control">
                                                    <option value="" selected disabled>Elija:</option>
                                                    <option value="04">Pago de 1/2 jornada laboral (04)</option>
                                                    <option value="30">Ausentarse en horas de labores sin autorización (30)</option>
                                                    <option value="31">Ausentarse en horas de labores sin autorización del turno opcional o percepción adicional (31)</option>
                                                    <option value="80">Suspensión en nómina por renuncia (80)</option>
                                                    <option value="81">Suspensión en nómina por defunción (81)</option>
                                                    <option value="82">Suspensión en nómina por cese (82)</option>
                                                    <option value="83">Suspensión en nómina por licencia médica definitiva (83)</option>
                                                    <option value="84">Suspensión en nómina por pensión (84)</option>
                                                    <option value="85">Suspensión en nómina por jubilación (85)</option>
                                                    <option value="86">Suspensión temporal en nómina por sanción administrativa (86)</option>
                                                    <option value="87">Suspensión en nómina por abandono de empleo sin justificación (87)</option>
                                                    <option value="88">Suspensión en nómina por término de interinato (88)</option>
                                                    <option value="89">Suspensión en nómina por término de comisión (89)</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-lg-3" id="doct">
                                                <span id="">Doctor:</span>
                                                <input type="text" name="doctor" id="" class="form-control" autocomplete="off" placeholder="El que otorgó la licencia">
                                            </div>
                                            <!--
                                            <div class="form-group col-lg-3" id="div-lic-sg">
                                                <span id="">Opción</span> <br>
                                                <select name="li-sg" id="lisg" class="form-control">
                                                    <option value="1">Licencia sin goce Por antiguedad</option>
                                                    <option value="2">Para practicar servicio social o pasantía</option>
                                                    <option value="3">Se pasa de base a confianza</option>
                                                </select>
                                            </div>
                                            -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!--FIN DIV CLASS ROW_NUEVA APROBACION-->

                        <div class="card-footer">
                            <div class="dropdown">
                                <input type="submit" name="Aceptar" value="Guardar" class="btn btn-primary btn-sm" />
                            </div>
                        </div>

                    </div> <!--FIN DIV animated fadeIn-->
                </div> <!--FIN DIV content mt-3--> 
            </form>  <!-- FIN DEL FORM -->
        </div> <!-- FIN right-panel -->       
    </body>
    <?php require("../ht/modalCambiarPass.php"); ?>
</html>