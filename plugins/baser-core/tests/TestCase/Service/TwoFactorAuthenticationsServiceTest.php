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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\TwoFactorAuthenticationsService;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\EmailTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class UsersServiceTest
 */
class TwoFactorAuthenticationsServiceTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use EmailTrait;

    /**
     * @var TwoFactorAuthenticationsService
     */
    public $TwoFactorAuthenticationsService;

    /**
     * @var TwoFactorAuthenticationsTable
     */
    public $TwoFactorAuthentications;

    /**
     * @var mixed
     */
    private $originalAllowTime;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->originalAllowTime = Configure::read('BcApp.twoFactorAuthenticationCodeAllowTime');
        Configure::write('BcApp.twoFactorAuthenticationCodeAllowTime', 10);
        $this->TwoFactorAuthenticationsService = new TwoFactorAuthenticationsService();
        $this->TwoFactorAuthentications = TableRegistry::getTableLocator()->get('BaserCore.TwoFactorAuthentications');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        Configure::write('BcApp.twoFactorAuthenticationCodeAllowTime', $this->originalAllowTime);
        unset($this->originalAllowTime);
        unset($this->TwoFactorAuthenticationsService);
        unset($this->TwoFactorAuthentications);
        parent::tearDown();
    }

    /**
     * Test construct
     */
    public function testConstruct()
    {
        $this->assertTrue(isset($this->TwoFactorAuthentications));
    }

    /**
     * Test send
     */
    public function testSend()
    {
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfigsService->setValue('email', 'from@example.com');

        $this->assertEquals(0, $this->TwoFactorAuthentications->find()->count());

        // 送信
        $this->TwoFactorAuthenticationsService->send(1, 'test@example.com');
        $count = $this->TwoFactorAuthentications->find()
            ->where(['user_id' => 1])
            ->where(['is_verified' => 0])
            ->count();
        $this->assertEquals(1, $count);
        $this->assertMailSentTo('test@example.com');

        // 別のユーザーに送信
        $this->TwoFactorAuthenticationsService->send(5, 'test2@example.com');
        $count = $this->TwoFactorAuthentications->find()
            ->where(['user_id' => 5])
            ->where(['is_verified' => 0])
            ->count();
        $this->assertEquals(1, $count);
        $this->assertMailSentTo('test2@example.com');

        // 同一ユーザーに複数回送信した場合はデータ上書き
        $this->TwoFactorAuthenticationsService->send(5, 'test2@example.com');
        $count = $this->TwoFactorAuthentications->find()
            ->where(['user_id' => 5])
            ->where(['is_verified' => 0])
            ->count();
        $this->assertEquals(1, $count);
        $this->assertMailSentTo('test2@example.com');

        $this->assertEquals(2, $this->TwoFactorAuthentications->find()->count());

        // コードは6桁の数字
        $twoFactorAuthentication = $this->TwoFactorAuthentications->find()
            ->first();
        $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $twoFactorAuthentication->code);
    }

    /**
     * 認証コードの再送信はクールダウン中は拒否する（総当たり対策）
     */
    public function test_send_cooldown()
    {
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfigsService->setValue('email', 'from@example.com');

        // クールダウンを強制して初回送信
        $this->TwoFactorAuthenticationsService->send(1, 'test@example.com', true);
        // 直後の再送信はクールダウンで例外
        $this->expectException(\BaserCore\Error\BcException::class);
        $this->TwoFactorAuthenticationsService->send(1, 'test@example.com', true);
    }

    /**
     * 初回のクールダウンを強制しない送信では例外は発生しない（初回コード送信の想定）
     */
    public function test_send_withoutCooldownEnforcement()
    {
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfigsService->setValue('email', 'from@example.com');

        $this->TwoFactorAuthenticationsService->send(1, 'test@example.com');
        // クールダウンを強制しなければ連続送信できる
        $this->TwoFactorAuthenticationsService->send(1, 'test@example.com');
        $this->assertEquals(1, $this->TwoFactorAuthentications->find()->where(['user_id' => 1])->count());
    }

    /**
     * 認証コード検証に失敗すると、そのユーザーの有効なコードを無効化する（総当たり対策）
     */
    public function test_verify_invalidatesCodeOnFailure()
    {
        $this->TwoFactorAuthentications->save($this->TwoFactorAuthentications->newEntity([
            'user_id' => 1, 'code' => '123456', 'is_verified' => 0,
        ]));
        // 間違ったコードで検証すると false
        $this->assertFalse($this->TwoFactorAuthenticationsService->verify(1, '000000'));
        // 失敗後は正しいコードでも通らない（無効化されている）
        $this->assertFalse($this->TwoFactorAuthenticationsService->verify(1, '123456'));
    }

    /**
     * Test verify
     *
     * @dataProvider getTestVerifyProvider
     */
    public function testVerify($expected, $saveData, $verifyData)
    {
        $allowTime = Configure::read('BcApp.twoFactorAuthenticationCodeAllowTime');
        Configure::write('BcApp.twoFactorAuthenticationCodeAllowTime', 10);
        try {
            $this->TwoFactorAuthentications->save($this->TwoFactorAuthentications->newEntity($saveData));
            $result = $this->TwoFactorAuthenticationsService->verify($verifyData['user_id'], $verifyData['code']);
            $this->assertEquals($expected, $result);
        } finally {
            Configure::write('BcApp.twoFactorAuthenticationCodeAllowTime', $allowTime);
        }
    }

    public static function getTestVerifyProvider()
    {
        return [
            [
                true, [
                    'user_id' => 1,
                    'code' => '123456',
                    'is_verified' => 0,
                ], [
                    'user_id' => 1,
                    'code' => '123456',
                ]
            // 認証済み
            ], [
                false, [
                    'user_id' => 1,
                    'code' => '123456',
                    'is_verified' => 1,
                ], [
                    'user_id' => 1,
                    'code' => '123456',
                ]
            // 異なるコード
            ], [
                false, [
                    'user_id' => 1,
                    'code' => '123456',
                    'is_verified' => 0,
                ], [
                    'user_id' => 1,
                    'code' => '1234',
                ]
            // 異なるユーザーID
            ], [
                false, [
                    'user_id' => 1,
                    'code' => '123456',
                    'is_verified' => 0,
                ], [
                    'user_id' => 2,
                    'code' => '123456',
                ]
            // 有効期限切れ
            ], [
                false, [
                    'user_id' => 1,
                    'code' => '123456',
                    'is_verified' => 0,
                    'modified' => date('Y-m-d H:i:s', strtotime('-1 year'))
                ], [
                    'user_id' => 1,
                    'code' => '123456',
                ],
            ],
        ];
    }
}
