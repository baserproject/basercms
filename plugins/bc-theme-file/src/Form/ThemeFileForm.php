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

use BcThemeFile\Model\Entity\ThemeFile;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Form\Form;
use Cake\Form\Schema;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
     */
    protected function _execute(array $data): bool
    {
        if(!in_array($data['mode'], ['create', 'update'])) return false;

        if($data['mode'] === 'create') {
            $oldPath = $newPath = $fullpath = $data['fullpath'] . $data['base_name'] . '.' . $data['ext'];
            if (!is_dir(dirname($fullpath))) {
                $folder = new Folder();
                $folder->create(dirname($fullpath), 0777);
            }
        } elseif($data['mode'] === 'update') {
            $oldPath = rawurldecode($data['fullpath']);
            $newPath = dirname($data['fullpath']) . DS . rawurldecode($data['base_name']);
            if ($data['ext']) $newPath .= '.' . $data['ext'];
        }

        $entity = new ThemeFile(['fullpath' => $newPath]);
        if ($entity->type === 'text') {
            $file = new File($oldPath);
            if ($file->open('w')) {
                if (isset($data['contents'])){
                    $file->append($data['contents']);
                }
                $file->close();
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

}
