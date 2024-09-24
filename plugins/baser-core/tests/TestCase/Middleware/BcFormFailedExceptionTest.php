<?php

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Error\BcFormFailedException;
use BaserCore\TestSuite\BcTestCase;
use Cake\ORM\Table;
use Cake\Validation\ValidatorAwareInterface;
use PHPUnit\Framework\MockObject\Exception;

class BcFormFailedExceptionTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }


    /**
     * Test getForm
     */
    public function testGetForm(): void
    {
       $table = new Table();
       $exception = new BcFormFailedException($table, 'message');
       $this->assertEquals($table, $exception->getForm());
    }

}
