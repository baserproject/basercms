<?php
/* SVN FILE: $Id$ */
/**
 * ErrorHandler 拡張クラス
 *
 * =============================================================================
 * 独自の AppError を利用する場合は、このファイルをアプリケーションフォルダに
 * コピーして利用すると baserCMS のアップデート時に上書きされません。
 * =============================================================================
 * 
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
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
App::import('Core', 'BaserAppError', array('search' => BASER));
/**
 * ErrorHandler 拡張クラス
 * @package			baser
 */
class AppError extends BaserAppError {
}
?>