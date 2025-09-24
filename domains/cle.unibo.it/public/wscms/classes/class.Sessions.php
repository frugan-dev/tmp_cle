<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * classes/class.Sessions.php v.1.3.0. 14/09/2020
*/

class my_session {
   public $my_session_id; // il tempo per la garbage collection
   public $table_name = SESSIONS_TABLE_NAME; //'sitodemo509_sessions'; // il tempo per la garbage collection
   public $cookie_name = SESSIONS_COOKIE_NAME; // il tempo per la garbage collection

   //il costruttore della classe, inizializza le variabili
   public function __construct(public $session_time, public $session_gc_time,$cookie_name_def='') { 
   	if ($cookie_name_def != '') $this->cookie_name = $cookie_name_def;
      $this->my_session_id = (!isset($_COOKIE[$this->cookie_name]))
      ? md5(uniqid(microtime()))
      : $_COOKIE[$this->cookie_name];
      }

   // avvia o aggiorna la sessione
   public function my_session_start() {
   	$pdoCore = Sql::getInstanceDb();
      $cookie_expire = ($this->session_time > 0) ? (time() + $this->session_time) : 0;
		$cookieDomain = $_SERVER['SERVER_NAME'];
		$cookieSecure = false;	
		if (isset($_SERVER['HTTPS'])) $cookieSecure = true;
		$cookieHttponly = false; 
      if (!isset($_COOKIE[$this->cookie_name])) {
         setcookie($this->cookie_name, (string) $this->my_session_id, ['expires' => $cookie_expire, 'path' => '/', 'domain' => (string) $cookieDomain, 'secure' => $cookieSecure, 'httponly' => $cookieHttponly]);
         $sql = "INSERT INTO ".$this->table_name." VALUES('".$this->my_session_id."', '', " .time(). ")";
         $result = $pdoCore->query($sql) or die('Errore db linea 36!');
         } else {
            if ($this->session_time > 0) setcookie($this->cookie_name, (string) $this->my_session_id, ['expires' => $cookie_expire, 'path' => '/', 'domain' => (string) $cookieDomain, 'secure' => $cookieSecure, 'httponly' => $cookieHttponly]);
            }
      }

   /*   registra la variabili di sessione specificata nel database */
   public function my_session_register($name, $value) {   
      $_MY_SESSION = [];
      $sql = "SELECT session_vars FROM ".$this->table_name." WHERE sessid = '{$this->my_session_id}'";
      $pdoCore = Sql::getInstanceDb();
      $result = $pdoCore->query($sql) or die('Errore db linea 48!');
      if ($pdoCore->query("SELECT FOUND_ROWS()")->fetchColumn() > 0) {
         $row = $result->fetch(PDO::FETCH_ASSOC);              		
       	$_MY_SESSION = unserialize($row['session_vars']); 
       	$_MY_SESSION[$name] = $value;   	
       	Sql::initQuery($this->table_name,['session_vars'],[serialize($_MY_SESSION),$this->my_session_id],'sessid = ?');
       	Sql::updateRecord();
         } else {
            $_MY_SESSION[$name] = $value;
            $sql_b = "UPDATE ".$this->table_name." SET session_vars = '" . serialize($_MY_SESSION) . "' WHERE sessid = '{$this->my_session_id}'";
   			$result_b = $pdoCore->query($sql_b) or die('Errore db linea 59!');
            }
      }

    public function my_session_unsetVar($name) {   
      $_MY_SESSION = [];
      $sql = "SELECT session_vars FROM ".$this->table_name." WHERE sessid = '{$this->my_session_id}'";
      $pdoCore = Sql::getInstanceDb();
      $result = $pdoCore->query($sql) or die('Errore db linea 48!');
      if ($pdoCore->query("SELECT FOUND_ROWS()")->fetchColumn() > 0) {
         $row = $result->fetch(PDO::FETCH_ASSOC);              		
       	$_MY_SESSION = unserialize($row['session_vars']); 
       	unset($_MY_SESSION[$name]);
       	$sql_a = "UPDATE ".$this->table_name." SET session_vars = '" . serialize($_MY_SESSION) . "' WHERE sessid = '{$this->my_session_id}'";
         $result_a = $pdoCore->query($sql_a) or die('Errore db linea 55!');
         } else {
            $_MY_SESSION[$name] = '';
            $sql_b = "UPDATE ".$this->table_name." SET session_vars = '" . serialize($_MY_SESSION) . "' WHERE sessid = '{$this->my_session_id}'";
   			$result_b = $pdoCore->query($sql_b) or die('Errore db linea 59!');
            }
      }


   /*   legge e restituisce le variabili di sessione (o la singola variabile specificata */
   public function my_session_read($key = '') {
      $sql = "SELECT session_vars FROM ".$this->table_name." WHERE sessid = '{$this->my_session_id}'";  
      $pdoCore = Sql::getInstanceDb();
      $result = $pdoCore->query($sql) or die('Errore db linea 70!');
      if ($pdoCore->query("SELECT FOUND_ROWS()")->fetchColumn() > 0) {
      	$row = $result->fetch(PDO::FETCH_ASSOC); 
         $row['session_vars'] = stripslashes((string) $row['session_vars']);
         $session_vars = unserialize($row['session_vars']);
         return (isset($key) && $key) ? $session_vars[$key] : $session_vars;        
         }     
      }

   /*   distrugge la sessione, rimuovendo i relativi dati (non cancella il cookie) */
   public function my_session_destroy() {
   	$pdoCore = Sql::getInstanceDb();
      $sql = "UPDATE ".$this->table_name." SET session_vars = '' WHERE sessid = '{$this->my_session_id}'";
      $result = $pdoCore->query($sql) or die('Errore db linea 84!');
      }

   // procedura di garbage collection
   public function my_session_gc() {
      $pdoCore = Sql::getInstanceDb();
      $sql = "DELETE FROM ".$this->table_name." WHERE session_date < " . (time() - $this->session_gc_time);
      $result = $pdoCore->query($sql) or die('Errore db linea 92!');
      setcookie($this->cookie_name);
   }

  	public function addSessionsModuleVars($sessionsVars,$app,$sessionsDef) {
		$refresh = false;
		// se non esiste la sessione la crea di fdefault
		if (!isset($sessionsVars[$app])) {
			$sessionsVars[$app] = [];
			$refresh = true;
			}
		// controlla se sono settati i parametri di defautl
		if (is_array($sessionsDef) && count($sessionsDef) > 0) {
			foreach ($sessionsDef AS $key=>$value) {
				if (!isset($sessionsVars[$app][$key])) {
					$sessionsVars[$app][$key] = $value;
					$refresh = true;
					}				
  				}  
  			} 

		if ($refresh == true) {		
		   $this->my_session_register($app,$sessionsVars[$app]);	
			$sessionsVars = [];
			$sessionsVars = $this->my_session_read();
			}
  		return $sessionsVars;  	
  		}   

  	public function addSessionsModuleSingleVar($sessionsVars,$app,$key,$var) {
		$sessionsVars[$app][$key] = $var;		
		$this->my_session_register($app,$sessionsVars[$app]);	
		$sessionsVars = [];
		$sessionsVars = $this->my_session_read();
  		return $sessionsVars;  			
  		}	

  	public function addSessionsSingleVar($sessionsVars,$value) {	
		$this->my_session_register($sessionsVars,$value);	
		$sessionsVars = [];
		$sessionsVars = $this->my_session_read();
  		return $sessionsVars;  			
  		}	    
  	}	
  // end class
?>