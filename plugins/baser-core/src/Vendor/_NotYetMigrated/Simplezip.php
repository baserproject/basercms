<?php
/**
 * Simplezip
 *
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Vendor
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * シンプルなZIP利用クラス
 *
 * CAUTION 現在、Macで圧縮したZipファイルに対応していない
 */
class Simplezip
{

    /**
     * 圧縮データ
     *
     * @var        array
     * @access    public
     */
    var $compressedData = [];

    /**
     * Central Directory
     *
     * @var        array
     * @access    public
     */
    var $centralDirectory = [];

    /**
     * End Of Central Directory Record
     *
     * @var        string
     * @access    public
     */
    var $endOfCentralDirectory = "\x50\x4b\x05\x06\x00\x00\x00\x00";

    /**
     * オフセット
     *
     * @var        int
     * @access    public
     */
    var $oldOffset = 0;

    /**
     * エントリ（解凍用）
     *
     * @var        array
     * @access    public
     */
    var $entries = [];

    /**
     * Get Hexd Time
     *
     * @param int $time Unix timestamp
     * @return    hex    the date formated as a ZIP date
     * @access    public
     */
    function getMTime($time)
    {
        $mtime = ($time !== null? getdate($time) : getdate());
        $mtime = preg_replace(
            "/(..){1}(..){1}(..){1}(..){1}/",
            "\\x\\4\\x\\3\\x\\2\\x\\1",
            dechex(($mtime['year'] - 1980 << 25) |
                ($mtime['mon'] << 21) |
                ($mtime['mday'] << 16) |
                ($mtime['hours'] << 11) |
                ($mtime['minutes'] << 5) |
                ($mtime['seconds'] >> 1)));
        eval('$mtime = "' . $mtime . '";');
        return $mtime;
    }

    /**
     * フォルダを追加する
     *
     * @param string $directory
     * @param string $put_int 追加対象位置
     */
    function addFolder($directory, $put_into = '')
    {

        $handle = opendir($directory);
        if ($handle) {
            while(false !== ($file = readdir($handle))) {
                if (is_file($directory . $file)) {
                    $fp = fopen($directory . $file, 'rb');
                    $fileContents = fread($fp, filesize($directory . $file));
                    fclose($fp);
                    $this->addFile($fileContents, $put_into . $file, filemtime($directory . $file));
                } elseif ($file != '.' and $file != '..' and is_dir($directory . $file)) {
                    $this->addFolder($directory . $file . DS, $put_into . $file . DS);
                }
            }
        }

        closedir($handle);

    }

    /**
     * 圧縮対象データを追加
     *
     * @param binary $data
     * @param string $directoryName
     * @param int $time
     * @access    public
     */
    function addFile($data, $directoryName, $time = 0)
    {

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
        $gzCompressedData = substr($gzCompressedData, 2, strlen($gzCompressedData) - 6);
        $compressedLength = strlen($gzCompressedData);

        $feedArrayRow .= pack("V", $compression);
        $feedArrayRow .= pack("V", $compressedLength);
        $feedArrayRow .= pack("V", $uncompressedLength);
        $feedArrayRow .= pack("v", strlen($directoryName));
        $feedArrayRow .= pack("v", 0);
        $feedArrayRow .= $directoryName;

        $feedArrayRow .= $gzCompressedData;

        $this->compressedData[] = $feedArrayRow;

        $newOffset = strlen($feedArrayRow);

        $addCentralRecord = "\x50\x4b\x01\x02";
        $addCentralRecord .= "\x00\x00";
        $addCentralRecord .= "\x14\x00";
        $addCentralRecord .= "\x00\x00";
        $addCentralRecord .= "\x08\x00";
        $addCentralRecord .= $hexdtime;
        $addCentralRecord .= pack("V", $compression);
        $addCentralRecord .= pack("V", $compressedLength);
        $addCentralRecord .= pack("V", $uncompressedLength);
        $addCentralRecord .= pack("v", strlen($directoryName));
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("V", 32);
        $addCentralRecord .= pack("V", $this->oldOffset);
        $addCentralRecord .= $directoryName;

        $this->oldOffset += $newOffset;
        $this->centralDirectory[] = $addCentralRecord;

    }

    /**
     * Adds "file" to archive
     *
     * @param string   file contents
     * @param string   name of the file in the archive (may contains the path)
     * @param integer  the current timestamp
     */
    function unix2DosTime($unixtime = 0)
    {
        $timearray = ($unixtime == 0)? getdate() : getdate($unixtime);

        if ($timearray['year'] < 1980) {
            $timearray['year'] = 1980;
            $timearray['mon'] = 1;
            $timearray['mday'] = 1;
            $timearray['hours'] = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        } // end if

        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
            ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    } // end of the 'unix2DosTime()' method

