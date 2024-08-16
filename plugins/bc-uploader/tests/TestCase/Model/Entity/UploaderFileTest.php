<?php

namespace BcUploader\Test\TestCase\Model\Entity;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFile;
use BcUploader\Model\Entity\UploaderFile;
use BcUploader\Test\Factory\UploaderFileFactory;
use BcUploader\Test\Scenario\UploaderFilesScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class UploaderFileTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * @var UploaderFile
     */
    public $UploaderFile;
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderFile = $this->getTableLocator()->get('BcUploader.UploaderFiles');
    }

    public function tearDown(): void
    {
        unset($this->UploaderFile);
        parent::tearDown();
    }

    /**
     * test filesExists
     */
    public function test_filesExists()
    {
        //データを生成
        UploaderFileFactory::make(['name' => 'social_new.jpg', 'publish_begin' => '2017-07-09 03:38:07'])->persist();

        //テストファイルを生成
        $file = new BcFile('/var/www/html/webroot/files/uploads/limited/social_new__small.jpg');
        $file->create();
        $file = new BcFile('/var/www/html/webroot/files/uploads/limited/social_new__midium.jpg');
        $file->create();
        $file = new BcFile('/var/www/html/webroot/files/uploads/limited/social_new__large.jpg');
        $file->create();

        //テスト対象メソッドを呼ぶ
        $entity = $this->UploaderFile->find()->where(['UploaderFiles.name' => "social_new.jpg"])->first();
        $rs = $entity->filesExists();
        $this->assertEquals(['small' => true, 'midium' => true, 'large' => true], $rs);

        //テストファイルを削除
        unlink('/var/www/html/webroot/files/uploads/limited/social_new__small.jpg');
        unlink('/var/www/html/webroot/files/uploads/limited/social_new__midium.jpg');
        unlink('/var/www/html/webroot/files/uploads/limited/social_new__large.jpg');
    }

    /**
     * test _getSmall
     */
    public function test_getSmall()
    {
        //データを生成
        UploaderFileFactory::make(['name' => 'social_new.jpg', 'publish_begin' => '2017-07-09 03:38:07'])->persist();

        //テストファイルを生成
        $file = new BcFile('/var/www/html/webroot/files/uploads/limited/social_new__small.jpg');
        $file->create();

        //テスト対象メソッドを呼ぶ
        $entity = $this->UploaderFile->find()->where(['UploaderFiles.name' => "social_new.jpg"])->first();
        $this->assertTrue($this->execPrivateMethod($entity, '_getSmall', []));

        //テストファイルを削除
        $file->delete();
    }

    /**
     * test _getMidium
     */
    public function test_getMedium()
    {
        //データを生成
        UploaderFileFactory::make(['name' => 'social_new.jpg', 'publish_begin' => '2017-07-09 03:38:07'])->persist();

        //テストファイルを生成
        $file = new BcFile('/var/www/html/webroot/files/uploads/limited/social_new__midium.jpg');
        $file->create();

        //テスト対象メソッドを呼ぶ
        $entity = $this->UploaderFile->find()->where(['UploaderFiles.name' => "social_new.jpg"])->first();
        $this->assertTrue($this->execPrivateMethod($entity, '_getMidium', []));

        //テストファイルを削除
        $file->delete();
    }

    /**
     * test _getLarge
     */
    public function test_getLarge()
    {
        //データを生成
        UploaderFileFactory::make(['name' => 'social_new.jpg', 'publish_begin' => '2017-07-09 03:38:07'])->persist();

        //テストファイルを生成
        $file = new BcFile('/var/www/html/webroot/files/uploads/limited/social_new__large.jpg');
        $file->create();

        //テスト対象メソッドを呼ぶ
        $entity = $this->UploaderFile->find()->where(['UploaderFiles.name' => "social_new.jpg"])->first();
        $this->assertTrue($this->execPrivateMethod($entity, '_getLarge', []));

        //テストファイルを削除
        $file->delete();
    }

    /**
     * test getFileNameBySize
     */
    public function test_getFileNameBySize()
    {
        $this->loadFixtureScenario(UploaderFilesScenario::class);
        $uploaderFile = $this->UploaderFile->get(1);

        $result = $this->execPrivateMethod($uploaderFile, 'getFileNameBySize', ['small']);
        $this->assertEquals('social_new__small.jpg', $result);

        $result = $this->execPrivateMethod($uploaderFile, 'getFileNameBySize', ['midium']);
        $this->assertEquals('social_new__midium.jpg', $result);

        $result = $this->execPrivateMethod($uploaderFile, 'getFileNameBySize', ['large']);
        $this->assertEquals('social_new__large.jpg', $result);

        $result = $this->execPrivateMethod($uploaderFile, 'getFileNameBySize', ['mobile_small']);
        $this->assertEquals('social_new__mobile_small.jpg', $result);
    }

    /**
     * test fileExists
     */
    public function test_filesExistsOnLimited()
    {
        //データを準備
        UploaderFileFactory::make(['name' => 'social_new.jpg', 'publish_begin' => '2017-07-09 03:38:07'])->persist();
        $file = new BcFile('/var/www/html/webroot/files/uploads/limited/social_new__small.jpg');
        $file->create();

        //テスト対象メソッドを呼ぶ
        $entity = $this->UploaderFile->find()->where(['UploaderFiles.name' => "social_new.jpg"])->first();

        //ファイルが存在している場合、
        $this->assertTrue($entity->fileExists('social_new__small.jpg'));

        //ファイルが存在しない場合、
        $this->assertFalse($entity->fileExists('social_new__large.jpg'));

        //テストファイルを削除
        unlink('/var/www/html/webroot/files/uploads/limited/social_new__small.jpg');
    }

    /**
     * test isLimited
     */
    public function test_isLimited()
    {
        //isLimited true
        $uploaderFile = new UploaderFile([
            'publish_begin' => '2021-01-01 00:00:00',
            'publish_end' => '2021-12-31 23:59:59',
        ]);
        $this->assertTrue($uploaderFile->isLimited());

        //isLimited false
        $uploaderFile = new UploaderFile([
            'publish_begin' => null,
            'publish_end' => null,
        ]);
        $this->assertFalse($uploaderFile->isLimited());
    }

}
