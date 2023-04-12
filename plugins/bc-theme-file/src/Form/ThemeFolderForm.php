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

namespace BcThemeFile\Form;

use Cake\Filesystem\Folder;
use Cake\Form\Form;
use Cake\Form\Schema;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Validation\Validator;

/**
 * ThemeFolderForm
 */
class ThemeFolderForm extends Form
{

    /**
     * テーマファイルのスキーマを生成
     *
     * @param Schema $schema
     * @return Schema
     * @checked
     * @noTodo
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('fullpath', 'string')
            ->addField('name', 'string')
            ->addField('parent', 'string');
    }

    /**
     * フォルダの作成、リネームを実行する
     *
     * @param array $data
     * @return bool
     */
    protected function _execute(array $data): bool
    {
        $folder = new Folder();
        if($data['mode'] === 'create') {
            return $folder->create($data['fullpath'] . DS . $data['name'] . DS, 0777);
        } elseif($data['mode'] === 'update') {
            $newPath = dirname($data['fullpath']) . DS . $data['name'] . DS;
            if($newPath === $data['fullpath']) return true;
            return $folder->move($newPath, ['from' => $data['fullpath'], 'chmod' => 0777, 'skip' => ['_notes']]);
        }
        return false;
    }

    /**
     * Validation default
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->requirePresence('name', 'create', __d('baser_core', 'テーマフォルダー名を入力してください。'))
            ->notEmptyString('name', __d('baser_core', 'テーマフォルダー名を入力してください。'))
            ->add('name', [
                'duplicateThemeFolder' => [
                    'provider' => 'form',
                    'rule' => ['duplicateThemeFolder'],
                    'message' => __d('baser_core', '入力されたテーマフォルダー名は、同一階層に既に存在します。')
                ]])
            ->add('name', [
                'nameAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'テーマフォルダー名は半角英数字とハイフン、アンダースコアのみが利用可能です。')
                ]]);
        return $validator;
    }

    /**
     * フォルダーの重複チェック
     *
     * @param array $check
     * @return    boolean
     */
    public function duplicateThemeFolder($value, $context = null)
    {
        if (!$value) return true;
        if($context['data']['mode'] !== 'create') return true;
        $target = $context['data']['parent'] . $value;
        if (is_dir($target)) {
            return false;
        } else {
            return true;
        }
    }

}
