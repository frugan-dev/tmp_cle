<?php
/**
* Framework App PHP-MySQL
* PHP Version 8.4
* @copyright 2025 Websync
* classes/class.Mails.php v.2.0.0. 24/09/2025
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\FailoverTransport;
use Symfony\Component\Mailer\Transport\RoundRobinTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class Mails extends Core {

	public static function sendEmail($address,$subject,$content,$text_content,$opt) 
	{
		$optDef = ['sendDebug'=>0,'sendDebugEmail'=>'','fromEmail'=>'n.d','fromLabel'=>'n.d','attachments'=>''];	
		$opt = array_merge($optDef,$opt);
		
		match (self::$globalSettings['use send mail class']) {
            // Symfony Mailer (new default)
            1 => self::sendMailSymfony($address,$subject,$content,$text_content,$opt),
            // PHPMailer 6.x via composer
            2 => self::sendMailPHPMAILER($address,$subject,$content,$text_content,$opt),
            // Native PHP mail()
            default => self::sendMailPHP($address,$subject,$content,$text_content,$opt),
        };
	}

	/**
	 * Send email using Symfony Mailer
	 */
	public static function sendMailSymfony($address,$subject,$content,$text_content,$opt) 
	{
		$optDef = ['replyTo'=>[],'addBCC'=>[],'sendDebug'=>0,'sendDebugEmail'=>'','fromEmail'=>'n.d','fromLabel'=>'n.d','attachments'=>''];
		$opt = array_merge($optDef,$opt);
		
		try {
			// Build transports array from environment configuration
			$transports = self::buildTransports();
			
			if (empty($transports)) {
				// https://symfony.com/doc/current/mailer.html#disabling-delivery
				$transports['null'] = 'null://null';
			}
			
			// Create transport instances - some may be custom OAuth2 transports
			$transportInstances = [];
			foreach ($transports as $key => $dsnOrInstance) {
				if (is_object($dsnOrInstance)) {
					// Already a transport instance (OAuth2 custom transports)
					$transportInstances[] = $dsnOrInstance;
				} else {
					// Create from DSN (standard transports)
					$transportInstances[] = Transport::fromDsn(
						$dsnOrInstance,
						null,
						null,
						Logger::getInstance()
					);
				}
			}
			
			// Create final transport based on technique
			$technique = $_ENV['MAIL_TRANSPORTS_TECHNIQUE'] ?? 'failover';
			
			if (count($transportInstances) > 1) {
				if ($technique === 'roundrobin') {
					$transport = new RoundRobinTransport($transportInstances);
				} else {
					$transport = new FailoverTransport($transportInstances);
				}
			} else {
				$transport = $transportInstances[0];
			}

			$mailer = new Mailer($transport);

			// https://github.com/symfony/symfony/issues/41322
        	// https://stackoverflow.com/a/14253556/3929620
        	// https://stackoverflow.com/a/25873119/3929620
			$email = new Email()
				->from(new Address($opt['fromEmail'], $opt['fromLabel']))
				->to($address)
				->subject($subject)
				->text($text_content)
				->html($content);

			// Add reply-to addresses
			if (is_array($opt['replyTo']) && count($opt['replyTo'])) {
				foreach ($opt['replyTo'] as $key => $value) {
					if (is_string($key)) {
						$email->addReplyTo(new Address($key, $value));
					} else {
						$email->addReplyTo($value);
					}
				}
			}

			// Add BCC addresses
			if (is_array($opt['addBCC']) && count($opt['addBCC'])) {
				foreach ($opt['addBCC'] as $key => $value) {
					if (is_string($key)) {
						$email->addBcc(new Address($key, $value));
					} else {
						$email->addBcc($value);
					}
				}
			}

			// Add debug BCC if enabled
			if ($opt['sendDebug'] == 1 && !empty($opt['sendDebugEmail'])) {
				$email->addBcc($opt['sendDebugEmail']);
			}

			// Add attachments
			if (is_array($opt['attachments']) && count($opt['attachments'])) {
				foreach ($opt['attachments'] as $attachment) {
					$email->attachFromPath($attachment['filename'], $attachment['title'] ?? null);
				}
			}

			$mailer->send($email);
			Core::$resultOp->error = 0;
			
			Logger::info('Email sent successfully', [
				'to' => $address,
				'subject' => $subject,
				'transport_count' => count($transportInstances)
			]);
			
		} catch (\Exception $exception) {
			Core::$resultOp->error = 1;
			Logger::error($exception->getMessage(), [
				'exception' => $exception,
				'to' => $address,
				'subject' => $subject,
			]);
		}
	}
	
	/**
	 * Build transports configuration for Symfony Mailer with OAuth2 support
	 * Public method so Logger class can use it too
	 */
	public static function buildTransports(): array 
	{
		$transports = [];
		
		if (empty($_ENV['MAIL_TRANSPORTS'])) {
			return $transports;
		}
		
		// https://github.com/symfony/mailer
        // https://symfony.com/doc/current/mailer.html
        // https://github.com/swiftmailer/swiftmailer/issues/866
        // https://github.com/swiftmailer/swiftmailer/issues/633
		foreach (array_map('trim', explode(',', (string) $_ENV['MAIL_TRANSPORTS'])) as $val) {
			switch ($val) {
				case 'oauth2-smtp':
					if (self::isOAuth2SMTPConfigured()) {
						try {
							$transport = self::createOAuth2SMTPTransport();
							$transports['oauth2-smtp'] = $transport;
							Logger::debug('OAuth2 SMTP transport created');
						} catch (Exception $e) {
							Logger::error('Failed to create OAuth2 SMTP transport', [
								'error' => $e->getMessage()
							]);
						}
					} else {
						Logger::warning('OAuth2 SMTP transport skipped - not configured');
					}
					break;
					
				case 'oauth2-graph':
					if (self::isOAuth2GraphConfigured()) {
						try {
							$transport = new GraphAPITransport();
							if ($transport->isConfigured()) {
								$transports['oauth2-graph'] = $transport;
								Logger::debug('OAuth2 Graph API transport created');
							}
						} catch (Exception $e) {
							Logger::error('Failed to create OAuth2 Graph API transport', [
								'error' => $e->getMessage()
							]);
						}
					} else {
						Logger::warning('OAuth2 Graph API transport skipped - not configured');
					}
					break;
				
				// it requires proc_*() functions
				case 'smtp':
				case 'smtps':
					$transports[$val] = $val . '://';
					if (!empty($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_USERNAME']) && 
						!empty($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_PASSWORD'])) {
						$transports[$val] .= rawurlencode((string) $_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_USERNAME']) . 
											':' . rawurlencode((string) $_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_PASSWORD']) . '@';
					}

					$transports[$val] .= $_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_HOST'] . 
										':' . $_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_PORT'] . '?';

					foreach ([
						'verify_peer',
						'local_domain',
						'restart_threshold',
						'restart_threshold_sleep',
						'ping_threshold',
					] as $item) {
						if (isset($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_' . mb_strtoupper($item, 'UTF-8')])) {
							$transports[$val] .= '&' . $item . '=' . 
								rawurlencode($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_' . mb_strtoupper($item, 'UTF-8')]);
						}
					}
					break;

				// if 'command' isn't specified, it will fallback to '/usr/sbin/sendmail -bs' (no ini_get() detection)
				case 'sendmail':
					$transports[$val] = $val . '://default?';
					foreach (['command'] as $item) {
						if (isset($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_' . mb_strtoupper($item, 'UTF-8')])) {
							$transports[$val] .= '&' . $item . '=' . 
								strtr(rawurlencode($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_' . mb_strtoupper($item, 'UTF-8')]), [
									'%2F' => '/',
								]);
						}
					}
					break;

				// it uses sendmail or smtp transports with ini_get() detection
                // When using native://default, if php.ini uses the sendmail -t command, you won't have error reporting and Bcc headers won't be removed.
                // It's highly recommended to NOT use native://default as you cannot control how sendmail is configured (prefer using sendmail://default if possible).
				case 'native':
					$transports[$val] = $val . '://default';
					break;

				//TODO
                // only if proc_*() functions are not available...
				// case 'mail':
				// case 'mail+api':
				// 	$transports[$val] = $val . '://default';
				// 	break;

				default:
					Logger::warning("Unknown transport type: {transport_type}", [
						'transport_type' => $val,
					]);
					break;
			}
		}
		
		return $transports;
	}

	/**
	 * Check if OAuth2 SMTP is configured
	 */
	public static function isOAuth2SMTPConfigured(): bool
	{
		$required = [
			'MAIL_OAUTH2_TENANT_ID',
			'MAIL_OAUTH2_CLIENT_ID', 
			'MAIL_OAUTH2_CLIENT_SECRET'
		];
		
		foreach ($required as $var) {
			if (empty($_ENV[$var])) {
				return false;
			}
		}
		
		$username = $_ENV['MAIL_OAUTH2_SMTP_USERNAME'] ?: $_ENV['MAIL_FROM_EMAIL'] ?? '';
		return !empty($username);
	}
	
	/**
	 * Check if OAuth2 Graph API is configured
	 */
	public static function isOAuth2GraphConfigured(): bool
	{
		$required = [
			'MAIL_OAUTH2_TENANT_ID',
			'MAIL_OAUTH2_CLIENT_ID',
			'MAIL_OAUTH2_CLIENT_SECRET'
		];
		
		foreach ($required as $var) {
			if (empty($_ENV[$var])) {
				return false;
			}
		}
		
		$userId = $_ENV['MAIL_OAUTH2_GRAPH_USER_ID'] ?: $_ENV['MAIL_FROM_EMAIL'] ?? '';
		return !empty($userId);
	}

	/**
	 * Create OAuth2 SMTP transport
	 */
	private static function createOAuth2SMTPTransport(): TransportInterface
	{
		$host = $_ENV['MAIL_OAUTH2_SMTP_HOST'] ?? 'smtp.office365.com';
		$port = (int)($_ENV['MAIL_OAUTH2_SMTP_PORT'] ?? 587);
		$username = $_ENV['MAIL_OAUTH2_SMTP_USERNAME'] ?? $_ENV['MAIL_FROM_EMAIL'] ?? '';
		
		if (empty($username)) {
			throw new Exception('OAuth2 SMTP requires username (MAIL_OAUTH2_SMTP_USERNAME or MAIL_FROM_EMAIL)');
		}
		
		// Create SMTP transport
		$transport = new EsmtpTransport(
			$host,
			$port,
			false, // TLS will be started automatically
			null,
			Logger::getInstance()
		);
		
		// Set up OAuth2 authentication
		$tokenProvider = Office365TokenProvider::createFromEnv();
		$authenticator = new OAuth2Authenticator($tokenProvider);
		
		$transport->setUsername($username);
		$transport->setPassword('oauth2'); // Placeholder, replaced by authenticator
		$transport->addAuthenticator($authenticator);
		
		Logger::debug('OAuth2 SMTP transport configured', [
			'host' => $host,
			'port' => $port,
			'username' => $username
		]);
		
		return $transport;
	}

	/**
	 * Send email using PHPMailer 6.x
	 */
	public static function sendMailPHPMAILER($address,$subject,$content,$text_content,$opt) 
	{
		$optDef = ['replyTo'=>[],'addBCC'=>[],'sendDebug'=>0,'sendDebugEmail'=>'','fromEmail'=>'n.d','fromLabel'=>'n.d','attachments'=>''];	
		$opt = array_merge($optDef,$opt);	
	
		try {
			$mail = new PHPMailer(true); // Enable exceptions
			
			// Server settings based on global configuration
			switch (self::$globalSettings['mail server']) {
				case 'SMTP':
					$mail->isSMTP();
					$mail->Host = self::$globalSettings['SMTP server'];
					$mail->Port = (int)self::$globalSettings['SMTP port'];
					
					if (!empty(self::$globalSettings['SMTP username'])) {
						$mail->SMTPAuth = true;
						$mail->Username = self::$globalSettings['SMTP username'];
						$mail->Password = self::$globalSettings['SMTP password'];
					}
					
					// Auto-detect encryption
					if ((int)self::$globalSettings['SMTP port'] === 465) {
						$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
					} elseif ((int)self::$globalSettings['SMTP port'] === 587) {
						$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
					}
					break;
					
				case 'sendmail':
					$mail->isSendmail();
					if (!empty(self::$globalSettings['sendmail server'])) {
						$mail->Sendmail = self::$globalSettings['sendmail server'];
					}
					break;
					
				default:
					$mail->isMail();
					break;
			}

			// Recipients and content
			$mail->setFrom($opt['fromEmail'], $opt['fromLabel']);
			$mail->addAddress($address);
			$mail->isHTML(true);
			$mail->CharSet = 'UTF-8';
			$mail->Subject = $subject;
			$mail->AltBody = $text_content;
			$mail->Body = $content;

			// Reply-to addresses
			if (is_array($opt['replyTo']) && count($opt['replyTo'])) {			
				foreach ($opt['replyTo'] as $key => $value) {
					if (is_string($key)) {
						$mail->addReplyTo($key, $value);
					} else {
						$mail->addReplyTo($value);
					}
				}
			}
			
			// BCC addresses
			if (is_array($opt['addBCC']) && count($opt['addBCC'])) {			
				foreach ($opt['addBCC'] as $key => $value) {
					if (is_string($key)) {
						$mail->addBCC($key, $value);
					} else {
						$mail->addBCC($value);
					}
				}
			}

			// Debug BCC
			if ($opt['sendDebug'] == 1 && !empty($opt['sendDebugEmail'])) {
				$mail->addBCC($opt['sendDebugEmail']);
			}
				
			// Attachments
			if (is_array($opt['attachments']) && count($opt['attachments'])) {			
				foreach ($opt['attachments'] as $attachment) {
					$mail->addAttachment($attachment['filename'], $attachment['title'] ?? '');
				}
			}
				
			$mail->send();
			Core::$resultOp->error = 0;
			
		} catch (PHPMailerException $exception) {
			Core::$resultOp->error = 1;
			Logger::error($e->getMessage(), [
				'exception' => $exception,
			]);
		}
	}	
		
	/**
	 * Send email using native PHP mail() function
	 */
	public static function sendMailPHP($address,$subject,$content,$text_content,$opt) 
	{
		$optDef = ['sendDebug'=>0,'sendDebugEmail'=>'','fromEmail'=>'n.d','fromLabel'=>'n.d','attachments'=>''];	
		$opt = array_merge($optDef,$opt);	
		
		$mail_boundary = "=_NextPart_" . md5(uniqid(time()));	
		$headers = "From: " . $opt['fromLabel'] . " <" . $opt['fromEmail'] . ">\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: multipart/alternative;\n\tboundary=\"$mail_boundary\"\n";
		$headers .= "X-Mailer: PHP " . phpversion();
		
		// Build message body
		$msg = "This is a multi-part message in MIME format.\n\n";
		$msg .= "--$mail_boundary\n";
		$msg .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
		$msg .= "Content-Transfer-Encoding: 8bit\n\n";
		$msg .= $text_content; // Add text version
 
		$msg .= "\n--$mail_boundary\n";
		$msg .= "Content-Type: text/html; charset=\"UTF-8\"\n";
		$msg .= "Content-Transfer-Encoding: 8bit\n\n";
		$msg .= $content; // Add HTML version
 
		// End multipart/alternative boundary
		$msg .= "\n--$mail_boundary--\n";
		
		$sender = $opt['fromEmail'];
		// Set Return-Path (works only on Windows hosting)
		ini_set("sendmail_from", $sender); 
		// Send message, the fifth parameter "-f$sender" sets Return-Path on Linux hosting
		$result = mail((string) $address, (string) $subject, $msg, $headers, "-f$sender");		
		
		if (!$result) {   
			Core::$resultOp->error = 1;
			Logger::error('PHP mail() function failed for address: {address}', [
				'address' =>  $address,
			]);
		} else {
			Core::$resultOp->error = 0;
		}
	}

	/**
	 * Parse mail template content with placeholders
	 */				
	public static function parseMailContent($post,$content,$opt=[]) 
	{
		$optDef = ['customFields'=>[],'customFieldsValue'=>[]];	
		$opt = array_merge($optDef,$opt);
		
		$content = preg_replace('/%SITENAME%/',(string) SITE_NAME,(string) $content);

		$content = preg_replace('/{{/','%',(string) $content);
		$content = preg_replace('/}}/','%',(string) $content);
		
		if (isset($post['urlconfirm'])) $content = preg_replace('/%URLCONFIRM%/',$post['urlconfirm'],(string) $content);
		if (isset($post['hash'])) $content = preg_replace('/%HASH%/',$post['hash'],(string) $content);
		if (isset($post['username'])) $content = preg_replace('/%USERNAME%/',$post['username'],(string) $content);
		if (isset($post['name'])) $content = preg_replace('/%NAME%/',$post['name'],(string) $content);
		if (isset($post['surname'])) $content = preg_replace('/%SURNAME%/',$post['surname'],(string) $content);
		if (isset($post['email'])) $content = preg_replace('/%EMAIL%/',$post['email'],(string) $content);
		if (isset($post['subject'])) $content = preg_replace('/%SUBJECT%/',$post['subject'],(string) $content);	
		if (isset($post['object'])) $content = preg_replace('/%OBJECT%/',$post['object'],(string) $content);	
		if (isset($post['message'])) $content = preg_replace('/%MESSAGE%/',$post['message'],(string) $content);	
		
		if ((is_array($opt['customFields']) && count($opt['customFields'])) 
			&& (is_array($opt['customFieldsValue']) && count($opt['customFieldsValue'])) 
			&& (count($opt['customFields']) == count($opt['customFieldsValue']))
			) {			
			foreach ($opt['customFields'] as $key => $value) {
				$content = preg_replace('/'.$opt['customFields'][$key].'/',(string) $opt['customFieldsValue'][$key],(string) $content);
			}
		}
		
		return $content;
	}	
}
