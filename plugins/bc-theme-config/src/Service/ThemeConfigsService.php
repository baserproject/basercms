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

namespace BcThemeConfig\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use BaserCore\Vendor\Imageresizer;
use BcThemeConfig\Model\Entity\ThemeConfig;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;

/**
 * ThemeConfigsService
 */
class ThemeConfigsService implements ThemeConfigsServiceInterface
{

    /**
     * キャッシュ用 Entity
     * @var ThemeConfig
     */
    protected $entity;

    /**
     * constructor.
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->ThemeConfigs = TableRegistry::getTableLocator()->get('BcThemeConfig.ThemeConfigs');
    }

    /**
     * テーマ設定を取得
     * @return ThemeConfig|\Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get()
    {
        if (!$this->entity) {
            $this->entity = $this->ThemeConfigs->newEntity(
                $this->ThemeConfigs->getKeyValue(),
                ['validate' => 'keyValue']
            );
        }
        return $this->entity;
    }

    /**
     * キャッシュ用 Entity を削除
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function clearCache()
    {
        $this->entity = null;
    }

    /**
     * テーマ設定を更新する
     *
     * @param array $postData
     * @return ThemeConfig|\Cake\Datasource\EntityInterface|false
     * @noTodo
     * @checked
     * @unitTest
     */
    public function update(array $postData)
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }

        $entity = $this->ThemeConfigs->newEntity($postData, ['validate' => 'keyValue']);
        if ($entity->hasErrors()) {
            throw new PersistenceFailedException($entity, __d('baser_core', '入力エラーです。内容を修正してください。'));
        }

        $this->updateColorConfig($entity);
        $entity = $this->saveImage($entity);
        $entity = $this->deleteImage($entity);
        foreach($entity as $key => $value) {
            if (preg_match('/main_image_[0-9]_delete/', $key)) {
                unset($entity->{$key});
            }
        }

        $entityArray = $entity->toArray();
        if ($this->ThemeConfigs->saveKeyValue($entityArray)) {
            $this->entity = null;
            return $this->get();
        }
        return false;
    }


    /**
     * 画像を保存する
     *
     * @param EntityInterface $entity
     * @return EntityInterface
     * @noTodo
     * @checked
     * @unitTest
     */
    public function saveImage($entity)
    {
        $saveDir = WWW_ROOT . 'files' . DS . 'theme_configs' . DS;
        if(!is_dir($saveDir)) {
            $folder = new BcFolder($saveDir);
            $folder->create();
        }
        $thumbSuffix = '_thumb';
        $oldEntity = $this->ThemeConfigs->getKeyValue();

        foreach(['logo', 'main_image_1', 'main_image_2', 'main_image_3', 'main_image_4', 'main_image_5'] as $image) {
            if (!empty($entity->{$image}['tmp_name'])) {
                // 古い本体ファイルを削除
                @unlink($saveDir . $oldEntity[$image]);

                // 古いサムネイルを削除
                $pathinfo = pathinfo($oldEntity[$image]);
                @unlink($saveDir . $pathinfo['filename'] . $thumbSuffix . '.' . $pathinfo['extension']);

                // 本体ファイルを保存
                $ext = pathinfo($entity->{$image}['name'], PATHINFO_EXTENSION);
                $fileName = $image . '.' . $ext;
                $filePath = $saveDir . $fileName;
                move_uploaded_file($entity->{$image}['tmp_name'], $filePath);

                // サムネイルを保存
                $imageresizer = new Imageresizer();
                $thumbPath = $saveDir . $image . $thumbSuffix . '.' . $ext;
                if (file_exists($filePath))
                    $imageresizer->resize($filePath, $thumbPath, 320, 320);

                // エンティティを更新
                $entity->{$image} = $fileName;
            } else {
                unset($entity->{$image});
            }
        }

        return $entity;
    }

    /**
     * 画像を削除する
     *
     * @param EntityInterface $entity
     * @return EntityInterface
     * @noTodo
     * @checked
     * @unitTest
     */
    public function deleteImage($entity)
    {
        $saveDir = WWW_ROOT . 'files' . DS . 'theme_configs' . DS;
        $images = ['logo', 'main_image_1', 'main_image_2', 'main_image_3', 'main_image_4', 'main_image_5'];
        $thumbSuffix = '_thumb';
        $oldEntity = $this->ThemeConfigs->getKeyValue();
        foreach($images as $image) {
            if (!empty($entity->{$image . '_delete'})) {
                @unlink($saveDir . $oldEntity[$image]);
                $pathinfo = pathinfo($oldEntity[$image]);
                @unlink($saveDir . $pathinfo['filename'] . $thumbSuffix . '.' . $pathinfo['extension']);
                $entity->{$image} = '';
            }
        }

        return $entity;
    }

    /**
     * テーマカラー設定を保存する
     *
     * @param EntityInterface $entity
     * @return boolean
     * @noTodo
     * @checked
     * @unitTest
     */
    public function updateColorConfig($entity)
    {
        $configPath = Plugin::path(BcUtil::getCurrentTheme()) . 'webroot' . DS . 'css' . DS . 'config.css';
        if (!file_exists($configPath)) {
            return false;
        }
        $File = new BcFile($configPath);
        $config = $File->read();
        $settings = [
            'MAIN' => 'color_main',
            'SUB' => 'color_sub',
            'LINK' => 'color_link',
            'HOVER' => 'color_hover'
        ];
        $settingExists = false;
        foreach($settings as $key => $setting) {
            if (empty($entity->{$setting})) {
                $config = preg_replace("/\n.+?" . $key . ".+?\n/", "\n", $config);
            } else {
                $config = str_replace($key, '#' . $entity->{$setting}, $config);
                $settingExists = true;
            }
        }
        $File = new BcFile(WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css', true, 0666);
        $File->write($config);
        if (!$settingExists) {
            unlink($configPath);
        }
        return true;
    }

}
