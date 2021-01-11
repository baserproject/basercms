<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ウィジェットエリア編集
 * @var BcAppView $this
 */
$this->BcBaser->i18nScript([
	'alertMessage1' => __d('baser', 'ウィジェットの削除に失敗しました。'),
	'alertMessage2' => __d('baser', 'ウィジェットエリアの並び替えの保存に失敗しました。'),
	'alertMessage3' => __d('baser', 'ウィジェットエリア名の保存に失敗しました。'),
	'alertMessage4' => __d('baser', 'ウィジェットの保存に失敗しました。'),
	'confirmMessage1' => __d('baser', '設定内容も削除されますが、本当に削除してもいいですか？'),
	'infoMessage1' => __d('baser', 'ウィジェットを削除しました。'),
	'infoMessage2' => __d('baser', 'ウィジェットエリア名を保存しました。'),
	'infoMessage3' => __d('baser', 'ウィジェットを保存しました。'),
]);
$this->BcBaser->js('admin/widget_areas/form', false, ['id' => 'AdminWidgetFormScript',
	'data-delWidgetUrl' => $this->BcBaser->getUrl(['controller' => 'widget_areas', 'action' => 'del_widget', $this->BcForm->value('WidgetArea.id')]),
	'data-currentAction' => $this->request->action
]);
?>


