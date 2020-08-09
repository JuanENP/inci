<?php
        if((!empty($_POST["nom"]) && !empty($_POST["contra"])) && ($_POST["nom"]!="" || $_POST["contra"]!=""))
        {
            
            $n=$_POST['nom'];
            $c=$_POST['contra'];

            $cadena =str_replace(' ', '', $n);
            $n=$cadena;

            if(strlen($n)>3)
            {
                if(strlen($c)>3)
                {
                    insertaUsuario($n,$c);  
                }
                else
                {
                    echo "<script> imprime('La contraseña debe ser de mínimo 4 dígitos y sin espacios en blanco, verifique'); </script>";
                }
            }
            else
            {
                echo "<script> imprime('El nombre de usuario debe contener mínimo 4 caracteres y sin espacios en blanco, verifique.'); </script>";
            }
        }
        else
        {
            echo "<script> imprime('Hay campos vacíos, verifique'); </script>";
        }

        function insertaUsuario($nombreUsuario,$contrasenaUsuario)
        {
            global $con;
            $sql="";
            $sql.="CREATE user '$nombreUsuario'@'localhost' identified by '$contrasenaUsuario';"; 
            $sql.="GRANT all privileges on checada6.* to '$nombreUsuario'@localhost;"; 
            $sql.="GRANT ALL PRIVILEGES ON mysql.user to '$nombreUsuario'@localhost;"; 
            $sql.="GRANT CREATE USER ON *.* to '$nombreUsuario'@'localhost';"; 
            $sql.="FLUSH privileges;";

            //mysqli_autocommit($con, FALSE);//quitar el autocommit hasta que todo haya resultado correcto

            if (mysqli_multi_query($con, $sql)) 
            {
                do 
                {
                    /* store first result set */
                    if ($result = mysqli_store_result($con)) 
                    {
                        //liberar el resultado, IMPORTANTE PARA QUE NO DE ERROR
                        mysqli_free_result($result);
                    }
                } while (mysqli_next_result($con));

                echo "<script> imprime('Usuario guardado correctamente'); </script>";
            }
            else
            {
                $error="";
                $er1=mysqli_errno($con);
                $err1="$er1";
                $er2=mysqli_error($con);
                $err2="$er2";
                //Hacer UN EXPLODE DE ERR2
                $divide=explode("'",$err2);
                $tamDivide=count($divide);//saber el tamaño del array
                if($tamDivide>0)//si el array posee datos
                {
                    $err2="";
                    for($i=0;$i<$tamDivide;$i++)
                    {
                        $err2.=$divide[$i];
                    }
                }

                $error="Error in create. $err1 : $err2. Este error suele surgir cuando el usuario que intenta registrar ya existe, verifique. En caso de que no sea ese el problema contacte al administrador. Líneas de error: 16, 41 y 42.";
                echo "<script> imprime('$error'); </script>";
            }
        }
?>