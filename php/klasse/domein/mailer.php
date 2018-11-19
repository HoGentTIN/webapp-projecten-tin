<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/phpmailer/phpmailer/Exception.php';
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/phpmailer/phpmailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/phpmailer/phpmailer/SMTP.php';

/**
 * Class Mailer
 */
class Mailer {
    /**
     * Private Members
     */
    private $_mail;

    /**
     * Mailer constructor.
     */
    public function __construct() {
        $databank_config = parse_ini_file( $_SERVER["DOCUMENT_ROOT"] . '/php/configuratie/.email.ini');
        $HOST = $databank_config['HOST'];
        $POORT = $databank_config['POORT'];
        $GEBRUIKERSNAAM = $databank_config['GEBRUIKERSNAAM'];
        $WACHTWOORD = $databank_config['WACHTWOORD']; // mD5: 'a135500f57f557c1deb762ade08fbc92';

        $this->_mail = new PHPMailer(true);                              // Passing `true` enables exceptions

        $this->_mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $this->_mail->isSMTP();                                      // Set mailer to use SMTP
        $this->_mail->Host = $HOST;  // Specify main and backup SMTP servers
        $this->_mail->SMTPAuth = true;                               // Enable SMTP authentication
        $this->_mail->Username = $GEBRUIKERSNAAM;                            // SMTP username
        $this->_mail->Password = $WACHTWOORD;                           // SMTP password
        $this->_mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $this->_mail->Port = $POORT;                                    // TCP port to connect to
    }

    /**
     * @param string $naar
     * @param string $onderwerp
     * @param $boodschap
     * @return bool
     */
    public function stuur_email(string $naar, $onderwerp="Projecten2sus", $boodschap) : bool {
        if($this->_mail === null) {
            return false;
        }
        try {
            // Ontvanger
            $van = 'sebastiaan.labijn@hogent.be';
            $this->_mail->setFrom($van, '[Projecten2sus] Sebastiaan Labijn');
            $this->_mail->addAddress($naar);
            // Inhoud
            $this->_mail->isHTML(false);
            $this->_mail->Subject = $onderwerp;
            $this->_mail->Body    = $boodschap;
            // mail versturen
            $this->_mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}