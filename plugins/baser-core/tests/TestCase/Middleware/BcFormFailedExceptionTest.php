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
        $result = $this->BcFormFailedException->getForm();
        $this->assertEquals($this->form, $result);
    }

    /**
     * Test __construct with message as a string
     */
    public function testConstructWithStringMessage(): void
    {
        $exception = $this->BcFormFailedException;

        $this->assertEquals('message', $exception->getMessage());
    }

    /**
     * Test __construct with message as an array
     */
    public function testConstructWithArrayMessage(): void
    {
        $this->form->setErrors([
            'field1' => ['This field is required'],
            'field2' => ['Invalid value']
        ]);

        $message = ['error occurred'];
        $expectedErrorMessages = 'field1.0: "This field is required", field2.0: "Invalid value"';

        $exception = new BcFormFailedException($this->form, $message);

        $expectedMessage = "Form error occurred failure. Found the following errors ($expectedErrorMessages).";

        $this->assertEquals($expectedMessage, $exception->getMessage());
    }

}
