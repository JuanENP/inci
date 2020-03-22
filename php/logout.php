<?php
session_start();
//Destruir la sesión para que obligue a iniciar desde el index otra vez.
session_destroy();
header("Location: ../index.html");
die();
?>