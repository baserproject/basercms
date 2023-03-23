<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * [ADMIN] ウィジェットエリア編集
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcWidgetArea\Model\Entity\WidgetArea $widgetArea
 * @var array $widgetInfos
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->i18nScript([
  'alertMessage1' => __d('baser_core', 'ウィジェットの削除に失敗しました。'),
  'alertMessage2' => __d('baser_core', 'ウィジェットエリアの並び替えの保存に失敗しました。'),
  'alertMessage3' => __d('baser_core', 'ウィジェットエリア名の保存に失敗しました。'),
  'alertMessage4' => __d('baser_core', 'ウィジェットの保存に失敗しました。'),
  'confirmMessage1' => __d('baser_core', '設定内容も削除されますが、本当に削除してもいいですか？'),
  'infoMessage1' => __d('baser_core', 'ウィジェットを削除しました。'),
  'infoMessage2' => __d('baser_core', 'ウィジェットエリア名を保存しました。'),
  'infoMessage3' => __d('baser_core', 'ウィジェットを保存しました。'),
]);
$this->BcBaser->js('BcWidgetArea.admin/widget_areas/form.bundle', false, [
  'id' => 'AdminWidgetAreasScript',
  'data-widgetAreaId' => $widgetArea->id
]);
$this->BcAdmin->setTitle(__d('baser_core', 'ウィジェットエリア編集'));
$this->BcAdmin->setHelp('widget_areas_form');
?>


<?php echo $this->BcAdminForm->create($widgetArea, ['id' => 'WidgetAreaUpdateTitleForm']) ?>
<?php echo $this->BcAdminForm->hidden('id') ?>
<?php echo $this->BcAdminForm->label('name', __d('baser_core', 'ウィジェットエリア名')) ?>&nbsp;
<?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 40, 'autofocus' => true]) ?>&nbsp;
<?php echo $this->BcAdminForm->submit(__d('baser_core', 'エリア名を保存する'), [
  'div' => false,
  'class' => 'bca-btn',
  'id' => 'WidgetAreaUpdateTitleSubmit',
  'data-bca-btn-type' => 'save'
]) ?>&nbsp;
<?php $this->BcBaser->img('admin/ajax-loader-s.gif', [
  'style' => 'display:none;',
  'class' => 'bca-small-loader',
  'id' => 'WidgetAreaUpdateTitleLoader'
]) ?>
<?php echo $this->BcAdminForm->error('name') ?>
<?php echo $this->BcAdminForm->end() ?>

