<?php
$editLink = $this->BcContents->settings[$srcContent['type']]['routes']['edit'];
if($srcContent['entity_id']) {
    $editLink .= '/' . $srcContent['entity_id'];
}
$editLink .= '/content_id:' . $srcContent['id'] . '/parent_id:' . $srcContent['parent_id'];
?>


<table class="form-table">
    <tr>
        <th><?php echo $this->BcForm->label('Content.alias_id', '元コンテンツ') ?></th>
        <td>
            <?php echo $this->BcForm->input('Content.alias_id', array('type' => 'hidden')) ?>
            <small>[<?php echo $this->BcContents->settings[$srcContent['type']]['title'] ?>]</small>&nbsp;
           &nbsp;
           <?php $this->BcBaser->link($srcContent['title'], $editLink, array('target' => '_blank')) ?>
			<?php if($related): ?>
			<p>このコンテンツはメインサイトの連携エイリアスです。<br>レイアウトテンプレート以外を編集する場合は上記リンクをクリックしてメインサイトのコンテンツを編集してください。</p>
			<?php endif ?>
        </td>
    </tr>
</table>