<?php

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Error\BcFormFailedException;
use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\Form\ThemeFileForm;

class BcFormFailedExceptionTest extends BcTestCase
{
    protected $form;
    public function setUp(): void
    {
        parent::setUp();
        $this->form = new ThemeFileForm();
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
       $exception = new BcFormFailedException($this->form, 'message');
       $this->assertEquals($this->form, $exception->getForm());
    }
}
