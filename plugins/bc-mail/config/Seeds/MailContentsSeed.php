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
    public function run(): void
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
                'widget_area' => NULL,
                'ssl_on' => 0,
                'save_info' => 1,
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'created' => '2016-08-07 23:10:38',
                'modified' => '2020-09-14 19:37:11',
            ]
        ];

        $table = $this->table('mail_contents');
        $table->insert($data)->save();
    }
}
