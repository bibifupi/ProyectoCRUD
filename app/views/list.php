<form>
    <button type="submit" name="orden" value="Nuevo"> Cliente Nuevo </button><br>
</form>
<br>

<table>
    <tr>
        <th><a href="?orden=Ordenar&valor=id">id</a></th>
        <th><a href="?orden=Ordenar&valor=first_name">first_name</a></th>
        <th><a href="?orden=Ordenar&valor=email">e-mail</a></th>
        <th><a href="?orden=Ordenar&valor=gender">gender</a></th>
        <th><a href="?orden=Ordenar&valor=ip_address">ip_address</a></th>
        <th><a href="?orden=Ordenar&valor=telefono">teléfono</a></th>
    </tr>
    <?php foreach ($tvalores as $valor) : ?>
        <tr>
            <td><?= $valor->id ?> </td>
            <td><?= $valor->first_name ?> </td>
            <td><?= $valor->email ?> </td>
            <td><?= $valor->gender ?> </td>
            <td><?= $valor->ip_address ?> </td>
            <td><?= $valor->telefono ?> </td>
            <td><a href="#" onclick="confirmarBorrar('<?= $valor->first_name ?>',<?= $valor->id ?>);">Borrar</a></td>
            <td><a href="?orden=Modificar&id=<?= $valor->id ?>">Modificar</a></td>
            <td><a href="?orden=Detalles&id=<?= $valor->id ?>">Detalles</a></td>

        <tr>
        <?php endforeach ?>
</table>

<form>
    <br>
    <button type="submit" name="nav" value="Primero">
        << </button>
            <button type="submit" name="nav" value="Anterior">
                < </button>
                    <button type="submit" name="nav" value="Siguiente"> > </button>
                    <button type="submit" name="nav" value="Ultimo"> >> </button>
</form>