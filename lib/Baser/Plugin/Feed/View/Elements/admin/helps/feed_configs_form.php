<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] フィード設定一覧　ヘルプ
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<p>フィード設定の基本項目を入力します。<br />
	フィードごとにデザインを変更する場合には、画面下の「オプション」をクリックしてテンプレート名を変更します。<br />
	<small>※ テンプレート名を変更した場合は、新しい名称のテンプレートを作成しアップロードする必要があります。</small><br />
	<a href="http://basercms.net/manuals/designers/7.html" target="_blank" class="outside-link">フィード読み込み部分のテンプレートを変更する</a></p>
<ul>
	<li>一つの設定につき、フィードは複数登録する事ができます。複数登録した場合は、複数のフィードを合わせた上で日付順に並び替えられます。</li>
	<li>フィードを追加するには、画面下の「フィード一覧」の「新規追加」ボタンをクリックします。</li>
</ul>

<div class="section">
	<h3 id="headHowTo">フィードの読み込み方法</h3>
	<p>以下のjavascriptを読み込みたい場所に貼り付けてください。</p>
	<textarea cols="100" rows="2" onclick="this.select(0,this.value.length)" readonly="readonly">
<?php echo $javascript->link('/feed/ajax/'.$this->data['FeedConfig']['id']) ?>
	</textarea>
	<br />
	<p>また、フィードの読み込みにはjQueryが必要ですので事前に読み込んでおく必要があります。</p>
	<h4>jQueryの読み込み例</h4>
	<textarea cols="100" rows="2" onclick="this.select(0,this.value.length)" readonly="readonly">
<?php echo $javascript->link('jquery-1.6.4.min') ?>
	</textarea>
</div>