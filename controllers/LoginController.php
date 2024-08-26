<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();

            if(empty($alertas)) {
                // verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado ) {
                    Usuario::setAlerta('error', 'el usuario no existe o no esta confirmado');
                } else {
                    // el usuario existe
                    if( password_verify($_POST['password'], $usuario->password) ) {

                        // iniciar sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // redireccionar 
                        header('location: /dashboard');

                    } else {
                        Usuario::setAlerta('error', 'password incorrecto');
                    }
                }
            }
        }
        $alertas = Usuario::getAlertas();

        // Rende a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);

    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        header('location: /');
    }

    public static function crear(Router $router) {
        $alertas = [];
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            $existeUsuario = Usuario::where('email', $usuario->email );

            if(empty($alertas)) {

                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // hashear el password
                    $usuario->hashPassword();

                    // eliminar password2
                    unset($usuario->password2);

                    // generar token
                    $usuario->crearToken();

                    // Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    // enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion(); 
                    

                    if($resultado) {
                        header('location: /mensaje');
                    }

                }
            }
        }
        // rende a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea Tu Cuenta En UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas 
        ]);
    }

    public static function olvide(Router $router) {
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();
            
            if(empty($alertas)) {
                //buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);
                
                if($usuario && $usuario->confirmado) {
                    // generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);
                    
                    // actualizar el usuario 
                    $usuario->guardar();
                    
                    // enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    
                    // imprimir alerta
                    Usuario::setAlerta('exito', 'revisa tu email');
                    
                }   else {
                    Usuario::setAlerta('error', 'el usuario no existe o no esta confirmado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        // rende a la vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvide Mi Password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router) {

        $token = s( $_GET['token']);
        $mostrar = true;

        if(!$token) header('location: /');
        
        // identificar el usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'token no valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            // añadir el nuevo password
            $usuario->sincronizar($_POST);

            // validar el password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                // hashear nuevo password
                $usuario->hashPassword();

                // eliminar el token
                $usuario->token = null;

                // guardar el usuario
                $resultado = $usuario->guardar();

                // redireccionar
                if($resultado) {
                    header('location: /');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        // rende a la vista
         $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Mi Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router) {
      

        // rende a la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
        
    }
    
    public static function confirmar(Router $router) {
        
        $token = s($_GET['token']);

        if(!$token) header('location: /');

        // encontro al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            // no se encontro token
            Usuario::setAlerta('error', 'token no valido');
        } else {
            //confirmar la cuenta
            unset($usuario->password2);
            $usuario->token = '';
            $usuario->confirmado = 1;
            
            //guardar enla base de datos
            $usuario->guardar();
            
            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');

            
        }

        $alertas = Usuario::getAlertas();

        
        
        //rende a la vista
        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar Tu Cuenta UpTask',
            'alertas' => $alertas

        ]);

        
    }

}