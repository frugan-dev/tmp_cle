p
defined( 'VALID_ACCESS' ) or die( 'Forbidden!' );

#########################################################################
date_default_timezone_set("Europe/Rome"); 

ini_set('display_errors', 0);
//ini_set('error_reporting', -1);
//ini_set('error_reporting', "E_ALL & ~E_DEPRECATED");
//ini_set('error_reporting', 'E_ALL');
//ini_set('error_reporting', 128);

//ini_set('max_execution_time', '7200');
//ini_set('memory_limit', '1024M');

@ini_set('zlib.output_compression',0);
@ini_set('implicit_flush',1);

//@ob_end_clean();
set_time_limit(0);
setlocale(LC_ALL, 'it_IT.utf8');
 
ob_implicit_flush(1);
//@ob_end_flush();

#########################################################################
include_once(dirname(__FILE__).'/../include.php');
include_once(dirname(__FILE__).'/function.php');
include_once(dirname(__FILE__).'/check.class.php');

#########################################################################
global $gCms, $arr_mlelang;
$config =& $gCms->GetConfig();
$db = &$gCms->GetDb();
$lang = (isset($gCms->current_language)) ? substr($gCms->current_language, 0, 2) : 'it';

#########################################################################
$debug = '';
function debug() {
 global $debug;
 
 $args = func_get_args();
 $debug .= '<h3>'.$args[0].'</h3>'.PHP_EOL;
 for($i = 1, $l = count($args); $i < $l; $i++) {
  if(is_array($args[$i])) $debug .= var_export($args[$i], true).'<br />'.PHP_EOL;
  else $debug .= htmlspecialchars($args[$i]).'<br />'.PHP_EOL;
 }
 $debug .= '<hr />'.PHP_EOL;
 
 return $debug;
}

#########################################################################
$e = false;
$error = array(); 

#########################################################################
$step = (isset($_GET['step']) && array_key_exists('step_'.$_GET['step'].'_title', $arr_mlelang)) ? $_GET['step'] : 1;
if(!isset($_SESSION['step_'.$step])) $_SESSION['step_'.$step] = array();
debug('$_SESSION_step_'.$step, $_SESSION['step_'.$step]);
if(!isset($_SESSION['step_1']['frequency'])) $_SESSION['step_1']['frequency'] = key($config['step_1']);
/*if(!isset($_SESSION['step_2']['pagamento'])) {
 if($_SESSION['step_1']['frequency'] == key($config['step_1'])) $_SESSION['step_2']['pagamento'] = key($config['step_3']);
}*/
if(!isset($_SESSION['step_3']['newsletter'])) $_SESSION['step_3']['newsletter'] = 1;
if(!isset($_SESSION['step_3']['privacy'])) $_SESSION['step_3']['privacy'] = 1;

#########################################################################
$count_step = 0;
for($i=1;$i<=100;$i++) {
 if(array_key_exists('step_'.$i.'_title', $arr_mlelang)) {
  $count_step++;
 }
 else break;
}

