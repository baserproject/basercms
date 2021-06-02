<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use BaserCore\Service\PluginsServiceInterface;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PluginsController
 *
 * https://localhost/baser/api/baser-core/plugins/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 */
class PluginsController extends BcApiController
{
    /**
     * プラグイン情報一覧取得
     * @param PluginsServiceInterface $plugins
     * @checked
     * @unitTest
     * @noTodo
     */
    public function index(PluginsServiceInterface $plugins)
    {
        $this->set([
            'plugins' => $plugins->getIndex($this->request->getQuery('sortmode') ?? '0')
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugins']);
    }
}
