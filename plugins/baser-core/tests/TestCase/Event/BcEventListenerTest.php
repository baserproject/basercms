<?php

namespace BaserCore\Test\TestCase\Event;

use BaserCore\Event\BcEventListener;
use BaserCore\TestSuite\BcTestCase;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;

class BcEventListenerTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcEventListener = new BcEventListener();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test getAction
     *
     */
    public function testGetAction()
    {
        //create a new ServerRequest
        $request = new ServerRequest([
            'params' => [
                'controller' => 'BlogPosts',
                'action' => 'index'
            ]
        ]);

        // Set the request to the Router
        Router::setRequest($request);

        //when isContainController is true
        $result = $this->BcEventListener->getAction(true);
        $this->assertEquals('BlogPosts.Index', $result);

        // when isContainController is false
        $result = $this->BcEventListener->getAction(false);
        $this->assertEquals('Index', $result);
    }

    /**
     * Test isAction
     * @param $currentAction
     * @param $actionToCheck
     * @param $isContainController
     * @param $expected
     * @dataProvider isActionDataProvider
     */
    public function testIsAction($currentAction, $actionToCheck, $isContainController, $expected)
    {
        $this->BcEventListener = $this->getMockBuilder(BcEventListener::class)
            ->onlyMethods(['getAction'])
            ->getMock();

        $this->BcEventListener->method('getAction')
            ->with($isContainController)
            ->willReturn($currentAction);

        $result = $this->BcEventListener->isAction($actionToCheck, $isContainController);
        $this->assertEquals($expected, $result);
    }

    public static function isActionDataProvider()
    {
        return [
            ['Users.Index', 'Users.Index', true, true],
            ['Users.Index', 'Users.View', true, false],
            ['Users.Index', ['Users.View', 'Users.Index'], true, true],
            ['Users.Index', ['Users.View', 'Users.Edit'], true, false],
            ['Index', 'Index', false, true],
            ['Index', 'View', false, false],
            ['Users.Index', 'Users.Index', true, true],
        ];
    }
}
