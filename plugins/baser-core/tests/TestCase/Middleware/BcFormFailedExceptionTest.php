<?php

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Error\BcFormFailedException;
use BaserCore\TestSuite\BcTestCase;
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
     * @throws Exception
     */
    public function testGetForm(): void
    {
        $mockForm = $this->createMock(ValidatorAwareInterface::class);
        $exception = new BcFormFailedException($mockForm, 'Test message');

        $this->assertSame($mockForm, $exception->getForm());
    }

}
