<?php
    function analizaYCargaExcel($origen,$destino,$elArchivo,$name,$extension)
    {
        # si es un formato de excel
        if($extension=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
        {
            $tipo=".xlsx";
            # movemos el archivo
            if(@move_uploaded_file($origen, $destino))
            {
                rename ("../../vacaciones"."/".$elArchivo, "../../vacaciones"."/".$name.$tipo);
                $ruta="../../vacaciones"."/".$name.$tipo;
                return $ruta;
            }else
            {
                echo "<script> imprime('No se ha podido mover el archivo excel, reintente'); </script>";
                exit();
            }
        }else
        {
            if($extension=="application/vnd.ms-excel")
            {
                $tipo=".xls";
                # movemos el archivo
                if(@move_uploaded_file($origen, $destino))
                {
                    rename ("../../vacaciones"."/".$elArchivo, "../../vacaciones"."/".$name.$tipo);
                    $ruta="../../vacaciones"."/".$name.$tipo;
                    return $ruta;
                }else
                {
                    echo "<script> imprime('No se ha podido mover el archivo excel, reintente'); </script>";
                    exit();
                }
            }
            else
            {
                if($extension=="text/csv")
                {
                    $tipo=".csv";
                    # movemos el archivo
                    if(@move_uploaded_file($origen, $destino))
                    {
                        rename ("../../vacaciones"."/".$elArchivo, "../../vacaciones"."/".$name.$tipo);
                        $ruta="../../vacaciones"."/".$name.$tipo;
                        return $ruta;
                    }else
                    {
                        echo "<script> imprime('No se ha podido mover el archivo excel, reintente'); </script>";
                        exit();
                    }
                }
                else
                {
                    echo "<script> imprime('Lo que usted cargó NO es un archivo excel, verifique'); </script>";
                    exit();
                }
            }
        }
    }//Fin de función analizaYCargaExcel
?>