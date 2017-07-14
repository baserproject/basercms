<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 4.0.5
 * @license			http://basercms.net/license/index.html
 */

/**
 * ブログタグ一覧ウィジェット設定
 * 
 * @var \BcAppView $this
 */
$title = 'ブログタグ一覧';
$description = 'ブログのタグ一覧を表示します。';
?>


<?php echo $this->BcForm->label($key . '.view_count', '記事数表示') ?>&nbsp;
<?php echo $this->BcForm->input($key . '.view_count', ['type' => 'radio', 'options' => $this->BcText->booleanDoList(''), 'legend' => false, 'default' => 0]) ?><br>
<?php echo $this->BcForm->label($key . '.blog_content_id', 'ブログ') ?>&nbsp;
<?php echo $this->BcForm->input($key . '.blog_content_id', ['type' => 'select', 'options' => $this->BcForm->getControlSource('Blog.BlogContent.id'), 'empty' => '指定しない']) ?><br>
