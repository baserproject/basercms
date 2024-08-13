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
}
