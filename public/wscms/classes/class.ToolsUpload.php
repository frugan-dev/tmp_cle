<?php
/**
 * Framework siti html-PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * classes/class.ToolsUpload.php v.2.6.4. 15/06/2016
*/

class ToolsUpload extends Config {
	static $filename = '';
	static $filenameWithId = '';
	static $filenameWithTime = '';
	static $filenameMd5 = '';
	static $orgFilename = '';
	static $tempFilename = '';
	static $fileExtension = '';
	static $fileExtension1 = '';
	static public $fileSize;
	static public $imageSize;
	static $fileType = '';
	static $fieldPostImage = 'filename';
	static $filenameFormat = [];
	public function __construct(){
		parent::__construct();
	}

	public static function getFilenameFromFormArray($id=0,$key = null) 
	{
		if (isset($_FILES[self::$fieldPostImage])) {

			$FILES = $_FILES[self::$fieldPostImage];
			if ($FILES['error'][$key] == 0) {
				self::$tempFilename = $FILES['tmp_name'][$key];
				self::$filename = (isset($FILES['name'][$key]) && $FILES['name'][$key] != '') ? $FILES['name'][$key] : '';
				self::$orgFilename = self::$filename;
				self::$filename = str_replace(" ", "",strip_tags(trim((string) self::$filename)));
				self::$fileExtension = strtolower(substr(strrchr(self::$filename ,"."),1));

				if (strnatcmp(phpversion(),'5.3.6') >= 0) {
					# equal or newer
					$info = new SplFileInfo(self::$filename);
					self::$fileExtension = $info->getExtension();
				} else {
        			self::$fileExtension = strtolower(substr(strrchr(self::$filename ,"."),1));
    			}

				/* filename options */
				self::$filenameWithId = $id.'-'.self::$filename;
				self::$filenameWithTime = time().self::$filename;
				self::$filenameMd5 = md5(self::$filenameWithTime).".".self::$fileExtension;
				self::$fileType = $FILES['type'][$key];
				self::$fileSize = $FILES['size'][$key];

				/* trova dimensione immagine */
				if (getimagesize(self::$tempFilename)) {
					$imagesize = getimagesize(self::$tempFilename);
					[$width, $height, $type, $attr] = $imagesize;
					self::$imageSize = $width.'x'.$height;
				} else {
					self::$imageSize = '';
				}

				/* controlli */
				/* tipo file */
				if (count(self::$filenameFormat) > 0) {
					$foo = strtoupper(self::$fileExtension);
					if (!in_array($foo,self::$filenameFormat)) {
						self::clearAll();
						self::$resultOp->error = 1;
						echo self::$resultOp->message =  'Errore formato file! Formati ammessi: '.implode(', ',self::$filenameFormat);
					}
				}
			} else {
				self::$tempFilename ='';
				self::$filename = '';
				self::$orgFilename = '';
				self::$filename = '';
				self::$fileExtension = '';
				self::$filenameWithId = '';
				self::$filenameWithTime = '';
				self::$filenameMd5 = '';
				self::$fileType = '';
				self::$fileSize = '';
				Core::$resultOp->error = 1;
				Core::$resultOp->message = 'Errore lettura file!';
			}
		}
	}

