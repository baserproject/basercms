<?php
/* SVN FILE: $Id$ */
/**
 * ErrorHandler 拡張クラス
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Core', 'Error');
/**
 * ErrorHandler 拡張クラス
 * @package baser
 */
class BaserAppError extends ErrorHandler {
/**
 * クラスが見つからない
 * @param array $params
 */
	function missingClass($params) {
		if($params['className']) {
			$this->controller->set('className',$params['className']);
		}
		if($params['notice']) {
			$this->controller->set('notice', $params['notice']);
		}
		$this->_outputMessage('missing_class');
	}
	
}
?>