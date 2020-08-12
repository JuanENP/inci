<?php
// $saltos;
$contenido='';
    $contenido.='<div id="main-menu" class="main-menu collapse navbar-collapse">';
        $contenido.='<ul class="nav navbar-nav">';
            $contenido.='<li>';
                $contenido.='<a href="'.$saltos.'panel_control.php" title="Volver al panel principal"> <i class="menu-icon fa fa-dashboard"></i>Panel de Control </a>';
            $contenido.='</li>';
            $contenido.='<li id="Menu_Personal" class="menu-item-has-children dropdown">';
                $contenido.='<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-users"></i>Personal</a>';
                $contenido.='<ul class="sub-menu children dropdown-menu">';
                    $contenido.='<li><i class="fa fa-crosshairs"></i><a href="'.$saltos.'ht/categoria.php">Categorias</a></li>';
                    $contenido.='<li><i class="fa fa-sitemap"></i><a href="'.$saltos.'ht/departamentos.php">Departamentos</a></li>';
                    $contenido.='<li><i class="fa fa-male"></i><a href="'.$saltos.'ht/tipoempleado.php">Tipo Empleado</a></li>';
                    $contenido.='<li><i class="fa fa-users"></i><a href="'.$saltos.'ht/trabajadores.php">Personal</a></li>';
                    $contenido.='<li class="menu-item-has-children">';                             
                        $contenido.='<a  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-plane"></i>Vacaciones</a>';
                        $contenido.='<ul class="sub-menu children dropdown">';
                            $contenido.='<li><i class="menu-icon fa fa-bicycle"><a href="'.$saltos.'ht/vacaciones.php"></i>Normales</a></li>';
                            $contenido.='<li><i class="menu-icon fa fa-flask"><a href="'.$saltos.'ht/vacaciones-r.php"></i>Eman. Radio</a></li>';
                        $contenido.='</ul>';
                    $contenido.='</li>';
                $contenido.='</ul>';
            $contenido.='</li>';
            $contenido.='<li id="Menu_Dispositivo" class="menu-item-has-children dropdown">';
                $contenido.='<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-desktop"></i>Dispositivo</a>';
                $contenido.='<ul class="sub-menu children dropdown-menu">';
                    $contenido.='<li><i class="fa fa-plus-circle"></i><a href="'.$saltos.'ht/dispositivos.php">Dispositivo</a></li>';
                $contenido.='</ul>';
            $contenido.='</li>';
            $contenido.='<li id="Menu_Asistencia" class="menu-item-has-children dropdown">';
                $contenido.='<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-clock-o"></i>Asistencia</a>';
                $contenido.='<ul class="sub-menu children dropdown-menu">';
                    $contenido.='<li><i class="fa fa-calendar"></i><a href="'.$saltos.'ht/turnos.php">Turnos</a></li>';
                    $contenido.='<li><i class="fa fa-check-square-o"></i><a href="'.$saltos.'ht/aprobaciones.php">Aprobaciones</a></li>';
                    $contenido.='<li><i class="fa fa-files-o"></i><a href="'.$saltos.'ht/reportes.php">Reportes</a></li>';
                    $contenido.='<li><i class="fa fa-shield"></i><a href="'.$saltos.'ht/conceptos.php">Tipo de Incidencias</a></li>';
                    $contenido.='<li><i class="fa fa-thumbs-up"></i><a href="'.$saltos.'ht/verAsistencias.php">Asistencias de hoy</a></li>';
                    $contenido.='<li><i class="fa fa-ban"></i><a href="'.$saltos.'ht/bajas.php">Bajas</a></li>';
                $contenido.='</ul>';
            $contenido.='</li>';
            $contenido.='<li id="Menu_Sistema" class="menu-item-has-children dropdown">';
                $contenido.='<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Sistema</a>';
                $contenido.='<ul class="sub-menu children dropdown-menu">';
                    $contenido.='<li><a class="nav-link" href="#" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#mimodal"  name="boton"><i class="fa fa-key"></i> Cambiar contraseña</a></li>';
                    if($nombre=="AdministradorGod")
                        {
                            $contenido.='<li><i class="fa fa-book"></i><a href="'.$saltos.'ht/zbitacoras.php">Bitácoras</a></li>'; 
                            $contenido.='<li><i class="fa fa-picture-o"></i><a href="'.$saltos.'ht/cambiar-logo-principal.php">Logo página principal</a></li>';
                            $contenido.='<li><i class="fa fa-users"></i><a href="'.$saltos.'ht/usuarios.php">Usuarios</a></li>';  
                        }
                $contenido.='</ul>';
            $contenido.='</li>';
        $contenido.='</ul>';
    $contenido.='</div>';

    echo $contenido;
?>