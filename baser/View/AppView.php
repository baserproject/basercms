<?php
/* SVN FILE: $Id$ */
/**
 * View 拡張クラス
 *
 * =============================================================================
 * 独自の AppView を利用する場合は、このファイルをアプリケーションフォルダに
 * コピーして利用すると baserCMS のアップデート時に上書きされません。
 * =============================================================================
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Core', 'BaserAppView', array('search' => BASER_VIEWS));
/**
 * View 拡張クラス
 *
 * @package			baser.views
 */
class AppView extends BaserAppView {
}
?>