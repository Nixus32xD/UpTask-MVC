<?php

namespace Controllers;

use Classes\Email;
use Model\Proyecto;
use Model\Tarea;
use Model\Usuario;
use MVC\Router;

class DashboardController
{

    public static function index(Router $router)
    {
        session_start();
        isAuth();
        $id = $_SESSION['id'];
        $usuario = Usuario::where('id', $id);
        isAuthMail($usuario->email);
        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router)
    {
        session_start();
        isAuth();
        $usuario = Usuario::where('id', $_SESSION['id']);
        isAuthMail($usuario->email);
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);
            //validacion
            $alertas = $proyecto->validarProyecto();

            if (empty($alertas)) {
                //Generar una Url unica
                $proyecto->url = md5(uniqid());

                //Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                //Guardar Proyecto
                $proyecto->guardar();
                //Redireccionar
                header('Location: /proyecto?url=' . $proyecto->url);
            }
        }
        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router)
    {
        session_start();
        isAuth();
        $usuario = Usuario::where('id', $_SESSION['id']);
        isAuthMail($usuario->email);

        $token = $_GET['id'];
        if (!$token) header('Location: /dashboard');
        //Revisar que la persona que visita el proyecto sea el q lo creo
        $proyecto = Proyecto::where('url', $token);
        if ($proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }

    public static function perfil(Router $router)
    {
        session_start();
        isAuth();
        $usuario = Usuario::where('id', $_SESSION['id']);
        isAuthMail($usuario->email);

        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);
        $mail = $usuario->email;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarCambiosUsuario();

            if (empty($alertas)) {
                //Buscar Usuario con el mismo mail
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    //Mensaje de error
                    Usuario::setAlerta('error', 'Email no valido, ya se encuentra registrado');
                } else {
                    //Verificar El mail cambiado
                    if ($_SESSION['email'] !== $usuario->email) {
                        $usuario->emailTemp = $usuario->email;

                        Usuario::setAlerta('pendiente', 'Por favor Verifique el Mail para Confirmar que es Usted y Validar el Cambio de Email');
                        $usuario->crearToken();
                        $email = new Email($usuario->nombre, $usuario->emailTemp, $usuario->token);

                        $email->cambiarMail();
                        $usuario->email = $mail;
                    }
                    //Guardar Cambios
                    $resultado = $usuario->guardar();
                    $_SESSION['nombre'] = $usuario->nombre;
                    if ($resultado) {
                        Usuario::setAlerta('exito', 'Datos Actualizados Correctamente');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiarPassword(Router $router)
    {
        session_start();
        isAuth();
        $usuario = Usuario::where('id', $_SESSION['id']);
        isAuthMail($usuario->email);
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);

            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevoPassword();

            if (empty($alertas)) {
                $resultado = $usuario->comprobarPassword();

                if ($resultado) {
                    //Asignar el nuevo password

                    $usuario->password = $usuario->password_nuevo;

                    //Eliminar Propiedades no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);
                    //Hashear el nuevo password
                    $usuario->hashPassword();

                    //Actualizar contraseña
                    $resultado = $usuario->guardar();
                    if ($resultado) {
                        Usuario::setAlerta('exito', 'Contraseña Actualizado Correctamente');
                    }
                } else {
                    Usuario::setAlerta('error', 'Contraseña Incorrecta');
                }

                $alertas = Usuario::getAlertas();
            }
        }

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Contraseña',
            'alertas' => $alertas
        ]);
    }


    public static function eliminar()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            $proyecto = Proyecto::where('url', $_POST['url']);

            $tareas = Tarea::belongsTo('proyectoId', $proyecto->id);

            if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al borrar el proyecto'
                ];
                echo json_encode($respuesta);
                return;
            }
            if ($tareas) {
                foreach ($tareas as $tarea) {
                    $tarea->eliminar();
                }
            }
            $resultado = $proyecto->eliminar();
            
            $respuesta = [
                'tipo' => 'exito',
                'resultado' => $resultado,
                'mensaje' => 'Eliminado correctamente'
            ];

            echo json_encode($respuesta);
        }
    }
}
