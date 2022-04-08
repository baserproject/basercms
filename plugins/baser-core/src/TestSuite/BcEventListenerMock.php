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

namespace BaserCore\TestSuite;

use Cake\Event\EventListenerInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcEventListenerMock
 */
class BcEventListenerMock implements EventListenerInterface
{

    /**
     * Events
     * @var array
     */
	public $events = [];

	/**
	 * BcEventListenerMock constructor.
	 * @param $events
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function __construct($events)
	{
		$this->events = $events;
	}

	/**
     * implementedEvents
	 * @return array
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function implementedEvents(): array
	{
		return $this->events;
	}

}
