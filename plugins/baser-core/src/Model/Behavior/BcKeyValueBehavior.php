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
namespace BaserCore\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;

/**
 * Class BcKeyValueBehavior
 * @package BaserCore\Model\Table
 */
class BcKeyValueBehavior extends Behavior
{

    /**
     * Key Value 形式のテーブルよりデータを取得して
     * １レコードとしてデータを展開する
     *
     * @return array
     */
    public function getKeyValue()
    {
        $records = $this->_table->find()
            ->select(['name', 'value'])
            ->all();
        $expandedData = [];
        if ($records) {
            foreach($records as $record) {
                $expandedData[$record->name] = $record->value;
            }
        }
        return $expandedData;
    }

    /**
     * Key Value 形式のテーブルにデータを保存する
     *
     * @param array $siteConfigs
     * @return boolean
     */
    public function saveKeyValue($siteConfigs)
    {
        $result = true;
        foreach($siteConfigs as $key => $value) {
            if ($this->_table->find()->where(['name' => $key])->count() > 1) {
                $this->_table->deleteAll(['name' => $key]);
            }
            $record = $this->_table->find()->where(['name' => $key])->first();
            if (!$record) {
                $record = $this->_table->newEntity([
                    'name' => $key,
                    'value' => $value
                ]);
                if(!$this->_table->save($record)) $result = false;
            } else {
                $record = $this->_table->patchEntity($record, [
                    'value' => $value
                ]);
                if(!$this->_table->save($record)) $result = false;
            }
        }
        return $result;
    }

    /**
     * 値を取得する
     *
     * @return mixed
     */
    public function getValue($key)
    {
        $record = $this->_table->find()
            ->select(['name', 'value'])
            ->where(['name' => $key])
            ->first();
        if($record) {
            return $record['value'];
        } else {
            return false;
        }
    }

    /**
     * 値を保存する
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function saveValue($key, $value)
    {
        $record = $this->_table->find()
            ->where(['name' => $key])
            ->first();
        $record = $this->_table->patchEntity($record, [
            'value' => $value
        ]);
        return (bool) $this->_table->save($record);
    }

}
