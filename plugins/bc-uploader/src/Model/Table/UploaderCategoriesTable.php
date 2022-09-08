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

 namespace BcUploader\Model\Table;

use BaserCore\Model\Table\AppTable;

/**
 * ファイルカテゴリモデル
 *
 * @package         Uploader.Model
 */
class UploaderCategoriesTable extends AppTable
{

    /**
     * バリデート
     *
     * @var        array
     * @access    public
     */
    public $validate = [
        'name' => [
            [
                'rule' => ['notBlank'],
                'message' => 'カテゴリ名を入力してください。'
            ]
        ]
    ];

    /**
     * コピーする
     *
     * @param int $id
     * @param array $data
     * @return mixed page Or false
     */
    public function copy($id = null, $data = [])
    {
        if ($id) {
            $data = $this->find('first', ['conditions' => ['UploaderCategory.id' => $id]]);
        }
        $oldData = $data;

        // EVENT UploaderCategory.beforeCopy
        $event = $this->dispatchEvent('beforeCopy', [
            'data' => $data,
            'id' => $id,
        ]);
        if ($event !== false) {
            $data = $event->getResult() === true ? $event->getData('data') : $event->getResult();
        }

        $data['UploaderCategory']['name'] .= '_copy';
        $data['UploaderCategory']['id'] = $this->getMax('id', ['UploaderCategory.id' => $data['UploaderCategory']['id']]) + 1;

        unset($data['UploaderCategory']['id']);
        unset($data['UploaderCategory']['created']);
        unset($data['UploaderCategory']['modified']);

        $this->create($data);
        $result = $this->save();
        if ($result) {
            $result['UploaderCategory']['id'] = $this->getLastInsertID();
            $data = $result;

            // EVENT UploaderCategory.afterCopy
            $event = $this->dispatchEvent('afterCopy', [
                'id' => $data['UploaderCategory']['id'],
                'data' => $data,
                'oldId' => $id,
                'oldData' => $oldData,
            ]);

            return $result;
        } else {
            if (isset($this->validationErrors['name'])) {
                return $this->copy(null, $data);
            } else {
                return false;
            }
        }
    }
}
