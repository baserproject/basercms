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

use BaserCore\Model\Entity\Page;
use BaserCore\Utility\BcUtil;
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
 * @property BcContentsHelper $BcContents
 */
class BcPageHelper extends Helper
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;

    /**
     * ヘルパー
     *
     * @var array
     */
    public array $helpers = ['BaserCore.BcContents'];

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
        if(!BcUtil::isInstalled()) return;
        $this->ContentsService = $this->getService(ContentsServiceInterface::class);
    }

    /**
     * ページ機能用URLを取得する
     *
     * @param Page $page 固定ページデータ
     * @return string URL
     * @checked
     * @noTodo
     */
    public function getUrl($page)
    {
        if($page->content?->url) {
            return $page->content?->url;
        }
        return '';
    }

    /**
     * ページリストを取得する
     * 戻り値は、固定ページ、または、コンテンツフォルダが対象
     *
     * @param int $pageCategoryId カテゴリID
     * @param int $recursive 関連データの階層
     * @return array
     * @checked
     * @noTodo
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

}
