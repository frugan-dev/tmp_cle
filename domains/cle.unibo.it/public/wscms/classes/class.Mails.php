<?php
/**
* Framework App PHP-MySQL
* PHP Version 7
* @copyright 2020 Websync
* classes/class.Mails.php v.1.0.0. 20/01/2021
*/

class Mails extends Core {

	public function __construct() 	{
		parent::__construct();					
		}
		
	public static function sendEmail($address,$subject,$content,$text_content,$opt) 
	{
		$optDef = array('sendDebug'=>0,'sendDebugEmail'=>'','fromEmail'=>'n.d','fromLabel'=>'n.d','attachments'=>'');	
		$opt = array_merge($optDef,$opt);
		if (self::$globalSettings['use send mail class'] == 1) {
			self::sendMailClass($address,$subject,$content,$text_content,$opt);
			} else if (self::$globalSettings['use send mail class'] == 2) {
				self::sendMailPHPMAILER($address,$subject,$content,$text_content,$opt);
				} else {
					self::sendMailPHP($address,$subject,$content,$text_content,$opt);
				}
		
	}		
	
	public static function sendMailClass($address,$subject,$content,$text_content,$opt) 
	{
		$optDef = array('sendDebug'=>0,'sendDebugEmail'=>'','fromEmail'=>'n.d','fromLabel'=>'n.d','attachments'=>'');
		$opt = array_merge($optDef,$opt);	
		$transport = '';
		switch (self::$globalSettings['mail server']) {
			case 'SMTP':
				$transport = new Swift_SmtpTransport(self::$globalSettings['SMTP server'], self::$globalSettings['SMTP port']);
				if (isset(self::$globalSettings['SMTP username']) && self::$globalSettings['SMTP username'] != '') $transport->setUsername(self::$globalSettings['SMTP username']);
				if (isset(self::$globalSettings['SMTP password']) && self::$globalSettings['SMTP password'] != '') $transport->setPassword(self::$globalSettings['SMTP password']);
			break;
			
			default:
				$transport = new Swift_SendmailTransport(self::$globalSettings['sendmail path']);
			break;
			}

		try {
			$mailer = new Swift_Mailer($transport);
			// Create a message
			$message = (new Swift_Message($subject))
	  			->setFrom([$opt['fromEmail']=>$opt['fromLabel']])
	  			->setTo([$address])
	  			->setBody($content, 'text/html')
				->addPart($text_content, 'text/plain');
	  		;
			// Send the message
			try {
				$mailer->send($message);
				} catch (\Swift_TransportException $e) {
					Core::$resultOp->error = 1;
					//echo $e->getMessage();
					}       
	    	} catch (Swift_TransportException $e) {
	        	//return $e->getMessage();
	        	Core::$resultOp->error = 1;
	    	} catch (Exception $e) {
	      	//return $e->getMessage();
	      	Core::$resultOp->error = 1;
	    		}
	}
		
	/* versione PHP MAILER */
	public static function sendMailPHPMAILER($address,$subject,$content,$text_content,$opt) 
	{
		include_once("class.phpmailer.php");
		include_once("class.pop3.php");
		include_once("class.smtp.php");
		$optDef = array('replyTo'=>array(),'addBCC'=>array(),'sendDebug'=>0,'sendDebugEmail'=>'','fromEmail'=>'n.d','fromLabel'=>'n.d','attachments'=>'','classMailer'=>'');	
		$opt = array_merge($optDef,$opt);	
	
		$mail = new PHPMailer();
		$mail->SetFrom($opt['fromEmail'],$opt['fromLabel']);
		$mail->IsHTML(true);
		$mail->CharSet = 'UTF-8';
		$mail->Subject = $subject;
		$mail->AltBody = $text_content;
		$mail->MsgHTML($content);	
		$mail->AddAddress($address);	

		if (is_array($opt['replyTo']) && count($opt['replyTo'])) {			
			foreach ($opt['replyTo'] AS $key=>$value) {
				if(is_string($key)) {
					$mail->addReplyTo($key, $value);
				} else {
					$mail->addReplyTo($value);
				}
			}
		}
		
		/* BCC */
		if (is_array($opt['addBCC']) && count($opt['addBCC'])) {			
			foreach ($opt['addBCC'] AS $key=>$value) {
				if(is_string($key)) {
					$mail->addBCC($key, $value);
				} else {
					$mail->addBCC($value);
				}
			}
		}

		if ($opt['sendDebug'] == 1) {
			if ($opt['sendDebugEmail'] != '') $mail->AddBCC($opt['sendDebugEmail']);
		}
			
		/* allegati */
		if (is_array($opt['attachments']) && count($opt['attachments'])) {			
			foreach ($opt['attachments'] AS $key=>$value) {
				$mail->addAttachment($value['filename'],$value['title']);    // Optional name
			}
		}
			
		if (!$mail->Send()) {
			Core::$resultOp->error = 1;
			} else {
			Core::$resultOp->error = 0;
			}
	}	
		
