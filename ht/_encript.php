<?php
    //Para generar una URL segura para el método GET
    function generaURL ($valor)
    {
        return base64_encode($valor);
    }
?>