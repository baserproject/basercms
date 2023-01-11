<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * BlogPosts seed.
 */
class BlogPostsSeed extends AbstractSeed
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
                'id' => '1',
                'blog_content_id' => '1',
                'no' => '1',
                'name' => NULL,
                'title' => 'メールフォーム機能について説明します',
                'content' => '',
                'detail' => '<p>baserCMSのメールフォームでは、管理画面上にて入力項目を自由に変更することができ、受信したメールを管理画面で閲覧することができます。</p>

<h3>入力項目の変更</h3>

<p>メールフォームの各入力項目をフィールドと呼びます。フィールドを削除したり新しく追加するには、まず、管理画面より、[お問い合わせ] &rarr; [フィールド] と移動し、登録されているフィールドを確認しましょう。その画面よりフィールドの新規登録や変更、削除が行えます。</p>

<h3>受信メールの確認</h3>

<p>管理画面より、[お問い合わせ] &rarr; [受信メール] と移動すると、受信したメールを一覧で確認できます。データベースに受信したメールを保存しない場合は、[お問い合わせ] &rarr; [設定] &rarr; [詳細設定] より、[送信情報をデータベースに保存しない] にチェックを入れて保存します。</p>',
                'blog_category_id' => '1',
                'user_id' => '1',
                'status' => '1',
                'posted' => '2022-10-01 09:00:00',
                'content_draft' => '',
                'detail_draft' => '',
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'exclude_search' => '0',
                'eye_catch' => '2016/08/00000001_eye_catch.jpg',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => '2',
                'blog_content_id' => '1',
                'no' => '2',
                'name' => NULL,
                'title' => 'ブログ機能について説明します',
                'content' => '<p>この文章はブログ記事の [概要] 欄に入力されています。ブログ記事の一覧にて概要だけを表示する場合に利用しますが、テーマの構成上で利用しない場合は、各ブログの [設定] より、 [概要] 欄を利用しないようにする事もできます。ちなみにこのサンプルテーマではブログ記事一覧において概要を利用していません。</p>',
                'detail' => '<p>ここからは、ブログ記事の [本文] 欄に入力されている文章となります。</p>

<h3>カテゴリ・タグ機能</h3>

<p>baserCMSでのカテゴリとタグは少し仕様が違います。一つの記事は複数のタグを付けることができますが、複数のカテゴリに属すことはできません。また、タグは全ブログ共通ですが、カテゴリは各ブログごとに分けて作ることができます。</p>

<p>なお、タグやカテゴリを利用するにはテーマ側が対応している必要があります。このサンプルテーマでは、タグの利用を想定していません。</p>

<h3>ブログコメント機能</h3>

<p>ブログの各記事には一般ユーザーがコメントを付ける機能がありますが、利用しない場合は、各ブログの [設定] 画面より簡単に非表示にすることができます。</p>',
                'blog_category_id' => '1',
                'user_id' => '1',
                'status' => '1',
                'posted' => '2022-10-01 09:00:00',
                'content_draft' => '',
                'detail_draft' => '',
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'exclude_search' => '0',
                'eye_catch' => '2016/08/00000002_eye_catch.jpg',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
        ];

        $table = $this->table('blog_posts');
        $table->insert($data)->save();
    }
}