######################################################################### pagonline
if((array_key_exists('statoattuale', $_REQUEST)
   && array_key_exists('tipomessaggio', $_REQUEST))
   || array_key_exists('donation_code', $_REQUEST)) {
 
 debug('_REQUEST', $_REQUEST);
 
 try {
  if($_REQUEST['statoattuale'] != 'KO') {
   $request_urldecoded = array_map("urldecode", $_REQUEST);
   
   ######################################################################### DB
   try {
    $query = "UPDATE ".$gCms->config['db_prefix2']."donation
	              SET _REQUEST = ?
	              WHERE id = ?";            
	            
	   $query_array = array(
	    serialize($request_urldecoded),
	    $_SESSION['insert_id']
	   );
         
	   debug('query UPDATE', $query, $query_array);           
	   $db->Execute($query, $query_array) or throwException($db->ErrorMsg());
	  }  
   catch (Exception $e) {  
    include("debug.php");
    
    /* no redirect alla pagina di errore, ma solo invio notifica errore */
    //redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_error_alias'].$config['page_extension']);
   }
   
   ######################################################################### 
   checkout_notify($request_urldecoded);
   checkout_log();
   session_destroy();
   redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_success_alias'].$config['page_extension']);
  
  }
  elseif(isset($_REQUEST['donation_code'])
         && $_REQUEST['donation_code'] == $_SESSION['donation_code']) {
   $request_urldecoded = array_map("urldecode", $_REQUEST);
   
   ######################################################################### DB
   try {
    $query = "UPDATE ".$gCms->config['db_prefix2']."donation
	              SET _REQUEST = ?
	              WHERE donation_code = ?";            
	            
	   $query_array = array(
	    serialize($request_urldecoded),
	    $_REQUEST['donation_code']
	   );
         
	   debug('query UPDATE', $query, $query_array);           
	   $db->Execute($query, $query_array) or throwException($db->ErrorMsg());
	  }  
   catch (Exception $e) {  
    include("debug.php");
    
    /* no redirect alla pagina di errore, ma solo invio notifica errore */
    //redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_error_alias'].$config['page_extension']);
   }
   
   ######################################################################### 
   checkout_notify($request_urldecoded);
   checkout_log();
   session_destroy();
   redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_success_alias'].$config['page_extension']);
  
  } else  {
   throwException($_REQUEST);
  }
 }
 catch (Exception $e) {  
  include("debug.php");
  session_destroy();
  redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_error_alias'].$config['page_extension']);
 }  
}

######################################################################### GetExpressCheckoutDetails
/**
 * This example assumes that this is the return URL in the SetExpressCheckout API call.
 * The PayPal website redirects the user to this page with a token.
 */

