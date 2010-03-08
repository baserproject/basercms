<?php
/* SVN FILE: $Id$ */
/**
 * インストーラー Step4
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
     $('#btnfinish').click( function()
     {
          if($("#installationAdminEmail").val() == ""){
            alert("管理用メールアドレスを入力して下さい。");
          }else if($("#installationAdminUsername").val() == ""){
            alert("ユーザー名を入力して下さい。");
          }else if($("#installationAdminPassword").val().length < 6){
            alert("あなたのパスワードを６文字以上で入力して下さい。");
          }else if($("#installationAdminPassword").val() != $("#installationAdminConfirmpassword").val()){
            alert("パスワードが確認欄のパスワードと同じではありません。");
          }else{
            $('#adminSettings').submit();
          }

          return false;
    });
});
</script>
<div id="installations">
    <form action="step5" method="post" name='adminSettings' id='adminSettings'>
        <div>
            <h3>管理用メールアドレス登録</h3>
            <ul>
                <li>
                    <?php echo $form->text('installation.admin_email', array('value'=>$adminEmail,'size'=>44)); ?>
                </li>
            </ul>
            <h3>管理ユーザー登録</h3>
            <p>ここで設定した管理者名とパスワードは忘れないように控えておいてください。</p>
            <ul>
                <li>
                    <label>管理者名</label><br />
                    <?php echo $form->text('installation.admin_username', array ('value'=>$adminUsername)); ?>
                </li>
                <li class="clearfix">
                    <label>パスワード</label><br />
                    <div class="float-left">
                        <?php echo $form->password('installation.admin_password', array ()); ?>
                    </div>
                    <div class="float-left">
                        <?php echo $form->password('installation.admin_confirmpassword', array()); ?><br />
                        <small>確認の為もう一度入力して下さい</small>
                    </div>
                </li>
            </ul>
        </div>
    </form>

    <div class="clearfix">
        <form action="step3" method="post" class="float-left">
            <button type="submit" class='btn-gray button' id='btnback' ><span>戻る</span></button>
        </form>
        <button class='btn-red button' name="step5" id='btnfinish' type='button'><span>完了</span></button>
    </div>
</div>