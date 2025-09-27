<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 *	classes/class.Form.php v.1.3.0. 11/09/2020
 */

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\Extra\SpoofCheckValidation;
use Egulias\EmailValidator\Validation\MessageIDValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Egulias\EmailValidator\Validation\RFCValidation;

class Form extends Core
{
	public static function getUpdateRecordFromPostResults($id, $resultOp, $opt)
	{
		$optDef = ['label done' => 'modifiche effettuate', 'modviewmethod' => 'formMod', 'label modified' => 'voce modificata', 'label modify' => 'modifica voce', 'label insert' => 'inserisci voce'];
		$opt = array_merge($optDef, $opt);
		$viewMethod = '';
		$pageSubTitle = '';
		$message = $resultOp->message;
		if ($resultOp->error == 1) {
			$pageSubTitle = ucfirst((string) $opt['label modify']);
			$viewMethod = $opt['modviewmethod'];
		} else {
			if (isset($_POST['submitForm'])) {
				$viewMethod = 'list';
				$message = ucfirst((string) $opt['label modified']) . '!';
			} else {
				if (isset($_POST['id'])) {
					$id = $_POST['id'];
					$pageSubTitle = $opt['label modify'];
					$viewMethod = $opt['modviewmethod'];
					$message = ucfirst((string) $opt['label done']) . '!';
				} else {
					$viewMethod = 'formNew';
					$pageSubTitle = $opt['label insert'];
				}
			}
		}
		return [$id, $viewMethod, $pageSubTitle, $message];
	}

	public static function getInsertRecordFromPostResults($id, $resultOp, $opt)
	{
		$optDef = ['label inserted' => 'voce inserita', 'label insert' => 'inserisci voce'];
		$opt = array_merge($optDef, $opt);
		$viewMethod = '';
		$pageSubTitle = '';
		$message = $resultOp->message;
		if ($resultOp->error == 1) {
			$pageSubTitle = $opt['label insert'];
			$viewMethod = 'formNew';
		} else {
			$viewMethod = 'list';
			$message = ucfirst((string) $opt['label inserted']) . '!';
		}
		return [$id, $viewMethod, $pageSubTitle, $message];
	}

