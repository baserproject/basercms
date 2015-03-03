<?php
/**
 * [ADMIN] ブログカテゴリー一覧ウィジェット設定
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$title = 'ブログカテゴリー一覧';
$description = 'ブログのカテゴリー一覧を表示します。';
?>

<script type="text/javascript">
$(function(){
	var key = "<?php echo $key ?>";
	$("#"+key+"ByYear").click(function(){
		if($("#"+key+"ByYear").attr('checked') == 'checked') {
			$("#"+key+"Depth").val(1);
			$("#Span"+key+"Depth").slideUp(200);
		} else {
			$("#Span"+key+"Depth").slideDown(200);
		}
	});
	if($("#"+key+"ByYear").attr('checked') == 'checked') {
		$("#"+key+"Depth").val(1);
		$("#Span"+key+"Depth").hide();
	}
});
</script>

<?php echo $this->BcForm->label($key . '.limit', '表示数') ?>&nbsp;
<?php echo $this->BcForm->input($key . '.limit', array('type' => 'text', 'size' => 6)) ?>&nbsp;件<br />
<?php echo $this->BcForm->label($key . '.view_count', '記事数表示') ?>&nbsp;
<?php echo $this->BcForm->input($key . '.view_count', array('type' => 'radio', 'options' => $this->BcText->booleanDoList(''), 'legend' => false, 'default' => 0)) ?><br />
<?php echo $this->BcForm->input($key . '.by_year', array('type' => 'checkbox', 'label' => '年別に表示する')) ?><br />
<p id="Span<?php echo $key ?>Depth"><?php echo $this->BcForm->label($key . '.depth', '深さ') ?>&nbsp;
	<?php echo $this->BcForm->input($key . '.depth', array('type' => 'text', 'size' => 6, 'default' => 1)) ?>&nbsp;階層</p>
<?php echo $this->BcForm->label($key . '.blog_content_id', 'ブログ') ?>&nbsp;
<?php echo $this->BcForm->input($key . '.blog_content_id', array('type' => 'select', 'options' => $this->BcForm->getControlSource('Blog.BlogContent.id'))) ?><br />
<small>ブログページを表示している場合は、上記の設定に関係なく、<br />対象ブログのブログカテゴリー一覧を表示します。</small>