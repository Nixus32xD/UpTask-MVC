<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{

    public static function login(Router $router)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                //Verificar que el usuario exista
                $usuario = Usuario::where('email', $auth->email);

                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El email no existe o no esta confirmado');
                } else {
                    //El usuario Existe
                    if (password_verify($auth->password, $usuario->password)) {
                        //Iniciar Session
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionar
                        header('Location: /dashboard');
                    } else {
                        Usuario::setAlerta('error', 'Contraseña Incorrecta');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        //Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }

    public static function crear(Router $router)
    {

        $usuario = new Usuario();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarCuentaNueva();

            if (empty($alertas)) {
                $existeUsuario = $usuario::where('email', $usuario->email);

                if ($existeUsuario) {
                    //Verificar que no este registrado el correo
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    //Hashear password
                    $usuario->hashPassword();

                    //Eliminar password 2
                    unset($usuario->password2);
                    //Generar token
                    $usuario->crearToken();
                    //Crear un nuevo usuario
                    $resultado = $usuario->guardar();
                    //Enviar email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        //Render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crear tu Cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas)) {
                //Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if ($usuario && $usuario->confirmado) {
                    //Encontro el usuario
                    //Generar un nuevo Token
                    unset($usuario->password2);
                    $usuario->crearToken();
                    //Actualizar el usuario
                    $usuario->guardar();
                    //Enviar email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();
                    //Imprimir la alerta
                    $alertas = Usuario::setAlerta('exito', 'Instrucciones enviadas correctamente');
                } else {
                    Usuario::setAlerta('error', 'El Usuario no Existe o No esta Confirmado');
                }
                $alertas = Usuario::getAlertas();
            }
        }

        //Render a la vista
        $router->render('auth/olvide', [
            'titulo' => 'Recupera tu Contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function confirmarMail(Router $router)
    {
        $token = s($_GET['token']);

        if (!$token) header('Location: /');
        //Identificar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token No valido');
        } else {

            $usuario->email = $usuario->emailTemp;
            unset($usuario->emailTemp);
            unset($usuario->token);

            $usuario->guardar();

            Usuario::setAlerta('exito', 'Email Cambiado Correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-mail', [
            'titulo' => 'Confirmar Mail',
            'alertas' => $alertas,
        ]);
    }
    public static function reestablecer(Router $router)
    {

        $token = s($_GET['token']);
        $mostrar = true;

        if (!$token) header('Location: /');
        //Identificar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token No valido');
            $mostrar = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Añadir el nuevo password
            $usuario->sincronizar($_POST);
            //Validar el password
            $alertas = $usuario->validarPassword();

            if (empty($alertas)) {
                //Hashear el password
                $usuario->hashPassword();
                //Eliminar el token
                $usuario->token = null;
                //Guardar usuario
                $resultado = $usuario->guardar();
                //Redireccionar
                if ($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/reestablecer', [
            'titulo' => 'Restablece tu Contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router)
    {
        //Render a la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada exitosamente'
        ]);
    }

    public static function confirmar(Router $router)
    {
        $token = s($_GET['token']);

        if (!$token) {
            header('Location: /');
        }
        //Encontrar el usuario con ese token
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            //No se encontro el usuario con ese token
            Usuario::setAlerta('error', 'Token No Valido');
        } else {
            //confirmar la cuenta
            $usuario->confirmado = 1;
            unset($usuario->password2);
            $usuario->token = null;

            //Guardar en la base de datos
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Confirmada con exito');
        }

        $alertas = Usuario::getAlertas();
        //Render a la vista
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu Cuenta UpTask',
            'alertas' => $alertas
        ]);
    }
}
