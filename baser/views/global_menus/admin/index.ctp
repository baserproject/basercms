<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] グローバルメニュー一覧
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<script type="text/javascript">
    $(document).ready(function(){
        <?php if($form->value('GlobalMenu.open')): ?>
            $("#GlobalMenuSearchBody").show();
        <?php endif ?>
    });
</script>

<h2><?php $baser->contentsTitle() ?></h2>

<h3><a href="javascript:void(0);" class="slide-trigger" id="GlobalMenuSearch">検索</a></h3>
<div class="function-box corner10" id="GlobalMenuSearchBody" style="display:none">
    <?php echo $formEx->create('GlobalMenu',array('url'=>array('action'=>'index'))) ?>
    <p>
        <small>タイプ</small>
        <?php echo $formEx->select('GlobalMenu.menu_type',  $formEx->getControlSource('menu_type'),null,array(),false) ?>　
        <small>公開設定</small>
        <?php echo $formEx->select('GlobalMenu.status', $textEx->booleanMarkList()) ?>　
    </p>
    <?php echo $formEx->hidden('GlobalMenu.open',array('value'=>true)) ?>
    <div class="align-center">
        <?php echo $formEx->submit('検　索',array('div'=>false,'class'=>'btn-orange button')) ?>
    </div>
</div>


<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableGlobalMenus">
    <tr>
        <th>操作</th>
        <th>NO</th>
        <th>タイプ</th>
        <th>メニュー名</th>
        <th>リンクURL</th>
        <th>登録日</th>
        <th>更新日</th>
    </tr>
    <?php if(!empty($listDatas)): ?>
    <?php $count=0; ?>
    <?php foreach($listDatas as $listData): ?>
        <?php if (!$listData['GlobalMenu']['status']): ?>
            <?php $class=' class="disablerow"'; ?>
        <?php elseif ($count%2 === 0): ?>
            <?php $class=' class="altrow"'; ?>
        <?php else: ?>
            <?php $class=''; ?>
        <?php endif; ?>
        <tr<?php echo $class; ?>>
            <td class="operation-button">
                <?php $baser->link('編集',array('action'=>'edit', $listData['GlobalMenu']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
                <?php $baser->link('削除', array('action'=>'delete', $listData['GlobalMenu']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $listData['GlobalMenu']['name']),false); ?>
				<?php $baser->link('▲',array('action'=>'index','sortup'=>$listData['GlobalMenu']['id'])) ?>
				<?php $baser->link('▼',array('action'=>'index','sortdown'=>$listData['GlobalMenu']['id'])) ?>
            </td>
            <td><?php echo $listData['GlobalMenu']['no']; ?></td>
            <td><?php echo $textEx->listValue('GlobalMenu.menu_type',$listData['GlobalMenu']['menu_type']); ?></td>
            <td><?php $baser->link($listData['GlobalMenu']['name'],array('action'=>'edit',$listData['GlobalMenu']['id'])); ?></td>
            <td><?php $baser->link($listData['GlobalMenu']['link'],$listData['GlobalMenu']['link'],array('target'=>'_blank')); ?></td>
            <td><?php echo $timeEx->format('y-m-d',$listData['GlobalMenu']['created']); ?></td>
            <td><?php echo $timeEx->format('y-m-d',$listData['GlobalMenu']['modified']); ?></td>
        </tr>
        <?php $count++; ?>
    <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7"><p class="no-data">データが見つかりませんでした。</p></td></tr>
    <?php endif; ?>
</table>

<div class="align-center"><?php $baser->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?></div>