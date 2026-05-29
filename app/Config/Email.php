<?php

// Configuracion: define ajustes usados por la aplicacion.

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = '';
    public string $fromName   = '';
    public string $recipients = '';

    
    /**
     * The "user agent"
     */

    public string $userAgent = 'CodeIgniter';

    
    public string $protocol = 'mail';

    
    /**
     * The server path to Sendmail.
     */

    public string $mailPath = '/usr/sbin/sendmail';

    
    public string $SMTPHost = '';

    
    /**
     * Which SMTP authentication method to use: login, plain
     */

    public string $SMTPAuthMethod = 'login';

    
    public string $SMTPUser = '';

    
    /**
     * SMTP Password
     */

    public string $SMTPPass = '';

    
    public int $SMTPPort = 25;

    
    /**
     * SMTP Timeout (in seconds)
     */

    public int $SMTPTimeout = 5;

    
    public bool $SMTPKeepAlive = false;

    
    /**
     * SMTP Encryption.
     *
     * @var string '', 'tls' or 'ssl'. 'tls' will issue a STARTTLS command
     *             to the server. 'ssl' means implicit SSL. Connection on port
     *             465 should set this to ''.
     */

    public string $SMTPCrypto = 'tls';

    
    /**
     * Enable word-wrap
     */

    public bool $wordWrap = true;

    
    public int $wrapChars = 76;

    
    /**
     * Type of mail, either 'text' or 'html'
     */

    public string $mailType = 'text';

    
    public string $charset = 'UTF-8';

    
    /**
     * Whether to validate the email address
     */

    public bool $validate = false;

    
    public int $priority = 3;

    
    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */

    public string $CRLF = "\r\n";

    
    public string $newline = "\r\n";

    
    /**
     * Enable BCC Batch Mode.
     */

    public bool $BCCBatchMode = false;

    
    public int $BCCBatchSize = 200;

    
    /**
     * Enable notify message from server
     */

    public bool $DSN = false;
}
