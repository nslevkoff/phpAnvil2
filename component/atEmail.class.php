<?php
require_once('atObject.abstract.php');


/**
 * Email Wrapper Class
 *
 * This class provides support for dynamic properties and methods.
 *
 * @copyright     Copyright (c) 2009-2011 Nick Slevkoff ({@link http://www.slevkoff.com})
 * @license        http://www.phpanvil.com/LICENSE.txt        New BSD License
 * @version        1.0
 * @ingroup     phpAnvilTools
 */
class atEmail extends atObjectAbstract
{
    /**
     * Version number for this class release.
     *
     */
    const VERSION = '1.0';

    const PLATFORM_LINUX = 1;
    const PLATFORM_WINDOWS = 2;

    const LIBRARY_NONE = 0;
    const LIBRARY_SWIFT = 1;

    const SERVER_SMTP = 1;
    const SERVER_SENDMAIL = 2;
    const SERVER_EXCHANGE = 3;

    public $host = 'localhost';
    public $port = 25;
    public $username;
    public $password;

    public $libraryPath;
    public $libraryType = self::LIBRARY_NONE;
    public $platformType = self::PLATFORM_LINUX;
    public $serverType = self::SERVER_SMTP;

    public $fromName;
    public $fromEmail;
    public $recipientName;
    public $recipientEmail;
    public $replyToEmail;
    public $cc;
    public $bcc;
    public $subject;
    public $body;
    public $plainBody;
    public $xMailer;
    public $isHTML = true;

    public $library;
    public $failures;


    public function __construct($host = 'localhost', $port = 25)
    {
//        $this->enableTrace();

        $this->host = $host;
        $this->port = $port;

        $this->enableLog();

//        $this->libraryType = self::LIBRARY_NONE;
//        $this->platformType = self::PLATFORM_LINUX;
//        $this->serverType = self::SERVER_SMTP;
    }


    public function open()
    {

        if ($this->libraryType == self::LIBRARY_SWIFT) {
            require_once(COMMON_PATH . 'Swift_v4/swift_required.php');
//            require_once($this->libraryPath . 'Swift/Connection/SMTP.php');

//            $conn = new Swift_Connection_SMTP($this->host);
//            if (!empty($this->username)) {
//                $conn->setUsername($this->username);
//                $conn->setPassword($this->password);
//            }
//            $this->library = new Swift($conn);
        }
    }


    public function close()
    {
    }


//	public function authenticate($username, $password) {
//		return $this->library->authenticate($username, $password);
//	}


    public function simpleSend($fromName, $fromAddress, $toName, $toAddress, $subject, $body = '', $contentType = 'text/plain')
    {
        $return = false;

        if ($this->libraryType == self::LIBRARY_SWIFT) {
            $message = new Swift_Message($subject, $body, $contentType);
            $return = $this->library->send($message, new Swift_Address($toAddress, $toName), new Swift_Address($fromAddress, $fromName));
        }

        //		if ($contentType == 'text/plain') {
        //			return $this->_swift->send($toAddress, $fromAddress, $subject, $body, $contentType);
        //		} else {
        //			$this->_swift->addPart($body, $contentType);
        //			return $this->_swift->send($toAddress, $fromAddress, $subject);
        //		}

        return $return;
    }

    public function send()
    {
//        fb::log('Sending email...');

        if ($this->libraryType == self::LIBRARY_SWIFT) {
            try {
                $transport = Swift_SmtpTransport::newInstance($this->host, $this->port);
                $transport->setUsername($this->username);
                $transport->setPassword($this->password);

                //Create the Mailer using your created Transport
                $mailer = Swift_Mailer::newInstance($transport);


                $message = Swift_Message::newInstance();

                $message->setFrom(array($this->fromEmail => $this->fromName));
    //            $message->setSender(array($this->fromEmail => $this->fromName));
                $message->setTo(array($this->recipientEmail => $this->recipientName));
                $message->setSubject($this->subject);

                if (!empty($this->replyToEmail)) {
                    $message->setReplyTo($this->replyToEmail);
                }

                if ($this->isHTML) {
                    $message->setBody($this->body, 'text/html');

                    if (!empty($this->plainBody)) {
    //                    $this->logDebug('Adding text/plain to email...');

                        $message->addPart($this->plainBody, 'text/plain');
                    }
                } else {
                    $message->setBody($this->body);
                }

    //            $this->logDebug($message, '$message');

                $return = $mailer->send($message, $this->failures);
            }
            catch (Swift_RfcComplianceException $e)
            {
                $this->logError($e, 'Swift_RfcComplianceException');
                $this->failures .= '[RFC Error] ' . $e->getMessage();
            }
            catch (Swift_TransportException $e)
            {
                $this->logError($e, 'Swift_TransportException');
                $this->failures .= '[Email Send Error] ' . $e->getMessage();
            }

        } else {
            $headers = 'From: ' . $this->fromName . ' <' . $this->fromEmail . '>';
            $headers .= "\r\n" . 'Reply-To: ' . $this->fromEmail;
            $headers .= "\r\n" . 'Return-Path: ' . $this->fromEmail;

            if (!empty($this->cc)) {
                $headers .= "\r\n" . 'Cc: ' . $this->cc;
            }

            if (!empty($this->bcc)) {
                $headers .= "\r\n" . 'Bcc: ' . $this->bcc;
            }

    //        $headers .= "\r\n" . 'To: ' . $this->recipientName . ' <' . $this->recipientEmail . '>';

            if (!empty($this->xMailer)) {
                $headers .= "\r\n" . 'X-Mailer: ' . $this->xMailer;
            }

            if ($this->isHTML) {
                $headers .= "\r\n" . 'MIME-Version: 1.0';
                $headers .= "\r\n" . 'Content-type: text/html; charset=iso-8859-1';
            }

            $this->logDebug($headers, '$headers');

            $recipient = $this->recipientName . ' <' . $this->recipientEmail . '>';

            $return = mail($recipient, $this->subject, $this->body, $headers, '-f' . $this->fromEmail);

        }

//        $this->logDebug($return, '$return');

        return $return;
    }
}

?>