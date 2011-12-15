<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページ一覧
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$users = $formEx->getControlSource("Page.user_id");
$baser->js('sorttable', false);
$allowOwners = array();
if(!empty($user)) {
	$allowOwners = array('', $user['user_group_id']);
}
$pageType = array();
if(Configure::read('Baser.mobile') || Configure::read('Baser.smartphone')) {
	$pageType = array('1' => 'PC');	
}
if(Configure::read('Baser.mobile')) {
	$pageType['2'] = 'モバイル';
}
if(Configure::read('Baser.smartphone')) {
	$pageType['3'] = 'スマートフォン';
}
?>

<script type="text/javascript">
$(document).ready(function(){
	<?php if($form->value('Page.open')): ?>
	$("#PageFilterBody").show();
	<?php endif ?>
	$('input[name="data[Page][page_type]"]').click(pageTypeChengeHandler);
});
function pageTypeChengeHandler() {
	var pageType = $('input[name="data[Page][page_type]"]:checked').val();
	$.ajax({
		type: "POST",
		url: $("#AjaxCategorySourceUrl").html()+'/'+pageType,
		beforeSend: function() {
			$("#CategoryAjaxLoader").show();
		},
		success: function(result){
			if(result) {
				var categoryId = $("#PagePageCategoryId").val();
				$("#PagePageCategoryId option").remove();
				$("#PagePageCategoryId").append('<option value="">指定しない</option>');
				$("#PagePageCategoryId").append('<option value="noncat">カテゴリなし</option>');
				$("#PagePageCategoryId").append($(result).find('option'));
				$("#PagePageCategoryId").val(categoryId);
			}
		},
		complete: function() {
			$("#CategoryAjaxLoader").hide();
		}
	});
}
</script>

<div id="AjaxCategorySourceUrl" class="display-none"><?php $baser->url(array('action' => 'ajax_category_source')) ?></div>

<?php echo $formEx->create('Sort', array('action' => 'update_sort', 'url' => am(array('controller'=>'pages'), $this->passedArgs))) ?>
<?php echo $formEx->input('Sort.id', array('type' => 'hidden')) ?>
<?php echo $formEx->input('Sort.offset', array('type' => 'hidden')) ?>
<?php echo $formEx->end() ?>

<div id="pageMessage" class="message" style="display:none"></div>

<h2>
	<?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?>
	<?php $baser->img('ajax-loader-s.gif', array('id' => 'ListAjaxLoader')) ?>
</h2>

<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ページ管理では、ブログやメールフォーム以外のWEBページを管理する事ができます。</p>
	<ul>
		<li>新しいページを登録するには、画面下の「新規登録」ボタンをクリックします。</li>
		<li>公開状態を設定する事ができ、一覧各ページ横の「確認」ボタンをクリックすると非公開のページも確認する事ができます。</li>
		<li>各ページは分類分け用の「カテゴリ」に属させる事ができ、階層構造のURLを実現できます。</li>
		<li>管理画面内では、公開状態、カテゴリによりページの検索を行う事ができます。<br />
			検索するには、すぐ下の「検索」をクリックして検索条件を表示させます。</li>
		<li>画面一番下の「並び替えモード」をクリックすると、表示される<?php $baser->img('sort.png',array('alt'=>'並び替え')) ?>マークをドラッグアンドドロップして行の並び替えができます。<br />
		<small>※ この並び順はローカルナビゲーション等に反映されます。</small></li>
		<li>オーサリングツールでの制作に慣れている方向けに、ファイルをアップロードしてデータベースに一括で読み込む機能を備えています。<br />
			ページを読み込むには、特定のフォルダにページテンプレートをアップロードして、サイドメニューの「ページテンプレート読込」を実行します。<br />
			<a href="http://basercms.net/manuals/etc/5.html" target="_blank">≫ ページテンプレート読込について</a></li>
	</ul>
	<div class="example-box">
		<div class="head">（例）ページ名「about」として作成したページを表示させる為のURL</div>
		<p>http://[baserCMS設置URL]/about</p>
		<div class="head">（例）カテゴリ「company」に属する、ページ名「about」として作成したページを表示させる為のURL</div>
		<p>http://[baserCMS設置URL]/company/about</p>
	</div>
</div>

