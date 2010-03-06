<?php
/* SVN FILE: $Id$ */
/**
 * アップロードコントローラー
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * アップロードコントローラー
 * @package			baser.controllers
 */
class UploadsController extends AppController {
    var $name = 'Uploads';
    var $uses = array();
/**
 * セッションに保存した一時ファイルを出力する
 * @param string $name
 */
    function tmp($name){
        Configure::write('debug',0);
        $type = $this->Session->read('Upload.'.$name.'_type');
        $ext = decodeContent($type,$name);
        if($ext != 'gif' && $ext != 'jpg' && $ext != 'png'){
            Header("Content-disposition: attachment; filename=".$name);
        }
        Header("Content-type: ".$type."; name=".$name);
        echo $this->Session->read('Upload.'.$name);
        exit();
    }
}
?>