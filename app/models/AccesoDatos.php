<?php

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería mysqli
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */
class AccesoDatos
{

    private static $modelo = null;
    private $dbh = null;

    public static function getModelo()
    {
        if (self::$modelo == null) {
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }



    // Constructor privado  Patron singleton

    private function __construct()
    {


        $this->dbh = new mysqli(DB_SERVER, DB_USER, DB_PASSWD, DATABASE);

        if ($this->dbh->connect_error) {
            die(" Error en la conexión " . $this->dbh->connect_errno);
        }
    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo()
    {
        if (self::$modelo != null) {
            $obj = self::$modelo;
            // Cierro la base de datos
            $obj->dbh->close();
            self::$modelo = null; // Borro el objeto.
        }
    }

    public function compruebausuario($login, $contraseña)
    {
        $tuser = [];
        // Crea la sentencia preparada
        $stmt_usuarios  = $this->dbh->prepare("select * from users where login=? and passwd=?");
        // Si falla termina el programa
        if ($stmt_usuarios == false) die(__FILE__ . ':' . __LINE__ . $this->dbh->error);
        // Ejecuto la sentencia
        $stmt_usuarios->bind_param('ss', $login, $contraseña);
        $stmt_usuarios->execute();
        // Obtengo los resultados
        $result = $stmt_usuarios->get_result();
        // Si hay resultado correctos
        if ($result) {
            // Obtengo cada fila de la respuesta como un objeto de tipo Usuario
            while ($user = $result->fetch_object('Usuario')) {
                $tuser[] = $user;
            }
        }
        return $tuser;
    }


    // Devuelvo cuantos filas tiene la tabla

    public function numClientes(): int
    {
        $result = $this->dbh->query("SELECT id FROM Clientes");
        $num = $result->num_rows;
        return $num;
    }


    // SELECT Devuelvo la lista de Usuarios
    public function getClientes($primero, $cuantos, $ordenacion, $numorden): array
    {
        $tuser = [];
        // Crea la sentencia preparada
        // echo "<h1> $primero : $cuantos  </h1>";
        if ($numorden == 1) {
            $stmt_usuarios  = $this->dbh->prepare("select * from Clientes order by $ordenacion Desc limit $primero,$cuantos");
        } else {
            $stmt_usuarios  = $this->dbh->prepare("select * from Clientes order by $ordenacion  limit $primero,$cuantos");
        }
        // Si falla termina el programa
        if ($stmt_usuarios == false) die(__FILE__ . ':' . __LINE__ . $this->dbh->error);
        // Ejecuto la sentencia
        $stmt_usuarios->execute();
        // Obtengo los resultados
        $result = $stmt_usuarios->get_result();
        // Si hay resultado correctos
        if ($result) {
            // Obtengo cada fila de la respuesta como un objeto de tipo Usuario
            while ($user = $result->fetch_object('Cliente')) {
                $tuser[] = $user;
            }
        }
        // Devuelvo el array de objetos
        return $tuser;
    }


    // SELECT Devuelvo un usuario o false
    public function getCliente(int $id)
    {
        $cli = false;

        $stmt_usuario   = $this->dbh->prepare("select * from Clientes where id =?");
        if ($stmt_usuario == false) die($this->dbh->error);

        // Enlazo $login con el primer ? 
        $stmt_usuario->bind_param("i", $id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ($result) {
            $cli = $result->fetch_object('Cliente');
        }

        return $cli;
    }

    public function getClienteSiguiente($id)
    {

        $cli = false;

        $stmt_usuario   = $this->dbh->prepare("select * from Clientes where id >? limit 1");
        if ($stmt_usuario == false) die($this->dbh->error);

        // Enlazo $login con el primer ? 
        $stmt_usuario->bind_param("i", $id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ($result) {
            $cli = $result->fetch_object('Cliente');
        }

        return $cli;
    }

    public function getClienteAnterior($id)
    {

        $cli = false;

        $stmt_usuario   = $this->dbh->prepare("select * from Clientes where id <? order by id DESC limit 1");
        if ($stmt_usuario == false) die($this->dbh->error);

        // Enlazo $login con el primer ? 
        $stmt_usuario->bind_param("i", $id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ($result) {
            $cli = $result->fetch_object('Cliente');
        }

        return $cli;
    }




    // UPDATE TODO
    public function modCliente($cli): bool
    {

        $stmt_moduser   = $this->dbh->prepare("UPDATE Clientes set first_name=?,last_name=?,email=?," .
            "gender=?, ip_address=?,telefono=? WHERE id=?");
        if ($stmt_moduser == false) die($this->dbh->error);

        $stmt_moduser->bind_param(
            "ssssssi",
            $cli->first_name,
            $cli->last_name,
            $cli->email,
            $cli->gender,
            $cli->ip_address,
            $cli->telefono,
            $cli->id
        );
        $stmt_moduser->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }


    //INSERT 
    public function addCliente($cli): bool
    {

        // El id se define automáticamente por autoincremento.
        $stmt_creauser  = $this->dbh->prepare(
            "INSERT INTO `Clientes`( `first_name`, `last_name`, `email`, `gender`, `ip_address`, `telefono`)" .
                "Values(?,?,?,?,?,?)"
        );
        if ($stmt_creauser == false) die($this->dbh->error);

        $stmt_creauser->bind_param(
            "ssssss",
            $cli->first_name,
            $cli->last_name,
            $cli->email,
            $cli->gender,
            $cli->ip_address,
            $cli->telefono
        );
        $stmt_creauser->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }


    //DELETE 
    public function borrarCliente(int $id): bool
    {
        $stmt_boruser   = $this->dbh->prepare("delete from Clientes where id =?");
        if ($stmt_boruser == false) die($this->dbh->error);

        $stmt_boruser->bind_param("i", $id);
        $stmt_boruser->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }

    // FUNCIONES CON EL ID
    public function getUltimoId(): int
    {
        $result = $this->dbh->query("SELECT id FROM Clientes ORDER BY id DESC LIMIT 1");
        $id = mysqli_fetch_row($result);
        return $id[0];
    }

    public function ultimoId(): int
    {
        //SELECT `AUTO_INCREMENT` id FROM  INFORMATION_SCHEMA.TABLES  WHERE AUTO_INCREMENT IS NOT NULL and AUTO_INCREMENT >1;
        $result = $this->dbh->query("SELECT id FROM Clientes order by id desc limit 1");
        $num = $result->fetch_object();
        return $num->id;
    }


    public function getPrimerId(): int
    {
        $result = $this->dbh->query("SELECT id FROM Clientes ORDER BY id LIMIT 1");
        $id = mysqli_fetch_row($result);
        return $id[0];
    }

    public function comprobarId($id)
    {
        $result = $this->dbh->query("SELECT * FROM Clientes WHERE id = '$id'");
        $existe = true;
        if ($result->num_rows == 0) {
            $existe = false;
        }
        return $existe;
    }

    public function noexisteEmail(string $email): bool
    {
        //echo("<script>console.log('PHP: NOEXISTEeMAIL');</script>");
        $existe = true;
        // Crea la sentencia preparada
        $stmt_usuarios  = $this->dbh->prepare("select * from Clientes where email=?");
        // Si falla termina el programa
        if ($stmt_usuarios == false) die(__FILE__ . ':' . __LINE__ . $this->dbh->error);
        // Ejecuto la sentencia
        $stmt_usuarios->bind_param('s', $email);
        $stmt_usuarios->execute();
        // Obtengo los resultados
        $result = $stmt_usuarios->get_result();
        // Si hay resultado correctos
        if ($result) {
            // Obtengo cada fila de la respuesta como un objeto de tipo Usuario
            if ($user = $result->fetch_object('Cliente')) {
                $existe = false;
            }
        }
        // Devuelvo el array de objetos
        return $existe;
    }

    public function checkLogIn($user, $password)
    {
        $stmtLogIn = $this->dbh->prepare("select * from users where user=? and pass=?");
        if ($stmtLogIn == false) die($this->dbh->error);
        $stmtLogIn->bind_param("ss", $user, $password);
        $stmtLogIn->execute();
        $result = $stmtLogIn->get_result();
        if (mysqli_num_rows($result)) {
            return true;
        } else {
            return false;
        }
    }

    public function getUser($user)
    {
        $stmtUser = $this->dbh->prepare("SELECT * FROM users WHERE user=?");
        $stmtUser->bind_param("s", $user);
        $stmtUser->execute();
        $result = $stmtUser->get_result();
        $user = $result->fetch_object('User');
        return $user;
    }

    // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    {
        trigger_error('La clonación no permitida', E_USER_ERROR);
    }
}
