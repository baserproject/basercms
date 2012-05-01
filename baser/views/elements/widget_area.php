<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ウィジェットエリア
 *
 * no を引き数で渡して利用する
 * <?php $bcBaser->element('widget_areas',array('no'=>1)) ?>
 *
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(Configure::read('BcRequest.isMaintenance')) {
	return;
}
if(!empty($no)){
	$widgets = $this->requestAction('/widget_areas/get_widgets/'.$no);
	if($widgets){
?>
<div class="widget-area widget-area-<?php echo $no ?>">
<?php
		foreach($widgets as $key => $widget){
			$key = key($widget);
			if($widget[$key]['status']){
				$params = array();
				$params['widget']=true;
				if(empty($_SESSION['Auth']['User']) && !isset($cache)){
					$params['cache']='+1 month';
				}
				$params = am($params,$widget[$key]);
				$params[$no.'_'.$widget[$key]['id']] = $no.'_'.$widget[$key]['id'];	// 同じタイプのウィジェットでキャッシュを特定する為に必要
				$bcBaser->element('widgets/'.$widget[$key]['element'],$params, false, $subDir);
			}
		}
?>
</div>
<?php
	}
}
?>