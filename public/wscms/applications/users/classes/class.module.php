<?php
/* wscms/users/class.module.php v.3.5.3. 12/09/2018 */

class Module {
	public $error;
	public $message;
	public $messages;
	public $errorType;

	public function __construct(private $action,$table) 	{
		$this->appTable = $table;
		$this->error = 0;	
		$this->message ='';
		$this->messages = [];
		}

		
	public function getUserTemplatesArray() {		
		$arr = [];
		if ($handle = opendir('templates/')){
			while ($file = readdir($handle)) {
				if (is_dir('templates/'.$file)) {
					if ($file != "." && $file != "..") $arr[] = $file;
		  			}
				}
			}
		closedir($handle);	
		return $arr;		
		}	

		
	public function checkUsername($id,$_lang) {
		$this->error = 0;	
		$this->message ='';
		$this->messages = [];
		$oldUsername = '';
		$App = new stdClass;	
      $App->oldItem = new stdClass;	
		if (intval($id) > 0) {
			/* recupera i dati memorizzati */
			Sql::initQuery($this->appTable,['username'],[$id],'id = ?');	
			$App->oldItem = Sql::getRecord();
			$oldUsername = $App->oldItem->username;			
			}
		if ($oldUsername != $_POST['username']) {
			Sql::initQuery($this->appTable,['id'],[$_POST['username']],'username = ?');
			$count = Sql::countRecord();
			if ($count > 0) {
				$this->message = preg_replace('/%USERNAME%/',(string) $_POST['username'],(string) $_lang['Username %USERNAME% risulta già presente nel nostro database!']);
	      	$this->error = 1;
	      	$this->errorType = 1;
	   		}	
	   	}
	   return $_POST['username'];
		}
		
	public function checkEmail($id,$_lang) {
		$this->error = 0;	
		$this->message ='';
		$this->messages = [];
		$oldEmail = '';
		$App = new stdClass;	
      $App->oldItem = new stdClass;	
		if (intval($id) > 0) {
			/* recupera i dati memorizzati */
			Sql::initQuery($this->appTable,['email'],[$id],'id = ?');
			$App->oldItem = Sql::getRecord();
			$oldEmail = $App->oldItem->email;			
			}
		if($oldEmail != $_POST['email']) {
			Sql::initQuery($this->appTable,['id'],[$_POST['email']],'email = ?');
			$count = Sql::countRecord();
			if ($count > 0) {
				$this->message = preg_replace('/%EMAIL%/',(string) $_POST['email'],(string) $_lang['Indirizzo email %EMAIL% risulta già presente nel nostro database!']);
	      	$this->error = 1;
	      	$this->errorType = 1;
	   		}	
	   	}
	   return $_POST['email'];
		}

		
	public function checkPassword($id,$_lang) {
		$this->error = 0;	
		$this->message ='';
		$this->messages = [];
		$id = intval($id);
		$App = new stdClass;	
      $App->oldItem = new stdClass;
		$oldPassword = '';
		if ($id > 0) {
			/* modifica*/
			/* recupera i dati memorizzati */
			Sql::initQuery($this->appTable,['password'],[$id],'id = ?');
			$App->oldItem = Sql::getRecord();
			$oldPassword = $App->oldItem->password;	
			if ($_POST['password'] != '') {
				if ($_POST['password'] === $_POST['passwordCF']) {
					//$_POST['password'] = md5($_POST['password']);
					$_POST['password'] = password_hash((string) $_POST['password'], PASSWORD_DEFAULT);	
		   		}	else {
		   			$_POST['password'] = $oldPassword;
		   			$this->message = $_lang['Le due password non corrispondono! Sarà comunque mantenuta quella precedentemente memorizzata'];
			      	$this->errorType = 2;
		   			}
				
				} else {
      			$_POST['password'] = $oldPassword;
      			}
			
			} else {
				/* inserisci */
				if ($_POST['password'] != '') {
					if ($_POST['password'] === $_POST['passwordCF']) {
		      			//$_POST['password'] = md5($_POST['password']);
		      			$_POST['password'] = password_hash((string) $_POST['password'], PASSWORD_DEFAULT);
		      		} else {
		      			$this->message = $_lang['Le due password non corrispondono!'];
		      			$this->error = 1;	
		      			$this->errorType = 1;	      			
		      			}
					} else {
				 		$this->message = $_lang['Devi inserire la password!'];
		      		$this->error = 1;
	      			$this->errorType = 1;
				 	}
				}
	   return $_POST['password'];
		}
		
