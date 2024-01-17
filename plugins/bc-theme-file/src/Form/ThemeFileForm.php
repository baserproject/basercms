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

use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BcThemeFile\Model\Entity\ThemeFile;
use Cake\Form\Form;
use Cake\Form\Schema;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Validation\Validator;

/**
 * ThemeFileForm
 */
class ThemeFileForm extends Form
{

    /**
     * テーマファイルのスキーマを生成
     *
     * @param Schema $schema
     * @return Schema
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('fullpath', 'string')
            ->addField('name', 'string')
            ->addField('base_name', 'string')
            ->addField('ext', 'string')
            ->addField('type', 'string')
            ->addField('path', 'string')
            ->addField('parent', 'string')
            ->addField('contents', 'string');
    }

    /**
     * ファイルの作成、保存を実行する
     *
     * @param array $data
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _execute(array $data): bool
    {
        if (!in_array($data['mode'], ['create', 'update'])) return false;

        if ($data['mode'] === 'create') {
            $oldPath = $newPath = $fullpath = $data['fullpath'] . $data['base_name'] . '.' . $data['ext'];
            if (!is_dir(dirname($fullpath))) {
                $folder = new BcFolder(dirname($fullpath));
                $folder->create();
            }
        } elseif ($data['mode'] === 'update') {
            $oldPath = rawurldecode($data['fullpath']);
            $newPath = dirname($data['fullpath']) . DS . rawurldecode($data['base_name']);
            if ($data['ext']) $newPath .= '.' . $data['ext'];
        }

        $entity = new ThemeFile(['fullpath' => $newPath]);
        if ($entity->type === 'text') {
            $file = new BcFile($oldPath);
            if ($file) {
                if (isset($data['contents'])){
                    $file->write($data['contents']);
                }
                unset($file);
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = true;
        }

        if ($oldPath !== $newPath) {
            rename($oldPath, $newPath);
        }
        return $result;
    }

    /**
     * Validation default
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('base_name')
            ->requirePresence('base_name', 'create', __d('baser_core', 'テーマファイル名を入力してください。'))
            ->notEmptyString('base_name', __d('baser_core', 'テーマファイル名を入力してください。'))
            ->add('base_name', [
                'duplicateThemeFile' => [
                    'provider' => 'form',
                    'rule' => ['duplicateThemeFile'],
                    'message' => __d('baser_core', '入力されたテーマファイル名は、同一階層に既に存在します。')
                ]])
            ->add('base_name', [
                'nameAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'テーマファイル名は半角英数字とハイフン、アンダースコアのみが利用可能です。')
                ]]);
        return $validator;
    }

    /**
     * ファイルの重複チェック
     *
     * @param array $check
     * @return    boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function duplicateThemeFile($value, $context = null)
    {
        if (!$value) return true;
        if($context['data']['mode'] !== 'create') return true;
        $target = $context['data']['parent'] . $value . '.' . $context['data']['ext'];
        if (is_file($target)) {
            return false;
        } else {
            return true;
        }
    }

}
