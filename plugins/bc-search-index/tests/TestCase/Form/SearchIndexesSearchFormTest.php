<?php

namespace BcSearchIndex\Test\TestCase\Form;

use BaserCore\TestSuite\BcTestCase;
use BcSearchIndex\Form\SearchIndexesSearchForm;
use Cake\Form\Schema;

class SearchIndexesSearchFormTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->SearchIndexesSearchForm = new SearchIndexesSearchForm();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_buildSchema(): void
    {
        $schema = $this->execPrivateMethod($this->SearchIndexesSearchForm, '_buildSchema', [new Schema()]);
        $this->assertEquals('string', $schema->fieldType('type'));
        $this->assertEquals('string', $schema->fieldType('site_id'));
        $this->assertEquals('string', $schema->fieldType('folder_id'));
        $this->assertEquals('string', $schema->fieldType('keyword'));
        $this->assertEquals('string', $schema->fieldType('status'));
        $this->assertEquals('string', $schema->fieldType('priority'));
    }
}