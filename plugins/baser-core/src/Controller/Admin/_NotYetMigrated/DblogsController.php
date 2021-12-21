<?php
// TODO : コード確認要
use BaserCore\Utility\BcSiteConfig;

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
 * Class DblogsController
 *
 * @package Baser.Controller
 */
class DblogsController extends AppController
{

    /**
     * コンポーネント
     *
     * @var array
     */
    public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

    /**
     * 一覧を取得
     */
    public function admin_ajax_index()
    {
        $this->autoLayout = false;
        $default = ['named' => ['num' => BcSiteConfig::get('admin_list_num')]];
        $this->setViewConditions('Dblog', ['default' => $default, 'action' => 'admin_index']);
        $this->paginate = [
            'order' => ['Dblog.created ' => 'DESC', 'Dblog.id' => 'DESC'],
            'limit' => $this->passedArgs['num']
        ];
        $this->set('dblogs', $this->paginate('Dblog'));
    }

    /**
     * [ADMIN] 最近の動きを削除
     *
     * @return void
     */
    public function admin_del()
    {
        $this->_checkSubmitToken();
        if ($this->Dblog->deleteAll('1 = 1')) {
            $this->BcMessage->setInfo(__d('baser', '最近の動きのログを削除しました。'));
        } else {
            $this->BcMessage->setError(__d('baser', '最近の動きのログ削除に失敗しました。'));
        }
        $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
    }

}