// Obtain the token from PayPal.
if(array_key_exists('token', $_REQUEST)
   && $_REQUEST['resp'] != 'cancel') {

 debug('_REQUEST', $_REQUEST);
 
 try {
  // Set request-specific fields.
  $token = urlencode(htmlspecialchars($_REQUEST['token']));
  debug('$token', $token); 
  
  // Add request-specific fields to the request string.
  $nvpStr = "&TOKEN=$token";
  debug('GetExpressCheckoutDetails $nvpStr', $nvpStr); 
  
  // Execute the API operation; see the PPHttpPost function above.
  $httpParsedResponseAr = PPHttpPost('GetExpressCheckoutDetails', $nvpStr);
  
  if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) 
     || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
   debug('GetExpressCheckoutDetails $httpParsedResponseAr', $httpParsedResponseAr);
      
  	// Extract the response details.
  	$payerID = $httpParsedResponseAr['PAYERID'];
  	$street1 = $httpParsedResponseAr["SHIPTOSTREET"];
  	if(array_key_exists("SHIPTOSTREET2", $httpParsedResponseAr)) {
  		$street2 = $httpParsedResponseAr["SHIPTOSTREET2"];
  	}
  	$city_name = $httpParsedResponseAr["SHIPTOCITY"];
  	$state_province = $httpParsedResponseAr["SHIPTOSTATE"];
  	$postal_code = $httpParsedResponseAr["SHIPTOZIP"];
  	$country_code = $httpParsedResponseAr["SHIPTOCOUNTRYCODE"];
  
  	//exit('Get Express Checkout Details Completed Successfully: '.print_r($httpParsedResponseAr, true));
  	
  	######################################################################### DoExpressCheckoutPayment 	
  	/**
    * This example assumes that a token was obtained from the SetExpressCheckout API call.
    * This example also assumes that a payerID was obtained from the SetExpressCheckout API call
    * or from the GetExpressCheckoutDetails API call.
    */
   // Set request-specific fields.
   $payerID = urlencode($payerID);
   //$token = urlencode($_SESSION['token']); // ottenuto da SetExpressCheckout (uguale a $_REQUEST['token'])
   $token = urldecode($httpParsedResponseAr["TOKEN"]); // ottenuto da GetExpressCheckoutDetails (uguale a $_REQUEST['token'])
   
   $paymentType = urlencode("Authorization");			// 'Authorization' or 'Sale' or 'Order'
   $paymentAmount = urlencode($_SESSION['frequency_value']);
   $currencyID = urlencode("EUR");						// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
   
   // Add request-specific fields to the request string.
   $nvpStr = "&TOKEN=$token&PAYERID=$payerID&PAYMENTACTION=$paymentType&AMT=$paymentAmount&CURRENCYCODE=$currencyID";
   debug('DoExpressCheckoutPayment $nvpStr', $nvpStr);
   
   // Execute the API operation; see the PPHttpPost function above.
   $httpParsedResponseAr = PPHttpPost('DoExpressCheckoutPayment', $nvpStr);
   
   if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) 
      || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
   	debug('DoExpressCheckoutPayment $httpParsedResponseAr', $httpParsedResponseAr);
   	//exit('Express Checkout Payment Completed Successfully: '.print_r($httpParsedResponseAr, true));
   	
   	######################################################################### CreateRecurringPaymentsProfile
   	if($_SESSION['step_1']['frequency'] != key($config['step_1'])) {
   	 //$token = urlencode($_SESSION['token']); // ottenuto da SetExpressCheckout (uguale a $_REQUEST['token'])
   	 $token = urldecode($httpParsedResponseAr["TOKEN"]); // ottenuto da DoExpressCheckoutPayment (uguale a $_REQUEST['token'])
   	   
   	 $startDate = urlencode("2011-11-28T0:0:0");
     $billingPeriod = urlencode("Month");				// or "Day", "Week", "SemiMonth", "Year"
     $billingFreq = urlencode("6");						// combination of this and billingPeriod must be at most a year
     
     $nvpStr="&TOKEN=$token&AMT=$paymentAmount&CURRENCYCODE=$currencyID&PROFILESTARTDATE=$startDate";
     $nvpStr .= "&BILLINGPERIOD=$billingPeriod&BILLINGFREQUENCY=$billingFreq";
     debug('CreateRecurringPaymentsProfile $nvpStr', $nvpStr);
     
     $httpParsedResponseAr = PPHttpPost('CreateRecurringPaymentsProfile', $nvpStr);
     
     if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) 
        || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
      debug('CreateRecurringPaymentsProfile $httpParsedResponseAr', $httpParsedResponseAr);   
     	//exit('CreateRecurringPaymentsProfile Completed Successfully: '.print_r($httpParsedResponseAr, true));
   	 } else {
      debug('CreateRecurringPaymentsProfile failed $httpParsedResponseAr', $httpParsedResponseAr); 
    	 //exit('CreateRecurringPaymentsProfile failed: ' . print_r($httpParsedResponseAr, true));
      $e = true;
      include("debug.php");
      session_destroy();
      redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_error_alias'].$config['page_extension']);
     }
   	}
   	
   	$httpParsedResponseAr_urldecoded = array_map("urldecode", $httpParsedResponseAr);
   	debug('$httpParsedResponseAr_urldecoded', $httpParsedResponseAr_urldecoded);
    $nvpStr_urldecoded = array_map("urldecode", parse_query($config['root_url'].'?'.$nvpStr));
    $nvpStr_urldecoded = array_filter($nvpStr_urldecoded);
    $nvpStr_urldecoded = array_unique($nvpStr_urldecoded);
   	debug('$nvpStr_urldecoded', $nvpStr_urldecoded);
   	
   	######################################################################### DB
    try {
     $query = "UPDATE ".$gCms->config['db_prefix2']."donation
	 	             SET nvpStr = ?,
	 	             httpParsedResponseAr = ?
	 	             WHERE id = ?";            
	 	           
	 	  $query_array = array(
	 	   $nvpStr,
	 	   serialize($httpParsedResponseAr_urldecoded),
	 	   $_SESSION['insert_id']
	 	  );
          
	 	  debug('query UPDATE', $query, $query_array);           
	 	  $db->Execute($query, $query_array) or throwException($db->ErrorMsg());
	 	 }  
    catch (Exception $e) {  
     include("debug.php");
     
     /* no redirect alla pagina di errore, ma solo invio notifica errore */
     //redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_error_alias'].$config['page_extension']);
    }
    
    ######################################################################### 
    checkout_notify($httpParsedResponseAr_urldecoded, $nvpStr_urldecoded);
    checkout_log();
    session_destroy();
    redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_success_alias'].$config['page_extension']);
   	
   } else  {
    debug('DoExpressCheckoutPayment failed $httpParsedResponseAr', $httpParsedResponseAr); 
   	//exit('DoExpressCheckoutPayment failed: ' . print_r($httpParsedResponseAr, true));
    throwException($httpParsedResponseAr);
   }
  } else  {
   debug('GetExpressCheckoutDetails failed $httpParsedResponseAr', $httpParsedResponseAr); 
  	//exit('GetExpressCheckoutDetails failed: ' . print_r($httpParsedResponseAr, true));
   throwException($httpParsedResponseAr);
  }
 }
 catch (Exception $e) {  
  include("debug.php");
  redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_error_alias'].$config['page_extension']);
 }
}

