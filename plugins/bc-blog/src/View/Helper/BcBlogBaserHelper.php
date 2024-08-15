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

namespace BcBlog\View\Helper;

use BaserCore\View\Helper\BcPluginBaserHelperInterface;
use Cake\View\Helper;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BlogBaserヘルパー
 *
 * BcBaserHelper より透過的に呼び出される
 *
 * 《利用例》
 * $this->BcBaser->blogPosts('news')
 *
 * BcBaserHeleper へのインターフェイスを提供する役割だけとし、
 * 実装をできるだけこのクラスで持たないようにし、BlogHelper 等で実装する
 *
 * @property BlogHelper $Blog
 */
#[\AllowDynamicProperties]
class BcBlogBaserHelper extends Helper implements BcPluginBaserHelperInterface
{

    /**
     * ヘルパー
     * @var array
     */
    public array $helpers = [
        'BcBlog.Blog'
    ];

    /**
     * メソッド一覧取得
     *
     * @return array[]
     * @checked
     * @noTodo
     * @unitTest
     */
    public function methods(): array
    {
        return [
            'blogPosts' => ['Blog', 'posts'],
            'getBlogPosts' => ['Blog', 'getPosts'],
            'isBlogCategory' => ['Blog', 'isCategory'],
            'isBlogTag' => ['Blog', 'isTag'],
            'isBlogDate' => ['Blog', 'isDate'],
            'isBlogMonth' => ['Blog', 'isMonth'],
            'isBlogYear' => ['Blog', 'isYear'],
            'isBlogSingle' => ['Blog', 'isSingle'],
            'isBlogHome' => ['Blog', 'isHome'],
            'getBlogs' => ['Blog', 'getContents'],
            'isBlog' => ['Blog', 'isBlog'],
            'getBlogCategories' => ['Blog', 'getCategories'],
            'hasChildBlogCategory' => ['Blog', 'hasChildCategory'],
            'getBlogTagList' => ['Blog', 'getTagList'],
            'blogTagList' => ['Blog', 'tagList'],
            'getBlogContentsUrl' => ['Blog', 'getContentsUrl'],
            'getBlogPostCount' => ['Blog', 'getPostCount'],
            'getBlogTitle' => ['Blog', 'getTitle'],
            'getBlogPostLinkUrl' => ['Blog', 'getPostLinkUrl'],
            'blogPostEyeCatch' => ['Blog', 'eyeCatch'],
            'blogPostDate' => ['Blog', 'postDate'],
            'blogPostTitle' => ['Blog', 'postTitle'],
            'blogPostCategory' => ['Blog', 'category'],
            'blogPostContent' => ['Blog', 'postContent'],
            'blogDescriptionExists' => ['Blog', 'descriptionExists'],
            'blogDescription' => ['Blog', 'description'],
            'getBlogPostContent' => ['Blog', 'getPostContent'],
            'blogPostPrevLink' => ['Blog', 'prevLink'],
            'blogPostNextLink' => ['Blog', 'nextLink'],
        ];
    }

}
