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

namespace BcThemeFile\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Error\BcFormFailedException;
use BaserCore\Utility\BcUtil;
use BcThemeFile\Form\ThemeFileForm;
use BcThemeFile\Model\Entity\ThemeFile;
use BcThemeFile\Utility\BcThemeFileUtil;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Http\Exception\NotFoundException;

/**
 * ThemeFilesService
 *
 * @property ThemeFileForm $ThemeFileForm
 */
class ThemeFilesService extends BcThemeFileService implements ThemeFilesServiceInterface
{

    /**
     * Constructor
     *
     * @checked
     * @noTodo
     */
    public function __construct()
    {
        $this->ThemeFileForm = new ThemeFileForm();
    }

    /**
     * テーマファイルの初期値を取得
     *
     * @param string $file
     * @param string $type
     * @return ThemeFile
     * @checked
     * @noTodo
     */
    public function getNew(string $file, string $type)
    {
        return new ThemeFile([
            'fullpath' => $file,
        ], ['new' => true, 'type' => $type]);
    }

    /**
     * 単一データ取得
     *
     * @param string $file
     * @return ThemeFile
     * @checked
     * @noTodo
     */
    public function get(string $file)
    {
        return new ThemeFile(['fullpath' => $file]);
    }

    /**
     * フォームを取得
     *
     * @param array $data
     * @return ThemeFileForm
     * @checked
     * @noTodo
     */
    public function getForm(array $data)
    {
        return (new ThemeFileForm())->setData($data);
    }

    /**
     * テーマファイルを作成
     *
     * @param array $postData
     * @return ThemeFileForm
     * @checked
     * @noTodo
     */
    public function create(array $postData)
    {
        $postData['mode'] = 'create';
        $form = new ThemeFileForm();
        if ($form->validate($postData)) {
            if ($form->execute($postData)) {
                $form->set('fullpath', $postData['fullpath'] . $postData['base_name'] . '.' . $postData['ext']);
                return $form;
            } else {
                throw new BcException(__d('baser', 'ファイルの作成に失敗しました。書き込み権限に問題がある可能性があります。'));
            }
        } else {
            throw new BcFormFailedException($form, __d('baser', 'ファイルの作成に失敗しました。'));
        }
    }

    /**
     * テーマファイルを編集
     *
     * @param array $postData
     * @return ThemeFileForm
     * @checked
     * @noTodo
     */
    public function update(array $postData)
    {
        $postData['mode'] = 'update';
        $themeFileForm = new ThemeFileForm();
        if ($themeFileForm->validate($postData)) {
            if ($themeFileForm->execute($postData)) {
                $themeFileForm->set('fullpath', dirname($postData['fullpath']) . DS . $postData['base_name'] . '.' . $postData['ext']);
                return $themeFileForm;
            } else {
                throw new BcException(__d('baser', '書き込み権限に問題がある可能性があります。'));
            }
        } else {
            throw new BcFormFailedException($themeFileForm, __d('baser', 'ファイルの保存に失敗しました。'));
        }
    }

    /**
     * テーマファイルを削除
     * @param string $fullpath
     * @return bool
     * @checked
     * @noTodo
     */
    public function delete(string $fullpath)
    {
        if (file_exists($fullpath)) {
            return unlink($fullpath);
        } else {
            return false;
        }
    }

    /**
     * コピー
     *
     * @param string $fullpath
     * @return ThemeFile|false
     * @checked
     * @noTodo
     */
    public function copy(string $fullpath)
    {
        $entity = $this->get($fullpath);
        $newPathBase = $entity->parent . $entity->base_name . '_copy';
        while(true) {
            if (!file_exists($newPathBase . '.' . $entity->ext)) {
                $newEntity = $this->get($newPathBase . '.' . $entity->ext);
                break;
            }
            $newPathBase .= '_copy';
        }
        $result = copy(rawurldecode($fullpath), $newEntity->fullpath);
        if ($result) {
            chmod($newEntity->fullpath, 0666);
            return $newEntity;
        } else {
            return false;
        }
    }

    /**
     * ファイルをアップロードする
     *
     * @param string $fullpath
     * @param array $postData
     */
    public function upload(string $fullpath, array $postData)
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d(
                'baser',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        $Folder = new Folder();
        $Folder->create($fullpath, 0777);
        $filePath = $fullpath . DS . $postData['file']['name'];
        if (!move_uploaded_file($postData['file']['tmp_name'], $filePath)) {
            throw new BcException(__d('baser', '書き込み権限に問題がある可能性があります。'));
        }
    }

    /**
     * 現在のテーマにファイルをコピー
     *
     * @param array $params
     * @return array|false|string|string[]
     * @checked
     * @noTodo
     */
    public function copyToTheme(array $params)
    {
        $theme = BcUtil::getCurrentTheme();
        if ($params['type'] !== 'etc') {
            if ($params['plugin'] && $params['assets']) {
                // TODO ucmitz 未検証
                $themePath = Plugin::path($theme) . $params['plugin'] . DS . $params['type'] . DS . $params['path'];
            } else {
                $themePath = Plugin::templatePath($theme) . $params['type'] . DS . $params['path'];
            }
        } else {
            $themePath = Plugin::templatePath($theme) . $params['path'];
        }
        $folder = new Folder();
        $folder->create(dirname($themePath), 0777);
        if (copy($params['fullpath'], $themePath)) {
            chmod($themePath, 0666);
            return str_replace(ROOT, '', $themePath);
        } else {
            return false;
        }
    }

    /**
     * テーマ内のイメージデータを取得する
     *
     * @param $args
     * @return array
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getImg($args)
    {
        $contents = ['jpg' => 'jpeg', 'gif' => 'gif', 'png' => 'png'];
        $pathinfo = pathinfo($args['fullpath']);

        if (!BcThemeFileUtil::getTemplateTypeName($args['type']) || !isset($contents[$pathinfo['extension']]) || !file_exists($args['fullpath'])) {
            throw new NotFoundException();
        }

        $file = new File($args['fullpath']);
        if (!$file->open('r')) {
            throw new NotFoundException();
        }

        return [
            'img' => $file->read(),
            'size' => $file->size(),
            'type' => $contents[$pathinfo['extension']]
        ];
    }

}