	public function checkUsernameAjax($id,$username){
		$App = new stdClass;	
      $App->oldItem = new stdClass;
		$oldUsername = '';
		$count = 0;
		if (intval($id) > 0) {
			/* recupera i dati memorizzati */		
			Sql::initQuery($this->appTable,['username'],[$id],'id = ?');	
			$App->oldItem = Sql::getRecord();
			$oldUsername = $App->oldItem->username;			
			}
		if($oldUsername != $username) {
			Sql::initQuery($this->appTable,['id'],[$_POST['username']],'username = ?');
			$count = Sql::countRecord();
			}
		return $count;
		}

	public function checkEmailAjax($id,$email){
		$App = new stdClass; 
		$oldItem = new stdClass;     
		$oldEmail = '';
		$count = 0;
		if (intval($id) > 0) {
			/* recupera i dati memorizzati */			
			Sql::initQuery($this->appTable,['email'],[$id],'id = ?');	
			$oldItem = Sql::getRecord();
			$oldEmail = $oldItem->email;			
			}
		if ($oldEmail != $email) {
			Sql::initQuery($this->appTable,['id'],[$email],'email = ?');
			$count = Sql::countRecord();
			}
		return $count;
		}

	public function getAvatarData($id,$_lang) {
		$this->error = 0;	
		$this->message ='';
		$this->messages = [];
		$avatar = '';
		$avatar_info = '';		
		if ($id > 0) {
			$oldItem = new stdClass;		
			Sql::initQuery($this->appTable,['avatar','avatar_info'],[$id],'id = ?');	
			$oldItem = Sql::getRecord();
			$avatar = '';
			$avatar_info = '';
			if (isset($oldItem->avatar)) $avatar = $oldItem->avatar;
			if (isset($oldItem->avatar_info)) $avatar_info = $oldItem->avatar_info;
			}		
		if (isset($_FILES['avatar']) && is_uploaded_file($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['size'] > 0) {	
			if ($_FILES['avatar']['error'] == 0 ) {         
            $array_avatarInfo = [];
            $max_size = 40000;
            $result = @is_uploaded_file($_FILES['avatar']['tmp_name']);
            if (!$result) {
               $this->message = $_lang['Impossibile eseguire upload! Se è presente è stato mantenuto il file precedente!'];
               $this->error = 0;
               $this->errorType = 2;
               } else {
                  $size = $_FILES['avatar']['size'];
                  if ($size > $max_size) {
                    	$this->message = $_lang['Il file indicato è troppo grande! Dimensioni massime %DIMENSIONS% Kilobyte. Se il file precedente è presente è stato mantenuto il file precedente!'];
							$this->message = preg_replace('/%DIMENSIONS%/',($max_size / 1000),(string) $this->message);       				
           				$this->error = 0;
           				$this->errorType = 2;
           				$App = new stdClass;			         	
                     } else {
                     	$array_avatarInfo['type'] = $_FILES['avatar']['type'];
                  		$array_avatarInfo['nome'] = $_FILES['avatar']['name'];
                  		$array_avatarInfo['size'] = $_FILES['avatar']['size'];
                  		$avatar = @file_get_contents($_FILES['avatar']['tmp_name']);
                 			$avatar_info = serialize($array_avatarInfo);
                 			}                  
                  }
             }	else {
             	$this->message = $_lang['Impossibile eseguire upload: problemi accesso immagine! Se è presente è stato mantenuto il file precedente!'];
               $this->error = 1;
             	}	            
         }
		return [$avatar,$avatar_info];
		}
		
	public function renderAvatarData($id) {
		$avatar = '';
		$info = '';
		if (intval($id) > 0) {
			/* recupera i dati memorizzati */
			$this->itemData = new stdClass;		
			Sql::initQuery($this->appTable,['avatar','avatar_info'],[$id],'id = ?');	
			$this->itemData = Sql::getRecord();
			$avatar = $this->itemData->avatar;
			$info = $this->itemData->avatar_info;
			}	
		return [$avatar,$info];
		}
	}
?>