<?php

namespace Model;

class Usuario extends ActiveRecord{
    //Base de datos
    /*Aquí se esta haciendo la representación de los datos
    de una tabla en el código, en cierta forma es un ORM */
    protected static $tabla = "usuarios";
    protected static $columnasDB = ['id','nombre','apellido','email', 'password', 
    'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '0';
    }

    //Mensajes de validación para la creación de usuarios
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][] = "El nombre es obligatorio";
        }
        if(!$this->apellido){
            self::$alertas['error'][] = "El apellido es obligatorio";
        }
        if(!$this->email){
            self::$alertas['error'][] = "El email es obligatorio";
        }
        if(!$this->password){
            self::$alertas['error'][] = "El password es obligatorio";
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = "El password debe contener almenos 6 caracteres";
        }
        return self::$alertas;
    }

    //Valida el inicio de sesión
    public function validarLogin(){
        /*Si el objeto que es creado cuando se incia sesión con el formulario
        no tiene los campos de este, se llenara el arreglos de alertas
        para mostrarlas en la vista */
        if(!$this->email){
            self::$alertas["error"][] = "El email es Obligatorio";
        }
        if(!$this->password){
            self::$alertas["error"][] = "El password es Obligatorio";
        }
        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas["error"][] = "El email es Obligatorio";
        }
        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas["error"][] = "El password es olbligatorio";
        } 
        if(strlen($this->password) < 6){
            self::$alertas["error"][] = "El password debe de tener al menos 6 caracteres";
        } 
        return self::$alertas;
    }

    //Verificar usuario
    public function existeUsuario(){
        $query = "SELECT * FROM ".self::$tabla." WHERE email = '".$this->email."' LIMIT 1";
        $resultado = self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas["error"][] = "El usuario ya esta registrado";
        }
        return $resultado;
    }


    //Encriptar password
    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //Confirmación por token
    public function crearToken(){
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($pasword){
        $resultado = password_verify($pasword,$this->password);
        
        if(!$this->confirmado){
            self::$alertas["error"][] = "Aun no a confirmado su cuenta";
            return false;
        }
        if(!$resultado){
            self::$alertas["error"][] = "Contraseña incorrecta";
            return false;
        } 
        else {
            return true;
        }
    }
}