<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Component;

use BaserCore\Error\BcException;
use Cake\Controller\Component;
use Cake\Controller\Component\FlashComponent;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcMessageComponent
 * 表示面へのメッセージをコントロールする為のコンポーネント
 * @package BaserCore\Controller\Component
 * @property FlashComponent $Flash
 */
class BcMessageComponent extends Component
{

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
            // TODO: DbLogの仕組み未実装
            // $AppModel = ClassRegistry::init('AppModel');
            // $AppModel->saveDblog($message);
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
     */
    public function setInfo($message, $log = false, $setFlash = true)
    {
        $this->set($message, false, $log, $setFlash, 'info-message');
    }

}
