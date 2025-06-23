<?php

namespace Controllers;

use Clases\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{

    public static function login(Router $router){
        $alertas = [];
        $auth = new Usuario();
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                //Comprobar que exista usuario
                $usuario = Usuario::where("email",$auth->email);

                //Si el usuario existe
                if($usuario){
                    //Verificar el password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)){
                        //Autenticar el usuario
                        //Se inician las variables de sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre." ".$usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        //Redireccionamiento en base al tipo de usuario
                        if($usuario->admin === "1"){
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header("Location: /admin");
                        } else {
                            header("Location: /cita");
                        }
                    }
                } else { //Si el usuario no existe
                    Usuario::setAlerta("error","Usuraio no econtrado");
                }
            }
        }

        //Reasigna la variable alertas con las nuevas en case de haber si generadas en los métodos anteriores
        $alertas = Usuario::getAlertas();
        $router->render("auth/login",[
            "alertas" => $alertas,
            "auth" => $auth
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header("Location: /");
    }

    public static function olvide(Router $router){
        $alertas = [];
        //Se revisa si se accedio a la URL por método post
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            //Comprueba si el email fue colocado en el form
            if(empty($alertas)){
                $usuario = Usuario::where("email",$auth->email);
                //Verifica si el eamil o si el usuario esta verificado en la BD
                if($usuario && $usuario->confirmado === "1"){
                    //Generar un Token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //Enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();
                    //Alerta exito
                    Usuario::setAlerta("exito","Revisa tu email");


                } else {
                    //Agrega el error en caso contrario
                    Usuario::setAlerta("error","El usuario no existe o no esta confirmado");
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render("auth/olvide-password",[
            "alertas" => $alertas
        ]);
    }

    public static function recuperar(Router $router){
        $alertas = [];
        $error = false;
        //Se obtiene el token que esta en la URL
        $token = s($_GET["token"]);    
        //Buscar token en la BD(Se obtiene el usuario de la BD)
        $usuario = Usuario::where('token',$token);
        //Comprueba si ese token(usuario) existe en la BD
        if(empty($usuario)){
            Usuario::setAlerta("error","Token no válido");
            $error = true;
        }
        //Comprueba desde el tipo de método de la dirección
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            //Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            //Se valida el password
            $alertas = $password->validarPassword();
            //Si no hay erroresde validación
            if(empty($alertas)){
                $usuario->password = null;
                //Se asigna el nuevo password
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado){
                    header("Location: /");
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render("auth/recuperar-password",[
            "alertas" => $alertas,
            "error" => $error,
        ]);
    }

    public static function crear(Router $router){
        //Creación del modelo asociado a la URL/Método
        $usuario = new Usuario();

        //Alertas vacías
        $alertas = [];
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //Revisar que la lareta este vacía
            if(empty($alertas)){
                //Verifica que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                } else {
                    //Guardar en la BD el usuario

                    //Se encripta la contraseña
                    $usuario->hashPassword();

                    //Se genera la confirmación por token
                    $usuario->crearToken();

                    //Envíar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

                    //Crear usuario
                    $resultado = $usuario->guardar();
                    if($resultado){
                        header("Location: /mensaje");
                    }
                }
            }
        }

        $router->render("auth/crear-cuenta",[
            'usuario' => $usuario,
            "alertas" =>$alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render("auth/mensaje");
    }

    public static function confirmar(Router $router){
        $alertas = [];

        $token = s($_GET["token"]);
        $usuario = Usuario::where('token',$token);
    

        if(empty($usuario)){
            //Mostrar mensaje de error
            Usuario::setAlerta('error','Token no Válido');
        } else {
            //Modificar a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito','Cuenta Comprobada Correctamente');
        }
        //Obtener alertas
        $alertas = Usuario::getAlertas();
        //Rendereizar vista
        $router->render('auth/confirmar-cuenta',[
            'alertas' => $alertas
        ]);
    }
}