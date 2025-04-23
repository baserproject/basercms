<?php

namespace BcMail\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcMail\View\Helper\BcMailBaserHelper;
use Cake\View\View;

class BcMailBaserHelperTest extends BcTestCase
{
    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcMailBaser = new BcMailBaserHelper(new View());
    }

    /**
     * tearDown
     *
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test methods
     */
    public function testMethods()
    {
        $methods = $this->BcMailBaser->methods();
        $this->assertEquals(['Mail', 'isMail'], $methods['isMail']);
        $this->assertEquals(['Mail', 'descriptionExists'], $methods['mailFormDescriptionExists']);
        $this->assertEquals(['Mail', 'description'], $methods['mailFormDescription']);
        $this->assertEquals(['Mail', 'thanksExists'], $methods['mailFormThanksExists']);
        $this->assertEquals(['Mail', 'thanks'], $methods['mailFormThanks']);
        $this->assertEquals(['Mail', 'unpublishExists'], $methods['mailFormUnpublishExists']);
        $this->assertEquals(['Mail', 'unpublish'], $methods['mailFormUnpublish']);
        $this->assertEquals(['Mailform', 'freeze'], $methods['freezeMailForm']);
        $this->assertEquals(['Mailform', 'create'], $methods['createMailForm']);
        $this->assertEquals(['Mailform', 'hidden'], $methods['mailFormHidden']);
        $this->assertEquals(['Mailform', 'authCaptcha'], $methods['mailFormAuthCaptcha']);
        $this->assertEquals(['Mailform', 'submit'], $methods['mailFormSubmit']);
        $this->assertEquals(['Mailform', 'end'], $methods['endMailForm']);
        $this->assertEquals(['Mailform', 'unlockField'], $methods['unlockMailFormField']);
        $this->assertEquals(['Mailform', 'getSourceValue'], $methods['getMailFormSourceValue']);
        $this->assertEquals(['Mailform', 'error'], $methods['mailFormError']);
        $this->assertEquals(['Mailform', 'control'], $methods['mailFormControl']);
        $this->assertEquals(['Mailform', 'getGroupValidErrors'], $methods['getMailFormGroupValidErrors']);
        $this->assertEquals(['Mailform', 'isGroupLastField'], $methods['isMailFormGroupLastField']);
        $this->assertEquals(['Mailform', 'label'], $methods['mailFormLabel']);
    }
}
