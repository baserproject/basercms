<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSeo\Event;

use ArrayObject;
use BaserCore\Event\BcModelEventListener;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Collection\CollectionInterface;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Class BcSeoModelEventListener
 */
class BcSeoModelEventListener extends BcModelEventListener
{
    use BcContainerTrait;

    public $events = [
        'beforeFind',
        'beforeMarshal',
    ];

    private bool $isEdit;
    private array $associatedTables = [];

    /**
     * beforeFind
     */
    public function beforeFind(EventInterface $event, SelectQuery $query)
    {
        if (!$this->isEdit()) {
            return;
        }

        $table = $event->getSubject();
        $this->setAssociation($table->getAlias());
        if (!$this->associatedTables) {
            return;
        }

        if (!empty($this->associatedTables[$table->getAlias()]) && $table->hasAssociation('SeoMetas')) {
            // 関連テーブル取得時にSeoMetasを含める
            $query->contain('SeoMetas');

            // 取得フィールドが指定されている場合はSeoMetasのフィールドも指定
            // 例: カスタムエントリー CustomEntriesService->get
            $select = $query->clause('select');
            if ($select && !isset($select['SeoMetas__id'])) {
                $seoMetasTable = TableRegistry::getTableLocator()->get('BcSeo.SeoMetas');
                $query->select($seoMetasTable);
            }
        }

        if ($table->hasAssociation('Contents')) {
            // PagesやBlogContentsなどContentsテーブル経由でSeoMetasを取得する場合、フォームに値が表示されるように調整
            $query->formatResults(function (CollectionInterface $results) {
                foreach ($results as $result) {
                    if (!empty($result->content)) {
                        $result->seo_meta = $result->content['seo_meta'];
                    }
                }
                return $results;
            });
        }
    }

    /**
     * beforeMarshal
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data)
    {
        if (!$this->isEdit()) {
            return;
        }

        $table = $event->getSubject();
        $this->setAssociation($table->getAlias());
        if (!$this->associatedTables) {
            return;
        }

        $request = Router::getRequest();
        $requestData = $request->getData();
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');

        // コンテンツの場合のデータ構造調整
        if (!empty($data['seo_meta']) && !empty($data['content']) && is_array($data['content'])) {
            $data['content']['seo_meta'] = $data['seo_meta'];
            unset($data['seo_meta']);
        }

        if (!empty($this->associatedTables[$table->getAlias()])) {
            // エイリアス編集の際にpatchEntityにデータが渡されないためリクエストから取得
            if ($controller === 'Contents' && $action === 'edit_alias' &&
                !empty($requestData['seo_meta']) && empty($data['seo_meta'])
            ) {
                $data['seo_meta'] = $requestData['seo_meta'];
            }
            $eventData = $event->getData();
            $options = $eventData['options'];
            $options['associated'][] = 'SeoMetas';
            $event->setData('options', $options);
        }
    }

    /**
     * 対象の画面か判定
     */
    private function isEdit(): bool
    {
        $request = Router::getRequest();
        if (!$request) {
            return false;
        }
        if (!in_array(BcUtil::getRequestPrefix(Router::getRequest()), ['Admin', 'Api/Admin'])) {
            return false;
        }

        if (isset($this->isEdit)) {
            return $this->isEdit;
        }

        $request = Router::getRequest();
        $plugin = $request->getParam('plugin');
        $controller = $request->getParam('controller');
        $configControllers = Configure::read('BcSeo.controllers');

        foreach ($configControllers as $configController) {
            if ($configController['plugin'] === $plugin && $configController['controller'] === $controller) {
                $this->isEdit = true;
                return true;
            }
        }

        $this->isEdit = false;
        return false;
    }

    /**
     * テーブル関連付け
     */
    private function setAssociation(string $tableAlias)
    {
        if (isset($this->associatedTables[$tableAlias])) {
            return;
        }

        $this->associatedTables[$tableAlias] = false;

        $associations = Configure::read('BcSeo.associations');
        foreach ($associations as $association) {
            if ($association['table'] !== $tableAlias) {
                continue;
            }
            $table = TableRegistry::getTableLocator()->get($association['tablePlugin'] . '.' . $association['table']);
            $conditions = [
                'SeoMetas.table_alias' => $table->getAlias(),
            ];
            if ($table->getAlias() === 'CustomEntries') {
                $conditions['SeoMetas.table_id'] = $table->tableId;
            }
            $table->hasOne('SeoMetas', [
                'className' => 'BcSeo.SeoMetas',
                'dependent' => true,
                'cascadeCallbacks' => true,
                'foreignKey' => 'entity_id',
                'conditions' => $conditions,
            ]);
            $this->associatedTables[$association['table']] = true;
        }
    }
}
