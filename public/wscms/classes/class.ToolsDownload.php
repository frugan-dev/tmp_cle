<?php
/* classes/class.ToolsDownload.php v.3.5.1. 23/01/2018 */

class ToolsDownload extends Core {	
	public function __construct(){
		parent::__construct();
		}
	
	public static function downloadFileFromPath($path,$filename) 
	{
		if ($filename) { 
 			if (file_exists($path.$filename)) {
				$dim = filesize($filename); 
				###########################################################################    
				// fix for IE catching or PHP bug issue
				header("Pragma: public");
				header("Expires: 0"); // set expiration time
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				// browser must download file from server instead of cache
				// force download dialog
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");
				// use the Content-Disposition header to supply a recommended filename and
				// force the browser to display the save dialog.
				header("Content-Disposition: file; filename=".$filename.";");
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".$dim);
				readfile($path.$filename);
  				exit();
			} else { 
					//echo 'il file '.$path.$filename.' non esiste!';
			}
		}
	}

	public static function downloadFileFromDB($path,$opt) {
		$optDef = ['fileFieldName'=>'filename','fileOrgFieldName'=>'org_filename','fieldFolderName'=>'','folderName'=>'','table'=>'','valuesClause'=>[],'whereClause'=>'id = ?'];
		$opt = array_merge($optDef,$opt);
		$obj = new stdClass;	
		Sql::initQuery($opt['table'],['*'],$opt['valuesClause'],$opt['whereClause']);	 
		$obj = Sql::getRecord();	
		if (Core::$resultOp->type == 1) die ('Errore database download file pagina!');
		$fieldFile = $opt['fileFieldName'];
		$fieldFileOrg = $opt['fileOrgFieldName'];
		if (isset($obj->$fieldFile) && $obj->$fieldFile != '') {	
			$file = basename((string) $obj->$fieldFile);
			$orgfile = $obj->$fieldFileOrg;
			$file_extension = strtolower(substr(strrchr($file,'.'),1));
			if ($file != '') {
			   $ctype = self::getFileTypeExtension($file);			   	
		   	$pathfile = $path.$opt['folderName'].$file;
				
		   	if(file_exists($pathfile)) {
		   		$dim = filesize($pathfile) or die('Errore lettura dimensioni file! '.$pathfile); 
		   		
					header("Pragma: public");
					header("Expires: 0"); // set expiration time
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					// browser must download file from server instead of cache
					// force download dialog
					header("Content-Type: application/force-download");
					header("Content-Type: application/octet-stream");
					header("Content-Type: application/download");
					// use the Content-Disposition header to supply a recommended filename and
					// force the browser to display the save dialog.
					header("Content-Disposition: file; filename=".$orgfile.";");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".$dim);
					readfile($pathfile);
	  				exit();


				   header('Pragma: public');
				   header('Expires: 0');
				   header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				   header('Cache-Control: private',false);
				   header('Content-Type: '.$ctype);
				   header('Content-Disposition: attachment; filename="'.$orgfile.'";');
				   header('Content-Transfer-Encoding: binary');
				   header('Content-Length: '.@filesize($pathfile));
				   if (!ini_get('safe_mode')) set_time_limit(0);
				   readfile($pathfile) or die('Errore lettura file! '.$pathfile);
			   } else {
			   	Core::$resultOp->error = 1;
					echo Core::$resultOp->message = 'Errore lettura file!';
			   }
		 	} else {
		   	Core::$resultOp->error = 1;
				echo Core::$resultOp->message = 'Il file non esiste nel db!';
		   }
		} else {
			Core::$resultOp->error = 1;
			echo Core::$resultOp->message = 'Il file non esiste nel db!';
		}
	}

