<?php
//These are a number of functions pulled into a class so they don't conflict with other sources, but give us some handy file-system related control to build on.
class filesystem {
	private $create = <<<'HERE'
CREATE TABLE `hitchhiker` (
  `hitchhiker_id` int(11) NOT NULL AUTO_INCREMENT,
  `hitchhiker_file` text NOT NULL,
  `hitchhiker_owner` text,
  `hitchhiker_group` text,
  `hitchhiker_perms` text,
  `hitchhiker_last_edit` text,
  `hitchhiker_hash` text,
  `hitchhiker_report` text,
  PRIMARY KEY (`hitchhiker_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0DEFAULT CHARSET=latin1;
HERE;
	
	
	public function install(){
		$sql = 'SHOW TABLES LIKE "hitchhiker"';
		$query = $this->db->getRows($sql, []);
		$check = $query->fetchAll();
		if(!$check){
			
			$this->exeScan(true);
			$this->checkup(false, true);
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/.user.ini')){
				$settings = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/.user.ini');
					if(!isset($settings['auto_prepend_file'])){
						$file = fopen($_SERVER['DOCUMENT_ROOT'].'/.user.ini', 'a');
						$string = PHP_EOL.'auto_prepend_file = "'.__DIR__.'/hitchhiker.php"';
						fwrite($file, $string);
						fclose($file);
					}
			}else{
				$file = fopen($_SERVER['DOCUMENT_ROOT'].'/.user.ini', 'a');
				$string = 'auto_prepend_file = "'.__DIR__.'/hitchhiker.php"';
				fwrite($file, $string);
				fclose($file);
			}
		}
	}
	
	 public function checkup($output = false, $approve = false){
		global $th;
		$sql = 'SELECT * FROM hitchhiker';
		$query = $th->t_query($sql, []);
		$exes = $query->fetchAll();
		if($output == true){
			echo count($exes).' files to be scanned.<br>';
		}
		$i = 0;
		foreach($exes as $exe){
			$filename = $exe['hitchhiker_file'];
			$file = file_get_contents($filename);
			$hash = hash('sha512', $file);
			unset($file);
			$owner = fileowner($filename);
			$group = filegroup($filename);
			$perms = fileperms($filename);
			$lastEdit = filemtime($filename);
			if(empty($exe['hitchhiker_report'])){
				$report = [ 'owner' => $owner, 'group' => $group, 'perms' => $perms, 'lastEdit' => $lastEdit, 'hash' => $hash ];
				$report = serialize($report);
				$sql = 'UPDATE hitchhiker SET hitchhiker_owner = :owner, hitchhiker_group = :group, hitchhiker_perms = :perms, hitchhiker_last_edit = :last, hitchhiker_hash = :hash, hitchhiker_report = :report WHERE hitchhiker_id = :id';
				$params = [ ':owner' => $owner, ':group' => $group, ':perms' => $perms, ':last' => $lastEdit, ':hash' => $hash, ':report' => $report, ':id' => $exe['executable_id'] ];
				$th->t_query($sql, $params);
			}else{
				$report = [ 'owner' => $owner, 'group' => $group, 'perms' => $perms, 'lastEdit' => $lastEdit, 'hash' => $hash ];
				$report = serialize($report);
				$sql = 'UPDATE hitchhiker SET hitchhiker_report = :report WHERE hitchhiker_id = :id';
				$params = [':report' => $report, ':id' => $exe['executable_id']];
				$th->t_query($sql, $params);
			}
			$change = false;
			$changeSum = '';
			if($owner != $exe['hitchhiker_owner']){
				$changeSum .= 'New Owner.'.PHP_EOL;
				$change = true;
			}
			if($group != $exe['hitchhiker_group']){
				$changeSum .= 'New Group.'.PHP_EOL;
				$change = true;
			}
			if($perms != $exe['hitchhiker_perms']){
				$changeSum .= 'New Permissions.'.PHP_EOL;
				$change = true;
			}
			if($lastEdit != $exe['hitchhiker_last_edit']){
				$changeSum .= 'Timestamp Change.'.PHP_EOL;
				$change = true;
			}
			if($hash != $exe['hitchhiker_hash']){
				$changeSum .= 'File Contents Changed.'.PHP_EOL;
				$change = true;
			}
			if($change == true && $output == true){
				$i++;
				$report = unserialize($report);
				echo '<h2>'.$exe['hitchhiker_file'].'</h2>Change Summary:<br>';
				echo nl2br($changeSum);
				$report['perms'] = substr(sprintf('%o',$report['perms']), -4);
				$report['owner'] = posix_getpwuid($report['owner'])['name'];
				$report['group'] = posix_getgrgid($report['group'])['name'];
				$report['lastEdit'] = date('m/d/Y g:ia', $report['lastEdit']);
				echo '<pre>';
				var_dump($report);
				echo '</pre>';
			}
			if($change == true && $approve == true){
				$sql = 'UPDATE executable SET executable_owner = :owner, executable_group = :group, executable_perms = :perms, executable_last_edit = :last, executable_hash = :hash WHERE executable_id = :id';
				$params = [ ':owner' => $owner, ':group' => $group, ':perms' => $perms, ':last' => $lastEdit, ':hash' => $hash, ':id' => $exe['executable_id'] ];
				$th->t_query($sql, $params);
			}
		}
		if($output == true){
			echo $i.' files changed.';
		}
	}
	
	public function exeScan($approve = false){
		global $th;
		$files = $this->recursiveScan($_SERVER['DOCUMENT_ROOT']);
		foreach($files as $file){
			$sql = "SELECT * FROM hitchhiker WHERE hitchhiker_file = :file";
			$params = [':file' => $file];
			$query = $th->t_query($sql, $params);
			$return = $query->fetchAll();
			if(!$return && $approve == true){
				$sql = "INSERT INTO hitchhiker (hitchhiker_file) VALUES (:file)";
				$th->t_query($sql, $params);
			}else if($approve == false && !$return){
				echo $file.'<br>';
			}
		}
	}
	
	public function recursiveScan($loc, $all = false){
		$array = scandir($loc);
		$return = [];
		foreach($array as $file){
			if($file != '.' && $file != '..'){
				if(is_dir($loc.'/'.$file)){
					$newArray = $this->recursiveScan($loc.'/'.$file, $all);
					foreach($newArray as $item){
						$return[] = $item;
					}
				}else{
					if($all == false){
						if(stristr($file, '.php') !== false){
							$return[] = $loc.'/'.$file;
						}
					}else{
						$return[] = $loc.'/'.$file;
					}
				}
			}
		}
		return $return;
	}
	
	
	
	/*
	Name: Steph Kennedy
	Date: 12/9/18
	Comment: This is always a problem. Sometimes images aren't what they pretend to be, waiting for some other script to either modify the .htaccess to make them executable or another script to rename them.
	
	Best defense I've found so far is to test the image data to see if it can be processed by the built in image size function (thanks php.net comments), and then try to read the data of the image to see if there is potentially a php string hidden in there.
	
	It can lead to false positives, so always review the files it flags.
	*/
	public function imageTest($file){
		$check = getimagesize($file);
		if($check === false){
			//Then it's not an image at all
			return false;
		}else{
			//Then it could be an image, but we're going to read the image to be sure.
			//Rewrite this to read the image line by line to lower memory usage, but keep in mind we'll have to track individual matches at that point, to account for cross line strings, which could raise our false positives.
			$image = file_get_contents($file);
			$pattern = '/<\?.+\?>/s';
			$check = preg_match($pattern, $image);
			if($check === 1){
				//Then we found a potential php string inside our image
				return false;
			}else{
				//As far as we know, this is just an image
				return true;
			}
		}
	}
	
	//Base level functions pulled from other sources.
	/*
	Name: Steph Kennedy
	Date: 12/9/18
	Comment: Original function from http://php.net/manual/en/function.filesize.php first comment from "rommel at rommelsantor dot com", as the comment says, it's extremely simple, and from my experiences dependable, no reason to reinvent the wheel.
	*/
	public function human_filesize($bytes, $decimals = 2) { 
		$sz = 'BKMGTP'; 
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor]; 
	}
	
	/*
	Name: Steph Kennedy
	Date: 12/9/18
	Comment: This was pieced together from a number of comments from: https://stackoverflow.com/questions/3856293/how-to-convert-seconds-to-time-format
	*/
	public function secondToTime($time){ 
		$t = $time; 
		if(($t/60)/24 >= 1){ 
			return sprintf('%2d Days %2d Hours %2d Minutes %2d Seconds', floor(($t/3600)/24), (($t/3600)%24), (($t/60)%60), $t%60); 
		}else if(($t/60)%24 != 0){ 
			return sprintf('%2d Hours %2d Minutes %2d Seconds', (($t/3600)%24), (($t/60)%60), $t%60); 
		}else{ 
			return sprintf('%2d Minutes %2d Seconds', (($t/60)%60), $t%60); 
		} 
	}
	
	/*
	Name: Steph Kennedy
	Date: 12/9/18
	Comment: Taken from Abhinav bhardwaj's comment from: https://stackoverflow.com/questions/2556345/detect-base64-encoding-in-php ,
	function renamed and stripped of comments.
	*/
	public function base64Detect($string){
		$decoded = base64_decode($string, true);
		if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) return false;
		if (!base64_decode($string, true)) return false;
		if (base64_encode($decoded) != $string) return false;
		return true;
	}
	
	/*
	Name: Steph Kennedy
	Date: 12/9/18
	Comment: Could have sworn this was from the php.net entry on glob, down in the comments, but the only function close to it I could find is one that doesn't support Glob Brace. Please, if you know who came up with this, give me a shout out. The only backup copy I had was minified so my original comment of attribution was missing.
	*/
	//We could be using iterators here, look into replacing in a future version
	public function rglob($pattern, $flags = 0) {
		if($flags == 0){
			$flags = GLOB_BRACE;
		}
		$files = glob($pattern, $flags); 
		foreach (glob(dirname($pattern).DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
			$files = array_merge($files, $this->rglob($dir.'/'.basename($pattern), $flags));
		}
		return $files;
	}
	
	public function dirscan($dir){
		$array = scandir($dir);
		$dir = [];
		$file = [];
		foreach($array as $item){
			if($item != '.' || $item != '..'){
				if(is_dir($item)){
					$dir[] = $item;
				}else{
					$file[] = $item;
				}
			}
		}
		return[
			'dirs' => $dir,
			'files' => $file
		];
	}

	/**
	 * Source: https://stackoverflow.com/questions/16511021/convert-mime-type-to-file-extension-php
	 */
	public function mime2ext($mime) {
		$mime_map = [
			'video/3gpp2'                                                               => '3g2',
			'video/3gp'                                                                 => '3gp',
			'video/3gpp'                                                                => '3gp',
			'application/x-compressed'                                                  => '7zip',
			'audio/x-acc'                                                               => 'aac',
			'audio/ac3'                                                                 => 'ac3',
			'application/postscript'                                                    => 'ai',
			'audio/x-aiff'                                                              => 'aif',
			'audio/aiff'                                                                => 'aif',
			'audio/x-au'                                                                => 'au',
			'video/x-msvideo'                                                           => 'avi',
			'video/msvideo'                                                             => 'avi',
			'video/avi'                                                                 => 'avi',
			'application/x-troff-msvideo'                                               => 'avi',
			'application/macbinary'                                                     => 'bin',
			'application/mac-binary'                                                    => 'bin',
			'application/x-binary'                                                      => 'bin',
			'application/x-macbinary'                                                   => 'bin',
			'image/bmp'                                                                 => 'bmp',
			'image/x-bmp'                                                               => 'bmp',
			'image/x-bitmap'                                                            => 'bmp',
			'image/x-xbitmap'                                                           => 'bmp',
			'image/x-win-bitmap'                                                        => 'bmp',
			'image/x-windows-bmp'                                                       => 'bmp',
			'image/ms-bmp'                                                              => 'bmp',
			'image/x-ms-bmp'                                                            => 'bmp',
			'application/bmp'                                                           => 'bmp',
			'application/x-bmp'                                                         => 'bmp',
			'application/x-win-bitmap'                                                  => 'bmp',
			'application/cdr'                                                           => 'cdr',
			'application/coreldraw'                                                     => 'cdr',
			'application/x-cdr'                                                         => 'cdr',
			'application/x-coreldraw'                                                   => 'cdr',
			'image/cdr'                                                                 => 'cdr',
			'image/x-cdr'                                                               => 'cdr',
			'zz-application/zz-winassoc-cdr'                                            => 'cdr',
			'application/mac-compactpro'                                                => 'cpt',
			'application/pkix-crl'                                                      => 'crl',
			'application/pkcs-crl'                                                      => 'crl',
			'application/x-x509-ca-cert'                                                => 'crt',
			'application/pkix-cert'                                                     => 'crt',
			'text/css'                                                                  => 'css',
			'text/x-comma-separated-values'                                             => 'csv',
			'text/comma-separated-values'                                               => 'csv',
			'application/vnd.msexcel'                                                   => 'csv',
			'application/x-director'                                                    => 'dcr',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
			'application/x-dvi'                                                         => 'dvi',
			'message/rfc822'                                                            => 'eml',
			'application/x-msdownload'                                                  => 'exe',
			'video/x-f4v'                                                               => 'f4v',
			'audio/x-flac'                                                              => 'flac',
			'video/x-flv'                                                               => 'flv',
			'image/gif'                                                                 => 'gif',
			'application/gpg-keys'                                                      => 'gpg',
			'application/x-gtar'                                                        => 'gtar',
			'application/x-gzip'                                                        => 'gzip',
			'application/mac-binhex40'                                                  => 'hqx',
			'application/mac-binhex'                                                    => 'hqx',
			'application/x-binhex40'                                                    => 'hqx',
			'application/x-mac-binhex40'                                                => 'hqx',
			'text/html'                                                                 => 'html',
			'image/x-icon'                                                              => 'ico',
			'image/x-ico'                                                               => 'ico',
			'image/vnd.microsoft.icon'                                                  => 'ico',
			'text/calendar'                                                             => 'ics',
			'application/java-archive'                                                  => 'jar',
			'application/x-java-application'                                            => 'jar',
			'application/x-jar'                                                         => 'jar',
			'image/jp2'                                                                 => 'jp2',
			'video/mj2'                                                                 => 'jp2',
			'image/jpx'                                                                 => 'jp2',
			'image/jpm'                                                                 => 'jp2',
			'image/jpeg'                                                                => 'jpeg',
			'image/pjpeg'                                                               => 'jpeg',
			'application/x-javascript'                                                  => 'js',
			'application/json'                                                          => 'json',
			'text/json'                                                                 => 'json',
			'application/vnd.google-earth.kml+xml'                                      => 'kml',
			'application/vnd.google-earth.kmz'                                          => 'kmz',
			'text/x-log'                                                                => 'log',
			'audio/x-m4a'                                                               => 'm4a',
			'audio/mp4'                                                                 => 'm4a',
			'application/vnd.mpegurl'                                                   => 'm4u',
			'audio/midi'                                                                => 'mid',
			'application/vnd.mif'                                                       => 'mif',
			'video/quicktime'                                                           => 'mov',
			'video/x-sgi-movie'                                                         => 'movie',
			'audio/mpeg'                                                                => 'mp3',
			'audio/mpg'                                                                 => 'mp3',
			'audio/mpeg3'                                                               => 'mp3',
			'audio/mp3'                                                                 => 'mp3',
			'video/mp4'                                                                 => 'mp4',
			'video/mpeg'                                                                => 'mpeg',
			'application/oda'                                                           => 'oda',
			'audio/ogg'                                                                 => 'ogg',
			'video/ogg'                                                                 => 'ogg',
			'application/ogg'                                                           => 'ogg',
			'font/otf'                                                                  => 'otf',
			'application/x-pkcs10'                                                      => 'p10',
			'application/pkcs10'                                                        => 'p10',
			'application/x-pkcs12'                                                      => 'p12',
			'application/x-pkcs7-signature'                                             => 'p7a',
			'application/pkcs7-mime'                                                    => 'p7c',
			'application/x-pkcs7-mime'                                                  => 'p7c',
			'application/x-pkcs7-certreqresp'                                           => 'p7r',
			'application/pkcs7-signature'                                               => 'p7s',
			'application/pdf'                                                           => 'pdf',
			'application/octet-stream'                                                  => 'pdf',
			'application/x-x509-user-cert'                                              => 'pem',
			'application/x-pem-file'                                                    => 'pem',
			'application/pgp'                                                           => 'pgp',
			'application/x-httpd-php'                                                   => 'php',
			'application/php'                                                           => 'php',
			'application/x-php'                                                         => 'php',
			'text/php'                                                                  => 'php',
			'text/x-php'                                                                => 'php',
			'application/x-httpd-php-source'                                            => 'php',
			'image/png'                                                                 => 'png',
			'image/x-png'                                                               => 'png',
			'application/powerpoint'                                                    => 'ppt',
			'application/vnd.ms-powerpoint'                                             => 'ppt',
			'application/vnd.ms-office'                                                 => 'ppt',
			'application/msword'                                                        => 'doc',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
			'application/x-photoshop'                                                   => 'psd',
			'image/vnd.adobe.photoshop'                                                 => 'psd',
			'audio/x-realaudio'                                                         => 'ra',
			'audio/x-pn-realaudio'                                                      => 'ram',
			'application/x-rar'                                                         => 'rar',
			'application/rar'                                                           => 'rar',
			'application/x-rar-compressed'                                              => 'rar',
			'audio/x-pn-realaudio-plugin'                                               => 'rpm',
			'application/x-pkcs7'                                                       => 'rsa',
			'text/rtf'                                                                  => 'rtf',
			'text/richtext'                                                             => 'rtx',
			'video/vnd.rn-realvideo'                                                    => 'rv',
			'application/x-stuffit'                                                     => 'sit',
			'application/smil'                                                          => 'smil',
			'text/srt'                                                                  => 'srt',
			'image/svg+xml'                                                             => 'svg',
			'application/x-shockwave-flash'                                             => 'swf',
			'application/x-tar'                                                         => 'tar',
			'application/x-gzip-compressed'                                             => 'tgz',
			'image/tiff'                                                                => 'tiff',
			'font/ttf'                                                                  => 'ttf',
			'text/plain'                                                                => 'txt',
			'text/x-vcard'                                                              => 'vcf',
			'application/videolan'                                                      => 'vlc',
			'text/vtt'                                                                  => 'vtt',
			'audio/x-wav'                                                               => 'wav',
			'audio/wave'                                                                => 'wav',
			'audio/wav'                                                                 => 'wav',
			'application/wbxml'                                                         => 'wbxml',
			'video/webm'                                                                => 'webm',
			'image/webp'                                                                => 'webp',
			'audio/x-ms-wma'                                                            => 'wma',
			'application/wmlc'                                                          => 'wmlc',
			'video/x-ms-wmv'                                                            => 'wmv',
			'video/x-ms-asf'                                                            => 'wmv',
			'font/woff'                                                                 => 'woff',
			'font/woff2'                                                                => 'woff2',
			'application/xhtml+xml'                                                     => 'xhtml',
			'application/excel'                                                         => 'xl',
			'application/msexcel'                                                       => 'xls',
			'application/x-msexcel'                                                     => 'xls',
			'application/x-ms-excel'                                                    => 'xls',
			'application/x-excel'                                                       => 'xls',
			'application/x-dos_ms_excel'                                                => 'xls',
			'application/xls'                                                           => 'xls',
			'application/x-xls'                                                         => 'xls',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
			'application/vnd.ms-excel'                                                  => 'xlsx',
			'application/xml'                                                           => 'xml',
			'text/xml'                                                                  => 'xml',
			'text/xsl'                                                                  => 'xsl',
			'application/xspf+xml'                                                      => 'xspf',
			'application/x-compress'                                                    => 'z',
			'application/x-zip'                                                         => 'zip',
			'application/zip'                                                           => 'zip',
			'application/x-zip-compressed'                                              => 'zip',
			'application/s-compressed'                                                  => 'zip',
			'multipart/x-zip'                                                           => 'zip',
			'text/x-scriptzsh'                                                          => 'zsh',
		];
	
		return isset($mime_map[$mime]) ? $mime_map[$mime] : false;
	}
}
?>