<?php if ($this->request->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('WidgetArea', ['url' => ['action' => 'add']]) ?>
<?php elseif ($this->request->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('WidgetArea', ['url' => ['action' => 'update_title', 'id' => false]]) ?>
<?php endif ?>

<?php echo $this->BcForm->hidden('WidgetArea.id') ?>

<?php echo $this->BcForm->label('WidgetArea.name', __d('baser', 'ウィジェットエリア名')) ?>&nbsp;
<?php echo $this->BcForm->input('WidgetArea.name', ['type' => 'text', 'size' => 40, 'autofocus' => true]) ?>&nbsp;
<span
	class="submit"><?php echo $this->BcForm->end(['label' => __d('baser', 'エリア名を保存する'), 'div' => false, 'class' => 'button btn-red', 'id' => 'WidgetAreaUpdateTitleSubmit']) ?></span>
<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['style' => 'vertical-align:middle;display:none', 'id' => 'WidgetAreaUpdateTitleLoader']) ?>
<?php echo $this->BcForm->error('WidgetArea.name') ?>

<?php if (!empty($widgetInfos)): ?>

	<?php echo $this->BcForm->create('WidgetArea', ['url' => ['action' => 'update_sort', $this->BcForm->value('WidgetArea.id'), 'id' => false]]) ?>
	<?php echo $this->BcForm->input('WidgetArea.sorted_ids', ['type' => 'hidden']) ?>
	<?php echo $this->BcForm->end() ?>

	<div id="WidgetSetting" class="clearfix">

		<!-- 利用できるウィジェット -->
		<div id="SourceOuter">
			<div id="Source">

				<h2><?php echo __d('baser', '利用できるウィジェット') ?></h2>
				<?php foreach($widgetInfos as $widgetInfo) : ?>
					<h3><?php echo h($widgetInfo['title']) ?></h3>
					<?php
					$widgets = [];
					foreach($widgetInfo['paths'] as $path) {
						$Folder = new Folder($path);
						$files = $Folder->read(true, true, true);
						$widgets = [];
						foreach($files[1] as $file) {
							$widget = ['name' => '', 'title' => '', 'description' => '', 'setting' => ''];
							ob_start();
							$key = 'Widget';
							// タイトルや説明文を取得する為、elementを使わず、includeする。
							// コントローラーでインクルードした場合、コントローラー内でヘルパ等が読み込まれていないのが原因で
							// エラーとなるのでここで読み込む
							include $file;
							$widget['name'] = basename($file, $this->ext);
							$widget['title'] = $title;
							$widget['description'] = $description;
							$widget['setting'] = ob_get_contents();
							$widgets[] = $widget;
							ob_end_clean();
						}
					}
					?>
					<?php foreach($widgets as $widget): ?>

						<div class="ui-widget-content draggable widget"
							 id="Widget<?php echo Inflector::camelize($widget['name']) ?>">
							<div class="head"><?php echo h($widget['title']) ?></div>
						</div>

						<div class="description"><?php echo h($widget['description']) ?></div>

						<div class="ui-widget-content sortable widget template <?php echo h($widget['name']) ?>"
							 id="<?php echo Inflector::camelize($widget['name']) ?>">
							<div class="clearfix">
								<div class="widget-name display-none"><?php echo h($widget['name']) ?></div>
								<div class="del"><?php echo __d('baser', '削除') ?></div>
								<div class="action"><?php echo __d('baser', '設定') ?></div>
								<div class="head"><?php echo $widget['title'] ?></div>
							</div>
							<div class="content" style="text-align:right">
								<p class="widget-name"><small><?php echo h($widget['title']) ?></small></p>
								<?php echo $this->BcForm->create('Widget', ['url' => ['controller' => 'widget_areas', 'action' => 'update_widget', $this->BcForm->value('WidgetArea.id')], 'class' => 'form']) ?>
								<?php echo $this->BcForm->input('Widget.id', ['type' => 'hidden', 'class' => 'id']) ?>
								<?php echo $this->BcForm->input('Widget.type', ['type' => 'hidden', 'value' => $widget['title']]) ?>
								<?php echo $this->BcForm->input('Widget.element', ['type' => 'hidden', 'value' => $widget['name']]) ?>
								<?php echo $this->BcForm->input('Widget.plugin', ['type' => 'hidden', 'value' => $widgetInfo['plugin']]) ?>
								<?php echo $this->BcForm->input('Widget.sort', ['type' => 'hidden']) ?>
								<?php echo $this->BcForm->label('Widget.name', __d('baser', 'タイトル')) ?>&nbsp;
								<?php echo $this->BcForm->input('Widget.name', ['type' => 'text', 'class' => 'name']) ?>
								<br/>
								<?php echo $widget['setting'] ?><br/>
								<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['style' => 'vertical-align:middle;display:none', 'id' => 'WidgetUpdateWidgetLoader', 'class' => 'loader']) ?>
								<?php echo $this->BcForm->input('Widget.use_title', ['type' => 'checkbox', 'label' => __d('baser', 'タイトルを表示'), 'class' => 'use_title', 'checked' => 'checked']) ?>
								<?php echo $this->BcForm->input('Widget.status', ['type' => 'checkbox', 'label' => __d('baser', '利用する'), 'class' => 'status']) ?>
								<?php echo $this->BcForm->end(['label' => __d('baser', '保存'), 'div' => false, 'id' => 'WidgetUpdateWidgetSubmit', 'class' => 'button']) ?>
							</div>
						</div>
					<?php endforeach ?>
				<?php endforeach ?>
			</div>
		</div>

		<!-- 利用中のウィジェット -->
		<div id="TargetOuter">
			<div id="Target">

				<h2><?php echo __d('baser', '利用中のウィジェット') ?><?php $this->BcBaser->img('admin/ajax-loader-s.gif', [
						'style' => 'vertical-align:middle;display:none',
						'id' => 'WidgetAreaUpdateSortLoader',
						'class' => 'loader']); ?></h2>

				<?php if ($this->BcForm->value('WidgetArea.widgets')): ?>
					<?php foreach($this->BcForm->value('WidgetArea.widgets') as $widget): ?>

						<?php $key = key($widget) ?>
						<?php $enabled = '' ?>
						<?php if ($widget[$key]['status']): ?>
							<?php $enabled = ' enabled' ?>
						<?php endif ?>

						<div
							class="ui-widget-content sortable widget setting <?php echo h($widget[$key]['element']) ?><?php echo $enabled ?>"
							id="Setting<?php echo $widget[$key]['id'] ?>">
							<div class="clearfix">
								<div class="widget-name display-none"><?php echo h($widget[$key]['element']) ?></div>
								<div class="del"><?php echo __d('baser', '削除') ?></div>
								<div class="action"><?php echo __d('baser', '設定') ?></div>
								<div class="head"><?php echo h($widget[$key]['name']) ?></div>
							</div>
							<div class="content" style="text-align:right">
								<p><small><?php echo $widget[$key]['type'] ?></small></p>
								<?php echo $this->BcForm->create('Widget', ['url' => ['controller' => 'widget_areas', 'action' => 'update_widget', $this->BcForm->value('WidgetArea.id'), 'id' => false], 'class' => 'form', 'id' => 'WidgetUpdateWidgetForm' . $widget[$key]['id']]) ?>
								<?php echo $this->BcForm->input($key . '.id', ['type' => 'hidden', 'class' => 'id']) ?>
								<?php echo $this->BcForm->input($key . '.type', ['type' => 'hidden']) ?>
								<?php echo $this->BcForm->input($key . '.element', ['type' => 'hidden']) ?>
								<?php echo $this->BcForm->input($key . '.plugin', ['type' => 'hidden']) ?>
								<?php echo $this->BcForm->input($key . '.sort', ['type' => 'hidden']) ?>
								<?php echo $this->BcForm->label($key . 'name', __d('baser', 'タイトル')) ?>&nbsp;
								<?php echo $this->BcForm->input($key . '.name', ['type' => 'text', 'class' => 'name']) ?>
								<br/>
								<?php $this->BcBaser->element('widgets/' . $widget[$key]['element'], ['key' => $key, 'plugin' => $widget[$key]['plugin'], 'mode' => 'edit'], ['plugin' => $widget[$key]['plugin']]) ?>
								<br/>
								<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['style' => 'vertical-align:middle;display:none', 'id' => 'WidgetUpdateWidgetLoader' . $widget[$key]['id'], 'class' => 'loader']) ?>
								<?php echo $this->BcForm->input($key . '.use_title', ['type' => 'checkbox', 'label' => __d('baser', 'タイトルを表示'), 'class' => 'use_title']) ?>
								<?php echo $this->BcForm->input($key . '.status', ['type' => 'checkbox', 'label' => __d('baser', '利用する'), 'class' => 'status']) ?>
								<?php echo $this->BcForm->end(['label' => __d('baser', '保存'), 'div' => false, 'id' => 'WidgetUpdateWidgetSubmit' . $widget[$key]['id'], 'class' => 'button']) ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>
