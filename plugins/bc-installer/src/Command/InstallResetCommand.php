<?php

namespace BcInstaller\Command;

use BaserCore\Utility\BcContainerTrait;
use BcInstaller\Service\InstallationsServiceInterface;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;

class InstallResetCommand extends Command
{
    /**
     * reset
     *
     * cake bc_manager reset
     */
    public function reset()
    {
        if (Configure::read('debug') != -1) {
            $this->err(__d('baser', 'baserCMSの初期化を行うには、debug を -1 に設定する必要があります。'));
            return false;
        }
        if (!$this->_reset()) {
            $this->err(__d('baser', 'baserCMSのリセットに失敗しました。ログファイルを確認してください。'));
        }
        $this->out(__d('baser', 'baserCMSのリセットが完了しました。'));
    }

    /**
     * reset
     */
    protected function _reset()
    {
        $dbConfig = getDbConfig();
        return $this->BcManager->reset($dbConfig);
    }

}
