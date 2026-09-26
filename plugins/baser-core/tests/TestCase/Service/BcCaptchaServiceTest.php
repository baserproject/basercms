<?php

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\BcCaptchaService;
use BaserCore\TestSuite\BcTestCase;
use Cake\Http\ServerRequest;
use Cake\Http\Session;

class BcCaptchaServiceTest extends BcTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->BcCaptchaService = new BcCaptchaService();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test check
     * @param $sessionValue
     * @param $inputValue
     * @param $expected
     * @dataProvider checkDataProvider
     */
    public function test_check($sessionValue, $inputValue, $expected)
    {
        $request = $this->createMock(ServerRequest::class);
        $session = $this->createMock(Session::class);

        $token = 'test_token';
        $key = 'captcha.' . $token;

        // Mock the session's read method
        $session->expects($this->once())
            ->method('read')
            ->with($key)
            ->willReturn($sessionValue);

        // Mock the request's getSession method
        $request->expects($this->once())
            ->method('getSession')
            ->willReturn($session);

        // Call the 'check' method
        $result = $this->BcCaptchaService->check($request, $token, $inputValue);

        // Assert the result
        $this->assertEquals($expected, $result);
    }

    public static function checkDataProvider()
    {
        return [
            ['correct_value', 'correct_value', true],
            ['correct_value', 'wrong_value', false],
            [null, 'some_value', false],
            ['some_value', '', false]
        ];
    }

    /**
     * test check - $consume=true で回答が消費され、同一セッションでの再利用（リプレイ）ができないこと
     */
    public function test_check_consume()
    {
        $request = $this->getRequest('/');
        $request->getSession()->write('captcha.token1', 'abcd');

        // 消費しない場合は繰り返し検証できる
        $this->assertTrue($this->BcCaptchaService->check($request, 'token1', 'abcd'));
        $this->assertTrue($this->BcCaptchaService->check($request, 'token1', 'abcd'));
        // 消費する場合は成功後にセッションから削除される
        $this->assertTrue($this->BcCaptchaService->check($request, 'token1', 'abcd', true));
        $this->assertNull($request->getSession()->read('captcha.token1'));
        $this->assertFalse($this->BcCaptchaService->check($request, 'token1', 'abcd', true));
        // 不一致・空の回答は消費されない
        $request->getSession()->write('captcha.token2', 'abcd');
        $this->assertFalse($this->BcCaptchaService->check($request, 'token2', 'wrong', true));
        $this->assertFalse($this->BcCaptchaService->check($request, 'token2', '', true));
        $this->assertEquals('abcd', $request->getSession()->read('captcha.token2'));
    }
}
