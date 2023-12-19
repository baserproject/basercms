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

namespace BaserCore\Test\Scenario;

use BaserCore\Test\Factory\ContentFolderFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * Contents
 *
 */
class ContentFoldersScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        ContentFolderFactory::make(
            [
                'id' => '1',
                'folder_template' => 'baserCMSサンプル',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ]
        )->persist();
        ContentFolderFactory::make(
            [
                'id' => '4',
                'folder_template' => 'サービスフォルダー',
                'page_template' => 'サービスページ',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ])->persist();
        ContentFolderFactory::make(
            [
                'id' => '10',
                'folder_template' => '削除済みフォルダー(親)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ])->persist();
        ContentFolderFactory::make(
            [
                'id' => '11',
                'folder_template' => '削除済みフォルダー(子)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ])->persist();
        ContentFolderFactory::make(
            [
                'id' => '12',
                'folder_template' => 'ツリー階層削除用フォルダー(親)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ])->persist();
        ContentFolderFactory::make(
            [
                'id' => '13',
                'folder_template' => 'ツリー階層削除用フォルダー(子)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ])->persist();
        ContentFolderFactory::make(
            [
                'id' => '14',
                'folder_template' => 'ツリー階層削除用フォルダー(孫)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ])->persist();
        ContentFolderFactory::make(
            [
                'id' => '15',
                'folder_template' => 'testEdit',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ])->persist();
        ContentFolderFactory::make(
            [
                'id' => '17',
                'folder_template' => 'default',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ])->persist();
        ContentFolderFactory::make(
            [
                'id' => '18',
                'folder_template' => 'default',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ])->persist();
    }

}
