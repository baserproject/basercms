<?php
namespace BcInstaller\Command;

use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcInstaller\Service\InstallationsServiceInterface;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;

class ReinstallCommand extends Command
{

    /**
     * 再インストール
     *
     * コマンドはインストールと同じ
     */
    public function reinstall()
    {
        if (Configure::read('debug') != -1) {
            $this->err(__d('baser', 'baserCMSの初期化を行うには、debug を -1 に設定する必要があります。'));
            return false;
        }
        $result = true;
        if (!$this->_reset()) {
            $result = false;
        }
        BcUtil::clearAllCache();
        if (!$this->_install()) {
            $result = false;
        }
        if (!$result) {
            $this->err(__d('baser', 'baserCMSの再インストールに失敗しました。ログファイルを確認してください。'));
        }
    }

}
