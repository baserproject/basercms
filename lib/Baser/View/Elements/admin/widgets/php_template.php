<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] テンプレートウィジェット設定
 */
$title = 'PHPテンプレート';
$description = 'PHPコードが書かれたテンプレートの読み込みが行えます。';
?>


<?php echo $this->BcForm->label($key . '.template', 'PHPテンプレート名') ?> 
<?php echo $this->BcForm->text($key . '.template', array('size' => 14)) ?> <?php echo $this->ext ?>
<p style="text-align:left"><small>テンプレートを利用中のテーマ内の次のパスに保存してください。<br />
		/app/webroot/theme/{テーマ名}/Elements/widgets/</small></p>