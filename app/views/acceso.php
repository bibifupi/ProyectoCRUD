<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link href="web/css/default.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div id="container" style="width: 300px;">
        <div id="header">
            <h1>Formulario de acceso</h1>
        </div>
        <div id="content">
            <b><?= $contenido ?></b>
            <hr>
            <form method="get">
                <table>
                    <tr>
                        <td>Usuario:</td>
                        <td><input type="text" name="login" id="usuario" /></td>
                    </tr>
                    <tr>
                        <td>Contraseña:</td>
                        <td><input type="password" name="clave" id="password" /></td>
                    </tr>

                </table>
                <br>
                <input type="submit" value="Enviar">
            </form>
        </div>
    </div>
</body>

</html>