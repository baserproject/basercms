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

namespace BaserCore\Test\TestCase\Model\Table\Exception;

use BaserCore\Model\Table\Exception\CopyFailedException;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class CopyFailedExceptionTest
 */
class CopyFailedExceptionTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var CopyFailedException
     */
    public $CopyFailedException;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.UserGroups',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CopyFailedException = new CopyFailedException();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->CopyFailedException);
        parent::tearDown();
    }

    /**
     * Test setErrors and getErrors
     */
    public function testSetErrorsAndGetErrors()
    {
        $this->CopyFailedException->setErrors(['testerror1', 'testerror2']);
        $errors = $this->CopyFailedException->getErrors();
        $this->assertEquals(['testerror1', 'testerror2'], $errors);

        $this->CopyFailedException->setErrors(null);
        $errors = $this->CopyFailedException->getErrors();
        $this->assertEquals(null, $errors);
    }

}
