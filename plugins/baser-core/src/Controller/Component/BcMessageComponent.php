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

namespace BaserCore\Controller\Component;

use BaserCore\Service\DblogsService;
use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Controller\Component;
use Cake\Controller\Component\FlashComponent;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcMessageComponent
 * 表示面へのメッセージをコントロールする為のコンポーネント
 * @property FlashComponent $Flash
 */
class BcMessageComponent extends Component
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * @var array
     */
    public $components = ['Flash'];

    /**
     * メッセージをセットする
     *
     * @param string $message メッセージ
     * @param bool $alert 警告かどうか
     * @param bool $saveDblog Dblogに保存するか
     * @param bool $setFlash flash message に保存するか
     * @param null|string $class 付与するクラス名
     * @checked
     * @unitTest
     * @noTodo
     */
    public function set($message, $alert = false, $saveDblog = false, $setFlash = true, $class = null)
    {
        if (!$class) {
            $class = 'notice-message';
            if ($alert) {
                $class = 'alert-message';
            }
        }
        if ($setFlash) {
            $this->Flash->set($message, [
                'element' => 'default',
                'params' => ['class' => $class]
            ]);
        }

        if ($saveDblog) {
            try {
                /** @var DblogsService $dblogs */
                $dblogs = $this->getService(DblogsServiceInterface::class);
                $dblogs->create(['message' => $message]);
            } catch (\Exception $e) {
                $this->Flash->set(__d('baser_core', 'DBログの保存に失敗しました。'), [
                    'element' => 'default',
                    'params' => ['class' => 'alert-message']
                ]);
            }
        }
    }

    /**
     * 成功メッセージをセットする
     *
     * @param string $message
     * @param bool $log DBログに保存するかどうか（初期値 : true）
     * @param bool $setFlash
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setSuccess($message, $log = true, $setFlash = true)
    {
        $this->set($message, false, $log, $setFlash);
    }

    /**
     * 失敗メッセージをセットする
     *
     * @param string $message メッセージ
     * @param bool $log DBログに保存するかどうか（初期値 : false）
     * @param bool $setFlash フラッシュメッセージにセットするかどうか
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setError($message, $log = false, $setFlash = true)
    {
        $this->set($message, true, $log, $setFlash, 'alert-message');
    }

    /**
     * 警告メッセージをセットする
     *
     * @param string $message メッセージ
     * @param bool $log DBログに保存するかどうか（初期値 : false）
     * @param bool $setFlash フラッシュメッセージにセットするかどうか
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setWarning($message, $log = false, $setFlash = true)
    {
        $this->set($message, true, $log, $setFlash, 'warning-message');
    }

    /**
     * インフォメーションメッセージをセットする
     *
     * @param string $message メッセージ
     * @param bool $log DBログに保存するかどうか（初期値 : false）
     * @param bool $setFlash フラッシュメッセージにセットするかどうか
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setInfo($message, $log = false, $setFlash = true)
    {
        $this->set($message, false, $log, $setFlash, 'info-message');
    }

}
