<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] グローバルメニュー一覧　ヘルプ
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

<p>公開ページに表示するメニューの管理ができます。<br />
	このメニューは必ずしも利用しなければならないというわけではありません。
	凝ったデザインのメニューが必要な場合や、
	メニューを可変させる必要がない場合等は、直接HTMLのコードを書いた方が柔軟に対応できる事があります。</p>
<ul>
	<li>一覧は、公開状態により絞り込みができます。</li>
	<li>公開ページでメニューを出力するには、テンプレート上に次のコードを記述します。リストタグで出力されます。<br />
		<pre>&lt;?php $bcBaser->element('global_menu') ?&gt;</pre></li>
	<li>一覧左上の「並び替え」をクリックすると表示される<?php $bcBaser->img('sort.png',array('alt'=>'並び替え')) ?>マークをドラッグアンドドロップして、行の並び替えができます。</li>
</ul>
