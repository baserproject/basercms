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

namespace BaserCore\Event;

use BaserCore\Utility\BcContainer;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcContainerEventListener
 * @package BaserCore\Event
 */
class BcContainerEventListener implements EventListenerInterface
{
    /**
     * implementedEvents
     * @return \string[][]
     * @checked
     * @unitTest
     * @noTodo
     */
    public function implementedEvents(): array
    {
        return [
            'Application.buildContainer' => ['callable' => 'buildContainer']
        ];
    }

    /**
     * コンテナ作成時イベント
     *
     * CakePHP4系にて、コンテナの利用対象がコントローラーのみとなっているため、
     * コンテナをシングルトンとしてヘルパなどで利用できるようにしている
     * 利用時は、BaserCore\Utility\BcContainerTrait を実装することで getService() にて
     * インターフェイスを指定して取得できる
     * 例）$this->getService(UsersServiceInterface::class);
     * @param Event $event
     * @checked
     * @unitTest
     * @noTodo
     */
    public function buildContainer(Event $event)
    {
        $container = $event->getData('container');
        BcContainer::set($container);
    }

}


