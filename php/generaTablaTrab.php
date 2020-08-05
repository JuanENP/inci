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
        header("Location: ../index.php");
        die();
    }
    $tabla="";
    
    if($_POST['textoEnviar'] !== "#.#")
    {
        if($_POST['textoEnviar'] == "todos" || $_POST['textoEnviar'] == "TODOS")
        {
            require("../ht/_encript.php");
            $q=$_POST['textoEnviar'];
            $sql="SELECT a.numero_trabajador,a.nombre,a.apellido_paterno,a.apellido_materno,a.depto_depto,a.categoria_categoria,b.descripcion FROM trabajador a 
            inner join tipo b on b.idtipo = a.tipo_tipo;";
            $query=mysqli_query($con, $sql);
            if(!$query)
            {
                echo "Error al recuperar los datos de la bitácora.";
                exit();
            }
    
            $tabla.="<table id='datos' class='table table-striped table-bordered display'>
            <thead>
                <tr>
                    <th>Número de trabajador</th>
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th> 
                    <th>Departamento</th> 
                    <th>Categoría</th> 
                    <th>Tipo de empleado</th> 
                    <th>Antigüedad</th>   
                    <th>Acción</th> 
                </tr>
            </thead>     
            <tbody>";
            while ($fila = mysqli_fetch_array($query)) 
            {
                $encript=generaURL($fila[0]);
                $tiempo=antiguedad($fila[0]);
                $tabla.="<tr>
                            <td>".$fila[0]."</td>
                            <td>".$fila[1]."</td>
                            <td>".$fila[2]."</td>
                            <td>".$fila[3]."</td>
                            <td>".$fila[4]."</td>
                            <td>".$fila[5]."</td>
                            <td>".$fila[6]."</td>
                            <td>".$tiempo."</td>
                            <td><a><button class='btn btn-danger btn-sm' id='$encript' onclick='preguntar(this);'><i class='fa fa-trash-o'></i>Eliminar </button></a> 
                            <a href='../php/editar-trabajadores.php?id=".$fila[0]."'><button class='btn btn-success btn-sm'><i class='fa fa-pencil-square-o'></i>Editar </button></a> </td>
                        </tr>";
            }
            $tabla.="</tbody></table>";
            echo $tabla;
        }
        else
        {   require("../ht/_encript.php");
            $q=$_POST['textoEnviar'];
            $sql="SELECT a.numero_trabajador,a.nombre,a.apellido_paterno,a.apellido_materno,a.depto_depto,a.categoria_categoria,b.descripcion FROM trabajador a 
            inner join tipo b on b.idtipo = a.tipo_tipo 
            where a.numero_trabajador like '%".$q."%' or a.nombre like '%".$q."%' or a.apellido_paterno like '%".$q."%' or a.apellido_materno like '%".$q."%'
            or a.depto_depto like '%".$q."%' or a.categoria_categoria like '%".$q."%'or b.descripcion like '%".$q."%';";
            $query=mysqli_query($con, $sql);
            if(!$query)
            {
                echo "Error al recuperar los datos de la bitácora.";
                exit();
            }
    
            $tabla.="<table id='datos' class='table table-striped table-bordered display'>
            <thead>
                <tr>
                    <th>Número de trabajador</th>
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th> 
                    <th>Departamento</th> 
                    <th>Categoría</th> 
                    <th>Tipo de empleado</th>  
                    <th>Antigüedad</th>  
                    <th>Acción</th> 
                </tr>
            </thead>     
            <tbody>";
            while ($fila = mysqli_fetch_array($query)) 
            {
                $encript=generaURL($fila[0]);
                $tiempo=antiguedad($fila[0]);
                $tabla.="<tr>
                            <td>".$fila[0]."</td>
                            <td>".$fila[1]."</td>
                            <td>".$fila[2]."</td>
                            <td>".$fila[3]."</td>
                            <td>".$fila[4]."</td>
                            <td>".$fila[5]."</td>
                            <td>".$fila[6]."</td>
                            <td>".$tiempo."</td>
                            <td><a><button class='btn btn-danger btn-sm' id='$encript' onclick='preguntar(this);'><i class='fa fa-trash-o'></i>Eliminar </button></a> 
                            <a href='../php/editar-trabajadores.php?id=".$fila[0]."'><button class='btn btn-success btn-sm'><i class='fa fa-pencil-square-o'></i>Editar </button></a> </td>
                        </tr>";
            }
            $tabla.="</tbody></table>";
            echo $tabla;
        }
    }
    else
    {
        require("../ht/_encript.php");
        $sql="SELECT a.numero_trabajador,a.nombre,a.apellido_paterno,a.apellido_materno,a.depto_depto,a.categoria_categoria,b.descripcion FROM trabajador a 
        inner join tipo b on b.idtipo = a.tipo_tipo and depto_depto='01100';";
        $query=mysqli_query($con, $sql);
        if(!$query)
        {
            echo "Error al recuperar los datos de la bitácora.";
            exit();
        }

        $tabla.="<table id='datos' class='table table-striped table-bordered display'>
        <thead>
            <tr>
                <th>Número de trabajador</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th> 
                <th>Departamento</th> 
                <th>Categoría</th> 
                <th>Tipo de empleado</th> 
                <th>Antigüedad</th>  
                <th>Acción</th> 
            </tr>
        </thead>     
        <tbody>";
        while ($fila = mysqli_fetch_array($query)) 
        {
            $encript=generaURL($fila[0]);
            $tiempo=antiguedad($fila[0]);
            $tabla.="<tr>
                        <td>".$fila[0]."</td>
                        <td>".$fila[1]."</td>
                        <td>".$fila[2]."</td>
                        <td>".$fila[3]."</td>
                        <td>".$fila[4]."</td>
                        <td>".$fila[5]."</td>
                        <td>".$fila[6]."</td>
                        <td>".$tiempo."</td>
                        <td><a><button class='btn btn-danger btn-sm' id='$encript' onclick='preguntar(this);'><i class='fa fa-trash-o'></i>Eliminar </button></a> 
                        <a href='../php/editar-trabajadores.php?id=".$fila[0]."'><button class='btn btn-success btn-sm'><i class='fa fa-pencil-square-o'></i>Editar </button></a> </td>
                    </tr>";
        }
        $tabla.="</tbody></table>";
        echo $tabla;
    }

    function antiguedad($numero)
    {
        global $con;
        $fecha_hoy=date("Y-m-d");//la fecha de hoy
        $fHoy= new DateTime( $fecha_hoy);
        $sql="select fecha_alta from tiempo_servicio where trabajador_trabajador='$numero';"; 
        $query=mysqli_query($con,$sql);
        if($query)
        {   
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul = mysqli_fetch_array($query);
                if($resul[0] !=='0000-00-00')
                {
                    $fecha_alta=$resul[0];
                    $f_alta= new DateTime($fecha_alta);
                    $antiguedad= $f_alta->diff($fHoy); 
                    $años=$antiguedad->format('%Y'); 
                    $meses=$antiguedad->format('%m');
                    $dias=$antiguedad->format('%d');
                    if($años==0)
                    {
                        if($dias==0)
                        {
                            $tiempoAntiguedad=$meses.' meses ';
                        }
                        else
                        {
                            $tiempoAntiguedad=$meses.' meses '.$dias.' dias';
                        }
                    }
                    else
                    {
                        if($meses==0)
                        {
                            if($dias==0)
                            {
                                $tiempoAntiguedad=$años.' años';
                            }
                            else
                            {
                                $tiempoAntiguedad=$años.' años '.$dias.' dias';
                            }
                        }
                        else
                        {
                            $tiempoAntiguedad=$años.' años '.$meses.' meses ' .$dias.' dias';
                        }
                    }
                    return  $tiempoAntiguedad;
                }
                else
                {
                    return 'Actualizar la fecha de alta';
                }
            }
        }
        else
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='838';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
        }  
    }
?>