<?php
/* wscms/site-core/module-class.php v.3.0.0. 19/10/2016 */

class Module {
	public $error;
	public $message;
	public $messages;
	public $errorType;
	
	public function __construct($table) {
		$this->appTable = $table;
		$this->error = 0;
		$this->message = '';
		$this->messages = array();
		}
	
	public function getAvatarData($id,$_lang) {
		$this->error = 0;
		$avatar = '';
		$avatar_info = '';		
		if ($id > 0) {
			$oldItem = new stdClass;		
			Sql::initQuery($this->appTable,array('avatar','avatar_info'),array($id),'id = ?');	
			$oldItem = Sql::getRecord();
			$avatar = '';
			$avatar_info = '';
			if (isset($oldItem->avatar)) $avatar = $oldItem->avatar;
			if (isset($oldItem->avatar_info)) $avatar_info = $oldItem->avatar_info;
			}		
		if (isset($_FILES['avatar']) && is_uploaded_file($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['size'] > 0) {	
			if ($_FILES['avatar']['error'] == 0 ) {         
            $array_avatarInfo = array();
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
							$this->message = preg_replace('/%DIMENSIONS%/',($max_size / 1000),$this->message);       				
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
		return array($avatar,$avatar_info);
		}
				
	public function getUserTemplatesArray() {
		$arr = array();
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

	}
?>