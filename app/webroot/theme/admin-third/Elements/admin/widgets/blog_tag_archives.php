<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 4.0.5
 * @license         https://basercms.net/license/index.html
 */

/**
 * ブログタグ一覧ウィジェット設定
 *
 * @var \BcAppView $this
 */
$title = __d('baser', 'ブログタグ一覧');
$description = __d('baser', 'ブログのタグ一覧を表示します。');
?>


<?php echo $this->BcForm->label($key . '.view_count', __d('baser', '記事数表示')) ?>&nbsp;
<?php echo $this->BcForm->input($key . '.view_count', ['type' => 'radio', 'options' => $this->BcText->booleanDoList(''), 'legend' => false, 'default' => 0]) ?>
<br>
<?php echo $this->BcForm->label($key . '.blog_content_id', __d('baser', 'ブログ')) ?>&nbsp;
<?php echo $this->BcForm->input($key . '.blog_content_id', ['type' => 'select', 'options' => $this->BcForm->getControlSource('Blog.BlogContent.id'), 'empty' => __d('baser', '指定しない')]) ?>
<br>
