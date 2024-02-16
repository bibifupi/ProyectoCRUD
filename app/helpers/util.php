<?php
function getLoca($ip)
{


    $infoip = "http://ip-api.com/php/" . $ip;

    //Lee y guarda ip
    $ipinfo = unserialize(file_get_contents($infoip));

    if (isset($ipinfo['lat']) && isset($ipinfo['lon'])) {
        $lat = $ipinfo['lat'];
        $lon = $ipinfo['lon'];

        $localizacion = "https://maps.google.com/maps?q=$lat,$lon&z=15&output=embed";
    } else {
        return "../uploads/anon.png";
    }
    return $localizacion;
}

//Funcion que devuelve la ruta de la bandera según la ip
function getbandera($ip)
{

    //Ruta a la info de la ip
    $infoip = "http://ip-api.com/php/" . $ip;

    //Lectura y guradado de los datos de ip
    $ipinfo = unserialize(file_get_contents($infoip));
    // var_dump($ipinfo);

    if (isset($ipinfo['countryCode'])) {

        //Codigo de pais
        $pais = strtolower($ipinfo['countryCode']);

        //Bandera del pais 
        $bandera = "https://flagpedia.net/data/flags/w702/" . $pais . ".webp";
    } else {
        return "./app/uploads/anon.png";
    }
    return $bandera;
}

// Se comprueba si la imagen es válida para subirlo al servidor
function imagenok($img)
{

    $nombre = $img['name'];
    $tipo = $img['type'];
    $tam = $img['size'];
    $tmp = $img['tmp_name'];
    $error = $img['error'];
    $destino = "./app/uploads/" . $nombre;

    if (($error == 0) && ($tam <= 3000000) && ($tipo == "image/jpeg")) {
        return true;
    }

    return false;
};

function get_errorimg($img)
{
    // se incluyen esta tabla de  códigos de error que produce la subida de archivos en PHPP
    // Posibles errores de subida segun el manual de PHP
    $codigosErrorSubida = [
        UPLOAD_ERR_OK         => 'Subida correcta',  // Valor 0
        UPLOAD_ERR_INI_SIZE   => 'El tamaño del archivo excede el admitido por el servidor',  // directiva upload_max_filesize en php.ini
        UPLOAD_ERR_FORM_SIZE  => 'El tamaño del archivo excede el admitido por el cliente',  // directiva MAX_FILE_SIZE en el formulario HTML
        UPLOAD_ERR_PARTIAL    => 'El archivo no se pudo subir completamente',
        UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo para ser subido',
        UPLOAD_ERR_NO_TMP_DIR => 'No existe un directorio temporal donde subir el archivo',
        UPLOAD_ERR_CANT_WRITE => 'No se pudo guardar el archivo en disco',  // permisos
        UPLOAD_ERR_EXTENSION  => 'Una extensión PHP evito la subida del archivo',  // extensión PHP

    ];

    $nombreFichero = $img['name'];
    $tipoFichero = $img['type'];
    $tamFichero = $img['size'];
    $errorFichero = $img['error'];
    $directorioSubida = './app/uploads';
    $mensaje = '';

    // Obtengo el código de error de la operación, 0 si todo ha ido bien
    if ($errorFichero > 0) {
        return   $mensaje .= "Se ha producido el error nº $errorFichero: <em>"
            . $codigosErrorSubida[$errorFichero] . '</em> <br />';
    }

    /* // Compruebo si el fichero existe en el directorio de destino
     if (file_exists($directorioSubida . '/' . $nombreFichero)) {
        $mensaje = 'El archivo ya existe en el servidor';
        return false;
    } */

    if ($tamFichero >= 3000000) {
        return $mensaje .= 'ERROR: El tamaño de la imagen es demasiado grande <br />';
    }

    if (($tipoFichero != "image/jpeg")) {
        return $mensaje .= 'ERROR: El tipo de imagen no es correcto <br />';
    }
    if (!is_dir($directorioSubida) || !is_writable($directorioSubida)) {
        return $mensaje .= 'ERROR: No existe el directorio o no se tiene permiso de escritura <br />';
    }

    return $mensaje;
}

//Subo la imagen al servidor
function up_img($img, $id)
{
    $ndig = strlen($id);
    $nombreFichero = generanombreimg($ndig, $id) . ".jpg";
    $temporalFichero = $img['tmp_name'];
    $directorioSubida = './app/uploads';
    $mensaje = '';
    //Intento mover el archivo temporal al directorio indicado
    if (move_uploaded_file($temporalFichero,  $directorioSubida . '/' . $nombreFichero) == true) {
        $mensaje .= 'Archivo guardado en: ' . $directorioSubida . '/' . $nombreFichero . ' <br />';
    } else {
        $mensaje .= 'ERROR: Archivo no guardado correctamente <br />';
    }
    return $mensaje;
} 

function getFoto($id)
{
    $nnum = strlen($id);
    $foto = "./app/uploads/" . generanombreimg($nnum, $id) . ".jpg";
    //Foto por defecto
    $fdefecto = "https://robohash.org/" . $id . "?set=set4";

    if (file_exists($foto)) {
        return $foto;
    }

    return $fdefecto;
}




function generanombreimg($digitos, $id)
{
    $ceros = 8 - $digitos;
    $nombreimg = "";
    for ($i = 0; $i < $ceros; $i++) {
        $nombreimg .= "0";
    }
    $nombreimg .= $id;
    return $nombreimg;
}


/*
 *  Funciones para limpiar la entrada de posibles inyecciones
 */

function limpiarEntrada(string $entrada): string
{
    $salida = trim($entrada); // Elimina espacios antes y después de los datos
    $salida = strip_tags($salida); // Elimina marcas
    return $salida;
}
// Función para limpiar todos elementos de un array
function limpiarArrayEntrada(array &$entrada)
{

    foreach ($entrada as $key => $value) {
        $entrada[$key] = limpiarEntrada($value);
    }
}
