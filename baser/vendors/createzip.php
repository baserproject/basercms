<?php
/* SVN FILE: $Id$ */
/**
 * ZIP生成クラス
 *
 * PHP versions 4 and 5
 *
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2010, Catchup, Inc.
 * @link			http://www.e-catchup.jp
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			MIT Lisense
 */
class Createzip {
/**
 * 圧縮データ
 *
 * @var		array
 * @access	public
 */
	var $compressedData = array();
/**
 * Central Directory
 *
 * @var		array
 * @access	public
 */
	var $centralDirectory = array();
/**
 * End Of Central Directory Record
 *
 * @var		string
 * @access	public
 */
	var $endOfCentralDirectory = "\x50\x4b\x05\x06\x00\x00\x00\x00";
/**
 * オフセット
 *
 * @var		int
 * @access	public
 */
	var $oldOffset = 0;
/**
 * Get Hexd Time
 *
 * @param	int	$time Unix timestamp
 * @return	hex	the date formated as a ZIP date
 * @access	public
 */
	function getMTime($time) {
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
 * フォルダを追加する
 *
 * @param	string	$directory
 * @param	string	$put_int	追加対象位置
 */
	function addFolder($directory, $put_into = '') {
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
				if (is_file($directory.$file)) {
					$fileContents = file_get_contents($directory.$file);
					$this->addFile($fileContents, $put_into.$file,filemtime($directory.$file));
				} elseif ($file != '.' and $file != '..' and is_dir($directory.$file)) {
					$this->addFolder($directory.$file.'/', $put_into.$file.'/');
				}
			}
		}
		closedir($handle);
	}
/**
 * 圧縮対象データを追加
 *
 * @param	binary	$data
 * @param	string	$directoryName
 * @param	int		$time
 * @access	public
 */
	function addFile($data, $directoryName, $time) {

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
 * 圧縮されたデータを取得する
 *
 * @return	binary	$zipedData
 * @access	public
 */
	function getZippedData() {

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
 * 圧縮ファイルをダウンロードする
 *
 * @param	string	$archiveName
 */
	function download($archiveName) {

		$headerInfo = '';

		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}

		// Security checks
		if( $archiveName == "" ) {
			echo "<html><title>Download Error</title><body><BR><B>ERROR:</B> The download file was NOT SPECIFIED.</body></html>";
			exit;
		}

		$zippedData = $this->getZippedData();
		$size = strlen(bin2hex($zippedData)) / 2;

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".$archiveName.";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$size);
		echo $zippedData;

	}

}
?>