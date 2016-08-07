
<div style="text-align: center; margin-top:60px;margin-bottom:60px;">
<?php $this->BcBaser->link('サーバーキャッシュ削除', array('controller' => 'site_configs', 'action' => 'del_cache', 'plugin' => null), array('class' => 'button-small', 'confirm' => 'サーバーキャッシュを削除します。いいですか？')) ?>　
<?php $this->BcBaser->link('固定ページテンプレート書出', array('controller' => 'pages', 'action' => 'write_page_files'), array('class' => 'button-small', 'confirm' => "データベース内のページデータを、ページテンプレートとして /app/View/Pages 内に全て書出します。\n本当によろしいですか？")) ?>　
<?php $this->BcBaser->link('固定ページテンプレート読込', array('controller' => 'pages', 'action' => 'entry_page_files'), array('class' => 'button-small', 'confirm' => "/app/View/Pages フォルダ内のページテンプレートを全て読み込みます。\n本当によろしいですか？")) ?>　
<?php $this->BcBaser->link('クレジット', 'javascript:void(0)', array('class' => 'button-small', 'id' => 'BtnCredit')) ?>
</div>