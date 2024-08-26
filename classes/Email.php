<?php


namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;

class Email {
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;

    }

    public function enviarConfirmacion() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'fb1eae81af3eed';
        $mail->Password = '105818d8691676';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Confirma Tu Cuenta';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has Creado Tu Cuenta En UpTask, solo debes confirmala en el siguiente enlance</p>";
        $contenido .= "<p>Presiona Aqui: <a href='http://localhost:3000/confirmar?token=" . $this->token . "'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta puedes, ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        // enviar email
        $mail->send();

    }

    public function enviarInstrucciones() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'fb1eae81af3eed';
        $mail->Password = '105818d8691676';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Reetablecer Tu Password';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Pacere que has olvidado tu password, sigue el siguiente enlaces para recuperarlo</p>";
        $contenido .= "<p>Presiona Aqui: <a href='http://localhost:3000/reestablecer?token=" . $this->token . "'>Reestablacer Password</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta puedes, ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        // enviar email
        $mail->send();
    }
}