<?php

namespace Clases;

use PHPMailer\PHPMailer\PHPMailer;

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
        //Crear objeto de Email
        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = $_ENV['EMAIL_HOST'];
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = $_ENV['EMAIL_PORT'];
        $phpmailer->Username = $_ENV['EMAIL_USER'];
        $phpmailer->Password = $_ENV['EMAIL_PASS'];

        $phpmailer->setFrom('cuentas@appsalon.com');
        $phpmailer->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $phpmailer->Subject = 'Confirma tu cuenta';

        $phpmailer->isHTML(TRUE);
        $phpmailer->CharSet = "UTF-8";

        $contenido = "<html>";
        $contenido .= "<p><strongg>Hola {$this->nombre} </strongg> Has creatu tu cuenta en App Salon, 
        solo debes de confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='".$_ENV['APP_URL']."/confirmar-cuenta?token={$this->token}'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";

        $phpmailer->Body = $contenido;

        $phpmailer->send();
    }

    public function enviarInstrucciones()
    {
        //Crear objeto de Email
        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = $_ENV['EMAIL_HOST'];
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = $_ENV['EMAIL_PORT'];
        $phpmailer->Username = $_ENV['EMAIL_USER'];
        $phpmailer->Password = $_ENV['EMAIL_PASS'];

        $phpmailer->setFrom('cuentas@appsalon.com');
        $phpmailer->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $phpmailer->Subject = 'Restablecer tu password';

        $phpmailer->isHTML(TRUE);
        $phpmailer->CharSet = "UTF-8";

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola {$this->nombre} </strong> Has solicitado reestablecer tu 
                reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
        $contenido .= "<p>Presiona aquí: <a href='".$_ENV['APP_URL']."/recuperar?token={$this->token}'>Reestablecer Password</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cambio, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";

        $phpmailer->Body = $contenido;

        $phpmailer->send();
    }
}
