<?php
// TODO : コード確認要
use BaserCore\Utility\BcUtil;

return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class UpdatersController
 *
 * アップデーターコントローラー
 *
 * baserCMSのコアや、プラグインのアップデートを行います
 *
 * @package    Baser.Controller
 */
class UpdatersController extends AppController
{
    /**
     * [ADMIN] アップデートスクリプトを実行する
     *
     * @return void
     */
    public function admin_exec_script()
    {
        if ($this->request->data) {
            $this->setUpdateLog(__d('baser', 'アップデートスクリプトの実行します。'));
            if ($this->_execScript($this->request->getData('Updater.plugin'), $this->request->getData('Updater.version'))) {
                BcUtil::clearAllCache();
                $this->BcManager->deployAdminAssets();
                $this->setUpdateLog(__d('baser', 'アップデートスクリプトの実行が完了しました。'));
                $this->_writeUpdateLog();
                $this->BcMessage->setInfo(__d('baser', 'アップデートスクリプトの実行が完了しました。<a href="#UpdateLog">アップデートログ</a>を確認してください。'));
                $this->redirect(['action' => 'exec_script']);
            } else {
                $this->BcMessage->setError(__d('baser', 'アップデートスクリプトが見つかりません。'));
            }
        }

        $updateLogFile = TMP . 'logs' . DS . 'update.log';
        $updateLog = '';
        if (file_exists($updateLogFile)) {
            $File = new File(TMP . 'logs' . DS . 'update.log');
            $updateLog = $File->read();
        }

        $this->setTitle(__d('baser', 'アップデートスクリプト実行'));
        $plugins = $this->Plugin->find('list', ['fields' => ['name', 'title']]);
        $this->set('plugins', $plugins);
        $this->set('log', $updateLog);
    }

}
