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

namespace BaserCore\Service;

use BaserCore\Model\Entity\Content;
use BaserCore\Model\Entity\Site;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class SitesService
 * @property SitesTable $Sites
 */
class SitesService implements SitesServiceInterface
{
    /**
     * Sites Table
     * @var SitesTable
     */
    public $Sites;

    /**
     * SitesService constructor.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function __construct()
    {
        $this->Sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
    }

    /**
     * サイトの新規データ用の初期値を含んだエンティティを取得する
     * @return Site
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface
    {
        return $this->Sites->newEntity([
            'status' => false,
        ], [
            'validate' => false,
        ]);
    }

    /**
     * サイトを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->Sites->get($id);
    }

    /**
     * サイト管理の一覧用のデータを取得
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query
    {
        $query = $this->Sites->find('all');
        if (!empty($queryParams['limit'])) {
            $query->limit($queryParams['limit']);
        }
        if (!empty($queryParams['name'])) {
            $query->where(['name LIKE' => '%' . $queryParams['name'] . '%']);
        }
        if (isset($queryParams['status'])) {
            $query->where(['status' => $queryParams['status']]);
        }
        return $query;
    }

    /**
     * サイト登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData): ?EntityInterface
    {
        $site = $this->Sites->newEmptyEntity();
        $site = $this->Sites->patchEntity($site, $postData);
        return $this->Sites->saveOrFail($site);
    }

    /**
     * サイト情報を更新する
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData): ?EntityInterface
    {
        $site = $this->Sites->patchEntity($target, $postData);
        return $this->Sites->saveOrFail($site);
    }

    /**
     * 公開状態にする
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish($id): bool
    {
        $site = $this->get($id);
        $site->status = true;
        return ($this->Sites->save($site)) ? true: false;
    }

    /**
     * 非公開状態にする
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish($id): bool
    {
        $site = $this->get($id);
        $site->status = false;
        return ($this->Sites->save($site)) ? true: false;
    }

    /**
     * サイト情報を削除する
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
    {
        $site = $this->get($id);
        if(!$site->main_site_id) {
            throw new Exception(__d('baser', 'メインサイトは削除できません。'));
        }
        return $this->Sites->delete($site);
    }

    /**
     * 選択可能なデバイスの一覧を取得する
     *
     * 現在のサイトとすでに利用されいているデバイスは除外する
     *
     * @param int $mainSiteId メインサイトID
     * @param int $currentSiteId 現在のサイトID
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSelectableDevices($mainSiteId, $currentSiteId = null): array
    {
        return $this->Sites->getSelectableDevices($mainSiteId, $currentSiteId);
    }

    /**
     * 選択可能が言語の一覧を取得する
     *
     * @param int $mainSiteId メインサイトID
     * @param int $currentSiteId 現在のサイトID
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSelectableLangs($mainSiteId, $currentSiteId = null): array
    {
        return $this->Sites->getSelectableLangs($mainSiteId, $currentSiteId);
    }

    /**
     * URLよりサイトを取得する
     *
     * @param string $url
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findByUrl($url): EntityInterface
    {
        return $this->Sites->findByUrl($url);
    }

    /**
     * IDよりサイトを取得する
     *
     * @param string $id
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findById($id): Query
    {
        return $this->Sites->findById($id);
    }


    /**
     * 言語リストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLangList(): array
    {
        $languages = Configure::read('BcLang');
        $langs = [];
        foreach($languages as $key => $lang) {
            $langs[$key] = $lang['name'];
        }
        return $langs;
    }

    /**
     * デバイスリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDeviceList(): array
    {
        $agents = Configure::read('BcAgent');
        $devices = [];
        foreach($agents as $key => $agent) {
            $devices[$key] = $agent['name'];
        }
        return $devices;
    }

    /**
     * サイトのリストを取得
     * @param array $options
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList($options = []): array
    {
        return $this->Sites->getList(null, $options);
    }

    /**
     * テーマのリストを取得する
     * @param Site $site
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getThemeList(): array
    {
        return BcUtil::getThemeList();
    }

    /**
     * サイトのルートコンテンツを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getRootContent($id)
    {
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        return $contentsTable->find()->select()->where(['Contents.site_root' => true, 'Contents.site_id' => $id])->first();
    }

    /**
     * コンテンツに関連したコンテンツをサイト情報と一緒に全て取得する
     *
     * @param $contentId
     * @return array|null $list
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getRelatedContents($contentId)
    {
        /* @var Content $content */
        $content = $this->Sites->Contents->get($contentId, ['contain' => ['Sites']]);
        $isMainSite = $this->Sites->isMain($content->site->id);
        $fields = ['id', 'name', 'alias', 'display_name', 'main_site_id'];
        $conditions = ['Sites.status' => true];
        if (is_null($content->site->main_site_id)) {
            $mainSiteContentId = $content->id;
            $conditions['or'] = [
                ['Sites.id' => $content->site->id],
                ['Sites.main_site_id' => $this->Sites->getRootMain()->id]
            ];
        } else {
            $conditions['or'] = [
                ['Sites.main_site_id' => $content->site->main_site_id],
                ['Sites.id' => $content->site->main_site_id]
            ];
            if ($isMainSite) {
                $conditions['or'][] = ['Site.main_site_id' => $content->site->id];
            }
            $mainSiteContentId = $content->main_site_content_id ?? $content->id;
        }
        $sites = $this->Sites->find()->select($fields)->where($conditions)->order('main_site_id')->toArray();
        $conditions = [
            'or' => [
                ['Contents.id' => $mainSiteContentId],
                ['Contents.main_site_content_id' => $mainSiteContentId]
            ]
        ];
        $list = [];
        $relatedContents = $this->Sites->Contents->find()->where($conditions)->toArray();
        foreach($sites as $key => $site) {
            foreach($relatedContents as $relatedContent) {
                $list[$key]['Site'] = $site;
                if ($relatedContent->site_id == $site->id) {
                    $list[$key]['Content'] = $relatedContent;
                    break;
                }
            }
        }
        return $list;
    }

}
