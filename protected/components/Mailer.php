<?php
/**
 * Organizzy
 * copyright (c) 2014 abie
 *
 * @author abie
 * @date 6/7/14 11:07 PM
 */

require_once (__DIR__ . '/../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php');

class Mailer extends CApplicationComponent {

    public $useSMTP = true;
    public $host = 'localhost';
    public $port = '465';
    public $secure = 'tls';

    public $userName = null;
    public $password = null;

    public $senderAddress = null;
    public $senderName = null;

    public $viewDir = 'mail';
    public $layout = 'template';
    public $title = '';
    public $contentType = 'text/html';

    public function compose($subject = null, $recipientAddress = null, $recipientName = null) {
        $mailer = new PHPMailer(true);

        if ($this->useSMTP) {
            $mailer->isSMTP();
        }
        $mailer->Host = $this->host;
        $mailer->Port = $this->port;
        $mailer->SMTPSecure = $this->secure;

        if ($this->userName) {
            $mailer->SMTPAuth = true;
            $mailer->Username = $this->userName;
            $mailer->Password = $this->password;
        }

        if ($this->senderAddress) {
            $mailer->setFrom($this->senderAddress, $this->senderName);
            $mailer->addReplyTo($this->senderAddress, $this->senderName);
        }

        if ($recipientAddress)
            $mailer->addAddress($recipientAddress, $recipientName);

        if ($subject)
            $mailer->Subject = $subject;

        return $mailer;
    }

    public function sendTemplate($view, $recipientAddress = null, $recipientName = null, $params = []) {
        $content = $this->render($view, $params);
        if ($this->layout) {
            $content = $this->render($this->layout, ['content' => $content]);
        }

        $mailer = $this->compose(null, $recipientAddress, $recipientName);
        $mailer->ContentType = $this->contentType;
        $mailer->Subject = '[' . O::app()->name . '] ' . $this->title;
        $mailer->msgHTML($content, O::app()->getBasePath() . '/../web/');
        $mailer->send();
    }


    private function render($view, $params = []) {
        $_viewFile = O::app()->getViewPath() . '/' . $this->viewDir . '/' . $view . '.php';
        extract($params);
        ob_start();
        include $_viewFile;
        return ob_get_clean();
    }
} 