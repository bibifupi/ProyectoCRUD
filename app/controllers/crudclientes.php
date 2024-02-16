<?php

function crudBorrar($id)
{
    $db = AccesoDatos::getModelo();
    $resu = $db->borrarCliente($id);
    if ($resu) {
        $_SESSION['msg'] = " El usuario " . $id . " ha sido eliminado.";
    } else {
        $_SESSION['msg'] = " Error al eliminar el usuario " . $id . ".";
    }
}

function crudTerminar()
{
    AccesoDatos::closeModelo();
    session_destroy();
}

function crudAlta()
{
    $cli = new Cliente();
    $orden = "Nuevo";
    $ruta = "";
    include_once "app/views/formulario.php";
}

function crudDetalles($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $ruta = getFoto($id);
    $bandera = getbandera($cli->ip_address);
    include_once "app/views/detalles.php";
}

function crudDetallesSiguiente($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    if ($cli) {
        $ruta = getFoto($cli->id);
        $bandera = getbandera($cli->ip_address);
        include_once "app/views/detalles.php";
    } else {
        cruddetalles($id);
    }
}

function crudDetallesAnterior($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    if ($cli) {
        $ruta = getFoto($cli->id);
        $bandera = getbandera($cli->ip_address);
        include_once "app/views/detalles.php";
    } else {
        cruddetalles($id);
    }
}


function crudModificar($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $orden = "Modificar";
    $ruta = getFoto($cli->id);
    include_once "app/views/formulario.php";
}

function crudModificarSiguiente($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    $orden = "Modificar";
    if ($cli) {
        $ruta = getFoto($cli->id);
        include_once "app/views/formulario.php";
        echo ("<script>console.log('PHP: if modificar siguiente');</script>");
    } else {
        echo ("<script>console.log('PHP: selse modificar siguiente');</script>");
        crudModificar($id);
    }
}

function crudModificarAnterior($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    $orden = "Modificar";
    if ($cli) {
        $ruta = getFoto($cli->id);
        include_once "app/views/formulario.php";
        echo ("<script>console.log('PHP: if modificar anterior');</script>");
    } else {
        echo ("<script>console.log('PHP: else modificar anterior');</script>");
        crudModificar($id);
    }
}

function crudPostAlta()
{
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    $db = AccesoDatos::getModelo();
    $cli = new Cliente();
    $error = 0;
    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->gender        = $_POST['gender'];

    if ((filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) && ($db->noexisteEmail($_POST['email']))) {
        $cli->email = $_POST['email'];
    } else {

        $error = 1;
        include_once "app/views/todo.php";
    }
    if (filter_var($_POST['ip_address'], FILTER_VALIDATE_IP)) {
        $cli->ip_address = $_POST['ip_address'];
    } else {
        $error = 2;
        include_once "app/views/todo.php";
    }
    if (preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $_POST['telefono'])) {
        $cli->telefono = $_POST['telefono'];
    } else {
        $error = 3;
        include_once "app/views/todo.php";
    }
    $db = AccesoDatos::getModelo();
    if ($error == 0) {
        if ($db->addCliente($cli) && !$error) {
            $_SESSION['msg'] = " El usuario " . $cli->first_name . " se ha dado de alta ";
        } else {
            $_SESSION['msg'] = " Error al dar de alta al usuario " . $cli->first_name . ".";
        }
    }
    //Si se ha enviado una imagen 
    if (isset($_FILES['imagen']['name'])) {
        $error = "";
        //Si la imagen pasa las condiciones y no hay ningún error 
        echo $error;
        if (imagenok($_FILES['imagen']) && ($error == "")) {
            //Añado el cliente
            $db->addCliente($cli);
            //Subo la imagen mediante la id de usuario subido(autoincremento) y muestro el msg correspondiente
            $msg = up_img($_FILES['imagen'], $db->ultimoId());
            include_once "app/views/todo.php";
            //Sino muestro el error que ha tenido la imagen
        } else {
            echo "no entre";
            $error = 0;
            echo $error;
            $msg = get_errorimg($_FILES['imagen']);
            include_once "app/views/todo.php";
        }
    }
}

function crudPostModificar()
{
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    $cli = new Cliente();
    $db = AccesoDatos::getModelo();
    $error = 0;
    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->gender        = $_POST['gender'];
    //comprobación email
    if ((filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) /*&& ($db->noexisteEmail($_POST['email']))*/) {
        $cli->email = $_POST['email'];
    } else {
        $error = 1;
        include_once "app/views/todo.php";
    }
    //comprobacion ip
    if (filter_var($_POST['ip_address'], FILTER_VALIDATE_IP)) {
        //echo ("<script>console.log('PHP: if ipvalidado');</script>");
        $cli->ip_address = $_POST['ip_address'];
    } else {
        //echo ("<script>console.log('PHP: if no ipvalidado');</script>");
        $error = 2;
        include_once "app/views/todo.php";
    }
    //comprobacion teléfono
    if (preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $_POST['telefono'])) {
        //echo ("<script>console.log('PHP: if telefonovalidado');</script>");
        $cli->telefono = $_POST['telefono'];
    } else {
        //echo ("<script>console.log('PHP: else telefonovalidado');</script>");
        $error = 3;
        include_once "app/views/todo.php";
    }

    if ($error == 0) {
        if ($db->modCliente($cli)) {
            $_SESSION['msg'] = " El usuario ha sido modificado";
        } else {
            $_SESSION['msg'] = " Error al modificar el usuario ";
        }
    }
    //Si se ha enviado una imagen 
    if (isset($_FILES['imagen']['name']) && ($_FILES['imagen']['name'] != "")) {
        print_r($_FILES['imagen']);
        $error = "";
        //Si la imagen pasa las condiciones y no hay ningún error 
        if (imagenok($_FILES['imagen']) && ($error == "")) {
            //Subo la imagen mediante la id de usuario subido(autoincremento) y muestro el msg correspondiente
            $msg = up_img($_FILES['imagen'], $cli->id);
            include_once "app/views/todo.php";
            //Sino muestro el error que ha tenido la imagen
        } else {
            echo "no entre";
            $error = 0;
            echo $error;
            $msg = get_errorimg($_FILES['imagen']);
            include_once "app/views/todo.php";
        }
    }
}
function  logIn($user, $password)
{
    //comprobamos que el usuario y la contraseña son correctos
    $encryptPass = md5($password, false);
    $db = AccesoDatos::getModelo();
    if ($db->checkLogIn($user, $encryptPass)) {
        return true;
    } else {
        return false;
    }
}
function obtenerUserName($user)
{
    $db = AccesoDatos::getModelo();
    $user = $db->getUser($user);
    $userName = $user->user;
    return $userName;
}

//Obtener el rol
function obtenerRol($user)
{
    $db = AccesoDatos::getModelo();
    $user = $db->getUser($user);
    $rol = $user->rol;
    return $rol;
}