#########################################################################
if(isset($_POST['submit']) && $_POST['submit']==true) {
 debug('$_POST', $_POST);
 $postdata = array_map("trim", $_POST);
 extract($postdata);
 foreach($postdata as $key => $value) {
  $_SESSION['step_'.$step][$key] = $value;
 } 
 debug('$_SESSION_step_'.$step, $_SESSION['step_'.$step]);
 
 $error = check_all($step);
 
 #########################################################################
 if(count($error) == 0) {
  
  #########################################################################
  if($step == $count_step) {
   
   $site_url = clear_url($config['root_url']);
   
   ######################################################################### NEWSLETTER
   $found = $newuser = false;
   $userid = '';
   
   if($_SESSION['step_3']['newsletter']) {
    try {
     $nms = cge_utils::get_module('NMS');
     
     $query = "SELECT * FROM ".NMS_USERS_TABLE." WHERE email = ?";
     $query_array = array($_SESSION['step_3']['email']);
 
     debug('query newsletter', $query, $query_array);
     $row = $db->GetRow($query, $query_array); 
     
     // email already exists
     if($row) {
      $userid = $row['userid'];
      $uniqueid = $row['uniqueid'];
      
      // update the lists he's a member of
	     $query = 'SELECT userid FROM '.NMS_LISTUSER_TABLE.' 
                 WHERE listid = ? AND userid = ?';
      $query_array = array($config['checkout_nms_list_id'], $userid);          
     
      debug('query newsletter', $query, $query_array);        
	     $found = $db->GetOne($query, $query_array); 
	    }
	    
	    // email doesn't exists
	    else {
	     $newuser = true;
 	
 	    $uniqueid = md5(uniqid(rand(),1));
 	    $confirmed = ($nms->GetPreference('require_confirmation',1)==1)?0:1;
 	    
 	    $query_array = array(); 
      $query_array["email"] = $_SESSION['step_3']['email'];
      $query_array["username"] = $_SESSION['step_3']['name'].' '.$_SESSION['step_3']['surname'];
      $query_array["uniqueid"] = $uniqueid;
      $query_array["disabled"] = 0;
      $query_array["confirmed"] = $confirmed;
      $query_array["htmlemail"] = 1;
      $query_array["dateadded"] = trim($db->DBTimeStamp(time()),"'");
	     
      $query = "INSERT INTO ".NMS_USERS_TABLE." 
            (
            email, 
            username, 
            uniqueid, 
            disabled, 
            confirmed, 
            htmlemail, 
            dateadded
            ) 
            VALUES (?,?,?,?,?,?,?)";      
            
      debug('query newsletter', $query, $query_array);
      $db->Execute($query, $query_array) or throwException($db->ErrorMsg()); 
     }
     
     debug('found', $found);
     debug('newuser', $newuser);
     
     if(!$found || $newuser) {
      // get user id back from the table
      $query = "SELECT userid FROM ".NMS_USERS_TABLE." WHERE uniqueid = ?";
      $query_array = array($uniqueid);
      
      debug('query !$found || $newuser', $query, $query_array);
      $dbresult = $db->Execute($query,$query_array) or throwException($db->ErrorMsg()); 
           
      if($dbresult && $dbresult->RecordCount() > 0) {
	      $row = $dbresult->FetchRow();
	      $userid = $row['userid'];
	      
	      // add him to his member lists
	      $query_array = array();
	      $query_array["userid"] = $userid;
	      $query_array["listid"] = $config['checkout_nms_list_id'];
	      $query_array["active"] = 1;
	      $query_array["entered"] = trim($db->DBTimeStamp(time()),"'");
	      
	      $query = "INSERT INTO ".NMS_LISTUSER_TABLE." 
                   (
                   userid, 
                   listid, 
                   active, 
                   entered
                   ) 
                   VALUES (?,?,?,?)";
                   
        debug('query newsletter', $query, $query_array);            
	      $db->Execute($query, $query_array) or throwException($db->ErrorMsg());  	
      }
     }
      
     if($newuser) {  	
      # see if the CMSMailer module is installed
      $cmsmailer_installed = FALSE;
      $cmsmailer = NULL;
      foreach($gCms->modules as $module) {
      	if (strtolower(get_class($module['object'])) == 'cmsmailer') { 
      		$cmsmailer_installed = TRUE;
      		$cmsmailer =& $module['object'];
      		break;
      	}
      } 	
      	
      $headers = "MIME-Version: 1.0\nContent-Transfer-Encoding: 8bit\nContent-Type: text/plain; charset=\"UTF-8\"";
      //$headers = "MIME-Version: 1.0\nContent-Transfer-Encoding: 8bit\nContent-Type: text/html; charset=\"UTF-8\"";
      
      $subject = html_entity_decode($arr_mlelang['obj_conferma'], ENT_QUOTES, 'UTF-8');
      
      $confirm_email = $nms->_cleanLink(
       $nms->_CreatePrettyLink($config['nms_unsubscribe_page_id'],$config['nms_unsubscribe_page_id'],'confirm_email',
		    	$nms->Lang('confirm'), 
		    	array('uniqueid' => $uniqueid), '', true)
		    );
      
      $full_message = $arr_mlelang['mail_sottoscrizione_1'].' '.$_SESSION['step_3']['name'].' '.$_SESSION['step_3']['surname'].'
      
'.sprintf($arr_mlelang['mail_sottoscrizione_2'], $site_url).'

'.$arr_mlelang['mail_sottoscrizione_3'].'

'.$confirm_email.'

'.$arr_mlelang['mail_sottoscrizione_4'].'

'.$arr_mlelang['mail_sottoscrizione_5'];
        
      if($cmsmailer_installed) {
 	    	$cmsmailer->SetCharSet('UTF-8');
 	    	$cmsmailer->AddAddress($_SESSION['step_3']['email']);
 	    	$cmsmailer->SetSubject($subject);
 	    	$cmsmailer->SetBody($full_message);
 	    	$cmsmailer->Send();
 	    	$cmsmailer->reset();
 	    } else {
 	    	$encoded_subject = "=?UTF-8?B?".base64_encode($subject)."?=\n";
 	    	mail(
 	    		$_SESSION['step_3']['email'], 	// address to send the mail to
 	    		$encoded_subject, 	// E-mail subject
 	    		$full_message,			// The E-mail message
 	    		$headers		// Additional mail headers
 	     );    
 	    }
	    }
	   }  
    catch (Exception $e) { 
     $error[] = 'Si è verificato un problema tecnico. Riprova più tardi.';
    }
   }
   
   if(count($error) == 0) {
    ######################################################################### DB
    try {
     $query = "INSERT INTO ".$gCms->config['db_prefix2']."donation
		            (
		            donation_code,
		            pagamento,
		            frequency,
		            frequency_value,
		            name,
		            surname, 
		            email,
		            phone,
		            luogo_nascita,
		            data_nascita,
		            codice_fiscale,
		            istituto_bancario,
		            filiale,
		            iban,
		            nms_userid
		            ) 
		            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		   
		   $_SESSION['donation_code'] = strtoupper(substr(md5(session_id() + rand()*100000), 0, 8));         
		   $_SESSION['frequency_value'] = ($_SESSION['step_1'][$_SESSION['step_1']['frequency'].'_value'] == 'altro') ? $_SESSION['step_1'][$_SESSION['step_1']['frequency'].'_altro_value'] : $_SESSION['step_1'][$_SESSION['step_1']['frequency'].'_value'];        
		   $nms_userid = ($found || $newuser) ? $userid : NULL;  
		              
		   $luogo_nascita = $data_nascita = $codice_fiscale = $istituto_bancario = $filiale = $iban = NULL;
		   
		   switch($_SESSION['step_2']['pagamento']) { 
      case 'rid':
       if($_SESSION['step_3']['luogo_nascita']) $luogo_nascita = $_SESSION['step_3']['luogo_nascita'];
       if($_SESSION['step_3']['data_nascita']) $data_nascita = $_SESSION['step_3']['data_nascita']; 
       if($_SESSION['step_3']['codice_fiscale']) $codice_fiscale = $_SESSION['step_3']['codice_fiscale']; 
       if($_SESSION['step_3']['istituto_bancario']) $istituto_bancario = $_SESSION['step_3']['istituto_bancario'];
       if($_SESSION['step_3']['filiale']) $filiale = $_SESSION['step_3']['filiale'];
       $iban = NULL;
       
       break;
     } 
        
		   $query_array = array(
      $_SESSION['donation_code'],
      $_SESSION['step_2']['pagamento'],
		    $_SESSION['step_1']['frequency'],
		    $_SESSION['frequency_value'],
		    $_SESSION['step_3']['name'],
		    $_SESSION['step_3']['surname'],
		    $_SESSION['step_3']['email'],
		    $_SESSION['step_3']['phone'],
		    $luogo_nascita,
		    $data_nascita,
		    $codice_fiscale,
		    $istituto_bancario,
		    $filiale,
		    $iban,
		    $nms_userid
		   );          
		             
		   debug('query INSERT', $query, $query_array);           
		   $db->Execute($query, $query_array) or throwException($db->ErrorMsg());
		   
		   $_SESSION['insert_id'] = $db->Insert_ID() or throwException($db->ErrorMsg());
	    $affected_rows = $db->Affected_Rows() or throwException($db->ErrorMsg());
		  }  
    catch (Exception $e) {  
     $error[] = 'Si è verificato un problema tecnico. Riprova più tardi.';
    }
    
    if(count($error) == 0) { 
     #########################################################################
     switch($_SESSION['step_2']['pagamento']) {
      
      #########################################################################
      case 'bonifico':
       checkout_notify();
       checkout_log();
       session_destroy();
       redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_success_alias'].$config['page_extension']);
       break;
       
      #########################################################################
      case 'bollettino':
       checkout_notify();
       checkout_log();
       session_destroy();
       redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_success_alias'].$config['page_extension']);
       break;
      
      #########################################################################
      case 'postepay':
       checkout_notify();
       checkout_log();
       session_destroy();
       redirect('http://www.postepay.it');
       break;
      
      #########################################################################
      case 'rid':
       checkout_notify();
       checkout_log();
       session_destroy();
       redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_success_alias'].$config['page_extension']);
       break;
      
      ######################################################################### 
      case 'cartadicredito':
       //28/04/2004: NUOVA ASSEGNAZIONE VARIABILI PROVENIENTI DALLA FORM ORDINE IN POST
       $numeroCommerciante = $config['pagonline_numeroCommerciante'];
       $stabilimento = $config['pagonline_stabilimento'];
       $userID = $config['pagonline_userID'];
       $password = $config['pagonline_password'];
       $numeroOrdine = $_SESSION['donation_code']; // alfanumerico di max ... caratteri
       $totaleOrdine = (int)($_SESSION['frequency_value'] * 100); // importo intero in formato ISO (senza separatori decimali). Per 1,00 Euro mettere 100
       $valuta = $config['pagonline_valuta'];
       $flagRiciclaOrdine = $config['pagonline_flagRiciclaOrdine'];
       $flagDeposito = $config['pagonline_flagDeposito'];
       $tipoRispostaApv = $config['pagonline_tipoRispostaApv'];
  
       $urlOk = $config['root_url'].'/'.$lang.'/'.$config['checkout_uri'].$config['page_extension'].'?resp=ok&donation_code='.$_SESSION['donation_code']; // Valori: un indirizzo http o https valido.
       //$urlKo = $config['root_url'].'/'.$lang.'/'.$config['checkout_uri'].$config['page_extension'].'?resp=ko'; // Valori: un indirizzo http o https valido.
       $urlKo = $config['root_url'].'/'.$lang.'/'.$config['checkout_error_alias'].$config['page_extension'];
               
       // Assegnazione tipo di operazione
       $stringaSegreta = $config['pagonline_stringaSegreta']; // da cambiare con la stringa segreta dell'esercente: in asp e jsp viene letta dal file di properties
 
       // qui potrei aggiungere gli eventuali parametri facoltativi :
       // 'tipoPagamento' e 'causalePagamento'
       //$tipoPagamento = $config['pagonline_tipoPagamento'];
       $causalePagamento = 'Codice donazione: '.$_SESSION['donation_code'];

       // Concatenazione input per il calcolo del MAC
       $inputMac  = "numeroCommerciante=".trim($numeroCommerciante);
       $inputMac .= "&stabilimento=".trim($stabilimento);
       $inputMac .= "&userID=".trim($userID);
       $inputMac .= "&password=".trim($password);
       $inputMac .= "&numeroOrdine=".trim($numeroOrdine);
       $inputMac .= "&totaleOrdine=".trim($totaleOrdine);
       $inputMac .= "&valuta=".trim($valuta);
       $inputMac .= "&flagRiciclaOrdine=".trim($flagRiciclaOrdine);
       $inputMac .= "&flagDeposito=".trim($flagDeposito);
       $inputMac .= "&tipoRispostaApv=".trim($tipoRispostaApv);
       $inputMac .= "&urlOk=".trim($urlOk);
       $inputMac .= "&urlKo=".trim($urlKo);
       //$inputMac .= "&tipoPagamento=".trim($tipoPagamento);
       $inputMac .= "&causalePagamento=".trim($causalePagamento);   
       
       $inputMac .= "&".trim($stringaSegreta);
       debug('$inputMac', $inputMac); 
       
       //Calcolo della firma digitale della stringa in input
       $MAC = md5($inputMac);
       $MACtemp = "";
       for($i=0;$i<strlen($MAC);$i=$i+2) {
       	$MACtemp .= chr(hexdec(substr($MAC,$i,2)));
       }
       $MAC = $MACtemp;
       
       // Codifica del MAC con lo standard BASE64
       $MACcode = base64_encode($MAC);
       debug('$MACcode', $MACcode);
       
       // Concatenazione input per URL di USI
       $inputUrl = $config['pagonline_inputUrl']."?numeroCommerciante=".trim($numeroCommerciante);
       $inputUrl .= "&stabilimento=".trim($stabilimento);
       $inputUrl .= "&userID=".trim($userID);
       $inputUrl .= "&password=".trim($password); //la password vera viene usata solo per il calcolo del MAC e non viene inviata al sito dei pagamenti (qui è sostituita con il valore fittizio "Password")
       $inputUrl .= "&numeroOrdine=".trim($numeroOrdine);
       $inputUrl .= "&totaleOrdine=".trim($totaleOrdine);
       $inputUrl .= "&valuta=".trim($valuta);
       $inputUrl .= "&flagRiciclaOrdine=".trim($flagRiciclaOrdine);
       $inputUrl .= "&flagDeposito=".trim($flagDeposito);
       $inputUrl .= "&tipoRispostaApv=".trim($tipoRispostaApv);
       $inputUrl .= "&urlOk=".urlencode(trim($urlOk));
       $inputUrl .= "&urlKo=".urlencode(trim($urlKo));
       //$inputUrl .= "&tipoPagamento=".trim($tipoPagamento);
       $inputUrl .= "&causalePagamento=".urlencode(trim($causalePagamento));        
       
       $inputUrl .= "&mac=".urlencode(trim($MACcode));
       debug('$inputUrl', $inputUrl);
       
       checkout_pre_notify();
       checkout_log();
                
       //echo '<!-- Invio transazione alla banca -->
       //<script language="JavaScript">
       //<!--
       //	document.location.href="'.$inputUrl.'";
       ////-->
       //</script>';       
       //die();
       
       redirect($inputUrl);
       break;
         
      #########################################################################     
      case 'paypal':

       try {
        // Set request-specific fields.
        $paymentAmount = urlencode($_SESSION['frequency_value']);
        $currencyID = urlencode('EUR');							// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
        $paymentType = urlencode('Authorization');				// 'Authorization' or 'Sale' or 'Order'
        
        $returnURL = urlencode($config['root_url'].'/'.$lang.'/'.$config['checkout_uri'].$config['page_extension'].'?resp=return');
        $cancelURL = urlencode($config['root_url'].'/'.$lang.'/'.$config['checkout_uri'].$config['page_extension'].'?resp=cancel');
        
        // Add request-specific fields to the request string.
        $nvpStr = "&Amt=$paymentAmount&ReturnUrl=$returnURL&CANCELURL=$cancelURL&PAYMENTACTION=$paymentType&CURRENCYCODE=$currencyID";
        debug('SetExpressCheckout $nvpStr', $nvpStr); 
        
        // Execute the API operation; see the PPHttpPost function above.
        $httpParsedResponseAr = PPHttpPost('SetExpressCheckout', $nvpStr);   
        
        if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) 
           || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
        	debug('SetExpressCheckout $httpParsedResponseAr', $httpParsedResponseAr);  
        	
        	// Redirect to paypal.com.
        	$token = urldecode($httpParsedResponseAr["TOKEN"]);
        	//$_SESSION['token'] = $token = urldecode($httpParsedResponseAr["TOKEN"]);
        	
        	$payPalURL = "https://www.paypal.com/webscr&cmd=_express-checkout&token=$token";
	        if("sandbox" === $config['paypal_environment'] 
	           || "beta-sandbox" === $config['paypal_environment']) {
	        	$payPalURL = "https://www.".$config['paypal_environment'].".paypal.com/webscr&cmd=_express-checkout&token=$token";
	        }
	        debug('SetExpressCheckout $payPalURL', $payPalURL); 
	        
        	checkout_pre_notify();
        	checkout_log();
        	
	        redirect($payPalURL);
         
        } else  {
         debug('SetExpressCheckout failed $httpParsedResponseAr', $httpParsedResponseAr);
         //exit('SetExpressCheckout failed: ' . print_r($httpParsedResponseAr, true));
         $e = true;
         $error[] = 'Si è verificato un problema tecnico. Riprova più tardi.';
         //redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_error_alias'].$config['page_extension']);
        }
       }
       catch (Exception $e) {  
        $error[] = 'Si è verificato un problema tecnico. Riprova più tardi.';
       }
       break;    
      
      default:
       $e = true;
       $error[] = 'Si è verificato un problema tecnico. Riprova più tardi.';
       //redirect($config['root_url'].'/'.$lang.'/'.$config['checkout_error_alias'].$config['page_extension']);
       break;
     }
    }
   }
  }
  
  #########################################################################
  else {
   $step++;
   header('Location: '.$config['root_url'].'/'.$lang.'/'.$config['checkout_uri'].$config['page_extension'].'?step='.$step);
   exit();
  }
 }
}

#########################################################################
include("form.php"); 
include("debug.php");