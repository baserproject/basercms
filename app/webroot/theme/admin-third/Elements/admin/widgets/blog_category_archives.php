<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログカテゴリー一覧ウィジェット設定
 */
$title = __d('baser', 'カテゴリ一覧');
$description = __d('baser', 'カテゴリ一覧を表示します。');
?>

<script type="text/javascript">
	$(function () {
		var key = "<?php echo $key ?>";
		$("#" + key + "ByYear").click(function () {
			if ($("#" + key + "ByYear").prop('checked')) {
				$("#" + key + "Depth").val(1);
				$("#Span" + key + "Depth").slideUp(200);
			} else {
				$("#Span" + key + "Depth").slideDown(200);
			}
		});
		if ($("#" + key + "ByYear").prop('checked')) {
			$("#" + key + "Depth").val(1);
			$("#Span" + key + "Depth").hide();
		}
	});
</script>

<?php echo $this->BcForm->label($key . '.limit', __d('baser', '表示数')) ?>&nbsp;
<?php echo $this->BcForm->input($key . '.limit', ['type' => 'text', 'size' => 6]) ?>&nbsp;件<br/>
<?php echo $this->BcForm->label($key . '.view_count', __d('baser', '記事数表示')) ?>&nbsp;
<?php echo $this->BcForm->input($key . '.view_count', ['type' => 'radio', 'options' => $this->BcText->booleanDoList(''), 'legend' => false, 'default' => 0]) ?>
<br/>
<?php echo $this->BcForm->input($key . '.by_year', ['type' => 'checkbox', 'label' => __d('baser', '年別に表示する')]) ?><br/>
<p id="Span<?php echo $key ?>Depth"><?php echo $this->BcForm->label($key . '.depth', __d('baser', '深さ')) ?>&nbsp;
	<?php echo $this->BcForm->input($key . '.depth', ['type' => 'text', 'size' => 6, 'default' => 1]) ?>
	&nbsp;<?php echo __d('baser', '階層') ?></p>
<?php echo $this->BcForm->label($key . '.blog_content_id', __d('baser', 'ブログ')) ?>&nbsp;
<?php echo $this->BcForm->input($key . '.blog_content_id', ['type' => 'select', 'options' => $this->BcForm->getControlSource('Blog.BlogContent.id')]) ?>
<br/>
<small><?php echo __d('baser', 'ブログページを表示している場合は、上記の設定に関係なく、<br>対象ブログのカテゴリ一覧を表示します。') ?></small>
