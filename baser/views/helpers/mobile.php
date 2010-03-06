<?php
/* SVN FILE: $Id$ */
/**
 * モバイルヘルパー
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
 * @package			baser.view.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * モバイルヘルパー
 *
 * @package			baser.views.helpers
 */
class MobileHelper extends Helper {
/**
 * afterLayout
 * 
 * @return	void
 * @access	public
 */
    function afterLayout() {

		/* 出力データをSJISに変換 */
		$view =& ClassRegistry::getObject('view');

        if(isset($this->params['url']['ext']) && $this->params['url']['ext'] == 'rss'){
            $rss = true;
        }else{
            $rss = false;
        }

        if($view && !$rss && Configure::read('Mobile.on') && $view->layoutPath != 'email'.DS.'text'){

			header("Content-type: application/xhtml+xml");

            $out = $view->output;
            $out = mb_convert_kana($out, "rak", "UTF-8");
            $out = mb_convert_encoding($out, "SJIS", "UTF-8");
            $view->output = $out;

			// キャッシュを再保存
			$caching = (
				isset($view->loaded['cache']) &&
				(($view->cacheAction != false)) && (Configure::read('Cache.check') === true)
			);
			if ($caching) {
				if (is_a($view->loaded['cache'], 'CacheHelper')) {
					$cache =& $view->loaded['cache'];
					$cache->base = $view->base;
					$cache->here = $view->here;
					$cache->helpers = $view->helpers;
					$cache->action = $view->action;
					$cache->controllerName = $view->name;
					$cache->layout	= $view->layout;
					$cache->cacheAction = $view->cacheAction;
					$cache->cache($___viewFn, $view->output, true);
				}
			}
			Configure::write('debug',0);
		
        }
    }
}
?>