	public static function downloadFileFromDB2($path,$opt) 
	{
		$optDef = ['fileFieldName'=>'filename','fileOrgFieldName'=>'org_filename','fieldFolderName'=>'','folderName'=>'','table'=>'','valuesClause'=>[],'whereClause'=>'id = ?'];
		$opt = array_merge($optDef,$opt);
		$obj = new stdClass;	

		Sql::initQuery($opt['table'],['*'],$opt['valuesClause'],$opt['whereClause']);	 
		$obj = Sql::getRecord();	
		if (Core::$resultOp->type == 1) die ('Errore database download file pagina!');
	
		$fieldFile = $opt['fileFieldName'];
		$fieldFileOrg = $opt['fileOrgFieldName'];
		if (isset($obj->$fieldFile) && $obj->$fieldFile != '') {

			$file = basename((string) $obj->$fieldFile);
			$orgfile = $obj->$fieldFileOrg;
			$file_extension = strtolower(substr(strrchr($file,'.'),1));
			if ($file != '') {

				if (strnatcmp(phpversion(),'5.3.6') >= 0) {
					# equal or newer
					$info = new SplFileInfo($file);
					$fileExtension = $info->getExtension();
				} else {
					$fileExtension = substr(strrchr($file ,"."),1);
				}
			   	$ctype = self::getFileTypeExtension($fileExtension);			   	
		   		$pathfile = $path.$opt['folderName'].$file;

				/*
				echo '<br>'.$file;
				echo '<br>'.$ctype;
				echo '<br>'.$orgfile;
				echo '<br>'.$pathfile;
				die();
				*/
				
		   		if(file_exists($pathfile)) {
		   			$dim = filesize($pathfile) or die('Errore lettura dimensioni file! '.$pathfile); 
		   		
					header("Pragma: public");
					header("Expires: 0"); // set expiration time
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					// browser must download file from server instead of cache
					// force download dialog
					header("Content-Type: application/force-download");
					header("Content-Type: application/octet-stream");
					header("Content-Type: application/download");
					// use the Content-Disposition header to supply a recommended filename and
					// force the browser to display the save dialog.
					header("Content-Disposition: file; filename=".$orgfile.";");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".$dim);
					readfile($pathfile);
	  				exit();
				   	header('Pragma: public');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Cache-Control: private',false);
					header('Content-Type: '.$ctype);
					header('Content-Disposition: attachment; filename="'.$orgfile.'";');
					header('Content-Transfer-Encoding: binary');
					header('Content-Length: '.@filesize($pathfile));
					if (!ini_get('safe_mode')) set_time_limit(0);
					readfile($pathfile) or die('Errore lettura file! '.$pathfile);
			   } else {
			   		Core::$resultOp->error = 1;
					echo Core::$resultOp->message = 'Errore lettura file!';
			   }

		 	} else {
		   		Core::$resultOp->error = 1;
				echo Core::$resultOp->message = 'Il file non esiste nel db!';
		   	}
		} else {
			Core::$resultOp->error = 1;
			echo Core::$resultOp->message = 'Il file non esiste nel db!';
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
	
	public static function getFileIcon($file,$opt) {
		$optDef = ['iconsize'=>'128x128'];
		$opt = array_merge($optDef,$opt);
		$fileExtension = strtolower(substr(strrchr((string) $file,'.'),1));									 
		$icon = match ($fileExtension) {
            'pdf' => 'fa-file-pdf-o',
            'doc' => 'fa-file-word-o',
            'docx' => 'fa-file-word-o',
            'txt' => 'fa-file-text-o',
            'xls' => 'fa-file-excel-o',
            'xlsx' => 'fa-file-excel-o',
            'xlsm' => 'fa-file-excel-o',
            'ppt' => 'fa-file-powerpoint-o',
            'pptx' => 'fa-file-powerpoint-o',
            'mp3' => 'fa-file-audio-o',
            'wmv' => 'fa-file-video-o',
            'mp4' => 'fa-file-movie-o',
            'mpeg' => 'fa-file-movie-o',
            'html' => 'fa-file-code-o',
            default => 'fa-file-o',
        };   
		return $icon;							
	}
	
	public static function getFileImage($file,$opt) {
		$optDef = ['iconsize'=>'128x128'];
		$opt = array_merge($optDef,$opt);
		$fileExtension = strtolower(substr(strrchr((string) $file,'.'),1));
		$pdfImg = '//cdn1.iconfinder.com/data/icons/CrystalClear/128x128/mimetypes/pdf.png';
		$docImg = '//cdn2.iconfinder.com/data/icons/sleekxp/Microsoft%20Office%202007%20Word.png';
		$pptImg = '//cdn2.iconfinder.com/data/icons/sleekxp/Microsoft%20Office%202007%20PowerPoint.png';
		$txtImg = '//cdn1.iconfinder.com/data/icons/CrystalClear/128x128/mimetypes/txt2.png';
		$xlsImg = '//cdn2.iconfinder.com/data/icons/sleekxp/Microsoft%20Office%202007%20Excel.png';
		$audioImg = '//cdn2.iconfinder.com/data/icons/oxygen/128x128/mimetypes/audio-x-pn-realaudio-plugin.png';
		$videoImg = '//cdn4.iconfinder.com/data/icons/Pretty_office_icon_part_2/128/video-file.png';
		$htmlImg = '//cdn1.iconfinder.com/data/icons/nuove/128x128/mimetypes/html.png';
		$fileImg = '//cdn3.iconfinder.com/data/icons/musthave/128/New.png';
									 
		$img = match ($fileExtension) {
            'pdf' => $pdfImg,
            'doc' => $docImg,
            'docx' => $docImg,
            'txt' => $txtImg,
            'xls' => $xlsImg,
            'xlsx' => $xlsImg,
            'xlsm' => $xlsImg,
            'ppt' => $pptImg,
            'pptx' => $pptImg,
            'mp3' => $audioImg,
            'wmv' => $videoImg,
            'mp4' => $videoImg,
            'mpeg' => $videoImg,
            'html' => $htmlImg,
            default => $fileImg,
        };   
		return $img;							
	}

}
?>