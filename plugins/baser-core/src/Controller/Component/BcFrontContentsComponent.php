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

namespace BaserCore\Controller\Component;

use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\Front\BcFrontContentsService;
use BaserCore\Service\Front\BcFrontContentsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Event\EventInterface;
use Cake\Http\ServerRequest;
use Cake\Controller\Component;
use Cake\Controller\Controller;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;

/**
 * Class BcContentsComponent
 *
 * 階層コンテンツと連携したフォーム画面を作成する為のコンポーネント
 *
 * 《役割》
 * - フロントエンドでコンテンツデータを設定
 *        Controller / View にて、$this->request->getAttribute('currentContent') で参照できる
 *
 * @package BaserCore\Controller\Component
 * @property Controller $_Controller
 * @property ServerRequest $Request
 * @property ContentsServiceInterface $ContentsService
 */
class BcFrontContentsComponent extends Component
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Initialize
     *
     * 設定の初期化を行う
     * - `isContentsPage`: 対象コンテンツのページかどうか
     *
     * @param array $config
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        $config = array_merge([
            'isContentsPage' => true
        ], $config);
        $this->setConfig($config);
    }

    /**
     * beforeFilter
     * @param array $config
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function beforeFilter(EventInterface $event): void
    {
        $this->setupFront();
    }

    /**
     * フロントエンドのセットアップ
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setupFront()
    {
        $controller = $this->getController();
        $request = $controller->getRequest();
        if(!$request->getAttribute('currentContent')) return;

        $currentContent = $request->getAttribute('currentContent');

        // 表示設定
        if ($currentContent) {
            $this->setLayout($currentContent);
            /** @var BcFrontContentsService $bcFrontContentsService */
            $bcFrontContentsService = $this->getService(BcFrontContentsServiceInterface::class);
            $controller->set($bcFrontContentsService->getViewVarsForFront($currentContent, $this->getConfig('isContentsPage')));
        }
    }

    /**
     * レイアウトをセットする
     * @param $currentContent
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setLayout($currentContent)
    {
        if ($currentContent->layout_template) {
            $layout = $currentContent->layout_template;
        } else {
            $contentsService = $this->getService(ContentsServiceInterface::class);
            $layout = $contentsService->getParentLayoutTemplate($currentContent->id);
        }
        $this->getController()->viewBuilder()->setLayout($layout);
    }

}
