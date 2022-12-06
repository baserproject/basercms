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
 * [ADMIN] テンプレートウィジェット設定
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $key
 * @checked
 * @noTodo
 * @unitTest
 */
$title = __d('baser', 'PHPテンプレート');
$description = __d('baser', 'PHPコードが書かれたテンプレートの読み込みが行えます。');
?>


<?php echo $this->BcAdminForm->label($key . '.template', __d('baser', 'PHPテンプレート名')) ?>
<?php echo $this->BcAdminForm->control($key . '.template', [
  'type' => 'text',
  'size' => 14]
) ?> <?php echo \Cake\Core\Configure::read('BcApp.templateExt') ?>
<p style="text-align:left">
  <small>
    <?php
    $webroot = preg_replace('/' . preg_quote(\BaserCore\Utility\BcUtil::docRoot(), '/') . '/', '', WWW_ROOT, 1);
    echo sprintf(__d('baser', 'テンプレートを利用中のテーマ内の次のパスに保存してください。<br />%stheme/{テーマ名}/Elements/widgets/'), $webroot);
    ?>
  </small>
</p>
