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

use BaserCore\Model\Entity\Content;
use BaserCore\Model\Entity\Site;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Service\ContentsService;
use Cake\Datasource\EntityInterface;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Service\PermissionsService;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\Doc;

/**
 * コンテンツヘルパ
 *
 * @var BcContentsHelper $this
 * @property ContentsTable $_Contents
 * @property PermissionsService $PermissionsService
 * @property ContentsServiceInterface|ContentsService $ContentsService
 * @property Helper\UrlHelper $Url
 */
#[\AllowDynamicProperties]
class BcContentsHelper extends Helper
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;


    /**
     * Helper
     *
     * @var array
     */
    public array $helpers = [
        'BaserCore.BcBaser',
        'Url'
    ];

    /**
     * initialize
     * @param array $config
     * @return void
     * @access public
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        if (!BcUtil::isInstalled()) return;
        $this->_Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $this->PermissionsService = $this->getService(PermissionsServiceInterface::class);
        $this->ContentsService = $this->getService(ContentsServiceInterface::class);
        if (BcUtil::isAdminSystem()) {
            $this->setup();
        }
        $this->request = $this->getView()->getRequest();
    }

    /**
     * セットアップ
     * @checked
     * @noTodo
     */
    public function setup()
    {
        $items = $this->_View->get('contentsItems');

        if (!$items) {
            return;
        }

        $existsTitles = $this->_getExistsTitles();
        $user = BcUtil::loginUser();

        foreach($items as $type => $item) {

            // title
            if (empty($item['title'])) {
                $item['title'] = $type;
            }

            // omitViewAction
            if (empty($item['omitViewAction'])) {
                $item['omitViewAction'] = false;
            }

            // exists
            if (empty($item['multiple'])) {
                $item['multiple'] = false;
                if (array_key_exists($item['plugin'] . '.' . $type, $existsTitles)) {
                    $item['exists'] = true;
                    $item['existsTitle'] = $existsTitles[$item['plugin'] . '.' . $type];
                } else {
                    $item['exists'] = false;
                    $item['existsTitle'] = '';
                }
            }

            // icon
            if (empty($item['icon'])) {
                $item['icon'] = 'bca-icon--file';
            }

            // routes
            foreach(['manage', 'add', 'edit', 'delete', 'copy', 'dblclick'] as $method) {
                if (empty($item['routes'][$method]) && !in_array($method, ['add', 'copy', 'manage', 'dblclick'])) {
                    $item['routes'][$method] = ['prefix' => 'Admin', 'controller' => 'Contents', 'action' => $method];
                }
                if (!empty($item['routes'][$method])) {
                    $route = $item['routes'][$method];
                    $item['url'][$method] = Router::url($route);
                }
            }
            // disabled
            if (!empty($item['url']['add'])) {
                $item['addDisabled'] = !($this->PermissionsService->check($item['url']['add'], Hash::extract($user->user_groups, '{n}.id')));
            } else {
                $item['addDisabled'] = true;
            }
            $items[$type] = $item;
        }
        $this->setConfig('items', $items);
    }

    /**
     * アクションが利用可能かどうか確認する
     *
     * @param string $type コンテンツタイプ
     * @param string $action アクション
     * @param int $entityId コンテンツを特定するID
     * @checked
     * @unitTest
     * @noTodo
     */
    public function isActionAvailable($type, $action, $entityId): bool
    {
        $user = BcUtil::loginUser();
        if (!isset($this->getConfig('items')[$type]['url'][$action])) {
            return false;
        }
        $url = $this->getConfig('items')[$type]['url'][$action] . '/' . $entityId;
        if (isset($user->user_groups)) {
            $userGroups = $user->user_groups;
            $userGroupIds = [];
            foreach($userGroups as $group) {
                $userGroupIds[] = $group->id;
            }
            if ($this->PermissionsService->check($url, $userGroupIds)) {
                return true;
            }
        }
        return false;
    }

    /**
     * シングルコンテンツで既に登録済のタイトルを取得する
     * @return array
     * @checked
     * @noTodo
     */
    protected function _getExistsTitles()
    {
        $contentItems = BcUtil::getContentsItem();
        // シングルコンテンツの存在チェック
        $conditions = [];
        foreach($contentItems as $name => $items) {
            foreach($items as $type => $item) {
                if (empty($item['multiple'])) {
                    $conditions = [
                        'OR' => [
                            'plugin' => $name,
                            'type' => $type,
                            'alias_id IS' => null,
                        ]
                    ];
                }
            }
        }
        $contents = $this->_Contents->find('all', ...['withDeleted'])->select(['plugin', 'type', 'title'])->where([$conditions]);
        $existContents = [];
        foreach($contents as $content) {
            $existContents[$content->plugin . '.' . $content->type] = $content->title;
        }
        return $existContents;
    }

    /**
     * コンテンツ設定を Json 形式で取得する
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getJsonItems()
    {
        return json_encode($this->getConfig('items'));
    }

    /**
     * プレフィックスなしのURLを取得する
     *
     * @param string $url
     * @param int $siteId
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためテスト不要
     */
    public function getPureUrl($url, $siteId)
    {
        return $this->_Contents->pureUrl($url, $siteId);
    }

    /**
     * 現在のURLを元に指定したサブサイトのURLを取得する
     *
     * @param string $siteName
     * @return mixed|string
     */
    public function getCurrentRelatedSiteUrl($siteName)
    {
        if (empty($this->request->getAttribute('currentSite'))) {
            return '';
        }
        $url = $this->getPureUrl('/' . $this->request->url, $this->request->getAttribute('currentSite')->id);
        $Site = ClassRegistry::init('Site');
        $site = $Site->find('first', ['conditions' => ['Site.name' => $siteName], 'recursive' => -1]);
        if (!$site) {
            return '';
        }
        $prefix = $Site->getPrefix($site);
        if ($prefix) {
            $url = '/' . $prefix . $url;
        }
        return $url;
    }

    /**
     * コンテンツリストをツリー構造で取得する
     *
     * @param int $id カテゴリID
     * @param int $level 関連データの階層
     * @param array $options
     *  - `type` : コンテンツタイプ
     *  - `order` : ソート順（初期値：['Contents.site_id', 'Contents.lft']）
     *  - `siteId` : サイトID
     * @checked
     * @noTodo
     */
    public function getTree($id = 1, $level = null, $options = [])
    {
        $options = array_merge([
            'type' => '',
            'order' => ['Contents.site_id', 'Contents.lft'],
            'siteId' => null
        ], $options);
        $conditions = array_merge($this->_Contents->getConditionAllowPublish(), ['Contents.id' => $id]);
        $content = $this->_Contents->find()->where($conditions)->first();
        if (!$content) {
            return [];
        }
        $conditions = array_merge($this->_Contents->getConditionAllowPublish(), [
            'Contents.site_root' => false,
            'rght <' => $content->rght,
            'lft >' => $content->lft
        ]);
        if($options['siteId']) {
            $conditions['Contents.site_id'] = $options['siteId'];
        }
        if ($level) {
            $level = $level + $content->level + 1;
            $conditions['Contents.level <'] = $level;
        }
        if (!empty($options['type'])) {
            $conditions['Contents.type IN'] = ['ContentFolder', $options['type']];
        }
        if (!empty($options['conditions'])) {
            $conditions = array_merge($conditions, $options['conditions']);
        }
        // CAUTION CakePHP2系では、fields を指定すると正常なデータが取得できない
        return $this->_Contents->find('threaded')
            ->orderBy($options['order'])
            ->where($conditions)->all();
    }

    /**
     * 親コンテンツを取得する
     *
     * - 引数なしで現在のコンテンツの親情報を取得
     * - $id を指定して取得する事ができる
     * - $direct を false に設定する事で、最上位までの親情報を取得
     *
     * @param null $id
     * @param bool $direct 直接の親かどうか
     * @return EntityInterface|array|false
     * @checked
     * @noTodo
     */
    public function getParent($id = null, $direct = true)
    {
        if (!$id && !empty($this->request->getAttribute('currentContent')->id)) {
            $id = $this->request->getAttribute('currentContent')->id;
        }
        if (!$id) {
            return false;
        }
        $siteId = $this->_Contents->find()->where(['Contents.id' => $id])->first()->site_id;
        if ($direct) {
            $parents = $this->_Contents->find('path', for: $id)->all()->toArray();
            if (!isset($parents[count($parents) - 2])) return false;
            $parent = $parents[count($parents) - 2];
            if ($parent->site_id === $siteId) {
                return $parent;
            } else {
                return false;
            }
        } else {
            $parents = $this->_Contents->find('path', for: $id)->all()->toArray();
            if ($parents) {
                $result = [];
                foreach($parents as $parent) {
                    if ($parent->id !== $id && $parent->site_id === $siteId) {
                        $result[] = $parent;
                    }
                }
                if ($result) {
                    return $result;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * サイト連携データかどうか確認する
     *
     * @param EntityInterface $content コンテンツデータ
     * @return bool
     * @unitTest
     */
    public function isSiteRelated(EntityInterface $content)
    {
        if(!$content || !$content->site) return false;
        if($content->site->relate_main_site && $content->main_site_content_id) {
            if($content->alias_id) return true;
            if($content->type === 'ContentFolder') return true;
        }
        return false;
    }

    /**
     * 関連サイトのコンテンツを取得
     *
     * @param int $id コンテンツID
     * @return array | false
     */
    public function getRelatedSiteContents($id = null, $options = [])
    {
        $options = array_merge([
            'excludeIds' => []
        ], $options);
        $this->_Contents->unbindModel(['belongsTo' => ['User']]);
        if (!$id) {
            if (!empty($this->request->getAttribute('currentContent'))) {
                $content = $this->request->getAttribute('currentContent');
                if ($content['main_site_content_id']) {
                    $id = $content['main_site_content_id'];
                } else {
                    $id = $content['id'];
                }
            } else {
                return false;
            }
        }
        return $this->_Contents->getRelatedSiteContents($id, $options);
    }

    /**
     * 関連サイトのリンク情報を取得する
     *
     * @param int $id
     * @return array
     */
    public function getRelatedSiteLinks($id = null, $options = [])
    {
        $options = array_merge([
            'excludeIds' => []
        ], $options);
        $contents = $this->getRelatedSiteContents($id, $options);
        $urls = [];
        if ($contents) {
            foreach($contents as $content) {
                $urls[] = [
                    'prefix' => $content['Site']['name'],
                    'name' => $content['Site']['display_name'],
                    'url' => $content['Content']['url']
                ];
            }
        }
        return $urls;
    }

    /**
     * フォルダリストを取得する
     *
     * @param int $siteId
     * @param array $options
     * @return array|bool
     * @checked
     * @noTodo
     */
    public function getContentFolderList($siteId = null, $options = [])
    {
        $contentsService = $this->getService(ContentsServiceInterface::class);
        return $contentsService->getContentFolderList($siteId, $options);
    }


    /**
     * コンテンツが編集可能かどうか確認
     *
     * @param Content $content コンテンツ、サイト情報を格納した配列
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEditable($content)
    {
        if (isset($content) && isset($content->site)) {
            $site = $content->site;
        } else {
            return false;
        }
        // サイトルートの場合は編集不可
        if ($content->site_root) {
            return false;
        }
        // サイトルート以外で、管理ユーザーの場合は、強制的に編集可
        if (BcUtil::isAdminUser()) {
            return true;
        }
        // エイリアスを利用してメインサイトと自動連携する場合、親サイトに関連しているコンテンツ（＝子サイト）
        if ($site->relate_main_site && $content->main_site_content_id) {
            // エイリアス、または、フォルダの場合は編集不可
            if ($content->alias_id || $content->type == 'ContentFolder') {
                return false;
            }
        }
        return true;
    }

    /**
     * エンティティIDからコンテンツの情報を取得
     *
     * @param int $id エンティティID
     * @param string $contentType コンテンツタイプ
     * ('Page','MailContent','BlogContent','ContentFolder')
     * @param string $field 取得したい値
     *  'name','url','title'など　初期値：Null
     *  省略した場合配列を取得
     * @return array|string|bool
     * @checked
     * @noTodo
     */
    public function getContentByEntityId($id, $contentType, $field = null)
    {
        $conditions = array_merge($this->_Contents->getConditionAllowPublish(), ['Contents.type' => $contentType, 'Contents.entity_id' => $id]);
        return $this->_getContent($conditions, $field);
    }

    /**
     * urlからコンテンツの情報を取得
     *
     * @param string $url
     * @param string $contentType コンテンツタイプ
     * ('Page','MailContent','BlogContent','ContentFolder')
     * @param string $field 取得したい値
     *  'name','url','title'など　初期値：Null
     *  省略した場合配列を取得
     * @return array|string|bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getContentByUrl($url, $contentType, $field = null)
    {
        $conditions = array_merge($this->_Contents->getConditionAllowPublish(), ['type' => $contentType, 'url' => $url]);
        return $this->_getContent($conditions, $field);
    }

    /**
     * 条件を指定してコンテンツを取得する
     * フィールドを指定した場合はフィールドの値を取得する
     * @param array $conditions
     * @param string|null $field
     * @return array|EntityInterface|false|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    private function _getContent($conditions, $field = null)
    {
        $content = $this->_Contents->find()->where($conditions)->orderBy(['Contents.id'])->first();
        if (!empty($content)) {
            if ($field) {
                return $content->{$field};
            } else {
                return $content;
            }
        } else {
            return false;
        }
    }

    /**
     * IDがコンテンツ自身の親のIDかを判定する
     *
     * @param int $id コンテンツ自身のID
     * @param int $parentId 親として判定するID
     * @return bool
     * @checked
     * @noTodo
     */
    public function isParentId($id, $parentId)
    {
        $parentIds = $this->_Contents->find('treeList', valuePath: 'id')->where(['id' => $id])->all()->toArray();
        if (!$parentIds) {
            return false;
        }
        if ($parentIds && in_array($parentId, $parentIds)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * フォルダかどうか確認する
     * @return bool
     * @checked
     * @noTodo
     */
    public function isFolder()
    {
        if (BcUtil::isAdminSystem() || !$this->getView()->getRequest()->getAttribute('currentContent')->type) {
            return false;
        }
        return ($this->getView()->getRequest()->getAttribute('currentContent')->type === 'ContentFolder');
    }

    /**
     * サイトIDからサイトルートとなるコンテンツを取得する
     *
     * @param int $siteId
     * @return Content
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteRoot($siteId)
    {
        return $this->ContentsService->getSiteRoot($siteId);
    }

    /**
     * サイトIDからサイトルートとなるコンテンツIDを取得する
     *
     * @param int $siteId
     * @return string|bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteRootId($siteId)
    {
        $content = $this->getSiteRoot($siteId);
        if ($content) {
            return $content->id;
        } else {
            return false;
        }
    }

    /**
     * コンテンツ管理上のURLを元に正式なURLを取得する
     *
     * ドメインからのフルパスでない場合、デフォルトでは、
     * サブフォルダ設置時等の baseUrl（サブフォルダまでのパス）は含まない
     *
     * @param string $url コンテンツ管理上のURL
     * @param bool $full http からのフルのURLかどうか
     * @param bool $useSubDomain サブドメインを利用しているかどうか
     * @param bool $base $full が false の場合、ベースとなるURLを含めるかどうか
     * @return string URL
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためテスト不要
     */
    public function getUrl($url, $full = false, $useSubDomain = false, $base = false)
    {
        if(BcUtil::isInstalled()) {
            return $this->ContentsService->getUrl($url, $full, $useSubDomain, $base);
        } else {
            return $this->Url->build($url, ['fullBase' => $full]);
        }
    }

    /**
     * コンテンツIDよりフルURLを取得する
     *
     * @param int $id コンテンツID
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためテスト不要
     */
    public function getUrlById($id, $full = false)
    {
        return $this->ContentsService->getUrlById($id, $full);
    }

    /**
     * 対象コンテンツが属するフォルダまでのフルパスを取得する
     * フォルダ名称部分にはフォルダ編集画面へのリンクを付与する
     * コンテンツ編集画面で利用
     *
     * @param Content $content コンテンツデータ
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFolderLinkedUrl(EntityInterface $content)
    {
        if ($content->url) {
            $urlArray = explode('/', preg_replace('/(^\/|\/$)/', '', $content->url));
            unset($urlArray[count($urlArray) - 1]);
        } elseif ($content->parent_id) {
            $parent = $this->ContentsService->get($content->parent_id);
            $urlArray = explode('/', preg_replace('/(^\/|\/$)/', '', $parent->url));
        }
        if (count($urlArray) === 1 && !$urlArray[0]) {
            $urlArray = [];
        }

        if ($content->site->same_main_url) {
            $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
            $site = $sites->findById($content->site->main_site_id)->first();
            array_shift($urlArray);
            if ($site->alias) {
                $urlArray = explode('/', $site->alias) + $urlArray;
            }
        }
        if ($content->site->use_subdomain) {
            $host = $this->getUrl('/' . $content->site->alias . '/', true, $content->site->use_subdomain);
            array_shift($urlArray);
            $checkUrl = '/' . $content->site->alias . '/';
        } else {
            $host = $this->getUrl('/', true, $content->site->use_subdomain);
            $checkUrl = '/';
        }

        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        foreach($urlArray as $key => $value) {
            $checkUrl .= $value . '/';
            $target = $contentsTable->find()->select('entity_id')->where(['url' => $checkUrl])->first();
            /* @var Content $target */
            $entityId = $target->entity_id;
            $urlArray[$key] = $this->BcBaser->getLink(rawurldecode($value), [
                'admin' => true,
                'plugin' => 'BaserCore',
                'controller' => 'content_folders',
                'action' => 'edit',
                $entityId
            ], ['forceTitle' => true]);
        }
        $folderLinkedUrl = $host;
        if ($urlArray) {
            $folderLinkedUrl .= implode('/', $urlArray) . '/';
        }
        return $folderLinkedUrl;
    }

    /**
     * データが公開状態にあるか確認する
     *
     * @param array $data コンテンツデータ
     * @param bool $self コンテンツ自身の公開状態かどうか
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためテスト不要
     */
    public function isAllowPublish($data, $self = false)
    {
        return $this->ContentsService->isAllowPublish($data, $self);
    }

    /**
     * フォルダ内の次のコンテンツへのリンクを取得する
     *
     * MEMO: BcRequest.(agent).aliasは廃止
     *
     * @param string $title
     * @param array $options オプション（初期値 : array()）
     *    - `class` : CSSのクラス名（初期値 : 'next-link'）
     *    - `arrow` : 表示文字列（初期値 : ' ≫'）
     *    - `overFolder` : フォルダ外も含めるかどうか（初期値 : false）
     *        ※ overFolder が true の場合は、BcPageHelper::contentsNaviAvailable() が false だとしても強制的に出力する
     *    - `escape` : エスケープするかどうか
     * @return mixed コンテンツナビが無効かつオプションoverFolderがtrueでない場合はfalseを返す
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function getNextLink($title = '', $options = [])
    {
        $request = $this->getView()->getRequest();
        if (empty($request->getAttribute('currentContent')->id) || empty($request->getAttribute('currentContent')->parent_id)) {
            return false;
        }
        $options = array_merge([
            'class' => 'next-link',
            'arrow' => ' ≫',
            'overFolder' => false,
            'escape' => true
        ], $options);

        $arrow = $options['arrow'];
        $overFolder = $options['overFolder'];
        unset($options['arrow']);
        unset($options['overFolder']);

        $neighbors = $this->getPageNeighbors($request->getAttribute('currentContent'), $overFolder);

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
     * フォルダ内の次のコンテンツへのリンクを出力する
     *
     * @param string $title
     * @param array $options オプション（初期値 : array()）
     *    - `class` : CSSのクラス名（初期値 : 'next-link'）
     *    - `arrow` : 表示文字列（初期値 : ' ≫'）
     *    - `overFolder` : フォルダ外も含めるかどうか（初期値 : false）
     *        ※ overFolder が true の場合は、BcPageHelper::contentsNaviAvailable() が false だとしても強制的に出力する
     * @return @return void コンテンツナビが無効かつオプションoverFolderがtrueでない場合はfalseを出力する
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function nextLink($title = '', $options = [])
    {
        echo $this->getNextLink($title, $options);
    }

    /**
     * フォルダ内の前のコンテンツへのリンクを取得する
     *
     * @param string $title
     * @param array $options オプション（初期値 : array()）
     *    - `class` : CSSのクラス名（初期値 : 'prev-link'）
     *    - `arrow` : 表示文字列（初期値 : ' ≫'）
     *    - `overFolder` : フォルダ外も含めるかどうか（初期値 : false）
     *    - `escape` : エスケープするかどうか
     * @return string|false
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function getPrevLink($title = '', $options = [])
    {
        $request = $this->getView()->getRequest();
        if (empty($request->getAttribute('currentContent')->id) || empty($request->getAttribute('currentContent')->parent_id)) {
            return false;
        }
        $options = array_merge([
            'class' => 'prev-link',
            'arrow' => '≪ ',
            'overFolder' => false,
            'escape' => true
        ], $options);

        $arrow = $options['arrow'];
        $overFolder = $options['overFolder'];
        unset($options['arrow']);
        unset($options['overFolder']);
        $content = $request->getAttribute('currentContent');
        $neighbors = $this->getPageNeighbors($content, $overFolder);

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
     * フォルダ内の前のコンテンツへのリンクを出力する
     *
     * @param string $title
     * @param array $options オプション（初期値 : array()）
     *    - `class` : CSSのクラス名（初期値 : 'prev-link'）
     *    - `arrow` : 表示文字列（初期値 : ' ≫'）
     *    - `overFolder` : フォルダ外も含めるかどうか（初期値 : false）
     *        ※ overFolder が true の場合は、BcPageHelper::contentsNaviAvailable() が false だとしても強制的に出力する
     * @return void コンテンツナビが無効かつオプションoverFolderがtrueでない場合はfalseを返す
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function prevLink($title = '', $options = [])
    {
        echo $this->getPrevLink($title, $options);
    }

    /**
     * 指定した固定ページデータの次、または、前のデータを取得する
     *
     * @param Content $content
     * @param bool $overFolder フォルダ外も含めるかどうか
     * @return array 次、または、前の固定ページデータ
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function getPageNeighbors($content, $overFolder = false)
    {
        $conditions = array_merge($this->ContentsService->getConditionAllowPublish(), [
            'Contents.type <>' => 'ContentFolder',
            'Contents.site_id' => $content->site_id
        ]);
        if ($overFolder !== true) {
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

    /**
     * 公開状態のサイトを全て取得する
     *
     * @return \Cake\Datasource\ResultSetInterface
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためテスト不要
     */
    public function getPublishedSites()
    {
        /** @var SitesTable $sites */
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        return $sites->getPublishedAll();
    }

    /**
     * フロントページにおいて現在のサイトを取得する
     * @return false|Site
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentSite()
    {
        if (BcUtil::isAdminSystem()) return false;
        return $this->getView()->getRequest()->getAttribute('currentSite');
    }

    /**
     * フロントページにおいて現在のコンテンツを取得する
     * @return false|Site
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentContent()
    {
        if (BcUtil::isAdminSystem()) return false;
        return $this->getView()->getRequest()->getAttribute('currentContent');
    }

}
