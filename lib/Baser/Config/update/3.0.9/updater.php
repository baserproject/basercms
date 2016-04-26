<?php
/**
 * 3.0.9 バージョン アップデートスクリプト
 *
 * ----------------------------------------
 * 　アップデートの仕様について
 * ----------------------------------------
 * アップデートスクリプトや、スキーマファイルの仕様については
 * 次のファイルに記載されいているコメントを参考にしてください。
 *
 * /lib/Baser/Controllers/UpdatersController.php
 *
 * スキーマ変更後、モデルを利用してデータの更新を行う場合は、
 * ClassRegistry を利用せず、モデルクラスを直接イニシャライズしないと、
 * スキーマのキャッシュが古いままとなるので注意が必要です。
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.9
 * @license			http://basercms.net/license/index.html
 */

/**
 * mail_contents テーブル変更
 */
    if($this->loadSchema('3.0.9', 'Mail', 'mail_contents', $filterType = 'alter')) {
        $this->setUpdateLog('mail_contents テーブルの構造変更に成功しました。');
    } else {
        $this->setUpdateLog('mail_contents テーブルの構造変更に失敗しました。', true);
    }

/**
 * データを更新
 *
 * MailContent.save_info
 */
    CakePlugin::load('Mail');
    App::uses('MailAppModel', 'Mail.Model');
    App::uses('MailContent', 'Mail.Model');

    $MailContent = new MailContent();
    $datas = $MailContent->find('all', array('recursive' => -1));
    $result = true;
    foreach($datas as $data) {
        $data['MailContent']['save_info'] = true;
        if(!$MailContent->save($data)) {
            $result = false;
        }
    }
    if($result){
        $this->setUpdateLog('mail_contents テーブルの変換に成功しました。');
    } else {
        $this->setUpdateLog('mail_contents テーブルの変換に失敗しました。', true);
    }

/**
 * 管理システム用アセットの再デプロイ
 */
    $this->Components->load('BcManager');
    if($this->BcManager->deployAdminAssets()) {
        $this->setUpdateLog('管理システム用アセットの再配置に成功しました。');
    } else {
        $this->setUpdateLog('管理システム用アセットの再配置に成功しました。', true);
    }