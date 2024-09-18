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

namespace BaserCore\Service;

use BaserCore\Error\BcException;
use BaserCore\Model\Table\AppTable;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use BaserCore\Utility\BcZip;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Log\LogTrait;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Throwable;

/**
 * UtilitiesService
 */
class UtilitiesService implements UtilitiesServiceInterface
{

    /**
     * Trait
     */
    use LogTrait;
    use BcContainerTrait;

    /**
     * ログのパス
     * @var string
     */
    public $logPath = LOGS . 'error.log';

    /**
     * コンテンツツリーの構造をチェックする
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function verityContentsTree(): bool
    {
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $result = $this->_verify($contentsTable);
        if ($result !== true) {
            foreach($result as $value) {
                $this->log(implode(', ', $value));
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * コンテンツツリーをリセットし全て同階層にする
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetContentsTree()
    {
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        return $contentsTable->resetTree();
    }

    /**
     * ツリー構造が壊れていないか確認する
     * CakePHP2系の TreeBehavior より移植
     *
     * @param Table $table
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _verify(Table $table)
    {
        $right = 'rght';
        $left = 'lft';
        $scope = '1 = 1';
        $parent = 'parent_id';
        $plugin = 'BaserCore';
        if (!$table->find()->applyOptions(['withDeleted'])->where([$scope])->count()) {
            return true;
        }
        $min = $this->_getMin($table, $scope, $left);
        $edge = $this->_getMax($table, $scope, $right);
        $errors = [];

        for($i = $min; $i <= $edge; $i++) {
            $count = $table->find()->applyOptions(['withDeleted'])->where([
                $scope,
                'OR' => [$left => $i, $right => $i]
            ])->count();
            if ($count != 1) {
                if (!$count) {
                    $errors[] = ['index', $i, 'missing'];
                } else {
                    $errors[] = ['index', $i, 'duplicate'];
                }
            }
        }
        $node = $table->find()->applyOptions(['withDeleted'])->where([$scope, $right . '< ' . $left])->first();
        $primaryKey = $table->getPrimaryKey();
        if ($node) {
            $errors[] = ['node', $node->{$primaryKey}, 'left greater than right.'];
        }

        $table->belongsTo('VerifyParent', [
            'className' => $plugin . '.' . $table->getAlias(),
            'propertyName' => 'VerifyParent',
            'foreignKey' => $parent
        ]);

        $rows = $table->find()->applyOptions(['withDeleted'])->where([$scope])->contain('VerifyParent')->all();
        foreach($rows as $instance) {
            if ($instance->{$left} === null || $instance->{$right} === null) {
                $errors[] = ['node', $instance->{$primaryKey},
                    'has invalid left or right values'];
            } elseif ($instance->{$left} == $instance->{$right}) {
                $errors[] = ['node', $instance->{$primaryKey},
                    'left and right values identical'];
            } elseif ($instance->{$parent}) {
                if (!$instance->VerifyParent->{$primaryKey}) {
                    $errors[] = ['node', $instance->{$primaryKey},
                        'The parent node ' . $instance->{$parent} . ' doesn\'t exist'];
                } elseif ($instance->{$left} < $instance->VerifyParent->{$left}) {
                    $errors[] = ['node', $instance->{$primaryKey},
                        'left less than parent (node ' . $instance->VerifyParent->{$primaryKey} . ').'];
                } elseif ($instance->{$right} > $instance->VerifyParent->{$right}) {
                    $errors[] = ['node', $instance->{$primaryKey},
                        'right greater than parent (node ' . $instance->VerifyParent->{$primaryKey} . ').'];
                }
            } elseif ($table->find()->where([
                $scope, $table->getAlias() . '.' . $left . ' <' => $instance->{$left},
                $table->getAlias() . '.' . $right . ' >' => $instance->{$right}
            ])->contain('VerifyParent')->count()) {
                $errors[] = ['node', $instance->{$primaryKey}, 'The parent field is blank, but has a parent'];
            }
        }
        if ($errors) {
            return $errors;
        }
        return true;
    }

    /**
     * テーブル内の left の最小値を取得
     *
     * @param Table $table
     * @param string $scope
     * @param string $left
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getMin(Table $table, $scope, $left)
    {
        $query = $table->find()->applyOptions(['withDeleted'])->where([$scope]);
        $min = $query->select([$left => $query->func()->min($left)])->first();
        return (empty($min->{$left}))? 0 : (int)$min->{$left};
    }

    /**
     * テーブル内の right の最大値を取得
     *
     * @param Table $table
     * @param string $scope
     * @param string $right
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getMax(Table $table, $scope, $right)
    {
        $query = $table->find()->applyOptions(['withDeleted'])->where([$scope]);
        $max = $query->select([$right => $query->func()->max($right)])->first();
        return (empty($max->{$right}))? 0 : (int)$max->{$right};
    }

    /**
     * クレジットを取得する
     *
     * @return mixed|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCredit()
    {
        $specialThanks = [];
        if (!Configure::read('debug')) {
            $specialThanks = Cache::read('specialThanks', '_bc_env_');
        }

        if ($specialThanks) {
            $json = json_decode($specialThanks);
        } else {
            if (Configure::read('BcLinks.specialThanks')) {
                $json = file_get_contents(Configure::read('BcLinks.specialThanks'), true);
            } else {
                throw new BcException(__d('baser_core', 'スペシャルサンクスのデータが読み込めませんでした。'));
            }
            if ($json) {
                Cache::write('specialThanks', $json, '_bc_env_');
                $json = json_decode($json);
            } else {
                $json = null;
            }
        }
        return $json;
    }

    /**
     * ログのZipファイルを作成する
     *
     * @return string|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createLogZip(): ?string
    {
        set_time_limit(0);
        $Folder = new BcFolder(LOGS);
        $files = $Folder->getFiles();
        $folders = $Folder->getFolders();
        if (count($files) === 0 && count($folders) === 0) {
            return false;
        }
        // ZIP圧縮して出力
        $distPath = TMP . 'basercms_logs_' . date('Ymd_His') . '.zip';
		$bcZip = new BcZip();
		$bcZip->create(LOGS, $distPath);

        return $distPath;
    }

    /**
     * ログを削除する
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteLog()
    {
        if (file_exists(LOGS)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(LOGS,
                        \FilesystemIterator::CURRENT_AS_FILEINFO |
                        \FilesystemIterator::KEY_AS_PATHNAME |
                        \FilesystemIterator::SKIP_DOTS
                )
            );
            $messages = [];
            foreach($files as $file) {
                if (unlink($file->getRealPath())) {
                } else {
                    $messages[] = __d('baser_core', 'ファイルが削除できませんでした。') . $file->getRealPath();
                }
            }
            if (count($messages) === 0) {
                return true;
            }
        } else {
            $messages[] = __d('baser_core', 'ログフォルダが存在しません。');
        }
        throw new BcException(implode("\n", $messages));
    }

    /**
     * DBバックアップを作成する
     *
     * @param $encoding
     * @return string|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function backupDb($encoding): ?string
    {
        set_time_limit(0);
        $this->resetTmpSchemaFolder();
        $tmpDir = TMP . 'schema' . DS;
		$distPath = TMP . 'baserbackup_' . BcUtil::getVersion() . '_' . date('Ymd_His') . '.zip';
        BcUtil::clearAllCache();
        $plugins = Plugin::loaded();
        $result = true;
        if ($plugins) {
            foreach($plugins as $plugin) {
                if(!$this->_writeBackup($tmpDir, $plugin, $encoding)) {
                    $result = false;
                }
            }
        }
        if(!$result) return null;
        // ZIP圧縮して出力
        $bcZip = new BcZip();
		$bcZip->create($tmpDir, $distPath);
        $this->resetTmpSchemaFolder();

        return $distPath;
    }

    /**
     * スキーマ用の一時フォルダをリセットする
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetTmpSchemaFolder(): bool
    {
        return BcUtil::emptyFolder(TMP . 'schema' . DS);
    }

    /**
     * バックアップファイルを書きだす
     *
     * @param string $path
     * @param string $plugin
     * @param $encoding
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _writeBackup($path, $plugin, $encoding)
    {
        /* @var BcDatabaseService $dbService */
        $dbService = $this->getService(BcDatabaseServiceInterface::class);

