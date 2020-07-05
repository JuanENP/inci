<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        if($_SESSION["name"]!="AdministradorGod")
        {
            echo "<script> alert('Usted no posee privilegios suficientes para elegir esta opción.'); history.back(); </script>";
            exit();
        }
        $ubicacion='../update/modificarPass.php';//sirve para indicar la ruta del form modalCambiarPass
    }
    else
    {
        header("Location: ../../index.php");
        die();
    }

    if((!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
    {
        $carpetaDestino="../../images/";//carpeta destino para el logo
        $name="LOGO_ISSSTE";
        //para que se evalue la imagen
        $laImagen=$_FILES["archivo"]["name"][0];
        $extension=$_FILES["archivo"]["type"][0];
        $origen=$_FILES["archivo"]["tmp_name"][0];
        $destino=$carpetaDestino.$laImagen;
        analizaYCargaImagen($origen,$destino,$laImagen,$name,$extension);
    }
    else
    {
        echo "<script> alert('No ha elegido la imagen a cargar.'); history.back(); </script>";
    }

    function analizaYCargaImagen($origen,$destino,$laImagen,$name,$extension)
    {
        # si es un formato de imagen
        if($extension=="image/jpeg" || $extension=="image/pjpeg" || $extension=="image/png")
        {
            if($extension=="image/jpeg")
            {
                $tipo=".jpeg";
            }
            else
            {
                if($extension=="image/pjpeg")
                {
                    $tipo=".pjpeg";
                }
                else
                {
                    if($extension=="image/png")
                    {
                        $tipo=".png";
                    }
                }
            }
            # movemos el archivo
            if(@move_uploaded_file($origen, $destino))
            {
                rename ("../../images"."/".$laImagen, "../../images"."/".$name.$tipo);
                echo "<script> alert('Logo principal actualizado correctamente.'); history.back(); </script>";
            }
            else
            {
                echo "<script> alert('NO SE PUDO CARGAR LA IMAGEN SELECCIONADA; REINTENTE.'); history.back(); </script>";
                exit();
            }
        }
        else
        {
            //echo "<br>".$laImagen." - NO es imagen jpg, png o gif";
            //return 2;//No es imagen
            echo "<script> alert('FORMATO DE ARCHIVO NO ACEPTADO. ASEGÚRESE DE ELEGIR UNA IMAGEN .jpg ò .png, REINTENTE.'); history.back(); </script>";
            exit();
        }
    }//Fin de función analizaYCargaImagen
?>