<!-- search -->
<h3><a href="javascript:void(0);" class="slide-trigger" id="PageFilter">検索</a></h3>
<div class="function-box corner10" id="PageFilterBody" style="display:none">
	<?php echo $formEx->create('Page', array('url' => array('action' => 'index'))) ?>
	<p>
		<span><small>ページ名</small> <?php echo $formEx->input('Page.name', array('type' => 'text', 'size' => '30')) ?></span>
		<span><small>公開状態</small>
		<?php echo $formEx->input('Page.status', array('type' => 'select', 'options' => $textEx->booleanMarkList(), 'empty' => '指定なし')) ?></span>　
		<span><small>作成者</small>
		<?php echo $formEx->input('Page.author_id', array('type' => 'select', 'options' => $users, 'empty' => '指定なし')) ?></span>　
<?php if($pageType): ?>
		<span><small>タイプ</small>
		<?php echo $formEx->input('Page.page_type', array(
				'type'		=> 'radio',
				'options'	=> $pageType)) ?></span>　
<?php endif ?>
<?php if($pageCategories): ?>
		<span><small>カテゴリ</small>
		<?php echo $formEx->input('Page.page_category_id', array(
				'type'		=> 'select',
				'options'	=> $pageCategories,
				'escape'	=> false)) ?></span>
		<?php $baser->img('ajax-loader-s.gif', array('id' => 'CategoryAjaxLoader', 'class' => 'display-none', 'style' => 'vertical-align:middle')) ?>　<br />
<?php endif ?>
</p>
		<div class="align-center"><?php echo $formEx->submit('検　索', array('div' => false, 'class' => 'btn-orange button')) ?></div> 
		<?php echo $formEx->hidden('Page.open',array('value'=>true)) ?>
	<?php $formEx->end() ?>
</div>

<!-- list-num -->
<?php $baser->element('list_num') ?>

<!-- pagination -->
<?php $baser->pagination('default', array(), null, false) ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01 sort-table" id="TablePages">
	<tr>
		<th>操作</th>
<?php if(!$sortmode): ?>
		<th><?php echo $paginator->sort(array('asc' => 'NO ▼', 'desc' => 'NO ▲'), 'id') ?></th>
		<th><?php echo $paginator->sort(array('asc' => 'カテゴリ ▼', 'desc' => 'カテゴリ ▲'), 'page_category_id') ?></th>
		<th>
			<?php echo $paginator->sort(array('asc' => 'ページ名 ▼', 'desc' => 'ページ名 ▲'), 'name') ?><br />
			<?php echo $paginator->sort(array('asc' => 'タイトル ▼', 'desc' => 'タイトル ▲'), 'title') ?>
		</th>
		<th>
			<?php echo $paginator->sort(array('asc' => '公開状態 ▼', 'desc' => '公開状態 ▲'), 'status') ?><br />
		</th>
		<th>
			<?php echo $paginator->sort(array('asc' => '作成者 ▼', 'desc' => '作成者 ▲'), 'author_id') ?><br />
		</th>
		<th>
			<?php echo $paginator->sort(array('asc' => '登録日 ▼', 'desc' => '登録日 ▲'), 'created') ?><br />
			<?php echo $paginator->sort(array('asc' => '更新日 ▼', 'desc' => '更新日 ▲'), 'modified') ?>
		</th>
<?php else: ?>
		<th>NO</th>
		<th>カテゴリ</th>
		<th>ページ名<br />タイトル</th>
		<th>公開状態</th>
		<th>作成者</th>
		<th>登録日<br />更新日</th>
<?php endif ?>
	</tr>
