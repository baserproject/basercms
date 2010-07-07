<?php
/* SVN FILE: $Id$ */
/**
 * BaserCMS初期化ページ
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
    $(function(){
        $("#ResetForm").submit(function(){
            if(confirm('本当にBaserCMSを初期化してもよろしいですか？')){
		return true;
            }else{
                return false;
            }
        });
    });
</script>
<h2><?php $baser->contentsTitle() ?></h2>
<?php if(!$complete): ?>
    <p>BaserCMSを初期化します。データベースのデータも全て削除されます。<br />
        データベースのバックアップをとられていない場合は必ずバックアップを保存してから実行して下さい。</p>
    <p>ファイルベースのデータベースの保存場所は下記のとおりです。</p>
    <ul>
        <li>SQLite：　/app/db/sqlite/</li>
        <li>CSV：　/app/db/csv/</li>
    </ul>
    <?php echo $form->create(array('action'=>'reset')) ?>
    <?php echo $form->hidden('Installation.reset',array('value'=>true)) ?>
    <?php echo $form->end(array('label'=>'初期化する','class'=>'button btn-gray')) ?>
<?php else: ?>
	<p>引き続きBaserCMSのインストールを行うには、「インストール実行」ボタンをクリックしてください。</p>
    <div class="align-center">
    <?php $baser->link('インストール実行','/',array('class'=>'button btn-red')) ?>
    </div>
<?php endif ?>