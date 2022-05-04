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

namespace BaserCore\View\Helper;

use Cake\View\View;
use Cake\View\Helper;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;

/**
 * BcPageHelper
 * @property BcAdminHelper $BcAdmin
 */
class BcPageHelper extends Helper
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;
    /**
     * ページモデル
     *
     * @var Page
     */
    public $Page = null;

    /**
     * data
     * @var array
     */
    public $data = [];

    /**
     * ヘルパー
     *
     * @var array
     */
    public $helpers = ['BcBaser', 'BcContents', 'BcAdmin'];

    /**
     * construct
     *
     * @param View $View
     */
    public function __construct(View $View, $settings = [])
    {
        parent::__construct($View, $settings);
        // TODO ucmitz 未移行のためコメントアウト
        /* >>>
        if (ClassRegistry::isKeySet('Page')) {
            $this->Page = ClassRegistry::getObject('Page');
        } else {
            $this->Page = ClassRegistry::init('Page', 'Model');
        }
        <<< */
    }

    /**
     * initialize
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->ContentsService = $this->getService(ContentsServiceInterface::class);
    }

    /**
     * ページ機能用URLを取得する
     *
     * @param array $page 固定ページデータ
     * @return string URL
     */
    public function getUrl($page)
    {
        if (isset($page['Content'])) {
            $page = $page['Content'];
        }
        if (!isset($page['url'])) {
            return '';
        }
        return $page['url'];
    }

    /**
     * ページリストを取得する
     * 戻り値は、固定ページ、または、コンテンツフォルダが対象
     *
     * @param int $pageCategoryId カテゴリID
     * @param int $recursive 関連データの階層
     * @return array
     */
    public function getPageList($id, $level = null, $options = [])
    {
        $options['type'] = 'Page';
        return $this->BcContents->getTree($id, $level, $options);
    }

    /**
     * 公開状態を取得する
     *
     * @param array データリスト
     * @return boolean 公開状態
     */
    public function allowPublish($data)
    {

        if (isset($data['Page'])) {
            $data = $data['Page'];
        }

        $allowPublish = (int)$data['status'];

        // 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
        if (($data['publish_begin'] != 0 && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
            ($data['publish_end'] != 0 && $data['publish_end'] <= date('Y-m-d H:i:s'))) {
            $allowPublish = false;
        }

        return $allowPublish;
    }

    /**
     * ページカテゴリ間の次の記事へのリンクを取得する
     *
     * MEMO: BcRequest.(agent).aliasは廃止
     *
     * @param string $title
     * @param array $options オプション（初期値 : array()）
     *    - `class` : CSSのクラス名（初期値 : 'next-link'）
     *    - `arrow` : 表示文字列（初期値 : ' ≫'）
     *    - `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
     *        ※ overCategory が true の場合は、BcPageHelper::contentsNaviAvailable() が false だとしても強制的に出力する
     *    - `escape` : エスケープするかどうか
     * @return mixed コンテンツナビが無効かつオプションoverCategoryがtrueでない場合はfalseを返す
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNextLink($title = '', $options = [])
    {
        $request = $this->getView()->getRequest();
        if (empty($request->getParam('Content.id')) || empty($request->getParam('Content.parent_id'))) {
            return false;
        }
        $options = array_merge([
            'class' => 'next-link',
            'arrow' => ' ≫',
            'overCategory' => false,
            'escape' => true
        ], $options);

        $arrow = $options['arrow'];
        $overCategory = $options['overCategory'];
        unset($options['arrow']);
        unset($options['overCategory']);

        $neighbors = $this->getPageNeighbors($request->getParam('Content'), $overCategory);

        if (empty($neighbors['next'])) {
            return false;
        } else {
            if (!$title) {
                $title = $neighbors['next']['title'] . $arrow;
            }
            $url = $neighbors['next']['url'];
            return $this->BcBaser->getLink($title, $url, $options);
        }
    }

    /**
     * ページカテゴリ間の次の記事へのリンクを出力する
     *
     * @param string $title
     * @param array $options オプション（初期値 : array()）
     *    - `class` : CSSのクラス名（初期値 : 'next-link'）
     *    - `arrow` : 表示文字列（初期値 : ' ≫'）
     *    - `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
     *        ※ overCategory が true の場合は、BcPageHelper::contentsNaviAvailable() が false だとしても強制的に出力する
     * @return @return void コンテンツナビが無効かつオプションoverCategoryがtrueでない場合はfalseを出力する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function nextLink($title = '', $options = [])
    {
        echo $this->getNextLink($title, $options);
    }

    /**
     * ページカテゴリ間の前の記事へのリンクを取得する
     *
     * @param string $title
     * @param array $options オプション（初期値 : array()）
     *    - `class` : CSSのクラス名（初期値 : 'prev-link'）
     *    - `arrow` : 表示文字列（初期値 : ' ≫'）
     *    - `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
     *    - `escape` : エスケープするかどうか
     * @return string|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPrevLink($title = '', $options = [])
    {
        $request = $this->getView()->getRequest();
        if (empty($request->getParam('Content.id')) || empty($request->getParam('Content.parent_id'))) {
            return false;
        }
        $options = array_merge([
            'class' => 'prev-link',
            'arrow' => '≪ ',
            'overCategory' => false,
            'escape' => true
        ], $options);

        $arrow = $options['arrow'];
        $overCategory = $options['overCategory'];
        unset($options['arrow']);
        unset($options['overCategory']);
        $content = $request->getParam('Content');
        $neighbors = $this->getPageNeighbors($content, $overCategory);

        if (empty($neighbors['prev'])) {
            return false;
        } else {
            if (!$title) {
                $title = $arrow . $neighbors['prev']['title'];
            }
            $url = $neighbors['prev']['url'];
            return $this->BcBaser->getLink($title, $url, $options);
        }
    }

    /**
     * ページカテゴリ間の前の記事へのリンクを出力する
     *
     * @param string $title
     * @param array $options オプション（初期値 : array()）
     *    - `class` : CSSのクラス名（初期値 : 'prev-link'）
     *    - `arrow` : 表示文字列（初期値 : ' ≫'）
     *    - `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
     *        ※ overCategory が true の場合は、BcPageHelper::contentsNaviAvailable() が false だとしても強制的に出力する
     * @return void コンテンツナビが無効かつオプションoverCategoryがtrueでない場合はfalseを返す
     * @checked
     * @noTodo
     * @unitTest
     */
    public function prevLink($title = '', $options = [])
    {
        echo $this->getPrevLink($title, $options);
    }

    /**
     * 指定した固定ページデータの次、または、前のデータを取得する
     *
     * @param Content $content
     * @param bool $overCategory カテゴリをまたがるかどうか
     * @return array 次、または、前の固定ページデータ
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function getPageNeighbors($content, $overCategory = false)
    {
        $conditions = array_merge($this->ContentsService->getConditionAllowPublish(), [
            'Contents.type <>' => 'ContentFolder',
            'Contents.site_id' => $content->site_id
        ]);
        if ($overCategory !== true) {
            $conditions['Contents.parent_id'] = $content->parent_id;
        }
        $options = [
            'field' => 'lft',
            'value' => $content->lft,
            'conditions' => $conditions,
            'order' => ['Contents.lft'],
        ];
        return $this->ContentsService->getNeighbors($options);
    }
}
