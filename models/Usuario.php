<?php

namespace Model;

class Usuario extends ActiveRecord
{
    //Conexion DB
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado', 'emailTemp'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $token;
    public $confirmado;
    public $emailTemp;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->emailTemp = $args['emailTemp'] ?? '';
    }
    //validacion para cuentas nuevas
    public function validarCuentaNueva()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no valido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El contraseña es obligatoria';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El contraseña debe contener al menos 6 caracteres';
        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Las contraseñas no son iguales';
        }

        return self::$alertas;
    }
    public function validarLogin()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no valido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El contraseña es obligatoria';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El contraseña debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    public function validarEmail()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no valido';
        }

        return self::$alertas;
    }


    public function validarPassword()
    {
        if (!$this->password) {
            self::$alertas['error'][] = 'El contraseña es obligatoria';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El contraseña debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    public function validarCambiosUsuario()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no valido';
        }
        return self::$alertas;
    }

    public function nuevoPassword() : array
    {
        if (!$this->password_actual) {
            self::$alertas['error'][] = 'La constrasña actual no puede ir vacia';
        }
        if (!$this->password_nuevo) {
            self::$alertas['error'][] = 'La constrasña nueva no puede ir vacia';
        }
        if (strlen($this->password_nuevo) < 6) {
            self::$alertas['error'][] = 'El contraseña nueva debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }
    //Comprobar password
    public function comprobarPassword(): bool
    {
        return password_verify($this->password_actual, $this->password);
    }
    //Hashea el password
    public function hashPassword() : void
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //Generar un token
    public function crearToken() : void
    {
        $this->token = md5(uniqid());
    }
}
