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

namespace BcWidgetArea\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BcWidgetArea\Model\Entity\WidgetArea;
use BcWidgetArea\Model\Table\WidgetAreasTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Throwable;

/**
 * WidgetAreasService
 *
 * @property WidgetAreasTable $WidgetAreas
 */
class WidgetAreasService implements WidgetAreasServiceInterface
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->WidgetAreas = TableRegistry::getTableLocator()->get('BcWidgetArea.WidgetAreas');
    }

    /**
     * 単一データ取得
     */
    public function get($id)
    {
        return $this->WidgetAreas->get($id);
    }

    /**
     * 一覧データ取得
     * @checked
     * @noTodo
     * @param array $queryParams
     * @return \Cake\ORM\Query
     */
    public function getIndex(array $queryParams = [])
    {
        $options = array_merge([
            'limit' => null
        ], $queryParams);
        $query = $this->WidgetAreas->find();
        if (!is_null($options['limit'])) $query->limit($options['limit']);
        return $query;
    }

    /**
     * 初期データ取得
     * @checked
     * @noTodo
     */
    public function getNew()
    {
        return $this->WidgetAreas->newEmptyEntity();
    }

    /**
     * 作成
     * @param array $postData
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     */
    public function create(array $postData)
    {
        $entity = $this->WidgetAreas->patchEntity($this->getNew(), $postData);
        return $this->WidgetAreas->saveOrFail($entity);
    }

    /**
     * 編集
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function update(EntityInterface $entity, array $postData)
    {
        $entity = $this->WidgetAreas->patchEntity($entity, $postData);
        return $this->WidgetAreas->saveOrFail($entity);
    }

    /**
     * 削除
     * @param int $id
     * @return bool
     * @throws Throwable
     * @checked
     * @noTodo
     */
    public function delete(int $id)
    {
        $entity = $this->WidgetAreas->get($id);
        try {
            $result = $this->WidgetAreas->delete($entity);
        } catch (Throwable $e) {
            throw $e;
        }
        return $result;
    }

    /**
     * IDからタイトルリストを取得する
     *
     * @param array $ids
     * @param array $ids
     * @return array
     * @checked
     */
    public function getTitlesById(array $ids): array
    {
        return $this->WidgetAreas->find('list')->select(['id', 'name'])->where(['id IN' => $ids])->toArray();
    }

    /**
     * 一括処理
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     */
    public function batch(string $method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->WidgetAreas->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->$method($id)) {
                $db->rollback();
                throw new BcException(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * ウィジェットをアップデートする
     *
     * @param int $widgetAreaId
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function updateWidget(int $widgetAreaId, array $postData)
    {
        $dataKey = key($postData);
        /** @var WidgetArea $widgetArea */
        $widgetArea = $this->WidgetAreas->get($widgetAreaId);
        $widgets = $widgetArea->widgets_array;
        $update = false;
        if ($widgets) {
            foreach($widgets as $key => $widget) {
                if (isset($postData[$dataKey]['id']) && isset($widget[$dataKey]['id']) && $widget[$dataKey]['id'] === $postData[$dataKey]['id']) {
                    $widgets[$key] = $postData;
                    $update = true;
                    break;
                }
            }
        } else {
            $widgets = [];
        }
        if (!$update) $widgets[] = $postData;
        $widgetArea->widgets = $widgets;
        return $this->WidgetAreas->saveOrFail($widgetArea);
    }

    /**
     * ウィジェットの並べ替えを更新する
     *
     * @param int $widgetAreaId
     * @param array $postData
     * @return WidgetArea|EntityInterface
     * @checked
     * @noTodo
     */
    public function updateSort(int $widgetAreaId, array $postData)
    {
        $ids = explode(',', $postData['sorted_ids']);
        /** @var WidgetArea $widgetArea */
        $widgetArea = $this->WidgetAreas->get($widgetAreaId);
        $widgets = $widgetArea->widgets_array;
        if ($widgets) {
            foreach($widgets as $key => $widget) {
                $widgetKey = key($widget);
                $widgets[$key][$widgetKey]['sort'] = array_search($widget[$widgetKey]['id'], $ids) + 1;
            }
            $widgetArea->widgets = $widgets;
            return $this->WidgetAreas->saveOrFail($widgetArea);
        }
        return $widgetArea;
    }

    /**
     * ウィジェットを削除する
     *
     * @param int $widgetAreaId
     * @param int $widgetId
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function deleteWidget(int $widgetAreaId, int $widgetId)
    {
        $widgetArea = $this->WidgetAreas->get($widgetAreaId);
        $widgets = $widgetArea->widgets_array;
        if (!$widgets) return $widgetArea;
        foreach($widgets as $key => $widget) {
            $type = key($widget);
            if ($widgetId == $widget[$type]['id']) {
                unset($widgets[$key]);
                break;
            }
        }
        $widgetArea->widgets = $widgets;
        return $this->WidgetAreas->saveOrFail($widgetArea);
    }

    /**
     * リストを取得する
     * @return array
     * @checked
     * @noTodo
     */
    public function getList(): array
    {
        return $this->WidgetAreas->find('list')->toArray();
    }

    /**
     * コントロールソース取得
     * @param string $field
     * @return array
     * @checked
     * @noTodo
     */
    public function getControlSource(string $field): array
    {
        $controllSource['id'] = $this->getList();
        if (isset($controllSource[$field])) {
            return $controllSource[$field];
        } else {
            return [];
        }
    }

}
