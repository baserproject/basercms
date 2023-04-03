<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use Authentication\Authenticator\ResultInterface;
use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Controller\AppController;
use BaserCore\Utility\BcApiUtil;
use Cake\Event\EventInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Routing\Router;

/**
 * Class BcApiController
 * @property AuthenticationComponent $Authentication
 */
class BcApiController extends AppController
{

    /**
     * トークンを取得する
     * @param ResultInterface $result
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAccessToken(ResultInterface $result): array
    {
        if ($result->isValid()) {
            $request = Router::getRequest();
            return BcApiUtil::createAccessToken($result->getData()->id, $request->getParam('prefix')?? 'Admin');
        } else {
            return [];
        }
    }

    /**
     * Before render
     * 日本語を Unicode エスケープしないようにする
     * @param EventInterface $event
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        $this->viewBuilder()->setOption('jsonOptions', JSON_UNESCAPED_UNICODE);
    }

}
