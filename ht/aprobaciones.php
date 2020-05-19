<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php");
    }
    else
    {
        header("Location: ../index.html");
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
        <link rel="stylesheet" href="../assets/css/mdtimepicker.min.css" />

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
                    $("#lice").prop("checked", true);
                    oculta(2);
                })
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
                    oculta(2);
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
                    oculta(2);
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
                    oculta(2);
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
                        if(cod=="csi" || cod=="cse" || cod=="com1")
                        {
                            document.getElementById('fecf').style.display = "block";
                            document.getElementById('he').style.display = "block";
                            document.getElementById('hs').style.display = "block";
                        }
                    }
                });

                
                $("#Selectlicencias").click(function()
                {
                    var cod = document.getElementById("Selectlicencias").value;
                    if(cod=="92") //Para la tolerancia de lactancia
                    {
                        document.getElementById('fecf').style.display = "none";
                        document.getElementById('vacio_').style.display = "none";
                        document.getElementById('div-tol-est').style.display = "none";
                        document.getElementById('div-perm-go').style.display = "none";
                        document.getElementById('licenciaHastaUnAnio').style.display = "none";
                        document.getElementById('div-tol-lac').style.display = "block";
                    }
                    else
                    {
                        if(cod=="93")//Para la tolerancia de estancia
                        {
                            document.getElementById('vacio_').style.display = "none";
                            document.getElementById('div-tol-lac').style.display = "none";
                            document.getElementById('div-perm-go').style.display = "none";
                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                            document.getElementById('fecf').style.display = "block";
                            document.getElementById('div-tol-est').style.display = "block";
                        }
                        else
                        {
                            if(cod=="41")//Para licencia goce
                            {
                                document.getElementById('div-tol-lac').style.display = "none";
                                document.getElementById('div-tol-est').style.display = "none";
                                document.getElementById('vacio_').style.display = "none";
                                document.getElementById('fecf').style.display = "none";
                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                document.getElementById('div-perm-go').style.display = "block";
                            }
                            else
                            {
                                if(cod=="48" || cod=="49") //licencia matrimonio y fallecimiento
                                {
                                    document.getElementById('fecf').style.display = "none";
                                    document.getElementById('div-tol-lac').style.display = "none";
                                    document.getElementById('div-tol-est').style.display = "none";
                                    document.getElementById('vacio_').style.display = "none";
                                    document.getElementById('div-perm-go').style.display = "none";
                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                }
                                else
                                {
                                    if(cod=="LSG")//licencia sin goce
                                    {
                                        document.getElementById('div-tol-lac').style.display = "none";
                                        document.getElementById('div-tol-est').style.display = "none";
                                        document.getElementById('div-perm-go').style.display = "none";
                                        document.getElementById('vacio_').style.display = "none";
                                        document.getElementById('fecf').style.display = "block";
                                        document.getElementById('licenciaHastaUnAnio').style.display = "block";
                                    }
                                    else
                                    {
                                        if(cod=="LSGSS")//licencia sin goce para servicio social
                                        {
                                            document.getElementById('div-tol-lac').style.display = "none";
                                            document.getElementById('div-tol-est').style.display = "none";
                                            document.getElementById('div-perm-go').style.display = "none";
                                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                            document.getElementById('vacio_').style.display = "none";
                                            document.getElementById('fecf').style.display = "block";
                                        }
                                        else//todas las demas
                                        {
                                            if(cod=="47" || cod=="51" || cod=="53" || cod=="54" || cod=="55" || cod=="62")
                                            {
                                                document.getElementById('div-tol-lac').style.display = "none";
                                                document.getElementById('div-tol-est').style.display = "none";
                                                document.getElementById('div-perm-go').style.display = "none";
                                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                document.getElementById('vacio_').style.display = "block";
                                                document.getElementById('fecf').style.display = "block";
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
                    document.getElementById('tipo-com').style.display = "none";
                    document.getElementById('fecf').style.display = "none";
                    document.getElementById('he').style.display = "none";
                    document.getElementById('hs').style.display = "none";
                    document.getElementById('empresa').style.display = "none";
                    document.getElementById('clavelicencia').style.display = "none";
                    document.getElementById('div-es').style.display = "none";
                    document.getElementById('prioridad').style.display = "none";
                    document.getElementById('div-curso1').style.display = "none";
                    document.getElementById('div-tol-lac').style.display = "none";
                    document.getElementById('div-tol-est').style.display = "none";
                    document.getElementById('div-perm-go').style.display = "none";
                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                    
                } 
                else 
                {
                    if(x==1)
                    {
                        document.getElementById('clavelicencia').style.display = "none";
                        document.getElementById('div-es').style.display = "none";
                        document.getElementById('vacio_').style.display = "none";
                        document.getElementById('div-curso1').style.display = "none";
                        document.getElementById('div-tol-lac').style.display = "none";
                        document.getElementById('div-tol-est').style.display = "none";
                        document.getElementById('div-perm-go').style.display = "none";
                        document.getElementById('licenciaHastaUnAnio').style.display = "none";
                        document.getElementById('tipo-com').style.display = "block";
                        document.getElementById('fecf').style.display = "block";
                        document.getElementById('he').style.display = "block";
                        document.getElementById('hs').style.display = "block";
                        document.getElementById('empresa').style.display = "block";
                        document.getElementById('prioridad').style.display = "block";
                    }
                    else
                    {
                        if (x == 2) 
                        {
                            document.getElementById('tipo-com').style.display = "none";
                            document.getElementById('he').style.display = "none";
                            document.getElementById('hs').style.display = "none";
                            document.getElementById('empresa').style.display = "none";
                            document.getElementById('div-es').style.display = "none";
                            document.getElementById('prioridad').style.display = "none";
                            document.getElementById('div-curso1').style.display = "none";
                            document.getElementById('div-tol-lac').style.display = "none";
                            document.getElementById('div-tol-est').style.display = "none";
                            document.getElementById('div-perm-go').style.display = "none";
                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                            document.getElementById('clavelicencia').style.display = "block";
                            document.getElementById('fecf').style.display = "block";
                            document.getElementById('vacio_').style.display = "block";
                        } 
                        else
                        {
                            if (x == 3) 
                            {
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
                                document.getElementById('div-es').style.display = "block";
                            }

                            else
                            {
                                if (x == 9) 
                                {
                                    document.getElementById('tipo-com').style.display = "none";
                                    document.getElementById('fecf').style.display = "none";
                                    document.getElementById('he').style.display = "none";
                                    document.getElementById('hs').style.display = "none";
                                    document.getElementById('empresa').style.display = "none";
                                    document.getElementById('clavelicencia').style.display = "none";
                                    document.getElementById('prioridad').style.display = "none";
                                    document.getElementById('div-es').style.display = "none";
                                    document.getElementById('vacio_').style.display = "none";
                                    document.getElementById('div-tol-lac').style.display = "none";
                                    document.getElementById('div-tol-est').style.display = "none";
                                    document.getElementById('div-perm-go').style.display = "none";
                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                    document.getElementById('div-curso1').style.display = "block";
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
                        }
                        else
                        {    
                            $(document).ready(function()
                            {
                                alertify.alert("Prioridad Normal");
                                $("#p_NA").val("n");//cambiar el option a normal
                            });
                        }
                    });
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
                    if(rad=="comision")
                    {
                        oculta(1);
                    }
                    if(rad=="licencia")
                    {
                        oculta(2);
                    }
                    if(rad=="permiso")
                    {
                        oculta(2);
                    }
                    if(rad=="guardia")
                    {
                        oculta(2);
                    }

                    if(rad=="curso")
                    {
                        oculta(2);
                    }

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
                        if(cod=="cse" || cod=="com1")
                        {
                            document.getElementById('fecf').style.display = "block";
                            document.getElementById('he').style.display = "block";
                            document.getElementById('hs').style.display = "block";
                        }
                    }

                    //las licencias
                    var cod = document.getElementById("Selectlicencias").value;
                    if(cod=="92") //Para la tolerancia de lactancia
                    {
                        document.getElementById('fecf').style.display = "none";
                        document.getElementById('vacio_').style.display = "none";
                        document.getElementById('div-tol-est').style.display = "none";
                        document.getElementById('div-perm-go').style.display = "none";
                        document.getElementById('licenciaHastaUnAnio').style.display = "none";
                        document.getElementById('div-tol-lac').style.display = "block";
                    }
                    else
                    {
                        if(cod=="93")//Para la tolerancia de estancia
                        {
                            document.getElementById('vacio_').style.display = "none";
                            document.getElementById('div-tol-lac').style.display = "none";
                            document.getElementById('div-perm-go').style.display = "none";
                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                            document.getElementById('fecf').style.display = "block";
                            document.getElementById('div-tol-est').style.display = "block";
                        }
                        else
                        {
                            if(cod=="41")//Para licencia goce
                            {
                                document.getElementById('div-tol-lac').style.display = "none";
                                document.getElementById('div-tol-est').style.display = "none";
                                document.getElementById('vacio_').style.display = "none";
                                document.getElementById('fecf').style.display = "none";
                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                document.getElementById('div-perm-go').style.display = "block";
                            }
                            else
                            {
                                if(cod=="48" || cod=="49") //licencia matrimonio y fallecimiento
                                {
                                    document.getElementById('fecf').style.display = "none";
                                    document.getElementById('div-tol-lac').style.display = "none";
                                    document.getElementById('div-tol-est').style.display = "none";
                                    document.getElementById('vacio_').style.display = "none";
                                    document.getElementById('div-perm-go').style.display = "none";
                                    document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                }
                                else
                                {
                                    if(cod=="LSG")//licencia sin goce
                                    {
                                        document.getElementById('div-tol-lac').style.display = "none";
                                        document.getElementById('div-tol-est').style.display = "none";
                                        document.getElementById('div-perm-go').style.display = "none";
                                        document.getElementById('vacio_').style.display = "none";
                                        document.getElementById('fecf').style.display = "block";
                                        document.getElementById('licenciaHastaUnAnio').style.display = "block";
                                    }
                                    else
                                    {
                                        if(cod=="LSGSS")//licencia sin goce para servicio social
                                        {
                                            document.getElementById('div-tol-lac').style.display = "none";
                                            document.getElementById('div-tol-est').style.display = "none";
                                            document.getElementById('div-perm-go').style.display = "none";
                                            document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                            document.getElementById('vacio_').style.display = "none";
                                            document.getElementById('fecf').style.display = "block";
                                        }
                                        else//todas las demas
                                        {
                                            if(cod=="47" || cod=="51" || cod=="53" || cod=="54" || cod=="55" || cod=="62")
                                            {
                                                document.getElementById('div-tol-lac').style.display = "none";
                                                document.getElementById('div-tol-est').style.display = "none";
                                                document.getElementById('div-perm-go').style.display = "none";
                                                document.getElementById('licenciaHastaUnAnio').style.display = "none";
                                                document.getElementById('vacio_').style.display = "block";
                                                document.getElementById('fecf').style.display = "block";
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
                                <li><i class="fa fa-users"></i><a href="trabajadores.php">Personal</a></li>
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
                </div> <!--FIN menu-principal-->
                
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
                                <a class="nav-link" href="updatePassword.php"><i class="fa fa-key"></i> Cambiar Contraseña</a>
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

            <form id="f1" method="POST" action="../php/insert/justificacion.php">
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

                                                <div class="form-1-2" id="radio-justificacion">
                                                    <input type="radio" name="opcion" value="justificar" id="jus-ret" onclick="oculta(0)" checked> Justificar retardos
                                                </div>
                                                <div class="form-1-2" id="radio-omision">
                                                    <!--<label for="caja_busqueda" id="MainContent_lbTrabajador">Buscar:</label>-->
                                                    <input type="radio" name="opcion" value="omision" id="jus-omi" onclick="oculta(3)"> Justificar omisión
                                                </div>
                                                <div class="form-1-2" id="radio-comision">
                                                    <input type="radio" name="opcion" value="comision" id="comi" onclick="oculta(1)"> Comisiones
                                                </div>

                                                <div class="form-1-2" id="radio-licencia">
                                                    <input type="radio" name="opcion" value="licencia" id="lice" onclick="oculta(2)"> Licencias
                                                </div>

                                                <div class="form-1-2" id="radio-permiso">
                                                    <input type="radio" name="opcion" value="permiso" id="perm" onclick="oculta(2)"> Permisos
                                                </div>

                                                <div class="form-1-2" id="radio-guardia">
                                                    <input type="radio" name="opcion" value="guardia" id="guard" onclick="oculta(2)"> Guardias
                                                </div>

                                                <div class="form-1-2" id="radio-pt">
                                                    <input type="radio" name="opcion" value="pt" id="pati" onclick="oculta(2)"> Pase de Salida
                                                </div>

                                                <div class="form-1-2" id="radio-curso">
                                                    <input type="radio" name="opcion" value="curso" id="cur" onclick="oculta(9)"> Curso capacitación
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
                                                        or descripcion like 'VACACIONES POR EM%'
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
                                                            echo "<option value='".$resul2[0]."'>".utf8_encode($resul2[0])."-".utf8_encode($resul2[1])."</option>";   
                                                          }
                                                        }
                                                    ?>
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

                                            <div class="form-group col-lg-3">
                                                <span id="">Fecha Inicial</span>
                                                <input name="fec" type="text" class="form-control datepicker" autocomplete="off" readonly="readonly" style="background:white"/>
                                            </div>

                                            <div class="form-group col-lg-3" id="div-es">
                                                <span id="">Omisión de:</span> <br>
                                                <select name="eOs" id="eOs" class="form-control">
                                                    <option value="e" selected>Entrada</option>
                                                    <option value="s">Salida</option>
                                                </select>
                                            </div>

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

                                            <div class="form-group col-lg-3" id="div-curso1">
                                                <span id="">Opción</span> <br>
                                                <select name="cucap" id="cucap" class="form-control">
                                                    <option value="o" selected>Omitir registros de asistencia</option>
                                                    <option value="d">Registrar asistencia con horario distinto</option>
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

                                            <div class="form-group col-lg-3" id="div-perm-go">
                                                <span id="">Motivo</span> <br>
                                                <select name="per-go" id="pergo" class="form-control">
                                                    <option value=""selected disabled>Elija:</option>
                                                    <option value="1">Por fuerza mayor</option>
                                                    <option value="2">Tramites para obtener pensión por jubilación, de retiro por edad, cesantía en edad avanzada</option>
                                                </select>
                                            </div>

                                            
                                            <div class="form-group col-lg-3" id="licenciaHastaUnAnio">
                                                <span id="">¿Es una licencia para enfermedad no profesional?</span> <br>
                                                <input type="checkbox" name="licUnAnio" value="1" id="li1Anio" onclick="unAnio()"> SI
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
</html>