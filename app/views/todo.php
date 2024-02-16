<?php
switch ($error) {
    case 0:
        echo '<h1>'.$msg.'</h1>';
        echo '<button onclick="history.back()"> Volver </button>';
        break;
    case 1:
        echo '<h1> El correo no es válido o está repetido </h1>';
        echo '<button onclick="history.back()"> Volver </button>';
        break;

    case 2:
        echo '<h1> La IP no es válida </h1>';
        echo '<button onclick="history.back()"> Volver </button>';
        break;

    case 3:
        echo '<h1> El teléfono no es válido (Por ejempl: 999-999-9999) </h1>';
        echo '<button onclick="history.back()"> Volver </button>';
        break;

    default : 
        echo '<h1>'.$msg.'</h1>';
        echo '<button onclick='.'location.href="http://localhost/PHP/PROYECTO/"'.'> Principal </button>';
        break;
    
}

?>