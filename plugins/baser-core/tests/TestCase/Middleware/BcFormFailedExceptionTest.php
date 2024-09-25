<?php

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Error\BcFormFailedException;
use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\Form\ThemeFileForm;

class BcFormFailedExceptionTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->form = new ThemeFileForm();
        $this->BcFormFailedException = new BcFormFailedException($this->form, 'message');
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
        $exception = $this->BcFormFailedException->getForm();
        $this->assertEquals($this->form, $exception);
    }
}
