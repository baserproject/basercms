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
use BaserCore\Vendor\Simplezip;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Log\LogTrait;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * UtilitiesService
 */
class UtilitiesService implements UtilitiesServiceInterface
{

    /**
     * Trait
     */
    use LogTrait;

    /**
     * ログのパス
     * @var string
     */
    public $logPath = LOGS . 'error.log';

    /**
     * コンテンツツリーの構造をチェックする
     * @return bool
     * @checked
     * @noTodo
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
     */
    public function resetContentsTree()
    {
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        return $contentsTable->resetTree();
    }

    /**
     * ツリー構造が壊れていないか確認する
     * CakePHP2系の TreeBehavior より移植
     * @param Table $table
     * @return array|false
     * @checked
     * @noTodo
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
            $count = $table->find()->where([
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
     */
    protected function _getMin(Table $table, $scope, $left)
    {
        $query = $table->find()->applyOptions(['withDeleted'])->where([$scope]);
        $max = $query->select([$left => $query->func()->max($left)])->first();
        return (empty($max->{$left}))? 0 : (int) $max->{$left};
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
     */
    protected function _getMax(Table $table, $scope, $right)
    {
        $query = $table->find()->applyOptions(['withDeleted'])->where([$scope]);
        $min = $query->select([$right => $query->func()->min($right)])->first();
        return (empty($min->{$right}))? 0 : (int) $min->{$right};
    }

    /**
     * クレジットを取得する
     * @return mixed|null
     * @checked
     * @noTodo
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
            if(Configure::read('BcLinks.specialThanks')) {
                $json = file_get_contents(Configure::read('BcLinks.specialThanks'), true);
            } else {
                throw new BcException(__d('baser', 'スペシャルサンクスのデータが読み込めませんでした。'));
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
     * @return Simplezip|false
     * @checked
     * @noTodo
     */
    public function createLogZip()
    {
        set_time_limit(0);
        $Folder = new Folder(LOGS);
        $files = $Folder->read(true, true, false);
        if (count($files[0]) === 0 && count($files[1]) === 0) {
            return false;
        }
        // ZIP圧縮して出力
        $simplezip = new Simplezip();
        $simplezip->addFolder(LOGS);
        return $simplezip;
    }

    /**
     * ログを削除する
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteLog()
    {
        if (file_exists($this->logPath)) {
            if (unlink($this->logPath)) {
                $messages[] = __d('baser', 'エラーログを削除しました。');
                return true;
            } else {
                $messages[] = __d('baser', 'エラーログが削除できませんでした。');
            }
        } else {
            $messages[] = __d('baser', 'エラーログが存在しません。');
        }
        throw new BcException(implode("\n", $messages));
    }

    public function backupDb()
    {

    }

    public function restoreDb()
    {

    }

    public function writeScheme()
    {

    }

    public function loadScheme()
    {

    }

}
