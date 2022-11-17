<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * MailContents seed.
 */
class MailContentsSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'description' => '<p>このコンテンツはメールフォーム機能により作られており、この文章については管理画面の [お問い合わせ] &rarr; [設定] より更新ができます。また、メールフォームは [コンテンツ管理] よりいくつでも作成することができます。</p>',
                'sender_1' => '',
                'sender_2' => '',
                'sender_name' => 'baserCMSサンプル',
                'subject_user' => '【baserCMS】お問い合わせ頂きありがとうございます。',
                'subject_admin' => '【baserCMS】お問い合わせを受け付けました',
                'form_template' => 'default',
                'mail_template' => 'mail_default',
                'redirect_url' => '/',
                'auth_captcha' => 1,
                'widget_area' => NULL,
                'ssl_on' => 0,
                'save_info' => 1,
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'created' => '2016-08-07 23:10:38',
                'modified' => '2020-09-14 19:37:11',
            ],
            [
                'id' => 2,
                'description' => NULL,
                'sender_1' => NULL,
                'sender_2' => NULL,
                'sender_name' => '送信先名を入力してください',
                'subject_user' => 'お問い合わせ頂きありがとうございます',
                'subject_admin' => 'お問い合わせを頂きました',
                'form_template' => 'default',
                'mail_template' => 'mail_default',
                'redirect_url' => NULL,
                'auth_captcha' => 0,
                'widget_area' => NULL,
                'ssl_on' => 0,
                'save_info' => 1,
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'created' => '2020-12-14 14:27:12',
                'modified' => '2020-12-14 14:27:12',
            ],
            [
                'id' => 4,
                'description' => NULL,
                'sender_1' => NULL,
                'sender_2' => NULL,
                'sender_name' => '送信先名を入力してください',
                'subject_user' => 'お問い合わせ頂きありがとうございます',
                'subject_admin' => 'お問い合わせを頂きました',
                'form_template' => 'default',
                'mail_template' => 'mail_default',
                'redirect_url' => NULL,
                'auth_captcha' => 0,
                'widget_area' => NULL,
                'ssl_on' => 0,
                'save_info' => 1,
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 5,
                'description' => NULL,
                'sender_1' => NULL,
                'sender_2' => NULL,
                'sender_name' => '送信先名を入力してください',
                'subject_user' => 'お問い合わせ頂きありがとうございます',
                'subject_admin' => 'お問い合わせを頂きました',
                'form_template' => 'default',
                'mail_template' => 'mail_default',
                'redirect_url' => NULL,
                'auth_captcha' => 0,
                'widget_area' => NULL,
                'ssl_on' => 0,
                'save_info' => 1,
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 6,
                'description' => NULL,
                'sender_1' => NULL,
                'sender_2' => NULL,
                'sender_name' => '送信先名を入力してください',
                'subject_user' => 'お問い合わせ頂きありがとうございます',
                'subject_admin' => 'お問い合わせを頂きました',
                'form_template' => 'default',
                'mail_template' => 'mail_default',
                'redirect_url' => NULL,
                'auth_captcha' => 0,
                'widget_area' => NULL,
                'ssl_on' => 0,
                'save_info' => 1,
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 7,
                'description' => NULL,
                'sender_1' => NULL,
                'sender_2' => NULL,
                'sender_name' => '送信先名を入力してください',
                'subject_user' => 'お問い合わせ頂きありがとうございます',
                'subject_admin' => 'お問い合わせを頂きました',
                'form_template' => 'default',
                'mail_template' => 'mail_default',
                'redirect_url' => NULL,
                'auth_captcha' => 0,
                'widget_area' => NULL,
                'ssl_on' => 0,
                'save_info' => 1,
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'created' => '2022-11-17 11:01:47',
                'modified' => '2022-11-17 11:01:47',
            ],
        ];

        $table = $this->table('mail_contents');
        $table->insert($data)->save();
    }
}
