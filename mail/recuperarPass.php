<!doctype html>
<html class="no-js" lang="">

<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Control de Asistencia</title>
        <meta name="description" content="Sistema de Control de Asistencia">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-icon.png">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" href="../assets/css/inicio.css">
        <link rel="stylesheet" href="../assets/css/normalize.css">
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="../assets/css/themify-icons.css">
        <link rel="stylesheet" href="../assets/css/flag-icon.min.css">
        <link rel="stylesheet" href="../assets/css/cs-skin-elastic.css">

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.css"
        />

        <link rel="stylesheet" href="../assets/scss/style.css">
        <link href="assets/css/lib/vector-map/jqvmap.min.css" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>
    </head>
    <body class="bg-dark">
        <div class="sufee-login d-flex align-content-center flex-wrap">
            <div class="container">
                <div class="login-content">
                    <div class="login-logo">
                        <img src="../images/LOGO_ISSSTE.png"></img>
                        <a href="#">
                            <span>¿Olvidaste tu contraseña?</span>
                            <p>Introduce tu usuario para restablecer tu contraseña</p>
                        </a>
                    </div>
                    <div class="login-form">
                        <form method="post" action="enviar_mail.php" id="form1" autocomplete="off">
                            <div class="form-group">
                               <label>Usuario:</label>
                               <input name="usuario" type="text" id="usuario" class="form-control"  required/>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="btnRestablecer" value="Restablecer contraseña" id="btnRestablecer" class="btn btn-success btn-flat m-b-30 m-t-30" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                setTimeout(function() {
                    $(".alert").fadeOut(1500);
                }, 4000);
            });
        </script>
    </body>

</html>