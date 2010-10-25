<?php

/**
 * Class to dynamically create a zip file (archive)
 *
 * @author Rochak Chauhan
 */

class createZip  {  

	var $compressedData = array();
	var $centralDirectory = array(); // central directory   
	var $endOfCentralDirectory = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
	var $oldOffset = 0;

	/**
	 * Function to create the directory where the file(s) will be unzipped
	 *
	 * @param $directoryName string 
	 *
	 */
	
	function addDirectory($directoryName,$time) {
		
		$directoryName = str_replace("\\", "/", $directoryName);
		$hexdtime = $this->getMTime($time);
		
		$feedArrayRow = "\x50\x4b\x03\x04";
		$feedArrayRow .= "\x0a\x00";
		$feedArrayRow .= "\x00\x00";    
		$feedArrayRow .= "\x00\x00";
		$feedArrayRow .= $hexdtime;
		$feedArrayRow .= pack("V",0x00);
		$feedArrayRow .= pack("V",0x00);
		$feedArrayRow .= pack("V",0x00);
		$feedArrayRow .= pack("v", strlen($directoryName) ); 
		$feedArrayRow .= pack("v", 0x00);
		$feedArrayRow .= $directoryName;  

		$this -> compressedData[] = $feedArrayRow;
		
		$newOffset = strlen($feedArrayRow);

		$addCentralRecord = "\x50\x4b\x01\x02";
		$addCentralRecord .="\x00\x00";    
		$addCentralRecord .="\x0a\x00";
		$addCentralRecord .="\x00\x00";    
		$addCentralRecord .="\x00\x00";
		$addCentralRecord .=$hexdtime;
		$addCentralRecord .= pack("V",0x00);
		$addCentralRecord .= pack("V",0x00);
		$addCentralRecord .= pack("V",0x00);
		$addCentralRecord .= pack("v", strlen($directoryName) ); 
		$addCentralRecord .= pack("v", 0x00 );
		$addCentralRecord .= pack("v", 0x00 );
		$addCentralRecord .= pack("v", 0x00 );
		$addCentralRecord .= pack("v", 0x00 );
		$addCentralRecord .= pack("V", 16 ); 
		$addCentralRecord .= pack("V", $this -> oldOffset );
		$addCentralRecord .= $directoryName;
		
		$this -> oldOffset += $newOffset;
		$this -> centralDirectory[] = $addCentralRecord;
		
	}	 
    /**
     * @param int $time Unix timestamp of the date to convert
     * @return the date formated as a ZIP date
     */
    function getMTime($time)
    {
        $mtime = ($time !== null ? getdate($time) : getdate());
        $mtime = preg_replace(
                     "/(..){1}(..){1}(..){1}(..){1}/",
                     "\\x\\4\\x\\3\\x\\2\\x\\1",
                     dechex(($mtime['year']-1980<<25)|
                            ($mtime['mon'    ]<<21)|
                            ($mtime['mday'   ]<<16)|
                            ($mtime['hours'  ]<<11)|
                            ($mtime['minutes']<<5)|
                            ($mtime['seconds']>>1)));
        eval('$mtime = "'.$mtime.'";');
        return $mtime;
    }
	/**
	 * Function to add file(s) to the specified directory in the archive 
	 *
	 * @param $directoryName string 
	 *
	 */
	
	function addFile($data, $directoryName, $time)   {
 
		$directoryName = str_replace("\\", "/", $directoryName);  
		$hexdtime = $this->getMTime($time);
		
		$feedArrayRow = "\x50\x4b\x03\x04";
		$feedArrayRow .= "\x14\x00";    
		$feedArrayRow .= "\x00\x00";    
		$feedArrayRow .= "\x08\x00";    
		$feedArrayRow .= $hexdtime;

		$uncompressedLength = strlen($data);  
		$compression = crc32($data);  
		$gzCompressedData = gzcompress($data);  
		$gzCompressedData = substr($gzCompressedData,2,strlen($gzCompressedData)-6);
		$compressedLength = strlen($gzCompressedData);
		
		$feedArrayRow .= pack("V",$compression); 
		$feedArrayRow .= pack("V",$compressedLength); 
		$feedArrayRow .= pack("V",$uncompressedLength); 
		$feedArrayRow .= pack("v", strlen($directoryName) ); 
		$feedArrayRow .= pack("v", 0x00 );
		$feedArrayRow .= $directoryName;  

		$feedArrayRow .= $gzCompressedData;  

		$this -> compressedData[] = $feedArrayRow;

		$newOffset = strlen($feedArrayRow);

		$addCentralRecord = "\x50\x4b\x01\x02";
		$addCentralRecord .="\x00\x00";    
		$addCentralRecord .="\x14\x00";    
		$addCentralRecord .="\x00\x00";    
		$addCentralRecord .="\x08\x00";    
		$addCentralRecord .=$hexdtime;
		$addCentralRecord .= pack("V",$compression); 
		$addCentralRecord .= pack("V",$compressedLength); 
		$addCentralRecord .= pack("V",$uncompressedLength); 
		$addCentralRecord .= pack("v", strlen($directoryName) ); 
		$addCentralRecord .= pack("v", 0x00 );
		$addCentralRecord .= pack("v", 0x00 );
		$addCentralRecord .= pack("v", 0x00 );
		$addCentralRecord .= pack("v", 0x00 );
		$addCentralRecord .= pack("V", 0x0000 );
		$addCentralRecord .= pack("V", $this -> oldOffset );
		$addCentralRecord .= $directoryName;

		$this -> oldOffset += $newOffset;
		$this -> centralDirectory[] = $addCentralRecord;

	}

	/**
	 * Fucntion to return the zip file
	 *
	 * @return zipfile (archive)
	 */

	function getZippedfile() { 

		$data = implode("", $this -> compressedData);  
		$controlDirectory = implode("", $this -> centralDirectory);  

		return
			$data.
			$controlDirectory. 
			$this -> endOfCentralDirectory.  
			pack("v", sizeof($this -> centralDirectory)).     
			pack("v", sizeof($this -> centralDirectory)).     
			pack("V", strlen($controlDirectory)).             
			pack("V", strlen($data));                             
	}

	/**
	 *
	 * Function to force the download of the archive as soon as it is created
	 *
	 * @param archiveName string - name of the created archive file
	 */

	function forceDownload($archiveName) {
		$headerInfo = '';
		 
		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}

		// Security checks
		if( $archiveName == "" ) {
			echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> The download file was NOT SPECIFIED.</body></html>";
			exit;
		} 
		elseif ( ! file_exists( $archiveName ) ) {
			echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> File not found.</body></html>";
			exit;
		}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".basename($archiveName).";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($archiveName));
		readfile("$archiveName");
		
	 }

}
class createDirZip extends createZip {

  function get_files_from_folder($directory, $put_into) {
    if ($handle = opendir($directory)) {
      while (false !== ($file = readdir($handle))) {
        if (is_file($directory.$file)) {
          $fileContents = file_get_contents($directory.$file,filemtime($directory.$file));
          $this->addFile($fileContents, $put_into.$file);
        } elseif ($file != '.' and $file != '..' and is_dir($directory.$file)) {
          $this->addDirectory($put_into.$file.'/',filemtime($directory.$file));
          $this->get_files_from_folder($directory.$file.'/', $put_into.$file.'/');
        }
      }
    }
    closedir($handle);
  }
}
?>