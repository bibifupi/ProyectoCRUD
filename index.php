<?php
session_start();
define('FPAG', 10); // Número de filas por página


require_once 'app/helpers/util.php';
require_once 'app/config/configDB.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/AccesoDatosPDO.php';
require_once 'app/controllers/crudclientes.php';

$midb = AccesoDatos::getModelo();

if (!isset($_SESSION['login']) && !isset($_SESSION['clave'])) {

    if (!empty($_GET['login']) && !empty($_GET['clave'])) {

        $tusuario = $midb->compruebausuario($_GET['login'], $_GET['clave']);
        if ((sizeof($tusuario) != 0) && $tusuario[0]->login == $_GET['login'] && $tusuario[0]->passwd == $_GET['clave']) {
            $_SESSION['rol'] = $tusuario[0]->rol;
            $_SESSION['login'] = $_GET['login'];
            $_SESSION['clave'] = $_GET['clave'];
            $_SESSION['nombre'] = $tusuario[0]->Nombre;
        }
        // user falso
        else {
            $contenido = "El nombre de usuario y/o la contraseña no son válidos";
            include_once('app/views/acceso.php');
            exit();
        }
    } else {
        $contenido = " Introduzca su nombre de usuario y  contraseña";
        include_once('app/views/acceso.php');
        exit();
    }
}

$nombre = $_SESSION['nombre'];
$rol = $_SESSION['rol'];

//---- PAGINACIÓN ----
$totalfilas = $midb->numClientes();
if ($totalfilas % FPAG == 0) {
    $posfin = $totalfilas - FPAG;
} else {
    $posfin = $totalfilas - $totalfilas % FPAG;
}

if (!isset($_SESSION['posini'])) {
    $_SESSION['posini'] = 0;
}
$posAux = $_SESSION['posini'];
//------------
if ((!isset($_SESSION['contorden']) || ($_SESSION['contorden'] == 2))) {
    $_SESSION['contorden'] = 0;
}

//Ordenacion por defecto
if (!isset($_SESSION['ordenacion'])) {
    $_SESSION['ordenacion'] = "id";
}

// Borro cualquier mensaje "
//$_SESSION['msg'] = " ";

ob_start(); // La salida se guarda en el bufer
if ($_SERVER['REQUEST_METHOD'] == "GET") {

    // Proceso las ordenes de navegación
    if (isset($_GET['nav'])) {
        switch ($_GET['nav']) {
            case "Primero":
                $posAux = 0;
                break;
            case "Siguiente":
                $posAux += FPAG;
                if ($posAux > $posfin) $posAux = $posfin;
                break;
            case "Anterior":
                $posAux -= FPAG;
                if ($posAux < 0) $posAux = 0;
                break;
            case "Ultimo":
                $posAux = $posfin;
        }
        $_SESSION['posini'] = $posAux;
    }


    // Proceso las ordenes de navegación en detalles
    if (isset($_GET['nav-detalles']) && isset($_GET['id'])) {
        switch ($_GET['nav-detalles']) {
            case "Siguiente":
                crudDetallesSiguiente($_GET['id']);
                break;
            case "Anterior":
                crudDetallesAnterior($_GET['id']);
                break;
        }
    }

    // Proceso las ordenes de navegación en modificar
    // if (isset($_GET['nav-modificar']) && isset($_GET['id'])) {
    //     switch ($_GET['nav-modificar']) {
    //         case "Siguiente":
    //             crudModificarSiguiente($_GET['id']);
    //             break;
    //         case "Anterior":
    //             crudModificarAnterior($_GET['id']);
    //             break;
    //     }
    // }

    // Proceso de ordenes de CRUD clientes
    if (isset($_GET['orden'])) {
        switch ($_GET['orden']) {
            case "Nuevo":
                crudAlta();
                break;
            case "Borrar":
                crudBorrar($_GET['id']);
                break;
            case "Modificar":
                crudModificar($_GET['id']);
                break;
            case "Detalles":
                crudDetalles($_GET['id']);
                break;
            case "Terminar":
                crudTerminar();
                break;
            case "Ordenar":
                $_SESSION['ordenacion'] = $_GET['valor'];
                $_SESSION['contorden']++;
                break;
        }
    }
}
// POST Formulario de alta o de modificación
else {
    if (isset($_POST['nav-modificar']) && isset($_POST['id'])) {
        switch ($_POST['nav-modificar']) {
            case "Siguiente":
                crudModificarSiguiente($_POST['id']);
                break;
            case "Anterior":
                crudModificarAnterior($_POST['id']);
                break;
        }
    }
    if (isset($_POST['orden'])) {
        switch ($_POST['orden']) {
            case "Nuevo":
                crudPostAlta();
                break;
            case "Modificar":
                crudPostModificar();
                break;
            case "Detalles":; // No hago nada
            case "Siguiente":
                crudModificarSiguiente($_POST['id']);
                break;
            case "Anterior":
                crudModificarAnterior($_POST['id']);
                break;
        }
    }
    
}

// Si no hay nada en la buffer 
// Cargo genero la vista con la lista por defecto
if (ob_get_length() == 0) {
    $db = AccesoDatos::getModelo();
    $ordenacion = $_SESSION['ordenacion'];
    $posini = $_SESSION['posini'];
    $contorden = $_SESSION['contorden'];
    $tvalores = $db->getClientes($posini, FPAG, $ordenacion, $contorden);
    require_once "app/views/list.php";
}
$contenido = ob_get_clean();
//$msg = $_SESSION['msg'];
// Muestro la página principal con el contenido generado
require_once "app/views/principal.php";