        /* @var AppTable $appTable */
        $appTable = TableRegistry::getTableLocator()->get('BaserCore.App');

        /* @var \Cake\Database\Connection $db */
        $db = $appTable->getConnection();

        $tables = $db->getSchemaCollection()->listTables();
        $dbService->clearAppTableList();
        $tableList = $dbService->getAppTableList();

        $prefix = BcUtil::getCurrentDbConfig()['prefix'];

        foreach($tables as $table) {
            $baredTable = preg_replace('/^' . $prefix . '/', '', $table);
            if (!isset($tableList[$plugin]) || !in_array($table, $tableList[$plugin])) continue;
            if (!$dbService->writeSchema($baredTable, [
                'path' => $path,
                'prefix' => $prefix
            ])) {
                return false;
            }
            if (!$dbService->writeCsv($table, [
                'path' => $path . $baredTable . '.csv',
                'encoding' => $encoding
            ])) {
                return false;
            }
        }
        return true;
    }

    /**
     * バックアップファイルよりレストアを行う
     *
     * @param array $postData
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function restoreDb(array $postData, array $uploaded): bool
    {
        set_time_limit(0);

        $postData = array_merge([
            'encoding' => 'UTF-8'
        ], $postData);

        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }

        if (empty($_FILES['backup']['tmp_name'])) {
            if (!empty($uploaded['backup']) && $uploaded['backup']->getError() === 1) {
                $message = __d('baser_core', 'サーバに設定されているサイズ制限を超えています。');
            } else {
                $message = __d('baser_core', 'バックアップファイルが送信されませんでした。');
            }
            throw new BcException($message);
        }

        $tmpPath = TMP . 'schema' . DS;
        if(!is_dir($tmpPath)) {
            (new BcFolder())->create($tmpPath);
        }
        $name = $uploaded['backup']->getClientFileName();
        $uploaded['backup']->moveTo($tmpPath . $name);
        $bcZip = new BcZip();
        if (!$bcZip->extract($tmpPath . $name, $tmpPath)) {
            throw new BcException(__d('baser_core', 'アップロードしたZIPファイルの展開に失敗しました。'));
        }
        unlink($tmpPath . $name);

        $result = true;
        try {
            /* @var \BaserCore\Service\BcDatabaseService $dbService */
            $this->_loadBackup($tmpPath, $postData['encoding']);
        } catch (\Throwable $e) {
            throw $e;
        }

        $dbService = $this->getService(BcDatabaseServiceInterface::class);
        $dbService->updateSequence();

        $this->resetTmpSchemaFolder();
        BcUtil::clearAllCache();
        return $result;
    }

    /**
     * データベースをレストア
     *
     * @param string $path スキーマファイルのパス
     * @param $encoding
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _loadBackup($path, $encoding)
    {
        $folder = new BcFolder($path);
        $files = $folder->getFiles();
        if (!is_array($files)) return;

        /* @var BcDatabaseService $dbService */
        $dbService = $this->getService(BcDatabaseServiceInterface::class);

        $prefix = BcUtil::getCurrentDbConfig()['prefix'];

        $db = BcUtil::getCurrentDb();
        $db->begin();
        // テーブルを削除する
        foreach($files as $file) {
            if (!preg_match("/\.php$/", $file)) continue;
            try {
                $dbService->loadSchema([
                    'type' => 'drop',
                    'path' => $path,
                    'file' => $file,
                    'prefix' => $prefix
                ]);
            } catch (Throwable $e) {
                $db->rollback();
                throw $e;
            }
        }

        // テーブルを読み込む
        foreach($files as $file) {
            if (!preg_match("/\.php$/", $file)) continue;
            try {
                if (!$dbService->loadSchema([
                    'type' => 'create',
                    'path' => $path,
                    'file' => $file,
                    'prefix' => $prefix
                ])) {
                    continue;
                }
            } catch (Throwable $e) {
                $db->rollback();
                throw $e;
            }
        }

        /* CSVファイルを読み込む */
        foreach($files as $file) {
            if (!preg_match("/\.csv$/", $file)) continue;
            try {
                if (!$dbService->loadCsv([
                    'path' => $path . $file,
                    'encoding' => $encoding
                ])) {
                    continue;
                }
            } catch (Throwable $e) {
                $db->rollback();
                throw $e;
            }
        }
        $db->commit();
    }

    /**
     * データをリセットする
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetData()
    {
        /* @var \BaserCore\Service\ThemesService $themesService */
        $themesService = $this->getService(ThemesServiceInterface::class);
        try {
            return $themesService->loadDefaultDataPattern(
                BcUtil::getRootTheme(),
                Configure::read('BcApp.defaultFrontTheme') . '.default'
            );
        } catch (\Throwable $e) {
            throw $e;
        }
    }

}
