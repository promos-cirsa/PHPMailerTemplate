<?php

namespace app\utility;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

/**
 * Description of mailManager
 *
 * @author Angel
 */
class MailHelper {

    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);

        $env = Dotenv::create(__DIR__ . '/../../');
        $env->load();

        //Server settings
        $this->mail->SMTPDebug = 0;                                             // 2 -> debug / 0 -> release
        $this->mail->isSMTP();                                                  // Set mailer to use SMTP
        $this->mail->Host = getenv('MAIL_HOST');                                // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                                           // Enable SMTP authentication
        $this->mail->Username = getenv('MAIL_USER');                            // SMTP username
        $this->mail->Password = getenv('MAIL_PASS');                            // SMTP password
        $this->mail->SMTPSecure = getenv('MAIL_SMTP');                          // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = getenv('MAIL_PASS');                                // TCP port to connect to
        $this->mail->CharSet = 'utf8';
        //Recipients
        $this->mail->setFrom(getenv('MAIL_REPLY_EMAIL'), getenv('MAIL_REPLY_NAME'));
        
        $this->mail->isHTML(true);                                  // Set email format to HTML
    }

    public function AddEmbeddedImage($path, $name, $fullName) {
        $this->mail->AddEmbeddedImage($path, $name, $fullName);
    }

    private function sendEmail() {
        $count = 3;
        $sended = false;

        do {
            try {
                $this->mail->send();
                $sended = true;
            } catch (Exception $e) {
                //echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
                $count = $count - 1;
            }
        } while (!$sended && $count > 0);

        return $sended;
    }

    public function Send($email, $emailOwner, $subject, $message, $altMessage = null) {

        $this->mail->addAddress($email, $emailOwner);     // Add a recipient

        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        $this->mail->AltBody = $altMessage;

        $sended = self::sendEmail();
        return $sended;
    }

}

//test
/*
$mail = new MailManager;
$title = "Lorem Ipsum";
$message = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
$messageHtml = "<p>$message</p>";

$mail->Send("Email", "manuelcastrocr@livestcr.com", $title, $messageHtml, $message);

//$mail->Send("proveedores@livestcr.com", "proveedores@livestcr.com", "Titulo del mensaje", "contenido del mensaje", "contenido del mensaje");
*/