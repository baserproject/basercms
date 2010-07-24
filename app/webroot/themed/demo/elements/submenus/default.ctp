<?php
/**
 * サイドメニュー
 */
?>

<div class="side-navi">
	<p style="margin-bottom:20px;text-align: center"> <a href="http://basercms.net" target="_blank"><img src="http://basercms.net/img/bnr_basercms.jpg" alt="コーポレートサイトにちょうどいいCMS、BaserCMS"/></a> </p>
	<h2>ログインメニュー</h2>
	<ul>
		<li>
			<?php $baser->link('管理者ログイン',array('plugin'=>null,'admin'=>true,'controller'=>'users','action'=>'login'),array('target'=>'_blank')) ?>
		</li>
	</ul>
	<p class="customize-navi corner10"> <small>公開する際にはログインメニューは削除をおすすめします。</small> </p>
</div>
