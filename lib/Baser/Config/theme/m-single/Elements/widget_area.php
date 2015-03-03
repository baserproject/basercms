<?php
/**
 * [PUBLISH] ウィジェットエリア
 *
 * no を引き数で渡して利用する
 * <?php $this->BcBaser->element('widget_areas',array('no'=>1)) ?>
 *
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Plugins.Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
if (Configure::read('BcRequest.isMaintenance')) {
	return;
}
if (!isset($subDir)) {
	$subDir = true;
}
if (!empty($no)) {
	$widgets = $this->requestAction('/widget_areas/get_widgets/' . $no);
	if ($widgets) {
		?>
		<div class="articleArea widgetArea widget-area-<?php echo $no ?>">
			<?php
			foreach ($widgets as $key => $widget) {
				$key = key($widget);
				if ($widget[$key]['status']) {
					$params = array();
					$plugin = '';
					$params['widget'] = true;
					if (empty($_SESSION['Auth']['User']) && !isset($cache)) {
						$params['cache'] = '+1 month';
					}
					$params = am($params, $widget[$key]);
					$params[$no . '_' . $widget[$key]['id']] = $no . '_' . $widget[$key]['id']; // 同じタイプのウィジェットでキャッシュを特定する為に必要
					if (!empty($params['plugin'])) {
						$plugin = Inflector::camelize($params['plugin']) . '.';
						unset($params['plugin']);
					}
					$this->BcBaser->element($plugin . 'widgets/' . $widget[$key]['element'], $params, array('subDir' => $subDir));
				}
			}
			?>
		</div>
		<?php
	}
}
?>
