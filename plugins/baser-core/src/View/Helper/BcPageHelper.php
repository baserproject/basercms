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
use Cake\Datasource\ResultSetDecorator;
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
#[\AllowDynamicProperties]
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
    public array $helpers = [
        'BaserCore.BcContents'
    ];

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
     * @unitTest
     */
    public function getUrl($page)
    {
        if($page->content?->url) {
            return $page->content?->url;
        }
        return '';
    }

    /**
     * 固定ページリストを取得する
     * 戻り値は、固定ページ、または、コンテンツフォルダが対象
     *
     * @param int $id コンテンツID
     * @param int $level 階層を指定する場合に階層数を指定
     * @param array $options オプション
     *  - `type` : コンテンツタイプ
     *  - `order` : ソート順（初期値：['Contents.site_id', 'Contents.lft']）
     *  - `siteId` : サイトID
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPageList(int $id, ?int $level = null, array $options = []): ResultSetDecorator
    {
        $options['type'] = 'Page';
        return $this->BcContents->getTree($id, $level, $options);
    }

}