<?php if(!empty($dbDatas)): ?>
	<?php $count=0; ?>
	<?php foreach($dbDatas as $dbData): ?>
		<?php if (!$page->allowPublish($dbData)): ?>
			<?php $class=' class="disablerow sortable"'; ?>
		<?php elseif ($count%2 === 0): ?>
			<?php $class=' class="altrow sortable"'; ?>
		<?php else: ?>
			<?php $class=' class="sortable"'; ?>
		<?php endif; ?>
		<?php if(empty($dbData['PageCategory']['id']) || $dbData['PageCategory']['name'] == 'mobile'): ?>
			<?php $ownerId = $baser->siteConfig['root_owner_id'] ?>
		<?php else: ?>
			<?php $ownerId = $dbData['PageCategory']['owner_id'] ?>
		<?php endif ?>
	<tr id="Row<?php echo $count+1 ?>" <?php echo $class; ?>>
		<td class="operation-button" style="width:15%">
		<?php if($sortmode): ?>
			<span class="sort-handle"><?php $baser->img('sort.png', array('alt' => '並び替え')) ?></span>
			<?php echo $formEx->input('Sort.id'.$dbData['Page']['id'], array(
				'type'	=> 'hidden',
				'class'	=> 'id',
				'value' => $dbData['Page']['id'])) ?>
		<?php endif ?>
		<?php $url = preg_replace('/index$/', '', $dbData['Page']['url']) ?>
			
		<?php if(!preg_match('/^\/'.Configure::read('AgentSettings.mobile.prefix').'\//is', $url) && !preg_match('/^\/'.Configure::read('AgentSettings.smartphone.prefix').'\//is', $url)): ?>
			<?php $baser->link('確認', $url, array('class' => 'btn-green-s button-s', 'target' => '_blank'), null, false) ?>
			
		<?php elseif(preg_match('/^\/'.Configure::read('AgentSettings.mobile.prefix').'\//is', $url)): ?>
			<?php $baser->link('確認',
					preg_replace('/^\/'.Configure::read('AgentSettings.mobile.prefix').'\//is', '/'.Configure::read('AgentSettings.mobile.alias').'/', $url),
					array('class' => 'btn-green-s button-s', 'target' => '_blank'),
					null, false) ?>			
			
		<?php elseif(preg_match('/^\/'.Configure::read('AgentSettings.smartphone.prefix').'\//is', $url)): ?>
			<?php $baser->link('確認',
					preg_replace('/^\/'.Configure::read('AgentSettings.smartphone.prefix').'\//is', '/'.Configure::read('AgentSettings.smartphone.alias').'/', $url),
					array('class' => 'btn-green-s button-s', 'target' => '_blank'),
					null, false) ?>			
		<?php endif ?>
			
		<?php $baser->link('詳細', 
				array('action' => 'edit', $dbData['Page']['id']),
				array('class' => 'btn-orange-s button-s'),
				null, false) ?>
		
		<?php if(in_array($ownerId, $allowOwners)||(!empty($user) && $user['user_group_id']==1)): ?>
			<?php $baser->link('削除', 
					array('action' => 'delete', $dbData['Page']['id']),
					array('class' => 'btn-gray-s button-s'),
					sprintf('%s を本当に削除してもいいですか？', $dbData['Page']['name']),
					false); ?>
		<?php endif ?>
		</td>
		<td style="width:5%"><?php echo $dbData['Page']['id']; ?></td>
		<td style="width:15%">
		<?php if(!empty($dbData['PageCategory']['title'])): ?>
			<?php echo $dbData['PageCategory']['title']; ?>
		<?php endif; ?>
		</td>
		<td style="width:30%">
			<?php $baser->link($dbData['Page']['name'], array('action' => 'edit', $dbData['Page']['id'])); ?><br />
			<?php echo $dbData['Page']['title']; ?>
		</td>
		<td style="width:10%;text-align:center">
			<?php echo $textEx->booleanMark($dbData['Page']['status']); ?><br />
		</td>
		<td style="width:15%;text-align:center">
		<?php if(isset($users[$dbData['Page']['author_id']])) : ?>
			<?php echo $users[$dbData['Page']['author_id']] ?>
		<?php endif ?>
		</td>
		<td style="width:10%;white-space: nowrap">
			<?php echo $timeEx->format('y-m-d',$dbData['Page']['created']) ?><br />
			<?php echo $timeEx->format('y-m-d',$dbData['Page']['modified']) ?>
		</td>
	</tr>
		<?php $count++; ?>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</table>

<!-- pagination -->
<?php $baser->pagination('default', array(), null, false) ?>

<div class="align-center">
<?php if($newCatAddable): ?>
	<?php $baser->link('新規登録', array('action' => 'add'), array('class' => 'btn-red button')) ?>
<?php endif ?>
<?php if(!$sortmode): ?>
	<?php $baser->link('並び替えモード', array('sortmode' => 1), array('class' => 'btn-orange button')) ?>
<?php else: ?>
	<?php $baser->link('ノーマルモード', array('sortmode' => 0), array('class' => 'btn-orange button')) ?>
<?php endif ?>
</div>
