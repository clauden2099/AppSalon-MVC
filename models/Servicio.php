<?php

namespace Model;

class Servicio extends ActiveRecord{
    //Base de datos
    protected static $tabla = "servicios";
    protected static $columnasDB = ["id", "nombre", "precio"];

    public $id;
    public $nombre;
    public $precio;

    public function __construct($args = [])
    {
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->precio = $args["precio"] ?? "";
    }

    public function validar(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre del servicio es Obligatorio';
        } 
        if(!$this->precio){
            self::$alertas['error'][] = 'El percio del servicio es Obligatorio';
        }
        if(!is_numeric($this->precio)){
            self::$alertas['error'][] = 'El percio no es valido';
        }
        return self::$alertas;
    }
}