<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\OAuth2\Validator;

use BcMcp\OAuth2\Validator\RedirectUriRegistrationValidator;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;

/**
 * RedirectUriRegistrationValidator Test Case
 */
class RedirectUriRegistrationValidatorTest extends TestCase
{

    /**
     * 許容される redirect_uri
     *
     * @return void
     */
    public function testValidateAllowsHttpsAndLoopback(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $validator->validate([
            'https://example.com/callback',
            'http://127.0.0.1:3000/callback',
            'http://[::1]:3000/callback',
            'http://localhost/callback',
        ]);
        $this->assertTrue(true, '例外が投げられないこと');
    }

    /**
     * 非ループバックの http は拒否する
     *
     * @return void
     */
    public function testValidateRejectsNonLoopbackHttp(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate(['http://example.com/callback']);
    }

    /**
     * フラグメント付きは拒否する
     *
     * @return void
     */
    public function testValidateRejectsFragment(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate(['https://example.com/callback#top']);
    }

    /**
     * 空の配列は拒否する
     *
     * @return void
     */
    public function testValidateRejectsEmptyList(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate([]);
    }

    /**
     * トップレベルが配列でない場合は拒否する（TypeError による 500 化の回帰テスト）
     *
     * @return void
     */
    public function testValidateRejectsNonArray(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate('not-an-array');
    }

    /**
     * 上限を超える件数は拒否する
     *
     * @return void
     */
    public function testValidateRejectsTooManyUris(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $uris = [];
        for ($i = 0; $i <= RedirectUriRegistrationValidator::MAX_URIS; $i++) {
            $uris[] = 'https://example.com/callback' . $i;
        }
        $this->expectException(InvalidArgumentException::class);
        $validator->validate($uris);
    }
}
