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

namespace BcInstaller\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * InstallationsAdminServiceInterface
 */
interface InstallationsAdminServiceInterface
{

    /**
     * ステップ２用の view 変数を取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForStep2(): array;

    /**
     * ステップ３用の view 変数を取得する
     *
     * @param bool $blDBSettingsOK
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForStep3(bool $blDBSettingsOK): array;

    /**
     * ステップ３用のフォーム初期値を取得する
     *
     * @param ServerRequest $request
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDefaultValuesStep3(ServerRequest $request): array;

    /**
     * ステップ４用のフォーム初期値を取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDefaultValuesStep4(ServerRequest $request): array;

    /**
     * DB設定をセッションに保存
     *
     * @param ServerRequest $request
     * @param array $data
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function writeDbSettingToSession(ServerRequest $request, array $data): void;

    /**
     * DB設定をセッションから取得
     *
     * @param ServerRequest $request
     * @param array $installationData
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function readDbSetting(ServerRequest $request, array $installationData = []): array;

    /**
     * 全てのテーブルを削除する
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteAllTables(ServerRequest $request): bool;

    /**
     * 管理情報を初期化する
     *
     * - 初期ユーザー情報
     * - サイト名
     *
     * @param ServerRequest $request
     * @throws PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initAdmin(ServerRequest $request): void;

    /**
     * インストールに関するファイルやフォルダを初期化する
     *
     * @param ServerRequest $request
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initFiles(ServerRequest $request): void;

    /**
     * データベースに接続する
     *
     * @param ServerRequest $request
     * @return \Cake\Datasource\ConnectionInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function connectDb(ServerRequest $request): \Cake\Datasource\ConnectionInterface;

    /**
     * 管理画面にログインする
     *
     * @param ServerRequest $request
     * @param Response $response
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function login(ServerRequest $request, Response $response): void;

    /**
     * データベースを初期化する
     *
     * @param ServerRequest $request
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initDb(ServerRequest $request): void;

}
