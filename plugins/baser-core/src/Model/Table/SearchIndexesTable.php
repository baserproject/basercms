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

namespace BaserCore\Model\Table;

use Cake\ORM\TableRegistry;
use BaserCore\Model\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * SearchIndexesTable
 */
class SearchIndexesTable extends AppTable
{
    // /**
    //  * クラス名
    //  *
    //  * @var string
    //  */
    // public $name = 'SearchIndex';

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }

    /**
     * 検索インデックス再構築
     *
     * @param int $parentContentId 親となるコンテンツID
     * @return bool
     */
    public function reconstruct($parentContentId = null)
    {
        $Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');;
        $conditions = [
            'OR' => [
                ['Site.status' => null],
                ['Site.status' => true]
            ]];
        if ($parentContentId) {
            $parentContent = $Contents->find()->select(['lft', 'rght'])->where(['id' => $parentContentId])->first();
            $conditions = array_merge($conditions, [
                'lft >' => $parentContent->lft,
                'rght <' => $parentContent->rght
            ]);
        }
        $contents = $Contents->find()->where($conditions)->orderby('lft')->all();
        $models = [];
        $db = $this->getDataSource();
        $this->begin();

        if (!$parentContentId) {
            $db->truncate('search_indices');
        }

        $result = true;
        if ($contents) {
            foreach($contents as $content) {
                if (isset($models[$content['Content']['type']])) {
                    $modelClass = $models[$content['Content']['type']];
                } else {
                    if (ClassRegistry::isKeySet($content['Content']['type'])) {
                        $models[$content['Content']['type']] = $modelClass = ClassRegistry::getObject($content['Content']['type']);
                    } else {
                        if ($content['Content']['plugin'] == 'BaserCore') {
                            $modelName = $content['Content']['type'];
                        } else {
                            $modelName = $content['Content']['plugin'] . '.' . $content['Content']['type'];
                        }
                        $models[$content['Content']['type']] = $modelClass = ClassRegistry::init($modelName);
                    }
                }
                $entity = $modelClass->find('first', ['conditions' => [$modelClass->name . '.id' => $content['Content']['entity_id']], 'recursive' => 0]);
                if (!$modelClass->save($entity, false)) {
                    $result = false;
                }
            }
        }
        if ($result) {
            $this->commit();
        } else {
            $this->roleback();
        }
        return $result;
    }

    /**
     * 公開状態確認
     *
     * @param array $data
     * @return bool
     */
    public function allowPublish($data)
    {
        if (isset($data['SearchIndex'])) {
            $data = $data['SearchIndex'];
        }
        $allowPublish = (int)$data['status'];
        if ($data['publish_begin'] == '0000-00-00 00:00:00') {
            $data['publish_begin'] = null;
        }
        if ($data['publish_end'] == '0000-00-00 00:00:00') {
            $data['publish_end'] = null;
        }
        // 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
        if (($data['publish_begin'] && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
            ($data['publish_end'] && $data['publish_end'] <= date('Y-m-d H:i:s'))) {
            $allowPublish = false;
        }
        return $allowPublish;
    }

}
