<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Dotenv\Dotenv as Dotenv;

$dotenv = Dotenv::createImmutable('../includes/.env');
$dotenv->safeLoad();

class Email
{

    public $email;
    public $nombre;
    public $token;

    public function __construct($nombre, $email, $token)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token;
    }

    public function enviarConfirmacion()
    {
        //Crear el objeto de mail version localhost
        // $mail = new PHPMailer();
        // $mail->isSMTP();
        // $mail->Host = 'smtp.mailtrap.io';
        // $mail->SMTPAuth = true;
        // $mail->Port = 2525;
        // $mail->Username = '8e2976aa37bd1c';
        // $mail->Password = '38bd8fbf5e4da8';
        //Crear el objeto de mail version deploy
        // $mail = new PHPMailer();
        // $mail->isSMTP();
        // $mail->Host = $_ENV['MAIL_HOST'];
        // $mail->SMTPAuth = true;
        // $mail->Port = $_ENV['MAIL_PORT'];
        // $mail->Username = $_ENV['MAIL_USER'];
        // $mail->Password = $_ENV['MAIL_PASSWORD'];

        //Crear el objeto de mail version gmail
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->Port = $_ENV['MAIL_PORT'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $email = $_ENV['EMAIL'];
        $mail->Username = $email;
        $mail->Password = $_ENV['EMAIL_PASSWORD'];
        $mail->setFrom($_ENV['EMAIL'], 'UpTask');
        $mail->addAddress($this->email, $this->nombre);
        $mail->Subject = "Confirma tu cuenta";

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p>Hola<strong> " . $this->nombre . "</strong> has creado tu cuenta en UpTask, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: <a href='" . $_ENV['SERVER_HOST'] . "/confirmar?token=" . $this->token . "'>Confirmar cuenta</a> </p>";
        $contenido .= "<p> Si tu no solicitaste esta cuenta, puedes ignorar este mensaje </p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        $mail->send();
    }

    public function enviarInstrucciones()
    {

        //Crear el objeto de mail version localhost
        // $mail = new PHPMailer();
        // $mail->isSMTP();
        // $mail->Host = 'smtp.mailtrap.io';
        // $mail->SMTPAuth = true;
        // $mail->Port = 2525;
        // $mail->Username = '8e2976aa37bd1c';
        // $mail->Password = '38bd8fbf5e4da8';
        //Crear el objeto de mail version deploy
        // $mail = new PHPMailer();
        // $mail->isSMTP();
        // $mail->Host = $_ENV['MAIL_HOST'];
        // $mail->SMTPAuth = true;
        // $mail->Port = $_ENV['MAIL_PORT'];
        // $mail->Username = $_ENV['MAIL_USER'];
        // $mail->Password = $_ENV['MAIL_PASSWORD'];
        //Crear el objeto de mail version gmail
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->Port = $_ENV['MAIL_PORT'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $email = $_ENV['EMAIL'];
        $mail->Username = $email;
        $mail->Password = $_ENV['EMAIL_PASSWORD'];
        $mail->setFrom($_ENV['EMAIL'], 'UpTask');
        $mail->addAddress($this->email, $this->nombre);
        $mail->Subject = "Restablece tu password";

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p>Hola<strong> " . $this->nombre . "</strong> has solicitado restablecer tu password, sigue el siguiente enlace para hacerlo</p>";
        $contenido .= "<p>Presiona aqui: <a href='" . $_ENV['SERVER_HOST'] . "/reestablecer?token=" . $this->token . "'>Restablecer Password</a> </p>";
        $contenido .= "<p> Si tu no solicisaste esta cuenta, puedes ignorar este mensaje </p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        $mail->send();
    }

    public function cambiarMail(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->Port = $_ENV['MAIL_PORT'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $email = $_ENV['EMAIL'];
        $mail->Username = $email;
        $mail->Password = $_ENV['EMAIL_PASSWORD'];
        $mail->setFrom($_ENV['EMAIL'], 'UpTask');
        $mail->addAddress($this->email, $this->nombre);
        $mail->Subject = "Verifica tu Nuevo Correo";

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p>Hola<strong> " . $this->nombre . "</strong> has solicitado cambiar tu correo de tu cuenta de UpTask, sigue el siguiente enlace para hacerlo</p>";
        $contenido .= "<p>Presiona aqui: <a href='" . $_ENV['SERVER_HOST'] . "/confirmar-mail?token=" . $this->token . "'>Confirmar Mail</a> </p>";
        $contenido .= "<p> Si tu no solicisaste esta cuenta, puedes ignorar este mensaje </p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        $mail->send();
    }
}
