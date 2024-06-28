<?php

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BaserCoreBaserHelper;
use Cake\View\View;

class BaserCoreBaserHelperTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BaserCoreBaserHelper = new BaserCoreBaserHelper(new View());
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_methods()
    {
        $methods = $this->BaserCoreBaserHelper->methods();
        $this->assertEquals(['BcContents', 'getParent'], $methods['getParentContent']);
        $this->assertEquals(['BcForm', 'create'], $methods['createForm']);
        $this->assertEquals(['BcForm', 'control'], $methods['formControl']);
        $this->assertEquals(['BcForm', 'hidden'], $methods['formHidden']);
        $this->assertEquals(['BcForm', 'submit'], $methods['formSubmit']);
        $this->assertEquals(['BcForm', 'error'], $methods['formError']);
        $this->assertEquals(['BcForm', 'label'], $methods['formLabel']);
        $this->assertEquals(['BcForm', 'end'], $methods['endForm']);
        $this->assertEquals(['Html', 'scriptStart'], $methods['scriptStart']);
        $this->assertEquals(['Html', 'scriptEnd'], $methods['scriptEnd']);
        $this->assertEquals(['Html', 'meta'], $methods['meta']);
        $this->assertEquals(['BcUpload', 'setTable'], $methods['setTableToUpload']);
        $this->assertEquals(['Text', 'truncate'], $methods['truncateText']);
        $this->assertEquals(['BcContents', 'getCurrentSite'], $methods['getCurrentSite']);
        $this->assertEquals(['BcContents', 'getCurrentContent'], $methods['getCurrentContent']);
    }

}