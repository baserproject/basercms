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
use BaserCore\Utility\BcContainerTrait;
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
     * プレビューモード
     *
     * @var string default Or alias
     */
    protected $preview = null;

    /**
     * Initialize
     *
     * @param array $config
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->ContentsService = $this->getService(ContentsServiceInterface::class);
        $this->setupFront();
    }

    /**
     * フロントエンドのセットアップ
     *
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setupFront()
    {
        $controller = $this->getController();
        $request = $controller->getRequest();
        // プレビュー時のデータセット
        if (!empty($request->getQuery('preview'))) {
            $this->preview = $request->getQuery('preview');
            if (!empty($request->getData())) {
                // 何か他の方法を考える、本当にその処理が必要かも確認
                $controller->setRequest($request->withParam('Content', $request->getData()));
                $controller->Security->validatePost = false;
                $controller->Security->csrfCheck = false;
            }
        }

        // 表示設定
        if (!empty($request->getAttributes())) {
            // レイアウトテンプレート設定
            $viewBuilder = $controller->viewBuilder();
            $viewBuilder->setLayout($request->getParam('Content.layout_template'));
            if (!$viewBuilder->getLayout()) {
                $controller->viewBuilder()->setLayout($this->ContentsService->getParentLayoutTemplate($request->getParam('Content.id')));
            }
            // パンくず
            $controller->set('crumbs', $this->getCrumbs($request->getParam('Content.id')));
            // 説明文
            $controller->set('description', $request->getParam('Content.description'));
            // タイトル
            $controller->setTitle($request->getParam('Content.title'));
        }
    }

    /**
     * パンくず用のデータを取得する
     *
     * @param $id
     * @return array
     */
    protected function getCrumbs($id)
    {
        if(!$id) return [];
        // ===========================================================================================
        // 2016/09/22 ryuring
        // PHP 7.0.8 環境にて、コンテンツ一覧追加時、検索インデックス作成のため、BcContentsComponent が
        // 呼び出されるが、その際、モデルのマジックメソッドの戻り値を返すタイミングで処理がストップしてしまう。
        // そのため、ビヘイビアのメソッドを直接実行して対処した。
        // CakePHPも、PHP自体のエラーも発生せず、ただ止まる。PHP7のバグ？PHP側のメモリーを256Mにしても変わらず。
        // ===========================================================================================
        $contents = $this->ContentsService->getPath($id)->all();
        $crumbs = [];
        foreach($contents as $content) {
            if (!$content->site_root) {
                $crumb = [
                    'name' => $content->title,
                    'url' => $content->url
                ];
                $crumbs[] = $crumb;
            }
        }
        return $crumbs;
    }

}
