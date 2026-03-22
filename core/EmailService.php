<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ROOT_PATH . '/vendor/autoload.php';

class EmailService {

    private PHPMailer $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host       = MAIL_HOST;
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = MAIL_USERNAME;
        $this->mailer->Password   = MAIL_PASSWORD;
        $this->mailer->Port       = MAIL_PORT;
        $this->mailer->CharSet    = 'UTF-8';
        $this->mailer->setFrom(MAIL_FROM, MAIL_NAME);
    }

    /**
     * Enviar email de verificación de cuenta
     */
    public function enviarVerificacion(string $email, string $nombre, string $token): bool {
        try {
            $enlace = BASE_URL . '/usuarios/verificar/' . $token;

            $this->mailer->addAddress($email, $nombre);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Verifica tu cuenta en RaptorLearn';
            $this->mailer->Body    = "
                <h2>¡Bienvenido a RaptorLearn, {$nombre}!</h2>
                <p>Para activar tu cuenta pulsa el siguiente enlace:</p>
                <p><a href='{$enlace}'>Verificar mi cuenta</a></p>
                <p>El enlace caduca en 24 horas.</p>
                <p>Si no te has registrado en RaptorLearn, ignora este mensaje.</p>
            ";
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log('Error al enviar email de verificación: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de recuperación de contraseña
     */
    public function enviarRecuperacion(string $email, string $nombre, string $token): bool {
        try {
            $enlace = BASE_URL . '/usuarios/resetPassword/' . $token;

            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $nombre);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Recuperación de contraseña - RaptorLearn';
            $this->mailer->Body    = "
                <h2>Recuperación de contraseña</h2>
                <p>Hola, {$nombre}. Has solicitado restablecer tu contraseña.</p>
                <p><a href='{$enlace}'>Restablecer contraseña</a></p>
                <p>El enlace caduca en 1 hora.</p>
                <p>Si no has solicitado esto, ignora este mensaje.</p>
            ";
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log('Error al enviar email de recuperación: ' . $e->getMessage());
            return false;
        }
    }
}