	public static function sendMailPHP($address,$subject,$content,$text_content,$opt) 
	{
		$optDef = array('sendDebug'=>0,'sendDebugEmail'=>'','fromEmail'=>'n.d','fromLabel'=>'n.d','attachments'=>'');	
		$opt = array_merge($optDef,$opt);	
		$mail_boundary = "=_NextPart_" . md5(uniqid(time()));	
		$headers = "From: ".$opt['fromLabel']." <".$opt['fromEmail'].">\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: multipart/alternative;\n\tboundary=\"$mail_boundary\"\n";
		$headers .= "X-Mailer: PHP " . phpversion();
		// Costruisci il corpo del messaggio da inviare
		$msg = "This is a multi-part message in MIME format.\n\n";
		$msg .= "--$mail_boundary\n";
		$msg .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
		$msg .= "Content-Transfer-Encoding: 8bit\n\n";
		$msg .= $text_content; // aggiungi il messaggio in formato text
 
		$msg .= "\n--$mail_boundary\n";
		$msg .= "Content-Type: text/html; charset=\"UTF-8\"\n";
		$msg .= "Content-Transfer-Encoding: 8bit\n\n";
		$msg .= $content;  // aggiungi il messaggio in formato HTML
 
		// Boundary di terminazione multipart/alternative
		$msg .= "\n--$mail_boundary--\n";
 		$sender = $opt['fromEmail'];
		// Imposta il Return-Path (funziona solo su hosting Windows)
		ini_set("sendmail_from", $sender); 
		// Invia il messaggio, il quinto parametro "-f$sender" imposta il Return-Path su hosting Linux
		$result = mail($address,$subject,$msg,$headers, "-f$sender");		
		if (!$result) {   
    		//echo "Error";
    		Core::$resultOp->error = 1;  
			} else {
    			//echo "Success";
    			Core::$resultOp->error = 0;
				}
	}

				
	public static function parseMailContent($post,$content,$opt=array()) 
	{
		$optDef = array('customFields'=>array(),'customFieldsValue'=>array());	
		$opt = array_merge($optDef,$opt);
		$content = preg_replace('/%SITENAME%/',SITE_NAME,$content);


		$content = preg_replace('/{{/','%',$content);
		$content = preg_replace('/}}/','%',$content);
		
		if (isset($post['urlconfirm'])) $content = preg_replace('/%URLCONFIRM%/',$post['urlconfirm'],$content);
		if (isset($post['hash'])) $content = preg_replace('/%HASH%/',$post['hash'],$content);
		if (isset($post['username'])) $content = preg_replace('/%USERNAME%/',$post['username'],$content);
		if (isset($post['name'])) $content = preg_replace('/%NAME%/',$post['name'],$content);
		if (isset($post['surname'])) $content = preg_replace('/%SURNAME%/',$post['surname'],$content);
		if (isset($post['email'])) $content = preg_replace('/%EMAIL%/',$post['email'],$content);
		if (isset($post['subject'])) $content = preg_replace('/%SUBJECT%/',$post['subject'],$content);	
		if (isset($post['object'])) $content = preg_replace('/%OBJECT%/',$post['object'],$content);	
		if (isset($post['message'])) $content = preg_replace('/%MESSAGE%/',$post['message'],$content);	
		if ((is_array($opt['customFields']) && count($opt['customFields'])) 
			&& (is_array($opt['customFieldsValue']) && count($opt['customFieldsValue'])) 
			&& (count($opt['customFields']) == count($opt['customFieldsValue']))
			) {			
			foreach ($opt['customFields'] AS $key=>$value) {
				$content = preg_replace('/'.$opt['customFields'][$key].'/',$opt['customFieldsValue'][$key],$content);
			}
		}
		return $content;
	}	

}
?>