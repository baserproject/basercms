<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ウィジェットエリア
 *
 * no を引き数で渡して利用する
 * <?php $baser->element('widget_areas',array('no'=>1)) ?>
 *
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
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(!empty($no)){
	$widgets = $this->requestAction('/widget_areas/get_widgets/'.$no);
	if($widgets){
?>
<div class="widget-area widget-area-<?php echo $no ?>">
<?php
		foreach($widgets as $widget){
			$key = key($widget);
			if($widget[$key]['status']){
				$params = array();
				$params['widget']=true;
				if(empty($_SESSION['Auth']['User']) && !isset($cache)){
					$params['cache']='+1 month';
				}
				$params = am($params,$widget[$key]);
				$params[$widget[$key]['id']] = $widget[$key]['id'];	// 同じタイプのウィジェットでキャッシュを特定する為に必要
				$baser->element('widgets/'.$widget[$key]['element'],$params, false, $subDir);
			}
		}
?>
</div>
<?php
	}
}
?>