<?php if (!empty($widgetInfos)): ?>

  <div id="WidgetSetting" class="clearfix">

    <!-- 利用できるウィジェット -->
    <div id="SourceOuter">
      <div id="Source">

        <h2><?php echo __d('baser_core', '利用できるウィジェット') ?></h2>
        <div id="WidgetsType">
          <?php foreach($widgetInfos as $widgetInfo) : ?>
            <h3><?php echo h($widgetInfo['title']) ?></h3>
            <div class="WidgetsTypeSection">
              <?php
              $widgets = [];
              foreach($widgetInfo['paths'] as $path) {
                $Folder = new \Cake\Filesystem\Folder($path);
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
                  $widget['name'] = basename($file, \Cake\Core\Configure::read('BcApp.templateExt'));
                  /** @var string $title */
                  $widget['title'] = $title;
                  /** @var string $description */
                  $widget['description'] = $description;
                  $widget['setting'] = ob_get_contents();
                  $widgets[] = $widget;
                  ob_end_clean();
                }
              }
              ?>
              <?php foreach($widgets as $widget): ?>

                <div class="ui-widget-content draggable widget" id="Widget<?php echo \Cake\Utility\Inflector::camelize($widget['name']) ?>">
                  <div class="head"><?php echo h($widget['title']) ?></div>
                </div>

                <div class="description"><?php echo h($widget['description']) ?></div>

                <div class="ui-widget-content sortable widget template <?php echo h($widget['name']) ?>"
                     id="<?php echo \Cake\Utility\Inflector::camelize($widget['name']) ?>">
                  <div class="clearfix">
                    <div class="widget-name display-none"><?php echo h($widget['name']) ?></div>
                    <div class="del"><?php echo __d('baser_core', '削除') ?></div>
                    <div class="action"><?php echo __d('baser_core', '設定') ?></div>
                    <div class="head"><?php echo h($widget['title']) ?></div>
                  </div>
                  <div class="content" style="text-align:right">
                    <p class="widget-name"><small><?php echo h($widget['title']) ?></small></p>
                    <?php echo $this->BcAdminForm->create(null, ['class' => 'form']) ?>
                    <?php echo $this->BcAdminForm->control('Widget.id', ['type' => 'hidden', 'class' => 'id']) ?>
                    <?php echo $this->BcAdminForm->control('Widget.type', ['type' => 'hidden', 'value' => $widget['title']]) ?>
                    <?php echo $this->BcAdminForm->control('Widget.element', ['type' => 'hidden', 'value' => $widget['name']]) ?>
                    <?php echo $this->BcAdminForm->control('Widget.plugin', ['type' => 'hidden', 'value' => $widgetInfo['plugin']]) ?>
                    <?php echo $this->BcAdminForm->control('Widget.sort', ['type' => 'hidden']) ?>
                    <p>
                      <?php echo $this->BcAdminForm->label('Widget.name', __d('baser_core', 'タイトル')) ?>&nbsp;
                      <?php echo $this->BcAdminForm->control('Widget.name', ['type' => 'text', 'class' => 'bca-textbox__input name']) ?>
                    </p>
                    <?php echo $widget['setting'] ?>
                    <p>
                      <?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['style' => 'display:none', 'id' => 'WidgetUpdateWidgetLoader', 'class' => 'bca-small-loader']) ?>
                      <?php echo $this->BcAdminForm->control('Widget.use_title', ['type' => 'checkbox', 'label' => __d('baser_core', 'タイトルを表示'), 'class' => 'bca-checkbox__input use_title', 'checked' => 'checked']) ?>
                      <?php echo $this->BcAdminForm->control('Widget.status', ['type' => 'checkbox', 'label' => __d('baser_core', '利用する'), 'class' => 'bca-checkbox__input status']) ?>
                      <?php echo $this->BcAdminForm->submit(__d('baser_core', '保存'), [
                        'div' => false,
                        'class' => 'bca-btn',
                        'id' => 'WidgetUpdateWidgetSubmit',
                        'data-bca-btn-type' => 'save'
                      ]) ?>
                      <?php echo $this->BcAdminForm->end() ?>
                    </p>
                  </div>
                </div>
              <?php endforeach ?>
            </div>
          <?php endforeach ?>
        </div>
      </div>
    </div>

    <!-- 利用中のウィジェット -->
    <div id="TargetOuter">
      <div id="Target">

        <h2>
          <?php echo __d('baser_core', '利用中のウィジェット ') ?>
          <?php $this->BcBaser->img('admin/ajax-loader-s.gif', [
            'style' => 'display:none',
            'id' => 'WidgetAreaUpdateSortLoader',
            'class' => 'bca-small-loader'
          ]) ?>
        </h2>

        <?php if ($widgetArea->widgets_array): ?>
          <?php foreach($widgetArea->widgets_array as $widget): ?>

            <?php
            $key = key($widget);
            $enabled = '';
            if ($widget[$key]['status']) {
              $enabled = ' enabled';
            }
            $this->setRequest($this->getRequest()->withParsedBody(array_merge(
              $this->getRequest()->getData(),
              [$key => $widget[$key]]
            )));
            ?>

            <div
              class="ui-widget-content sortable widget setting <?php echo h($widget[$key]['element']) ?><?php echo $enabled ?>"
              id="Setting<?php echo $widget[$key]['id'] ?>">
              <div class="clearfix">
                <div class="widget-name display-none"><?php echo h($widget[$key]['element']) ?></div>
                <div class="del"><?php echo __d('baser_core', '削除') ?></div>
                <div class="action"><?php echo __d('baser_core', '設定') ?></div>
                <div class="head"><?php echo h($widget[$key]['name']) ?></div>
              </div>
              <div class="content" style="text-align:right">
                <p><small><?php echo $widget[$key]['type'] ?></small></p>
                <?php echo $this->BcAdminForm->create(null, [
                  'class' => 'form',
                  'id' => 'WidgetUpdateWidgetForm' . $widget[$key]['id']
                ]) ?>
                <?php echo $this->BcAdminForm->control($key . '.id', ['type' => 'hidden', 'class' => 'id']) ?>
                <?php echo $this->BcAdminForm->control($key . '.type', ['type' => 'hidden']) ?>
                <?php echo $this->BcAdminForm->control($key . '.element', ['type' => 'hidden']) ?>
                <?php echo $this->BcAdminForm->control($key . '.plugin', ['type' => 'hidden']) ?>
                <?php echo $this->BcAdminForm->control($key . '.sort', ['type' => 'hidden']) ?>
                <p>
                  <?php echo $this->BcAdminForm->label($key . 'name', __d('baser_core', 'タイトル')) ?>&nbsp;
                  <?php echo $this->BcAdminForm->control($key . '.name', ['type' => 'text', 'class' => 'name bca-textbox__input']) ?>
                </p>
                <?php if (!empty($widget[$key]['element'])): ?>
                  <?php $this->BcBaser->element($widget[$key]['plugin'] . '.widget/' . $widget[$key]['element'], [
                    'key' => $key,
                    'plugin' => $widget[$key]['plugin'],
                    'mode' =>
                      'edit'
                  ], ['plugin' => $widget[$key]['plugin']]) ?>
                <?php endif ?>
                <p>
                  <?php $this->BcBaser->img('admin/ajax-loader-s.gif', [
                    'style' => 'display:none',
                    'id' => 'WidgetUpdateWidgetLoader' . $widget[$key]['id'],
                    'class' => 'bca-small-loader'
                  ]) ?>
                  <?php echo $this->BcAdminForm->control($key . '.use_title', ['type' => 'checkbox', 'label' => __d('baser_core', 'タイトルを表示'), 'class' => 'bca-checkbox__input use_title']) ?>
                  <?php echo $this->BcAdminForm->control($key . '.status', ['type' => 'checkbox', 'label' => __d('baser_core', '利用する'), 'class' => 'bca-checkbox__input status']) ?>
                  <?php echo $this->BcAdminForm->submit(__d('baser_core', '保存'), [
                    'div' => false,
                    'class' => 'bca-btn',
                    'id' => 'WidgetUpdateWidgetSubmit' . $widget[$key]['id'],
                    'data-bca-btn-type' => 'save'
                  ]) ?>
                  <?php echo $this->BcAdminForm->end() ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