    /**
     * addFile2
     *
     * @param mixed $data
     * @param string $name
     * @param int $time
     */
    function addFile2($data, $name, $time = 0)
    {
        $name = str_replace('\\', '/', $name);

        $dtime = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
            . '\x' . $dtime[4] . $dtime[5]
            . '\x' . $dtime[2] . $dtime[3]
            . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');

        $fr = "\x50\x4b\x03\x04";
        $fr .= "\x14\x00";            // ver needed to extract
        $fr .= "\x00\x00";            // gen purpose bit flag
        $fr .= "\x08\x00";            // compression method
        $fr .= $hexdtime;             // last mod time and date

        // "local file header" segment
        $unc_len = strlen($data);
        $crc = crc32($data);
        $zdata = gzcompress($data);
        $zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
        $c_len = strlen($zdata);
        $fr .= pack('V', $crc);             // crc32
        $fr .= pack('V', $c_len);           // compressed filesize
        $fr .= pack('V', $unc_len);         // uncompressed filesize
        $fr .= pack('v', strlen($name));    // length of filename
        $fr .= pack('v', 0);                // extra field length
        $fr .= $name;

        // "file data" segment
        $fr .= $zdata;

        // "data descriptor" segment (optional but necessary if archive is not
        // served as file)
        // nijel(2004-10-19): this seems not to be needed at all and causes
        // problems in some cases (bug #1037737)
        //$fr .= pack('V', $crc);                 // crc32
        //$fr .= pack('V', $c_len);               // compressed filesize
        //$fr .= pack('V', $unc_len);             // uncompressed filesize

        // add this entry to array
        $this->compressedData[] = $fr;

        // now add to central directory record
        $cdrec = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00";                // version made by
        $cdrec .= "\x14\x00";                // version needed to extract
        $cdrec .= "\x00\x00";                // gen purpose bit flag
        $cdrec .= "\x08\x00";                // compression method
        $cdrec .= $hexdtime;                 // last mod time & date
        $cdrec .= pack('V', $crc);           // crc32
        $cdrec .= pack('V', $c_len);         // compressed filesize
        $cdrec .= pack('V', $unc_len);       // uncompressed filesize
        $cdrec .= pack('v', strlen($name)); // length of filename
        $cdrec .= pack('v', 0);             // extra field length
        $cdrec .= pack('v', 0);             // file comment length
        $cdrec .= pack('v', 0);             // disk number start
        $cdrec .= pack('v', 0);             // internal file attributes
        $cdrec .= pack('V', 32);            // external file attributes - 'archive' bit set

        $cdrec .= pack('V', $this->oldOffset); // relative offset of local header
        $this->oldOffset += strlen($fr);

        $cdrec .= $name;

        // optional extra field, file comment goes here
        // save to central directory
        $this->centralDirectory[] = $cdrec;
    } // end of the 'addFile()' method

    /**
     * 圧縮されたデータを取得する
     *
     * @return    binary    $zipedData
     * @access    public
     */
    function getZippedData()
    {

        $data = implode("", $this->compressedData);
        $controlDirectory = implode("", $this->centralDirectory);

        return
            $data .
            $controlDirectory .
            $this->endOfCentralDirectory .
            pack("v", sizeof($this->centralDirectory)) .
            pack("v", sizeof($this->centralDirectory)) .
            pack("V", strlen($controlDirectory)) .
            pack("V", strlen($data)) . "\x00\x00";
    }

