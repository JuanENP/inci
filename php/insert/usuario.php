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
            $sql="create user '$nombreUsuario'@'localhost' identified by '$contrasenaUsuario'"; 
            $sql2="grant all privileges on checada6.* to $nombreUsuario@localhost"; 
            $sql3="grant all privileges on mysql.user to $nombreUsuario@localhost";
            $sqlFlush="flush privileges";//despues del 2 y del 3

            mysqli_autocommit($con, FALSE);//quitar el autocommit hasta que todo haya resultado correcto
            if(!(mysqli_query($con,$sql)))
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
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
            }
            else
            {
                if(!(mysqli_query($con,$sql2)))
                {
                    echo "error in grant 1 ".mysqli_errno($con) . ": " . mysqli_error($con);
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE); 
                }
                else
                {
                    if(!(mysqli_query($con,$sqlFlush)))
                    {
                        echo "error in flush 1 ".mysqli_errno($con) . ": " . mysqli_error($con);
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                    }
                    else
                    {
                        if(!(mysqli_query($con,$sql3)))
                        {
                            echo "error in grant 2".mysqli_errno($con) . ": " . mysqli_error($con);
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                        }
                        else
                        {
                            if(!(mysqli_query($con,$sqlFlush)))
                            {
                                echo "error in flush 2 ".mysqli_errno($con) . ": " . mysqli_error($con);
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE); 
                            }
                            else
                            {
                                mysqli_commit($con);
                                mysqli_autocommit($con, TRUE);
                                echo "<script> imprime('Usuario guardado correctamente'); </script>";
                            }
                        }
                    }
                }
            }
        }
?>