	public static function parsePostByFields($fields, $_lang, $opz)
	{
		//print_r($fields);
		$opzDef = ['stripmagicfields' => true];
		$opz = array_merge($opzDef, $opz);
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $fieldName => $value) {

				$fieldType = ($value['type'] ?? '');
				$fieldLabel = ($value['label'] ?? $fieldName);
				$fieldPostValue = ($_POST[$fieldName] ?? '');
				$fieldDetails = $value;


				//ToolsStrings::dump($fieldDetails);


				//echo '<br>namefield: '.$fieldName;
				//echo '<br>#'.$_POST[$fieldName].'#';
				//echo '<br>#forced value: '.$fieldDetails['forcedValue'].'#';

				$labelField = ($value['label'] ?? '');

				/* aggiorna con il default se vuoti */
				if (!isset($_POST[$fieldName])) {
					if (isset($value['defValue'])) $_POST[$fieldName] = $value['defValue'];
				}


				/* controlla se e richiesto */
				if (isset($fieldDetails['required']) && $fieldDetails['required'] == true) {
					//echo '<br>controlla richiesto '.$fieldName;
					self::checkIfRequired($fieldName,$fieldDetails,$_POST);
				}

				// valida i campi se richiesto
				if (isset($value['validate']) && $value['validate'] != false) {
					self::doFieldValidation($fieldName,$value,$_POST,$fieldLabel,$fieldPostValue);					
				}

				// forza il valore
				if ( !isset($_POST[$fieldName]) || ( isset($_POST[$fieldName]) && $_POST[$fieldName] == '' ) ) {
					//die('il campo e vuoto o non esiste');
					//echo '<br>il campo e vuoto o non esiste';

					//echo '<br>#forced value: '.$fieldDetails['forcedValue'].'#';
					if ( isset($fieldDetails['forcedValue']) && strval($fieldDetails['forcedValue']) != '' ) {
						//die('il campo forza e vuoto o non esiste');
						//echo '<br>forzo il valore al campo '.$fieldName;//die();

						$_POST[$fieldName] = $fieldDetails['forcedValue'];
					}
				}  

				// valida i i tipi di campo (se è text int varchar ecc)  e fa dei controlli. Es. se e varchar|255 controlla che non si superino i 255 caratteri		
				self::doValidationFieldType($fieldName,$fieldLabel,$fieldType,$fieldPostValue);					

				/* aggiunge gli slashes */
				if ($opz['stripmagicfields'] == true && isset($_POST[$fieldName])) $_POST[$fieldName] = SanitizeStrings::stripMagic($_POST[$fieldName]);

				//echo '<br>namefield: '.$fieldName;
				//echo '<br>valore post '.$_POST[$fieldName].'#';
				//echo '<br>---------------------------------------';
			}
		}
	}

	public static function doValidationFieldType($fieldName,$fieldLabel,$fieldType,$fieldPostvalue)
	{
		/*
		echo '<br>valida campo: '.$fieldName;
		echo '<br>label: '.$fieldLabel;
		echo '<br>type: '.$fieldType;
		echo '<br>valore: '.$fieldPostvalue;
		*/
		
		$foo = explode('|',(string) $fieldType);
		switch ($foo[0]) {
			

			case 'float':
				$valueRif = (isset($foo[1]) ? intval($foo[1]) : 0);
				$res = self::validateFloat($valueRif);
				if ($res == false) {
					Config::$resultOp->error = 1;
					$messaggio = preg_replace( ['/%ITEM%/'], [$fieldLabel,$valueRif], (string) Config::$langVars['Il campo %ITEM% NON è di tipo virgola mobile!'] );
					if ($messaggio != '') Config::$resultOp->messages[$fieldName] = $messaggio;
				}
			break;

			case 'varchar':
				$valueRif = (isset($foo[1]) ? intval($foo[1]) : 0);
				if ($valueRif > 0) {
					$res = self::validateMaxCharsInString($fieldPostvalue,$valueRif);
					if ($res == true) {
						Config::$resultOp->error = 1;
						$messaggio = preg_replace( ['/%ITEM%/','/%VALUERIF%/'], [$fieldLabel,$valueRif], (string) Config::$langVars['Il campo %ITEM% NON deve superare i %VALUERIF% caratteri!'] );
						if ($messaggio != '') Config::$resultOp->messages[$fieldName] = $messaggio;
					}

				}
			break;
		
			default:
			break;
		}
		
		
		
	}

	public static function doFieldValidation($fieldName,$fieldDetails,$fieldPostValueArray,$fieldLabel,$fieldPostValue)
	{
		
		//echo '<br>valida campo: '.$fieldName;
		//echo '<br>valida tipo: '.$fieldDetails['validate'];
		

		[$returnvalue, $result, $returnmessage] = self::validateField($fieldName, $fieldDetails, $fieldPostValueArray,$fieldLabel,$fieldPostValue);
		//echo '<br>returnmessage: '.$returnmessage;
			
		if ($result == false) {
			Config::$resultOp->error = 1;
			$messaggio = preg_replace( '/%ITEM%/', (string) $fieldName, (string) Config::$langVars['Il valore per il campo %ITEM% non è stato validato!'] );
			if (isset($fieldDetails['label'])) $messaggio = preg_replace( '/%ITEM%/', $fieldDetails['label'], (string) Config::$langVars['Il valore per il campo %ITEM% non è stato validato!'] );
			if ($returnmessage != '') $messaggio = $returnmessage;
			if (isset($fieldDetails['errorValidateMessage'])) $messaggio = $fieldDetails['errorValidateMessage'];
			if (isset($fieldDetails['error validate message'])) $messaggio = $fieldDetails['error validate message'];
			if ($messaggio != '') Config::$resultOp->messages[$fieldName] = $messaggio;
		}
		$_POST[$fieldName] = $returnvalue;

	}

	public static function checkIfRequired($fieldName,$fieldDetails,$fieldPostValue)
	{
		if (!isset($fieldPostValue[$fieldName]) || (isset($fieldPostValue[$fieldName]) && $fieldPostValue[$fieldName] == '')) {
			self::$resultOp->error = 1;
			if (isset($fieldDetails['label'])) $messaggio = preg_replace( '/%ITEM%/', $fieldDetails['label'], (string) Config::$langVars['Devi inserire il campo %ITEM%!'] );
			if (isset($fieldDetails['errorMessage'])) $messaggio = $fieldDetails['errorMessage'];
			if (isset($fieldDetails['error message'])) $messaggio = $fieldDetails['error message'];
			if ($messaggio != '') self::$resultOp->messages[$fieldName] = $messaggio;
		}
	}

	public static function validateField($fieldName, $fieldDetails, $fieldPostValueArray,$fieldLabel,$fieldPostValue)
	{
		$returnvalue = $fieldPostValue;
		$result = true;
		$message = '';
		switch ($fieldDetails['validate']) {
			case 'json':

				//echo '||'.$fieldPostValue.'||';

				$result = json_decode((string) $fieldPostValue);

				//echo $result;
				//ToolsStrings::dump($result);

			

				if (json_last_error() === JSON_ERROR_NONE) {
					$returnvalue =  $fieldPostValue;

					//die('fatto');


				} else {
					self::$resultOp->error = 1;
					self::$resultOp->messages[] = preg_replace('/%ITEM%/', (string) $fieldDetails['label'], 'il campo %ITEM% deve essere in formato json valido!');

					//die('fattoERRORE');
					$returnvalue =  '[]';
				}
			break;
			case 'int':
				$str = self::validateInt($fieldPostValue);
			break;
			case 'float':
				if (!isset($value['defValue'])) $value['defValue'] = '0.00';
				$str = $_POST[$fieldName];
				if (filter_var($str, FILTER_VALIDATE_FLOAT) == false) $str = $value['defValue'];
			break;
			case 'datetimeiso':
				if ($returnvalue == '') $returnvalue = $fieldDetails['defValue'];
				$result = self::validateDatetimeIso($returnvalue);
				if ($result == false) {
					$message = preg_replace('/%FIELD%/',(string) $fieldLabel,(string) Config::$langVars['La data %FIELD% non è valida!']);
				}
			break;
			case 'minmax':
				$minvalue = (isset($value['valuesRif']['min']) && $value['valuesRif']['min'] != '' ? $value['valuesRif']['min'] : 0);
				$maxvalue = (isset($value['valuesRif']['max']) && $value['valuesRif']['max'] != '' ? $value['valuesRif']['max'] : 0);
				$str = self::validateMinMaxValues($_POST[$fieldName],$fieldLabel, $minvalue, $maxvalue);
			break;
			case 'maxCharsInString':
				$valueRif = (isset($fieldDetails['valueRif']) && $fieldDetails['valueRif'] != '' ? $fieldDetails['valueRif'] : 0);
 				$valueCheked = $fieldPostValue;
				$foo = self::validateMaxCharsInString($valueCheked,$valueRif);
				$result = ($foo == true ? false : true); 
				
			break;
			/*
			case 'time':
				$str = self::validateTime($_POST[$fieldName], $fieldLabel);
			break;
			*/
			case 'explodearray':
				$opz1 = ($value['opz'] ?? []);
				$str = self::validateExplodearray($_POST[$fieldName], $opz1);
				break;

			/*
			case 'timepicker':
				if (!isset($value['defValue'])) $value['defValue'] = date('H:i:s');
				$time = DateFormat::convertDatepickerToIso($_POST[$fieldName], $_lang['datepicker time format'], 'H:i:s', $value['defValue']);
				$str = $time;
				break;

			case 'datetimepicker':
				$datetime = DateFormat::convertDatepickerToIso($_POST[$fieldName], Config::$langVars['datepicker data time format'], 'Y-m-d H:i:s', $value['defValue']);
				$str = $datetime;
				break;
		

			case 'datepicker':
				if (!isset($value['defValue'])) $value['defValue'] = date('Y-m-d');
				$date = DateFormat::convertDatepickerToIso($_POST[$fieldName], Config::$langVars['datepicker data format'], 'Y-m-d', $value['defValue']);
				$str = $date;
				break;
			*/

			case 'datetimepicker':
				$datetime = DateFormat::convertDateBetweenFormat($_POST[$fieldName], Config::$langVars['datepicker datatime format'], 'Y-m-d H:i:s');
				$returnvalue = $datetime;
			break;

			case 'codicefiscale':
				if ($_POST[$fieldName] != '') {
					[$result, $message] = self::validateCF($_POST[$fieldName],strtoupper((string) Config::$langVars['user']));	
					$returnvalue = $_POST[$fieldName];
				}		
			break;

			case 'partitaiva':
				if ($_POST[$fieldName] != '') {
					[$result, $message] = self::validateVAT($_POST[$fieldName],strtoupper((string) Config::$langVars['user']));
					$returnvalue = $_POST[$fieldName];
				}
			break;

			case 'isemail':
				//echo $fieldPostValue; 
				if ($fieldPostValue != '') {
					$result = self::validateEmail($fieldPostValue);
				}		
			break;

			case 'currency':
				$result = self::validateCurrency($returnvalue);
				if ($result == false) {
					$message = preg_replace('/%FIELD%/',(string) $fieldLabel,(string) Config::$langVars['Il valore %FIELD% non è di un formato valuta!']);	
				}
			break;

			default:
				$returnvalue = '';
			break;
		}

		return [$returnvalue,$result,$message];
	}

	/* VALITAZIONE CAMPI */

	public static function validateExplodearray($array, $opz)
	{
		$opzDef = ['delimiter' => ','];
		$opz = array_merge($opzDef, $opz);
		if (is_array($array)) {
			$array = implode( $opz['delimiter'],$array );
		}
		return $array;
	}

	/*
	public static function validateTime($value, $labelField)
	{
		$time = date('Y-m-d') . ' ' . $value;
		$res = DateFormat::checkDateTimeIso($time);
		if ($res == false) {
			$s = $_lang['La data %FIELD% inserita non è valida!'];
			$s = preg_replace('/%FIELD%/', $labelField, $s);
			self::$resultOp->messages[] = $s;
		}
	}
	*/

	public static function validateDatetimeIso($value)
	{
		//echo '<br>str: '.$value;

		$result = DateFormat::checkDateFormat($value, 'Y-m-d H:i:s');

		//echo '<br>'.($result == true ? 'true' : 'false');

		return $result;

	}
	public static function validateInt($value)
	{
		return intval($value);
	}

	public static function validateFloat($value)
	{
		return filter_var($value, FILTER_VALIDATE_FLOAT);
	}

	public static function validateEmail($email, $strictMode = true)
	{
	    if (empty($email)) {
	        return false;
	    }

	    $validator = new EmailValidator();
	
	    $validations = [
            // Standard RFC-like email validation.
            new RFCValidation(), 

            // RFC-like validation that will fail when warnings* are found.
            new NoRFCWarningsValidation()
        ];
	
	    // Add heavy validations only in strict mode
	    if ($strictMode) {
			// Will check if there are DNS records that signal that the server accepts emails.
            // This does not entails that the email exists.
	        $validations[] = new DNSCheckValidation();

			// Follows RFC2822 for message-id to validate that field, that has some differences in the domain part.
	        $validations[] = new MessageIDValidation();

			// Will check for multi-utf-8 chars that can signal an erroneous email name.
	        $validations[] = new SpoofCheckValidation();
	    }
	
	    $multipleValidations = new MultipleValidationWithAnd($validations);
	    $isValid = $validator->isValid($email, $multipleValidations);

	    // Log if email is invalid or has warnings
	    if (!$isValid || $validator->hasWarnings()) {
	        $warnings = [];
	        if ($validator->hasWarnings()) {
	            foreach ($validator->getWarnings() as $warning) {
	                $warnings[] = $warning->__toString();
	            }
	        }

	        Logger::warning('Email validation issue for address: {email}', [
	            'email' => $email,
	            'valid' => $isValid,
	            'warnings' => $warnings,
	            'strict_mode' => $strictMode
	        ]);
	    }

	    return $isValid;
	}

	public static function validateMinMaxValues($valuesrif,$labelField,$minvalue,$maxvalue)
	{
		if ($valuesrif < $minvalue || $valuesrif > $maxvalue) {
			self::$resultOp->error = 1;
			$s = Config::$langVars['Il campo %FIELD% deve avere un valore superiore o uguale a %MIN% e inferiore o uguale a %MAX%!'];
			$s = preg_replace('/%MIN%/', (string) $minvalue, (string) $s);
			$s = preg_replace('/%MAX%/', (string) $maxvalue, (string) $s);
			$s = preg_replace('/%FIELD%/', (string) $labelField, (string) $s);
			self::$resultOp->messages[] = $s;
		}
		return $valuesrif;
	}

	public static function validateMaxCharsInString($valueCheked,$valueRif)
	{
		return (mb_strlen((string) $valueCheked) > $valueRif ? true : false);
	}

	public static function validateVariableUsername($value)
	{
		$aValid = ['-', '_', '.', ',', '?', '#', '!'];

		if (!ctype_alnum(str_replace($aValid, '', $value))) {
			return false;
		} else {
			return true;
		}
	}

	public static function validateVAT($pi, $country = 'IT')
	{
		$validation = true;
		$message = '';

		if ($pi == '') {
			return [false,''];
		}
		switch ($country) {
			case 'IT':
				// -- BEGIN ITALIAN CHECK
				if (strlen((string) $pi) != 11) {
					$message = "La lunghezza della partita IVA non &egrave;\n" ."corretta: la partita IVA dovrebbe essere lunga\n" ."esattamente 11 caratteri.\n";
					$validation = false;
					return [$validation,$message];
				}
				if (!preg_match("/^[0-9]+$/", (string) $pi)) {
					$message = "La partita IVA contiene dei caratteri non ammessi:\n" ."la partita IVA dovrebbe contenere solo cifre.\n";
					$validation = false;
					return [$validation,$message];
				}
				$s = 0;
				for ($i = 0; $i <= 9; $i += 2) {
					$s += ord($pi[$i]) - ord('0');
				}
				for ($i = 1; $i <= 9; $i += 2) {
					$c = 2 * (ord($pi[$i]) - ord('0'));
					if ($c > 9) $c = $c - 9;
					$s += $c;
				}
				if ((10 - $s % 10) % 10 != ord($pi[10]) - ord('0')) {
					$message = "La partita IVA non &egrave; valida:\n" ."il codice di controllo non corrisponde.";
					$validation  = false;
					return [$validation,$message];
				}
				// -- END ITALIAN CHECK
				break;
				// -- HERE CODE FOR CHECK OTHER COUNTRY
			default:
			break;
		}
		//echo '<br>validation: '.($validation == true ? 'true' : 'false').'<br>';
		return [$validation,$message];
	}

	public static function validateCF($cf, $country = 'IT')
	{

		$validation = true;
		$message = '';
		if ($cf == '') {
			$validation = true;
			$message = '';
			//die(1);
		}
		switch ($country) {
			case 'IT':
				// -- BEGIN ITALIAN CHECK
				if (strlen((string) $cf) == 11) { // e' un codice fiscale di persona giuridica
					[$validation, $message] = self::validateVAT($cf, $country = 'IT');
					return [$validation,$message];

				}
				if (strlen((string) $cf) != 16) {
					$message = "La lunghezza del codice fiscale non &egrave;\n" . "corretta: il codice fiscale dovrebbe essere lungo\n" . "esattamente 16 caratteri.";
					$validation = false;
					return [$validation,$message];
				}
				$cf = strtoupper((string) $cf);
				if (!preg_match("/^[A-Z0-9]+$/", $cf)) {
					$message = "Il codice fiscale contiene dei caratteri non validi:\n" . "i soli caratteri validi sono le lettere e le cifre.";
					$validation = false;
					return [$validation,$message];
				}
				$s = 0;
				for ($i = 1; $i <= 13; $i += 2) {
					$c = $cf[$i];
					if ('0' <= $c && $c <= '9') {
						$s += ord($c) - ord('0');
					} else {
						$s += ord($c) - ord('A');
					}
				}

				for ($i = 0; $i <= 14; $i += 2) {
					$c = $cf[$i];
					switch ($c) {
						case '0':
							$s += 1;
							break;
						case '1':
							$s += 0;
							break;
						case '2':
							$s += 5;
							break;
						case '3':
							$s += 7;
							break;
						case '4':
							$s += 9;
							break;
						case '5':
							$s += 13;
							break;
						case '6':
							$s += 15;
							break;
						case '7':
							$s += 17;
							break;
						case '8':
							$s += 19;
							break;
						case '9':
							$s += 21;
							break;
						case 'A':
							$s += 1;
							break;
						case 'B':
							$s += 0;
							break;
						case 'C':
							$s += 5;
							break;
						case 'D':
							$s += 7;
							break;
						case 'E':
							$s += 9;
							break;
						case 'F':
							$s += 13;
							break;
						case 'G':
							$s += 15;
							break;
						case 'H':
							$s += 17;
							break;
						case 'I':
							$s += 19;
							break;
						case 'J':
							$s += 21;
							break;
						case 'K':
							$s += 2;
							break;
						case 'L':
							$s += 4;
							break;
						case 'M':
							$s += 18;
							break;
						case 'N':
							$s += 20;
							break;
						case 'O':
							$s += 11;
							break;
						case 'P':
							$s += 3;
							break;
						case 'Q':
							$s += 6;
							break;
						case 'R':
							$s += 8;
							break;
						case 'S':
							$s += 12;
							break;
						case 'T':
							$s += 14;
							break;
						case 'U':
							$s += 16;
							break;
						case 'V':
							$s += 10;
							break;
						case 'W':
							$s += 22;
							break;
						case 'X':
							$s += 25;
							break;
						case 'Y':
							$s += 24;
							break;
						case 'Z':
							$s += 23;
							break;
					}
				}
				if (chr($s % 26 + ord('A')) != $cf[15]) {
					$message = "Il codice fiscale non &egrave; corretto:\n" . "il codice di controllo non corrisponde.";
					$validation = false;
					return [$validation,$message];
				}
				// -- END ITALIAN CHECK
				break;
				// -- HERE CODE FOR CHECK OTHER COUNTRY
			default:
				
			break;
		}

		//echo '<br>validation: '.($validation == true ? 'true' : 'false').'<br>';
		return [$validation,$message];
	}

	public static function validateCurrency($value) {
		return preg_match("/^-?[0-9]+(?:\.[0-9]{1,2})?$/", (string) $value);
	}
}