    /**
     * 圧縮ファイルをダウンロードする
     *
     * @param string $archiveName
     */
    function download($archiveName)
    {

        $headerInfo = '';

        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        // Security checks
        if ($archiveName == "") {
            echo "<html><title>Download Error</title><body><BR><B>ERROR:</B> The download file was NOT SPECIFIED.</body></html>";
            exit;
        }

        if (!preg_match('/\.zip$/', $archiveName)) {
            $archiveName .= '.zip';
        }
        $zippedData = $this->getZippedData();
        $size = strlen(bin2hex($zippedData)) / 2;

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=" . $archiveName . ";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $size);
        echo $zippedData;

    }

    /**
     * 解凍したファイルを出力する
     *
     * @param <type> $path
     * @return <type>
     */
    function unzip($source, $tareget)
    {
        $tareget = preg_replace('/\/$/', '', $tareget);
        $entries = $this->_readFile($source);
        if (!$entries) {
            return false;
        }
        $result = true;
        foreach($entries as $entry) {
            $folder = new Folder($tareget . DS . $entry['Path'], true, 0777);
            $fp = @fopen($tareget . DS . $entry['Path'] . DS . $entry['Name'], 'wb');
            if ($fp) {
                if (!fwrite($fp, $entry['Data'])) {
                    $result = false;
                }
            } else {
                $result = false;
                continue;
            }
            fclose($fp);
        }
        return $result;
    }

    /**
     * ZIPファイルを読み込む
     *
     * @param string $path
     * @return    array
     * @access    protected
     */
    function _readFile($path)
    {
        ini_set('mbstring.func_overload', '0');
        $this->entries = [];

        $oF = fopen($path, 'rb');
        $vZ = fread($oF, filesize($path));
        fclose($oF);

        $aE = explode("\x50\x4b\x05\x06", $vZ);
        $aP = unpack('x16/v1CL', $aE[1]);
        $this->Comment = substr($aE[1], 18, $aP['CL']);
        $this->Comment = strtr($this->Comment, ["\r\n" => "\n",
            "\r" => "\n"]);
        $aE = explode("\x50\x4b\x01\x02", $vZ);
        $aE = explode("\x50\x4b\x03\x04", $aE[0]);
        array_shift($aE);

        foreach($aE as $vZ) {
            $aI = [];
            $aI['E'] = 0;
            $aI['EM'] = '';
            $aP = unpack('v1VN/v1GPF/v1CM/v1FT/v1FD/V1CRC/V1CS/V1UCS/v1FNL', $vZ);
            $bE = ($aP['GPF'] & 0x0001)? TRUE : FALSE;
            $nF = $aP['FNL'];

            if ($aP['GPF'] & 0x0008) {
                $aP1 = unpack('V1CRC/V1CS/V1UCS', substr($vZ, -12));
                $aP['CRC'] = $aP1['CRC'];
                $aP['CS'] = $aP1['CS'];
                $aP['UCS'] = $aP1['UCS'];
                $vZ = substr($vZ, 0, -12);
            }

            $aI['N'] = substr($vZ, 26, $nF);
            $aI['N'] = str_replace('/', DS, $aI['N']);
            if (substr($aI['N'], -1) == DS) {
                continue;
            }

            $aI['P'] = dirname($aI['N']);
            $aI['P'] = $aI['P'] == '.'? '' : $aI['P'];
            $aI['N'] = basename($aI['N']);

            $vZ = substr($vZ, 26 + $nF);

            if (strlen($vZ) != $aP['CS']) {
                $aI['E'] = 1;
                $aI['EM'] = 'Compressed size is not equal with the value in header information.';
            } else {
                if ($bE) {
                    $aI['E'] = 5;
                    $aI['EM'] = 'File is encrypted, which is not supported from this class.';
                } else {
                    switch($aP['CM']) {
                        case 0:
                            break;

                        case 8:
                            $vZ = gzinflate($vZ);
                            break;

                        case 12:
                            if (!extension_loaded('bz2')) {
                                if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                                    @dl('php_bz2.dll');
                                } else {
                                    @dl('bz2.so');
                                }
                            }

                            if (extension_loaded('bz2')) {
                                $vZ = bzdecompress($vZ);
                            } else {
                                $aI['E'] = 7;
                                $aI['EM'] = "PHP BZIP2 extension not available.";
                            }
                            break;

                        default:
                            $aI['E'] = 6;
                            $aI['EM'] = "De-/Compression method {$aP['CM']} is not supported.";
                    }

                    if (!$aI['E']) {
                        if ($vZ === FALSE) {
                            $aI['E'] = 2;
                            $aI['EM'] = 'Decompression of data failed.';
                        } else {
                            if (strlen($vZ) != $aP['UCS']) {
                                $aI['E'] = 3;
                                $aI['EM'] = 'Uncompressed size is not equal with the value in header information.';
                            } else {
                                if (crc32($vZ) != $aP['CRC']) {
                                    $aI['E'] = 4;
                                    $aI['EM'] = 'CRC32 checksum is not equal with the value in header information.';
                                }
                            }
                        }
                    }
                }
            }

            $aI['D'] = $vZ;

            $aI['T'] = mktime(($aP['FT'] & 0xf800) >> 11,
                ($aP['FT'] & 0x07e0) >> 5,
                ($aP['FT'] & 0x001f) << 1,
                ($aP['FD'] & 0x01e0) >> 5,
                ($aP['FD'] & 0x001f),
                (($aP['FD'] & 0xfe00) >> 9) + 1980);
            $entry = [];
            $entry['Data'] = $aI['D'];
            $entry['Error'] = $aI['E'];
            $entry['ErrorMsg'] = $aI['EM'];
            $entry['Name'] = $aI['N'];
            $entry['Path'] = $aI['P'];
            $entry['Time'] = $aI['T'];
            $this->entries[] = $entry;
        }

        return $this->entries;

    }

}
