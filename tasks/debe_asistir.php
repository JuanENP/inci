<?php 
date_default_timezone_set('America/Mexico_City');
require("../Acceso/global.php");

    
    
?>
<!-- select Valor from _posicion where idposicion=2 y guardar en variable pos -->
<!-- QUIENES DEBEN ASISTIR HOY guardar en array deben_hoy-->
<!-- Y QUIENES ASISTIERON HOY fecha entrada, turno_entrada, su numero e id (GUARDAR EN ARRAY asisten_hoy, CON ORDER BY POR NUMERO DE EMPLEADO cuyo id sea mayor que pos) -->
<!-- guardar el ultimo id del ultimo empleado en el array y actualizar _pos con ese id-->


<!-- Buscar cada empleado de deben_hoy en asisten_hoy
    Si lo encuentra
    {
        1.De su fecha de entrada guardar solo su hora de entrada
        2.Obtener la hora de entrada de su turno
        3. Y sacar los minutos despues
        4.En base al valor del punto 3  verificar si el empleado tiene una licencia o permiso, 01,02,03 u omision de entrada o nada
    }
    else
    {
        insertar omision de entrada a ese empleado
        Ver si tiene comision validez=1 cada trabajador del array asisten_hoy
        si (tiene comision validez=1)
        {
        si(comision es interna)
        {
            
        }
        else 
        { //Buscar si es licencia o permiso
            
        }
    }
 
    }
    

 -->