	public static function getFilenameFromForm($id=0) 
	{
		$FILES = $_FILES[self::$fieldPostImage];
		if ($FILES['error'] == 0) {

			//ToolsStrings::dump($FILES);die();
			self::$tempFilename = SanitizeStrings::stripMagic($FILES['tmp_name']);
			self::$filename = (isset($FILES['name']) && $FILES['name'] != '') ? SanitizeStrings::stripMagic($FILES['name']) : '';
			self::$orgFilename = self::$filename;
			self::$filename = str_replace(" ", "",strip_tags(trim((string) self::$filename)));
			//echo self::$orgFilename;
			if (strnatcmp(phpversion(),'5.3.6') >= 0) {
				# equal or newer
				$info = new SplFileInfo(self::$filename);
				self::$fileExtension = $info->getExtension();
			} else {
				self::$fileExtension = strtolower(substr(strrchr(self::$filename ,"."),1));
			}

			//die('aaaa'.self::$fileExtension);
			/* filename options */
			self::$filenameWithId = $id.'-'.self::$filename;
			self::$filenameWithTime = time().self::$filename;
			self::$filenameMd5 = md5(self::$filenameWithTime).".".self::$fileExtension;
			self::$fileType = $FILES['type'];
			self::$fileSize = $FILES['size'];

			/* trova dimensione immagine */
			if (getimagesize(self::$tempFilename)) {
				$imagesize = getimagesize(self::$tempFilename);
				[$width, $height, $type, $attr] = $imagesize;
				self::$imageSize = $width.'x'.$height;
			} else {
				self::$imageSize = '';
			}
			/* controlli */
			/* tipo file */
			if (count(self::$filenameFormat) > 0) {	
				$foo = strtolower(self::$fileExtension);
				if (!in_array($foo,self::$filenameFormat)) {
					self::clearAll();
					self::$resultOp->error =  1;
					self::$resultOp->messages[] =  'Errore formato file! Formati ammessi: '.implode(', ',self::$filenameFormat);
				}
			}

		} else {
			self::clearAll();
			//Core::$resultOp->errors->type =  1;
			//Core::$resultOp->errors->message = 'Errore lettura file!';
			//Core::$resultOp->error = 1;
			//Core::$resultOp->message = 'Errore lettura file!';
		}
	}

	
	public static function downloadFile($path,$itemData) {
		switch ($itemData['extension']) {
	      case 'ogg': $ctype='application/ogg'; break;
	      case 'pdf': $ctype='application/pdf'; break;
	      case 'exe': $ctype='application/octet-stream'; break;
	      case 'zip': $ctype='application/zip'; break;
	      case 'doc': $ctype='application/msword'; break;
	      case 'xls': $ctype='application/vnd.ms-excel'; break;
	      case 'ppt': $ctype='application/vnd.ms-powerpoint'; break;
	      case 'gif': $ctype='image/gif'; break;
	      case 'png': $ctype='image/png'; break;
	      case 'jpe': case 'jpeg':
	      case 'jpg': $ctype='image/jpg'; break;
	      default: $img = 'unknown.png'; $ctype='application/force-download';
	      }
	   $file = $path.$itemData['filename'];
	   header('Pragma: public');
	   header('Expires: 0');
	   header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	   header('Cache-Control: private',false);
	   header('Content-Type: '.$ctype);
	   header('Content-Disposition: attachment; filename="'.$itemData['filename'].'";');
	   header('Content-Transfer-Encoding: binary');
	   header('Content-Length: '.@filesize($file));
	   if (!ini_get('safe_mode')) set_time_limit(0);
	   readfile($file);
		}

	
		public static function downloadFileFromDB($path,$table,$id,$fieldFileName,$fieldOrgFileName,$fieldFolderName,$folderName) 
		{
			Core::setDebugMode(1);
			$folder_name = '';
			$file = '';
			$itemData = new stdClass;
			Sql::initQuery($table,['*'],[$id],'id = ?');
			$itemData = Sql::getRecord();

		//ToolsStrings::dump($itemData);

		//echo $itemData->$fieldFileName;
		//echo $itemData->$fieldFolderName;

		if (Core::$resultOp->type == 1) die ('Errore database download file pagina!');
		if (isset($itemData->$fieldFileName) && $itemData->$fieldFileName != '') {
			if($fieldFolderName != '' && isset($itemData->$fieldFolderName)) $folder_name = $itemData->$fieldFolderName;
			if($folderName !='') $folder_name = $folderName;

			$file = basename((string) $itemData->$fieldFileName);
			$orgfile = $itemData->$fieldOrgFileName;
			$file_extension = strtolower(substr(strrchr($file,'.'),1));

			if($file != '') {
				switch ($file_extension) {
			      case 'ogg': $ctype='application/ogg'; break;
			      case 'pdf': $ctype='application/pdf'; break;
			      case 'exe': $ctype='application/octet-stream'; break;
			      case 'zip': $ctype='application/zip'; break;
			      case 'doc': $ctype='application/msword'; break;
			      case 'xls': $ctype='application/vnd.ms-excel'; break;
			      case 'ppt': $ctype='application/vnd.ms-powerpoint'; break;
			      case 'gif': $ctype='image/gif'; break;
			      case 'png': $ctype='image/png'; break;
			      case 'jpe': case 'jpeg':
			      case 'jpg': $ctype='image/jpg'; break;
			      default: $img = 'unknown.png'; $ctype='application/force-download';
			      }
		   	$pathfile = $path.$folder_name.$file;

			//echo $ctype;
			//echo $pathfile;

		   	if (file_exists($pathfile) ) {
				   header('Pragma: public');
				   header('Expires: 0');
				   header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				   header('Cache-Control: private',false);
				   header('Content-Type: '.$ctype);
				   header('Content-Disposition: attachment; filename="'.$orgfile.'";');
				   header('Content-Transfer-Encoding: binary');
				   header('Content-Length: '.@filesize($pathfile));
				   //if (!ini_get('safe_mode')) set_time_limit(0);
				   readfile($pathfile);
			   	} else {
			   		Core::$resultOp->error = 1;
						Core::$resultOp->message = 'Errore lettura file!';
			   		}
		 		} else {
		   		Core::$resultOp->error = 1;
				Core::$resultOp->message = 'Il file non esiste nel db!';
		   		}
		   } else {
		   		Core::$resultOp->error = 1;
				Core::$resultOp->message = 'Il file non esiste nel db!';
		   	}
	}


	public static function readFile($file,$orgfile,$ctype) {
		if (file_exists($file)) {
		   header('Pragma: public');
		   header('Expires: 0');
		   header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		   header('Cache-Control: private',false);
		   header('Content-Type: '.$ctype);
		   header('Content-Disposition: attachment; filename="'.$orgfile.'";');
		   header('Content-Transfer-Encoding: binary');
		   header('Content-Length: '.@filesize($file));
		   if (!ini_get('safe_mode')) set_time_limit(0);
		   readfile($file);
	   	} else {
	   		Core::$resultOp->error = 1;
				Core::$resultOp->message = 'Errore lettura file!';
	   		}
		}

	public static function getFileTypeExtension($fileExtension) {
		$ctype = match ($fileExtension) {
            'ogg' => 'application/ogg',
            'pdf' => 'application/pdf',
            'exe' => 'application/octet-stream',
            'zip' => 'application/zip',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'jpe', 'jpeg', 'jpg' => 'image/jpg',
            default => 'application/force-download',
        };

		return $ctype;
		}

	public static function create_zip($files = [],$destination = '',$overwrite = false) {
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination) && !$overwrite) { return false; }
		//vars
		$valid_files = [];
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				if(file_exists($file)) {
					$valid_files[] = $file;
					}
				}
			}
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
				}
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($file,$file);
				}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

			//close the zip -- done!
			$zip->close();
			//check to make sure the file exists
			return file_exists($destination);
			} else {
				return false;
				}
		}

	/**
	* Delete a file or recursively delete a directory
	*
	* @param string $str Path to file or directory
	*/
	public static function recursiveDelete($str) {
		if (is_file($str)) {
        return @unlink($str);
   		} elseif (is_dir($str)) {
				$scan = glob(rtrim($str,'/').'/*');
        		foreach($scan as $index=>$path) {
           		self::recursiveDelete($path);
        			}
				return @rmdir($str);
    			}
		}

 	/* imposta i parametri */

 	public static function setFilenameFormat($value){
 		self::$filenameFormat = $value;
		}


 	/* */

 	public static function checkFilenameFormat($value = []){
		return self::$filenameFormat = $value;
		}

	public static function clearAll($value = []){
		self::$tempFilename ='';
		self::$filename = '';
		self::$orgFilename = '';
		self::$filename = '';
		self::$fileExtension = '';
		self::$filenameWithId = '';
		self::$filenameWithTime = '';
		self::$filenameMd5 = '';
		self::$fileType = '';
		self::$fileSize = '';
		}

	public static function getFilename(){
		return self::$filename;
		}

	public static function getFilenameWithId(){
		return self::$filenameWithId;
		}

	public static function getFilenameWithTime(){
		return self::$filenameWithTime;
		}

	public static function getFilenameMd5(){
		return self::$filenameMd5;
		}

	public static function getTempFilename(){
		return self::$tempFilename;
		}

	public static function getOrgFilename(){
		return self::$orgFilename;
		}

	public static function getFileExtension(){
		return self::$fileExtension;
	}

	public static function getFileSize(){
		return self::$fileSize;
		}

	public static function getFileType(){
		return self::$fileType;
		}

	public static function setFieldPostImage($value) {
		self::$fieldPostImage = $value;
		}
}
?>
