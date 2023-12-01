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

use BaserCore\Error\BcException;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Model\Entity\Content;
use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\SitesService;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcText;
use BaserCore\Utility\BcUtil;
use BaserCore\View\Helper\BcBaserHelper;
use BaserCore\View\Helper\BcContentsHelper;
use BaserCore\View\Helper\BcTimeHelper;
use BaserCore\View\Helper\BcUploadHelper;
use BcBlog\Model\Entity\BlogPost;
use BcBlog\Model\Entity\BlogTag;
use BcBlog\Model\Table\BlogCategoriesTable;
use BcBlog\Model\Table\BlogPostsTable;
use BcBlog\Service\BlogContentsService;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Service\BlogTagsService;
use BcBlog\Service\BlogTagsServiceInterface;
use BcBlog\Service\Front\BlogFrontService;
use BcBlog\Service\Front\BlogFrontServiceInterface;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\View\View;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * ブログヘルパー
 * @property BcTimeHelper $BcTime BcTimeヘルパ
 * @property BcBaserHelper $BcBaser BcBaserヘルパ
 * @property BcUploadHelper $BcUpload BcUploadヘルパ
 * @property BcContentsHelper $BcContents BcContentsヘルパ
 * @property Helper\HtmlHelper $Html
 */
class BlogHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use BcEventDispatcherTrait;

    /**
     * ヘルパー
     *
     * @var array
     */
    public $helpers = [
        'Html',
        'Url',
        'BaserCore.BcTime',
        'BaserCore.BcBaser',
        'BaserCore.BcUpload',
        'BaserCore.BcContents'
    ];

    /**
     * ブログカテゴリモデル
     *
     * @var BlogCategory
     */
    public $BlogCategory = null;

    /**
     * ブログコンテンツサービス
     * @var BlogContentsServiceInterface
     */
    public $BlogContentsService = null;

    /**
     * コンテンツ
     *
     * @var array
     */
    public $content = null;

    /**
     * コンストラクタ
     *
     * @param View $View Viewオブジェクト
     * @param array $settings 設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct(View $view, array $config = [])
    {
        parent::__construct($view, $config);
        // インストールが完了している場合のみ実行
        // インストール時に呼び出された際にサービスが利用できないため
        if(BcUtil::isInstalled() && $view->getName() !== 'Error') {
            $this->BlogContentsService = $this->getService(BlogContentsServiceInterface::class);
            $this->setContent();
        }
    }

    /**
     * ブログコンテンツデータをセットする
     *
     * アイキャッチを利用する場合に必ず設定が必要
     *
     * @param int $blogContentId ブログコンテンツID
     * @return void
     * @checked
     * @noTodo
     */
    public function setContent($blogContentId = null)
    {
        if($this->currentBlogContent) {
            if(is_null($blogContentId)) return;
            if($blogContentId === $this->currentBlogContent->id) return;
        }

        if($blogContentId) {
            if(!$this->BlogContentsService) return;
            try {
                $this->currentBlogContent = $this->BlogContentsService->get($blogContentId);
            } catch(RecordNotFoundException) {
                $this->currentBlogContent = null;
                $this->currentContent = null;
                return;
            } catch(\Throwable $e) {
                throw $e;
            }
            $contentTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
            // 現在のサイトにエイリアスが存在するのであればそちらを優先する
            $site = $this->_View->getRequest()->getAttribute('currentSite');
            $content = null;
            if (!empty($site->id)) {
                $content = $contentTable->find()->where([
                    'Contents.entity_id' => $this->currentBlogContent->id,
                    'Contents.type' => 'BlogContent',
                    'Contents.alias_id IS NOT' => null,
                    'Contents.site_id' => $site->id
                ])->first();
            }
            if(!$content) {
                $content = $contentTable->find()->where([
                    'Contents.entity_id' => $this->currentBlogContent->id,
                    'Contents.type' => 'BlogContent',
                    'Contents.alias_id IS' => null,
                ])->first();
            }
            $this->currentContent = $content;
        } else {
            if ($this->getView()->get('blogContent')) {
                $this->currentBlogContent = $this->getView()->get('blogContent');
                $this->currentContent = $this->currentBlogContent->content;
            }
        }

        if($this->currentBlogContent?->id) {
            /* @var BlogPostsTable $blogPostTable */
            $blogPostTable = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
            $blogPostTable->setupUpload($this->currentBlogContent->id);
        }
    }

    /**
     * ブログIDを出力する
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function currentBlogId()
    {
        echo $this->getCurrentBlogId();
    }

    /**
     * ブログIDを取得する
     *
     * @return integer
     * @checked
     * @noTodo
     */
    public function getCurrentBlogId()
    {
        return $this->currentBlogContent->id;
    }

    /**
     * ブログのコンテンツ名を出力する
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function blogName()
    {
        echo $this->getBlogName();
    }

    /**
     * ブログのコンテンツ名を取得する
     *
     * @return string
     * @checked
     * @noTodo
     */
    public function getBlogName()
    {
        return $this->currentContent->name;
    }

    /**
     * ブログタイトルを出力する
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function title()
    {
        echo $this->getTitle();
    }

    /**
     * タイトルを取得する
     *
     * @return string
     * @checked
     * @noTodo
     */
    public function getTitle()
    {
        return $this->currentContent->title;
    }

    /**
     * ブログの説明文を取得する
     *
     * @return string
     * @checked
     * @noTodo
     */
    public function getDescription()
    {
        return $this->currentBlogContent->description;
    }

    /**
     * ブログの説明文を出力する
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function description()
    {
        echo $this->getDescription();
    }

    /**
     * ブログの説明文が指定されているかどうかを判定する
     *
     * @return boolean
     * @checked
     * @noTodo
     */
    public function descriptionExists()
    {
        if (!empty($this->currentBlogContent->description)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 記事のタイトルを出力する
     *
     * @param BlogPost $post ブログ記事データ
     * @param boolean $link 詳細ページへのリンクをつける場合には、true を指定する（初期値 : true）
     * @return void
     * @checked
     * @noTodo
     */
    public function postTitle(BlogPost $post, bool $link = true, array $options = []): void
    {
        echo $this->getPostTitle($post, $link, $options);
    }

    /**
     * 記事タイトルを取得する
     *
     * @param BlogPost $post ブログ記事データ
     * @param boolean $link 詳細ページへのリンクをつける場合には、true を指定する（初期値 : true）
     * @param array $options オプション（初期値：arary()）
     *    - `escape` : エスケープ処理を行うかどうか
     *    ※ その他のオプションについては、HtmlHelper::link() を参照
     * @return string 記事タイトル
     * @checked
     * @noTodo
     */
    public function getPostTitle($post, $link = true, $options = [])
    {
        $options = array_merge([
            'escape' => true
        ], $options);
        $title = $post->title;
        if ($link) {
            $title = $this->getPostLink($post, $title, $options);
        } else {
            if (!empty($options['escape'])) {
                $title = h($title);
            }
        }
        return $title;
    }

    /**
     * 記事へのリンクを取得する
     *
     * @param BlogPost $post ブログ記事データ
     * @param string $title タイトル
     * @param array $options オプション（初期値 : array()）
     *    ※ オプションについては、 HtmlHelper::link() を参照
     * @return string 記事へのリンク
     * @checked
     * @noTodo
     */
    public function getPostLink($post, $title, $options = [])
    {
        $options = array_merge([
            'escape' => true,
            'full' => !$this->isSameSiteBlogContent($post->blog_content_id)
        ], $options);

        $url = $this->getPostLinkUrl($post, false, $options['full']);

        // EVENT BcBlog.Blog.beforeGetPostLink
        $event = $this->dispatchLayerEvent('beforeGetPostLink', [
            'post' => $post,
            'title' => $title,
            'options' => $options,
            'url' => $url,
        ], ['class' => 'Blog', 'plugin' => 'BcBlog']);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
            $post = $event->getData('post');
            $title = $event->getData('title');
            $url = $event->getData('url');
        }

        $out = $this->BcBaser->getLink($title, $url, $options);

        // EVENT BcBlog.Blog.afterGetPostLink
        $event = $this->dispatchLayerEvent('afterGetPostLink', [
            'post' => $post,
            'title' => $title,
            'out' => $out,
            'url' => $url,
        ], ['class' => 'Blog', 'plugin' => 'BcBlog']);
        if ($event !== false) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }
        return $out;
    }

    /**
     * ブログ記事のURLを取得する
     *
     * @param BlogPost $post ブログ記事データ
     * @param bool $base ベースとなるURLを付与するかどうか
     * @param bool $full
     * @return string ブログ記事のURL
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPostLinkUrl(BlogPost $post, bool $base = true, bool $full = true)
    {
        $this->setContent($post->blog_content_id);
        if (!$this->currentContent) return '';
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        $url = $blogPostsService->getUrl($this->currentContent, $post, $full);
        if ($base && !$full) {
            return $this->Url->build($url);
        } else {
            return $url;
        }
    }

    /**
     * 記事へのリンクを出力する
     *
     * @param BlogPost $post ブログ記事データ
     * @param string $title タイトル
     * @param array $options オプション（初期値 : array()）
     *    ※ オプションについては、 HtmlHelper::link() を参照
     * @return void
     * @checked
     * @noTodo
     */
    public function postLink($post, $title, $options = [])
    {
        echo $this->getPostLink($post, $title, $options);
    }

    /**
     * 記事の本文を表示する
     *
     * @param BlogPost $post ブログ記事データ
     * @param boolean $moreText 詳細データを表示するかどうか（初期値 : true）
     * @param mixed $moreLink 詳細ページへのリンクを表示するかどうか。true に指定した場合、
     *    「≫ 続きを読む」という文字列がリンクとして表示される。（初期値 : false）
     * また、文字列を指定するとその文字列がリンクとなる
     * @param mixed $cut 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力（初期値 : false）
     * @param mixed $lastText 本文後に文字列を挿入するかを真偽値で指定。挿入する場合、テキストを入力（初期値 : false）
     * @return void
     * @checked
     * @noTodo
     */
    public function postContent(
        BlogPost $post,
        bool $moreText = true,
        bool $moreLink = false,
        mixed $cut = false,
        bool $lastText = false
    )
    {
        echo $this->getPostContent($post, $moreText, $moreLink, $cut, $lastText);
    }

    /**
     * 記事の本文を取得する
     *
     * @param BlogPost $post ブログ記事データ
     * @param boolean $moreText 詳細データを表示するかどうか（初期値 : true）
     * @param mixed $moreLink 詳細ページへのリンクを表示するかどうか。true に指定した場合、
     *    「≫ 続きを読む」という文字列がリンクとして表示される。（初期値 : false）
     * また、文字列を指定するとその文字列がリンクとなる
     * @param mixed $cut 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力（初期値 : false）
     * @param mixed $lastText 本文後に文字列を挿入するかを真偽値で指定。挿入する場合、テキストを入力（初期値 : false）
     * @return string 記事本文
     * @checked
     * @noTodo
     */
    public function getPostContent(
        BlogPost $post,
        bool $moreText = true,
        mixed $moreLink = false,
        mixed $cut = false,
        bool $lastText = false
    )
    {
        if ($cut) {
            $out = str_replace(["\r\n", "\r", "\n"], '', $post->content . $post->detail);
            $out = html_entity_decode($out, ENT_QUOTES, 'UTF-8');
            if ($lastText && mb_strlen(strip_tags($out)) > $cut) {
                $out = mb_substr(strip_tags($out), 0, $cut, 'UTF-8') . strip_tags($lastText);
            } else {
                $out = mb_substr(strip_tags($out), 0, $cut, 'UTF-8');
            }
        } else {
            $out = $this->BcBaser->getElement('blog_post_content', [
                'moreText' => $moreText,
                'useContent' => $this->currentBlogContent->use_content,
                'post' => $post
            ]);
        }
        if ($moreLink && trim($post->detail) != "<br>") {
            if ($moreLink === true) $moreLink = __d('baser_core', '≫ 続きを読む');
            $out .= $this->BcBaser->getElement('blog_post_content_more', [
                'moreLink' => $moreLink,
                'post' => $post
            ]);
        }
        return $out;
    }

    /**
     * 記事の詳細を表示する
     *
     * @param BlogPost $post ブログ記事データ
     * @param array $options オプション（初期値 : array()）getPostDetailを参照
     * @return void
     * @checked
     * @noTodo
     */
    public function postDetail(BlogPost $post, array $options = [])
    {
        echo $this->getPostDetail($post, $options);
    }

    /**
     * 記事の詳細を取得する
     *
     * @param BlogPost $post ブログ記事データ
     * @param array $options オプション（初期値 : array()）
     *    - `cut` : 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力（初期値 : false）
     * @return string 記事本文
     * @checked
     * @noTodo
     */
    public function getPostDetail(BlogPost $post, array $options = [])
    {
        $options = array_merge([
            'cut' => false
        ], $options);
        $cut = $options['cut'];
        unset($options['cut']);
        $out = $post->detail;
        if ($cut) {
            $out = mb_substr(strip_tags($out), 0, $cut, 'UTF-8');
        }
        return $out;
    }

    /**
     * 記事が属するカテゴリ名を出力する
     *
     * @param BlogPost $post 記事データ
     * @param array $options オプション（初期値 : array()）
     *    - `link` : リンクをつけるかどうか（初期値 : true）
     *    ※ その他のオプションは、`link`オプションが`true`の場合に
     *    生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
     * @return void
     * @checked
     * @noTodo
     */
    public function category(BlogPost $post, array $options = [])
    {
        echo $this->getCategory($post, $options);
    }

    /**
     * 記事が属するカテゴリ名を取得する
     *
     * @param BlogPost $post 記事データ
     * @param array $options オプション（初期値 : array()）
     *    - `link` : リンクをつけるかどうか（初期値 : true）
     *    ※ その他のオプションは、`link`オプションが`true`の場合に
     *    生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
     * @return string カテゴリ名
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCategory(BlogPost $post, array $options = [])
    {
        if (!empty($post->blog_category->name)) {

            $options = array_merge(['link' => true], $options);
            $link = false;

            if ($options['link']) {
                $link = true;
            }

            unset($options['link']);

            if ($link) {
                $options['base'] = false;
                return $this->Html->link($post->blog_category->title, $this->getCategoryUrl($post->blog_category->id, $options), $options, null);
            } else {
                return $post->blog_category->title;
            }
        } else {
            return '';
        }
    }

    /**
     * タグを出力する
     *
     * 複数所属する場合は複数出力する
     *
     * @param array $post 記事データ
     * @param string $separator 区切り文字（初期値 :  , ）
     * @return void
     * @checked
     * @noTodo
     */
    public function tag($post, $separator = ' , ')
    {
        echo $this->getTag($post, $separator);
    }

    /**
     * タグを取得する
     *
     * 複数所属する場合は複数取得する
     *
     * @param array $post 記事データ
     * @param string $options
     *    - `separator` : 区切り文字（初期値 :  , ）
     *    - `tag` : リンク付きのタグで出力するかどうか（初期値 : true）
     *        ※ link に統合予定
     *    - `link` : リンク付きのタグで出力するかどうか（初期値 : true）
     *    ※ 文字列で指定した場合は、separator として扱う
     * @return mixed ''|string|array
     * @checked
     * @noTodo
     */
    public function getTag($post, $options = [])
    {
        if ($options && is_string($options)) {
            $separator = $options;
            $options = [];
            $options['separator'] = $separator;
        }
        $options = array_merge([
            'separator' => ' , ',
            'tag' => true,
            'crossing' => false,
            'link' => true
        ], $options);
        $tags = [];
        if ($options['crossing']) {
            $crossingId = null;
        } else {
            $crossingId = $this->currentBlogContent->id;
        }
        if ($options['tag'] === false) {
            $options['link'] = false;
        }
        if (!empty($post->blog_tags)) {
            foreach($post->blog_tags as $tag) {
                if ($options['link']) {
                    $tags[] = $this->BcBaser->getLink($tag['name'], $this->getTagLinkUrl($crossingId, $tag, false), ['escape' => true]);
                } else {
                    $tags[] = [
                        'name' => $tag['name'],
                        'url' => $this->getTagLinkUrl($crossingId, $tag)
                    ];
                }
            }
        }
        if ($tags) {
            if ($options['link']) {
                return implode($options['separator'], $tags);
            } else {
                return $tags;
            }
        } else {
            return '';
        }
    }

    /**
     * カテゴリ一覧へのURLを取得する
     *
     * [注意] リンク関数でラップする前提のためベースURLは考慮されない
     *
     * @param string $blogCategoyId ブログカテゴリID
     * @param array $options オプション（初期値 : array()）
     *    `named` : URLの名前付きパラメータ
     * @return string カテゴリ一覧へのURL
     * @checked
     * @noTodo
     */
    public function getCategoryUrl($blogCategoryId, $options = [])
    {
        $options = array_merge([
            'query' => [],
            'base' => true
        ], $options);
        $blogCategoriesTable = TableRegistry::getTableLocator()->get('BcBlog.BlogCategories');
        $blogCategory = $blogCategoriesTable->get($blogCategoryId);
        $categoryPath = $blogCategoriesTable->find('path', ['for' => $blogCategoryId]);
        $blogContentId = $blogCategory->blog_content_id;
        $this->setContent($blogContentId);
        $sitesTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sitesTable->findByUrl($this->currentContent->url);
        $contentUrl = $this->BcBaser->getContentsUrl($this->currentContent->url, !$this->isSameSiteBlogContent($blogContentId), !empty($site->use_subdomain), false);
        $path = ['category'];
        if ($categoryPath) {
            foreach($categoryPath as $category) {
                $path[] = rawurldecode($category->name);
            }
        }
        $url = $contentUrl . 'archives/' . implode('/', $path);
        if ($options['query']) {
            $queryArray = [];
            foreach($options['query'] as $key => $value) {
                $queryArray[] = $key . '=' . $value;
            }
            $url .= '?' . implode('&', $queryArray);
        }
        if ($options['base']) {
            return $this->Url->build($url);
        } else {
            return $url;
        }
    }

    /**
     * 記事の登録日を出力する
     *
     * @param BlogPost $post ブログ記事
     * @param string $format 日付フォーマット（初期値 : Y/m/d）
     * @return void
     * @checked
     * @noTodo
     */
    public function postDate(BlogPost $post, string $format = 'Y/m/d')
    {
        echo $this->getPostDate($post, $format);
    }

    /**
     * 登録日
     *
     * @param array $post ブログ記事
     * @param string $format 日付フォーマット（初期値 : Y/m/d）
     * @return string 登録日
     * @checked
     * @noTodo
     */
    public function getPostDate(BlogPost $post, $format = 'Y/m/d')
    {
        if (!isset($this->BcTime)) {
            $this->BcTime = new BcTimeHelper($this->_View);
        }
        return $this->BcTime->format($post->posted, $format);
    }

    /**
     * 記事の投稿者を出力する
     *
     * @param BlogPost $post ブログ記事
     * @return void
     * @checked
     * @noTodo
     */
    public function author(BlogPost $post)
    {
        echo h($this->BcBaser->getUserName($post->user));
    }

    /**
     * カテゴリーの一覧をリストタグで取得する
     *
     * @param array $categories カテゴリ一覧データ
     * @param int $depth 階層（初期値 : 3）
     * @param boolean $count 件数を表示するかどうか（初期値 : false）
     * @param array $options オプション（初期値 : array()）
     *    - `link` : リンクをつけるかどうか（初期値 : true）
     *    ※ その他のオプションは、`link`オプションが`true`の場合に
     *    生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
     * @return string HTMLのカテゴリ一覧
     * @checked
     * @noTodo
     */
    public function getCategoryList($categories, $depth = 3, $count = false, $options = [])
    {
        $options = array_merge([
            'current' => 1
        ], $options);
        if ($depth < $options['current']) return '';
        if ($categories) {
            return $this->BcBaser->getElement('BcBlog.blog_category_list', [
                'categories' => $categories,
                'depth' => $depth,
                'count' => $count,
                'options' => $options
            ]);
        } else {
            return '';
        }
    }

    /**
     * 前の記事へのリンクを出力する
     *
     * @param BlogPost $post ブログ記事
     * @param string $title タイトル
     * @param array $htmlAttributes HTML属性
     *    ※ HTML属性は、HtmlHelper::link() 参照
     * @return void
     * @checked
     * @noTodo
     */
    public function prevLink(BlogPost $post, string $title = '', array $htmlAttributes = [])
    {
        $prevPost = $this->getPrevPost($post);
        $htmlAttributes = array_merge(['class' => 'prev-link', 'arrow' => '≪ '], $htmlAttributes);
        $arrow = $htmlAttributes['arrow'];
        unset($htmlAttributes['arrow']);
        if ($prevPost) {
            if (!$title) {
                $title = $arrow . $prevPost->title;
            }
            echo $this->getPostLink($prevPost, $title, $htmlAttributes);
        }
    }

    /**
     * 前の記事へのリンクがあるかチェックする
     *
     * @param BlogPost $post ブログ記事
     * @return bool
     * @checked
     * @noTodo
     */
    public function hasPrevLink(BlogPost $post)
    {
        $prevPost = $this->getPrevPost($post);
        if ($prevPost) {
            return true;
        }
        return false;
    }

    /**
     * 次の記事へのリンクを出力する
     *
     * @param BlogPost $post ブログ記事
     * @param string $title タイトル
     * @param array $htmlAttributes HTML属性
     *    ※ HTML属性は、HtmlHelper::link() 参照
     * @return void
     * @checked
     * @noTodo
     */
    public function nextLink(BlogPost $post, string $title = '', array $htmlAttributes = [])
    {
        $nextPost = $this->getNextPost($post);
        $htmlAttributes = array_merge(['class' => 'next-link', 'arrow' => ' ≫'], $htmlAttributes);
        $arrow = $htmlAttributes['arrow'];
        unset($htmlAttributes['arrow']);
        if ($nextPost) {
            if (!$title) {
                $title = $nextPost->title . $arrow;
            }
            echo $this->getPostLink($nextPost, $title, $htmlAttributes);
        }
    }

    /**
     * 次の記事へのリンクが存在するかチェックする
     *
     * @param BlogPost $post ブログ記事
     * @return bool
     * @checked
     * @noTodo
     */
    public function hasNextLink($post)
    {
        $nextPost = $this->getNextPost($post);
        if ($nextPost) {
            return true;
        }
        return false;
    }

    /**
     * ブログテンプレートを取得
     *
     * コンボボックスのソースとして利用
     *
     * @return array ブログテンプレート一覧
     * @checked
     * @noTodo
     */
    public function getBlogTemplates($siteId = 0)
    {
        $templatesPaths = BcUtil::getFrontTemplatePaths($siteId, 'BcBlog');
        $_templates = [];
        foreach($templatesPaths as $templatePath) {
            $templatePath .= 'Blog' . DS;
            $folder = new Folder($templatePath);
            $files = $folder->read(true, true);
            if ($files[0]) {
                if ($_templates) {
                    $_templates = array_merge($_templates, $files[0]);
                } else {
                    $_templates = $files[0];
                }
            }
        }

        $excludes = Configure::read('BcAgent');
        $excludes = array_keys($excludes);

        $excludes[] = 'rss';
        $templates = [];
        foreach($_templates as $template) {
            if (!in_array($template, $excludes)) {
                $templates[$template] = $template;
            }
        }

        return $templates;
    }

    /**
     * 公開状態を取得する
     *
     * @param EntityInterface $post
     * @return boolean 公開状態
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためテスト不要
     */
    public function allowPublish(EntityInterface $post)
    {
        /* @var BlogPostsService $blogPostsService */
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        return $blogPostsService->allowPublish($post);
    }

    /**
     * 記事中の画像を出力する
     *
     * @param BlogPost $post ブログ記事
     * @param array $options オプション（初期値 : array()）
     *    - `num` : 何枚目の画像か順番を指定（初期値 : 1）
     *    - `link` : 詳細ページへのリンクをつけるかどうか（初期値 : true）
     *    - `alt` : ALT属性（初期値 : ブログ記事のタイトル）
     * @return void
     * @checked
     * @noTodo
     */
    public function postImg($post, $options = [])
    {
        echo $this->getPostImg($post, $options);
    }

    /**
     * 記事中の画像を取得する
     *
     * @param BlogPost $post ブログ記事
     * @param array $options オプション（初期値 : array()）
     *    - `num` : 何枚目の画像か順番を指定（初期値 : 1）
     *    - `link` : 詳細ページへのリンクをつけるかどうか（初期値 : true）
     *    - `alt` : ALT属性（初期値 : ブログ記事のタイトル）
     *    - `output` : 出力形式 tag, url のを指定できる（初期値 : ''）
     * @return string
     * @checked
     * @noTodo
     */
    public function getPostImg($post, $options = [])
    {
        $this->setContent($post->blog_content_id);
        $options = array_merge($_options = [
            'num' => 1,
            'link' => true,
            'alt' => $post->name,
            'output' => '', // 出力形式 tag or url
        ], $options);
        $num = $options['num'];
        $link = $options['link'];
        $output = $options['output'];
        unset($options['num']);
        unset($options['link']);
        unset($options['output']);

        $contents = $post->content . $post->detail;
        $pattern = '/<img.*?src="([^"]+)"[^>]*>/is';
        if (!preg_match_all($pattern, $contents, $matches)) {
            return '';
        }

        if (isset($matches[1][$num - 1])) {
            $url = $matches[1][$num - 1];
            $url = preg_replace('/^' . preg_quote($this->base, '/') . '/', '', $url);
            if ($output == 'url') {
                return $url; // 出力形式 が urlなら、URLを返す
            }
            $img = $this->BcBaser->getImg($url, $options);
            if ($link) {
                return $this->BcBaser->getLink($img, $this->currentContent->url . 'archives/' . $post->no);
            } else {
                return $img;
            }
        } else {
            return '';
        }
    }

    /**
     * 記事中のタグで指定したIDの内容を取得する
     *
     * @param BlogPost $post ブログ記事
     * @param string $id 取得したいデータが属しているタグのID属性
     * @return string 指定したIDの内容
     * @checked
     * @noTodo
     */
    public function getHtmlById($post, $id)
    {
        $content = $post->content . $post->detail;
        $pattern = '/<([^\s]+)\s[^>]*?id="' . $id . '"[^>]*>(.*?)<\/\1>/is';
        if (preg_match($pattern, $content, $matches)) {
            return $matches[2];
        } else {
            return '';
        }
    }

    /**
     * 親カテゴリを取得する
     *
     * @param array $post ブログ記事
     * @return array $parentCategory 親カテゴリ
     */
    public function getParentCategory($post)
    {
        if (empty($post->blog_category->id)) {
            return null;
        }
        $BlogCategory = ClassRegistry::init('BcBlog.BlogCategory');
        return $BlogCategory->getParentNode($post->blog_category->id);
    }

    /**
     * 同じタグの関連投稿を取得する
     *
     * @param array $post ブログ記事
     * @param EntityInterface $options オプション（初期値 : array()）
     *    - `recursive` : 関連データを取得する場合の階層（初期値 : -1）
     *    - `limit` : 件数（初期値 : 5）
     *    - `order` : 並び順指定（初期値 : BlogPost.posted DESC）
     * @return array
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは不要
     */
    public function getRelatedPosts(EntityInterface $post, array $options = [])
    {
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        return $blogPostsService->getRelatedPosts($post, $options);
    }

    /**
     * ブログのアーカイブタイプを取得する
     *
     * @return string ブログのアーカイブタイプ
     * @checked
     * @noTodo
     */
    public function getBlogArchiveType()
    {
        if ($this->getView()->get('blogArchiveType')) {
            return $this->getView()->get('blogArchiveType');
        } else {
            return '';
        }
    }

    /**
     * アーカイブページ判定
     *
     * @return boolean 現在のページがアーカイブページの場合は true を返す
     * @checked
     * @noTodo
     */
    public function isArchive()
    {
        return ($this->getBlogArchiveType());
    }

    /**
     * カテゴリー別記事一覧ページ判定
     *
     * @return boolean 現在のページがカテゴリー別記事一覧ページの場合は true を返す
     * @checked
     * @noTodo
     */
    public function isCategory()
    {
        return ($this->getBlogArchiveType() == 'category');
    }

    /**
     * タグ別記事一覧ページ判定
     *
     * @return boolean 現在のページがタグ別記事一覧ページの場合は true を返す
     * @checked
     * @noTodo
     */
    public function isTag()
    {
        return ($this->getBlogArchiveType() == 'tag');
    }

    /**
     * 日別記事一覧ページ判定
     *
     * @return boolean 現在のページが日別記事一覧ページの場合は true を返す
     * @checked
     * @noTodo
     */
    public function isDate()
    {
        return ($this->getBlogArchiveType() == 'daily');
    }

    /**
     * 月別記事一覧ページ判定
     *
     * @return boolean 現在のページが月別記事一覧ページの場合は true を返す
     * @checked
     * @noTodo
     */
    public function isMonth()
    {
        return ($this->getBlogArchiveType() == 'monthly');
    }

    /**
     * 年別記事一覧ページ判定
     *
     * @return boolean 現在のページが年別記事一覧ページの場合は true を返す
     * @checked
     * @noTodo
     */
    public function isYear()
    {
        return ($this->getBlogArchiveType() == 'yearly');
    }

    /**
     * 個別ページ判定
     *
     * @return boolean 現在のページが個別ページの場合は true を返す
     * @checked
     * @noTodo
     */
    public function isSingle()
    {
        if (empty($this->_View->getRequest()->getParam('plugin'))) {
            return false;
        }
        return ($this->_View->getRequest()->getParam('plugin') == 'BcBlog' &&
            $this->_View->getRequest()->getParam('controller') == 'Blog' &&
            $this->_View->getRequest()->getParam('action') == 'archives' &&
            !$this->getBlogArchiveType());
    }

    /**
     * インデックスページ判定
     *
     * @return boolean 現在のページがインデックスページの場合は true を返す
     * @checked
     * @noTodo
     */
    public function isHome()
    {
        if (empty($this->_View->getRequest()->getParam('plugin'))) {
            return false;
        }
        return ($this->_View->getRequest()->getParam('plugin') == 'BcBlog' && $this->_View->getRequest()->getParam('controller') == 'Blog' && $this->_View->getRequest()->getParam('action') == 'index');
    }

    /**
     * アイキャッチ画像を出力する
     *
     * @param BlogPost $post ブログ記事
     * @param array $options オプション（初期値 : array()）
     *    - `imgsize` : 画像サイズ[default|thumb|mobile_thumb]（初期値 : thumb）
     *  - `link` : 大きいサイズの画像へのリンク有無（初期値 : true）
     *  - `escape` : タイトルについてエスケープする場合に true を指定（初期値 : false）
     *    - `mobile` : モバイルの画像を表示する場合に true を指定（初期値 : false）
     *    - `alt` : alt属性（初期値 : ''）
     *    - `width` : 横幅（初期値 : ''）
     *    - `height` : 高さ（初期値 : ''）
     *    - `noimage` : 画像が存在しない場合に表示する画像（初期値 : ''）
     *    - `tmp` : 一時保存データの場合に true を指定（初期値 : false）
     *    - `class` : タグの class を指定（初期値 : img-eye-catch）
     *    - `force` : 画像が存在しない場合でも強制的に出力する場合に true を指定する（初期値 : false）
     *  ※ その他のオプションについては、リンクをつける場合、HtmlHelper::link() を参照、つけない場合、Html::image() を参照
     * @return void
     * @checked
     * @noTodo
     */
    public function eyeCatch(BlogPost $post, array $options = [])
    {
        echo $this->getEyeCatch($post, $options);
    }

    /**
     * アイキャッチ画像を取得する
     *
     * @param BlogPost|EntityInterface $post ブログ記事
     * @param array $options オプション（初期値 : array()）
     *  - `imgsize` : 画像サイズ[default|thumb|mobile_thumb]（初期値 : thumb）
     *  - `link` : 大きいサイズの画像へのリンク有無（初期値 : true）
     *  - `escape` : タイトルについてエスケープする場合に true を指定（初期値 : false）
     *  - `mobile` : モバイルの画像を表示する場合に true を指定（初期値 : false）
     *  - `alt` : alt属性（初期値 : ''）
     *  - `width` : 横幅（初期値 : ''）
     *  - `height` : 高さ（初期値 : ''）
     *  - `noimage` : 画像が存在しない場合に表示する画像（初期値 : ''）
     *  - `tmp` : 一時保存データの場合に true を指定（初期値 : false）
     *  - `class` : タグの class を指定（初期値 : img-eye-catch）
     *  - `force` : 画像が存在しない場合でも強制的に出力する場合に true を指定する（初期値 : false）
     *  - `output` : 出力形式 tag, url のを指定できる（初期値 : ''）
     *  ※ その他のオプションについては、リンクをつける場合、HtmlHelper::link() を参照、つけない場合、Html::image() を参照
     * @return string アイキャッチ画像のHTML
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEyeCatch($post, $options = [])
    {
        $options = array_merge([
            'imgsize' => 'thumb',
            'link' => true, // 大きいサイズの画像へのリンク有無
            'escape' => true, // エスケープ
            'mobile' => false, // モバイル
            'alt' => '', // alt属性
            'width' => '', // 横幅
            'height' => '', // 高さ
            'noimage' => '', // 画像がなかった場合に表示する画像
            'tmp' => false,
            'class' => 'img-eye-catch',
            'output' => '', // 出力形式 tag or url
        ], $options);

        $this->setContent($post->blog_content_id);
        $this->BcUpload->setTable('BcBlog.BlogPosts');
        return $this->BcUpload->uploadImage('eye_catch', $post, $options);
    }

    /**
     * メールフォームプラグインのフォームへのリンクを生成する
     *
     * @param string $title リンクのタイトル
     * @param string $contentsName メールフォームのコンテンツ名
     * @param array $datas メールフォームに引き継ぐデータ（初期値 : array()）
     * @param array $options a タグの属性（初期値 : array()）
     *    ※ オプションについては、HtmlHelper::link() を参照
     * @return void
     */
    public function mailFormLink($title, $contentsName, $datas = [], $options = [])
    {
        App::uses('MailHelper', 'BcMail.View/Helper');
        $MailHelper = new MailHelper($this->_View);
        $MailHelper->link($title, $contentsName, $datas, $options);
    }

    /**
     * 文字列から制御文字を取り除く
     * @checked
     * @noTodo
     */
    public function removeCtrlChars($string)
    {
        return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $string);
    }

    /**
     * 次の記事を取得する
     *
     * @param BlogPost $post ブログ記事
     * @return BlogPost
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストはスキップ
     */
    public function getNextPost($post)
    {
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        return $blogPostsService->getNextPost($post);
    }

    /**
     * 前の記事を取得する
     *
     * @param BlogPost $post ブログ記事
     * @return BlogPost
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストはスキップ
     */
    public function getPrevPost($post)
    {
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        return $blogPostsService->getPrevPost($post);
    }

    /**
     * 記事が属するカテゴリ名を取得
     *
     * @param BlogPost $post
     * @return string
     * @checked
     * @noTodo
     */
    public function getCategoryName(BlogPost $post)
    {
        if (empty($post->blog_category->name)) {
            return '';
        } else {
            return $post->blog_category->name;
        }
    }

    /**
     * 記事が属するカテゴリタイトルを取得
     *
     * @param BlogPost $post
     * @return string
     * @checked
     * @noTodo
     */
    public function getCategoryTitle(BlogPost $post)
    {
        if (empty($post->blog_category->title)) {
            return '';
        } else {
            return $post->blog_category->title;
        }
    }

    /**
     * 記事のIDを取得
     *
     * @param BlogPost $post
     * @return string
     * @checked
     * @noTodo
     */
    public function getPostId(BlogPost $post)
    {
        if (empty($post->id)) {
            return '';
        } else {
            return $post->id;
        }
    }

    /**
     * カテゴリを取得する
     *
     * @param array $options
     * @return mixed
     * @checked
     * @noTodo
     */
    public function getCategories($options = [])
    {
        $options = array_merge([
            'blogContentId' => null
        ], $options);
        $blogContentId = $options['blogContentId'];
        unset($options['blogContentId']);
        /* @var BlogCategoriesTable $blogCategoriesTable */
        $blogCategoriesTable = TableRegistry::getTableLocator()->get('BcBlog.BlogCategories');
        return $blogCategoriesTable->getCategoryList($blogContentId, $options);
    }

    /**
     * 子カテゴリを持っているかどうか
     *
     * @param int $id
     * @return mixed
     */
    public function hasChildCategory($id)
    {
        $BlogCategory = ClassRegistry::init('BcBlog.BlogCategory');
        return $BlogCategory->hasChild($id);
    }

    /**
     * ブログタグリストを取得する
     *
     * @param mixed $name
     * @param array $options
     *    - `conditions` : CakePHP形式の検索条件
     *  - `direction` : 並び順の方向
     *  - `sort` : 並び順の対象フィールド
     *  - `siteId` : サイトIDでフィルタリングする場合に指定する
     *  - `postCount` : 記事件数を表示するかどうか
     * @return array|null
     * @checked
     * @noTodo
     */
    public function getTagList($name, $options = [])
    {
        $options = array_merge([
            'conditions' => [],
            'direction' => 'ASC',
            'sort' => 'name',
            'siteId' => null,
            'postCount' => false
        ], $options);
        if ($name && !is_array($name)) {
            $name = [$name];
        }
        $options['contentId'] = $options['contentUrl'] = [];
        if ($name) {
            foreach($name as $value) {
                if (is_int($value)) {
                    $options['contentId'][] = $value;
                } else {
                    $options['contentUrl'][] = '/' . preg_replace("/^\/?(.*?)\/?$/", "$1", $value) . '/';
                }
            }
        }
        /** @var BlogTagsService $blogTagsService */
        $blogTagsService = $this->getService(BlogTagsServiceInterface::class);
        $tags = $blogTagsService->getIndex($options)->all();
        // 公開記事数のカウントを追加
        if ($options['postCount']) {
            $tags = $this->_mergePostCountToTagsData($tags, $options);
        }
        return $tags;
    }

    /**
     * タグリストを出力する
     *
     * @param mixed $name
     * @param array $options
     *    ※ オプションのパラーメーターは、BlogHelper::getTagList() に準ずる
     * @checked
     * @noTodo
     */
    public function tagList($name, $options = [])
    {
        $options = array_merge([
            'postCount' => false
        ], $options);
        $tags = $this->getTagList($name, $options);
        if ($name && !is_array($name)) {
            $name = [$name];
        }
        $blogContentId = null;
        if (!empty($name[0])) {
            if (is_int($name[0])) {
                $blogContentId = $name[0];
            } else {
                /** @var ContentsService $contentsService */
                $contentsService = $this->getService(ContentsServiceInterface::class);
                $url = '/' . preg_replace("/^\/?(.*?)\/?$/", "$1", $name[0]) . '/';
                /** @var Content $content */
                $content = $contentsService->Contents->find()
                    ->select(['entity_id'])
                    ->where(['Contents.url' => $url])
                    ->first();
                $blogContentId = $content->entity_id;
            }
        }
        $this->BcBaser->element('BcBlog.blog_tag_list', [
            'tags' => $tags,
            'blogContentId' => $blogContentId,
            'postCount' => $options['postCount']
        ]);
    }

    /**
     * タグ一覧へのURLを取得する
     *
     * @param int $blogContentId
     * @param BlogTag $tag
     * @param bool $base
     * @return string
     * @checked
     * @noTodo
     */
    public function getTagLinkUrl($blogContentId, $tag, $base = true)
    {
        $url = null;
        if ($blogContentId) {
            $this->setContent($blogContentId);
            if (!empty($this->currentContent->url)) {
                /** @var SitesService $sitesService */
                $sitesService = $this->getService(SitesServiceInterface::class);
                $site = $sitesService->findByUrl($this->currentContent->url);
                $url = $this->BcBaser->getContentsUrl($this->currentContent->url, !$this->isSameSiteBlogContent($blogContentId), !empty($site->useSubDomain), false);
                $url = $url . 'archives/tag/' . $tag->name;
            }
        }
        if (!$url) {
            $url = '/tags/' . $tag->name;
            $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
            $site = $sites->findByUrl($this->_View->getRequest()->getPath());
            if ($site && $site->alias && !$site->useSubDomain) {
                $url = '/' . $site->alias . $url;
            }
        }
        if ($base) {
            return $this->Url->build($url);
        } else {
            return $url;
        }
    }

    /**
     * タグ一覧へのリンクタグを取得する
     *
     * @param int $blogContentId
     * @param BlogTag $tag
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     */
    public function getTagLink($blogContentId, $tag, $options = [])
    {
        $url = $this->getTagLinkUrl($blogContentId, $tag, false);
        return $this->BcBaser->getLink($tag->name, $url, $options);
    }

    /**
     * タグ一覧へのリンクタグを出力する
     *
     * @param int $blogContentId
     * @param BlogTag $tag
     * @param array $options
     * @return void
     * @checked
     * @noTodo
     */
    public function tagLink($blogContentId, $tag, $options = [])
    {
        echo $this->getTagLink($blogContentId, $tag, $options);
    }

    /**
     * ブログタグリストに公開記事数を追加する
     *
     * @param array $tags BlogTagの基本情報の配列
     * @return array
     * @checked
     * @noTodo
     */
    private function _mergePostCountToTagsData(ResultSetInterface $tags, $options)
    {
        if (!$tags->count()) return $tags;
        $blogTagIds = Hash::extract($tags->toArray(), "{n}.id");

        /** @var BlogPostsService $blogPostsService */
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        $conditions = array_merge(
            ['BlogTags.id IN' => $blogTagIds],
            $blogPostsService->BlogPosts->getConditionAllowPublish()
        );
        if (!empty($options['contentId'])) $blogContentIds = $options['contentId'];
        if (!empty($options['contentUrl'])) {
            /** @var BlogContentsService $blogContentsService */
            $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
            $blogContents = $blogContentsService->BlogContents->find()
                ->select(['BlogContent.id'])
                ->where(array_merge(
                    $blogContentsService->BlogContents->Content->getConditionAllowPublish(),
                    ['Contents.url' => $options['contentUrl']]
                ))->all();
            $blogContentIds = Hash::extract($blogContents->toArray(), "{n}.id");
        }
        if (!empty($blogContentIds)) $conditions[] = ['BlogPosts.blog_content_id' => $blogContentIds];

        $tagIds = [];
        if (!empty($conditions['BlogTags.id IN'])) {
            $tagIds = $conditions['BlogTags.id IN'];
            unset($conditions['BlogTags.id IN']);
        }

        $query = $blogPostsService->BlogPosts->find()
            ->where($conditions)
            ->leftJoinWith('BlogTags')
            ->group(['BlogTags.id'])
            ->select(['BlogTags.id']);
        $query = $query->select([
            'post_count' => $query->func()->count('BlogPosts.id')
        ]);
        if ($tagIds) {
            $query = $query->matching('BlogTags', function($q) use ($tagIds) {
                return $q->where(['BlogTags.id IN' => $tagIds]);
            });
        }

        if (!$query->count()) {
            foreach($tags as $tag) {
                $tag->post_count = 0;
            }
            return $tags;
        }

        foreach($tags as $tag) {
            foreach($query->all() as $postCount) {
                if ($tag->id === $postCount->get('_matchingData')['BlogTags']->id) {
                    $tag->post_count = $postCount->post_count;
                }
            }
        }
        return $tags;
    }

    /**
     * ブログ記事一覧出力
     *
     * ページ編集画面等で利用する事ができる。
     * ビュー: lib/Baser/Plugin/Blog/View/blog/{コンテンツテンプレート名}/posts.php
     *
     * 《利用例》
     * $this->BcBaser->blogPosts('news', 3)
     *
     * 複数のコンテンツを指定する場合：配列にて複数のコンテンツ名を指定
     *                                    コンテンツテンプレート名は配列の先頭を利用する
     * $this->BcBaser->blogPosts(array('news', 'work'), 3)
     *
     * 全てのコンテンツを指定する場合：nullを指定
     *                                    contentsTemplateオプションにて
     *                                    コンテンツテンプレート名を指定する（必須）
     * $this->BcBaser->blogPosts(null, 3, array('contentsTemplate' => 'news'))
     *
     * @param string | array $contentsName 管理システムで指定したコンテンツ名（初期値 : null）２階層目以降はURLで指定
     * @param int $num 記事件数（初期値 : 5）
     * @param array $options オプション（初期値 : array()）
     *  - `conditions` : CakePHP形式の検索条件（初期値 : array()）
     *  - `category` : カテゴリで絞り込む（初期値 : null）
     *  - `tag` : タグで絞り込む（初期値 : null）
     *  - `year` : 年で絞り込む（初期値 : null）
     *  - `month` : 月で絞り込む（初期値 : null）
     *  - `day` : 日で絞り込む（初期値 : null）
     *  - `id` : 記事NO で絞り込む（初期値 : null）※ 後方互換のため id を維持
     *  - `no` : 記事NO で絞り込む（初期値 : null）
     *  - `keyword` : キーワードで絞り込む場合にキーワードを指定（初期値 : null）
     *  - `postId` : 記事ID で絞り込む（初期値 : null）
     *  - `siteId` : サイトID で絞り込む（初期値 : null）
     *  - `preview` : 非公開の記事も見る場合に指定（初期値 : false）
     *  - `contentsTemplate` : コンテンツテンプレート名を指定（初期値 : null）
     *  - `template` : 読み込むテンプレート名を指定する場合にテンプレート名を指定（初期値 : null）
     *  - `direction` : 並び順の方向を指定 [昇順:ASC or 降順:DESC or ランダム:RANDOM]（初期値 : null）
     *  - `page` : ページ数を指定（初期値 : null）
     *  - `sort` : 並び替えの基準となるフィールドを指定（初期値 : null）
     *  - `autoSetCurrentBlog` : $contentsName を指定していない場合、現在のコンテンツより自動でブログを指定する（初期値：true）
     *  - `data` : エレメントに渡したい変数（初期値 : array）
     * @return void
     * @checked
     * @noTodo
     */
    public function posts($contentsName = [], $num = 5, $options = [])
    {
        $options = array_merge([
            'preview' => false,
            'page' => 1,
            'data' => [],
        ], $options);

        if ($options['preview'] === false) {
            $options['status'] = 'publish';
        }

        if (!$contentsName && empty($options['contentsTemplate'])) {
            throw new BcException(__d('baser_core', '$contentsName を省略時は、contentsTemplate オプションで、コンテンツテンプレート名を指定してください。'));
        }

        $blogPosts = $this->getPosts($contentsName, $num, $options);
        $options = $this->parseContentName($contentsName, $options);

        /** @var BlogContentsService $BlogContent */
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $template = $blogContentsService->getContentsTemplateRelativePath($options);

        if (is_array($options['data'])) {
            $data = array_merge(['posts' => $blogPosts], $options['data']);
        } else {
            $data = ['posts' => $blogPosts];
        }

        if(is_array($contentsName)) {
            $blogContent = $blogContentsService->findByName($contentsName[0]);
        } else {
            $blogContent = $blogContentsService->findByName($contentsName);
        }

        $currentBlogContentId = null;
        if($this->currentBlogContent) {
            $currentBlogContentId = $this->currentBlogContent->id;
        }

        if (isset($blogContent->id))
            $this->setContent($blogContent->id);

        $this->BcBaser->element($template, $data);

        if($currentBlogContentId) {
            $this->setContent($currentBlogContentId);
        }
    }

    /**
     * ブログ記事を取得する
     *
     * @param array $contentsName
     * @param int $num
     * @param array $options
     *    ※ パラメーターは、contentTemplate / template 以外、BlogBaserHelper::blogPosts() に準ずる
     * @return mixed
     * @checked
     * @noTodo
     */
    public function getPosts($contentsName = [], $num = 5, $options = [])
    {
        $options = array_merge([
            'preview' => false,
            'page' => 1,
            'limit' => $num
        ], $options);

        $options = $this->parseContentName($contentsName, $options);
        return $this->getService(BlogPostsServiceInterface::class)->getIndex($options);
    }

    /**
     * コンテンツ名を解析して検索条件を設定する
     *
     * @param mixed $contentsName
     * @param array $options
     * @return mixed
     * @checked
     * @noTodo
     */
    public function parseContentName($contentsName, $options)
    {
        $options = array_merge([
            'contentUrl' => [],
            'contentId' => [],
            'autoSetCurrentBlog' => true
        ], $options);

        if ($contentsName && !is_array($contentsName)) {
            $contentsName = [$contentsName];
        }
        // 対象ブログを指定する条件を設定
        if ($contentsName) {
            foreach($contentsName as $value) {
                if (is_int($value)) {
                    $options['contentId'][] = $value;
                } else {
                    $options['contentUrl'][] = '/' . preg_replace("/^\/?(.*?)\/?$/", "$1", $value) . '/';
                }
            }
        }
        if ($options['autoSetCurrentBlog'] && empty($options['contentUrl']) && empty($options['contentId'])) {
            $currentContent = $this->currentContent;
            if ($this->isBlog() && !empty($currentContent->entity_id)) {
                $options['contentId'] = $currentContent->entity_id;
            }
            if ($this->isBlog() && !empty($currentContent->url)) {
                $options['contentUrl'] = $currentContent->url;
            }
        }
        return $options;
    }

    /**
     * Blogの基本情報を全て取得する
     *
     * @param string $name ブログのコンテンツ名を指定するとそのブログのみの基本情報を返す。空指定(default)で、全てのブログの基本情報。 ex) 'news' （初期値 : ''）
     * @param array $options オプション（初期値 :array()）
     *    - `sort` : データのソート順 取得出来るフィールドのどれかでソートができる ex) 'created DESC'（初期値 : 'id'）
     *  - `siteId` : サブサイトIDで絞り込む場合に指定する（初期値：0）
     *  - `postCount` : 公開記事数を取得するかどうか (初期値:false)
     * @return mixed false|array Blogの基本情報
     */
    public function getContents($name = '', $options = [])
    {
        $options = array_merge([
            'sort' => 'BlogContent.id',
            'siteId' => null,
            'postCount' => false,
        ], $options);
        $conditions['Content.status'] = true;
        if (!empty($name)) {
            if (is_int($name)) {
                $conditions['BlogContent.id'] = $name;
            } else {
                $conditions['Content.name'] = $name;
            }
        }
        if ($options['siteId'] !== '' && !is_null($options['siteId']) && $options['siteId'] !== false) {
            $conditions['Content.site_id'] = $options['siteId'];
        }
        /** @var BlogContent $BlogContent */
        $BlogContent = ClassRegistry::init('Blog.BlogContent');
        $BlogContent->unbindModel(
            ['hasMany' => ['BlogPost', 'BlogCategory']]
        );
        $datas = $BlogContent->find(
            'all',
            [
                'conditions' => $conditions,
                'order' => $options['sort'],
                'recursive' => 0
            ]
        );
        if (!$datas) {
            return false;
        }

        // 公開記事数のカウントを追加
        if ($options['postCount']) {
            $datas = $this->_mergePostCountToBlogsData($datas);
        }

        $contents = [];
        if (count($datas) === 1) {
            $datas = $BlogContent->constructEyeCatchSize($datas[0]);
            unset($datas['BlogContent']['eye_catch_size']);
            $contents[] = $datas;
        } else {
            foreach($datas as $val) {
                $val = $BlogContent->constructEyeCatchSize($val);
                unset($val['BlogContent']['eye_catch_size']);
                $contents[] = $val;
            }
        }
        if ($name && !is_array($name)) {
            $contents = $contents[0];
        }
        return $contents;
    }

    /**
     * Blogの基本情報に公開記事数を追加する
     *
     * @param array $blogsData Blogの基本情報の配列
     * @return array
     */
    private function _mergePostCountToBlogsData(array $blogsData)
    {

        /** @var BlogPost $BlogPost */
        $BlogPost = ClassRegistry::init('Blog.BlogPost');

        $blogContentIds = Hash::extract($blogsData, "{n}.BlogContent.id");
        $conditions = array_merge(
            ['BlogPost.blog_content_id' => $blogContentIds],
            $BlogPost->getConditionAllowPublish()
        );

        $postCountsData = $BlogPost->find('all', [
            'fields' => [
                'BlogPost.blog_content_id',
                'COUNT(BlogPost.id) as post_count',
            ],
            'conditions' => $conditions,
            'group' => ['BlogPost.blog_content_id'],
            'recursive' => -1,
        ]);

        if (empty($postCountsData)) {
            foreach($blogsData as $blogData) {
                $blogData['BlogContent']['post_count'] = 0;
            }
            return $blogsData;
        }

        foreach($blogsData as $index => $blogData) {

            $blogContentId = $blogData['BlogContent']['id'];
            $countData = array_values(array_filter($postCountsData, function(array $data) use ($blogContentId) {
                return $data['BlogPost']['blog_content_id'] == $blogContentId;
            }));

            if (empty($countData)) {
                $blogsData[$index]['BlogContent']['post_count'] = 0;
                continue;
            }

            $blogsData[$index]['BlogContent']['post_count'] = intval($countData[0][0]['post_count']);
        }

        return $blogsData;
    }

    /**
     * 現在のページがブログプラグインかどうかを判定する
     *
     * @return bool
     * @checked
     * @noTodo
     */
    public function isBlog()
    {
        return (!empty($this->currentContent->plugin) && $this->currentContent->plugin == 'BcBlog');
    }

    /**
     * ブログコンテンツのURLを取得する
     *
     * 別ドメインの場合はフルパスで取得する
     *
     * @param int $blogContentId ブログコンテンツID
     * @return string
     * @checked
     * @noTodo
     */
    public function getContentsUrl(int $blogContentId, $base = true)
    {
        $this->setContent($blogContentId);
        $sitesService = $this->getService(SitesServiceInterface::class);
        $site = $sitesService->findByUrl($this->currentContent->url);
        return $this->BcBaser->getContentsUrl($this->currentContent->url, !$this->isSameSiteBlogContent($blogContentId), !empty($site->useSubDomain), $base);
    }

    /**
     * 指定したブログコンテンツIDが、現在のサイトと同じかどうか判定する
     *
     * @param int $blogContentId ブログコンテンツID
     * @return bool
     * @checked
     * @noTodo
     */
    public function isSameSiteBlogContent($blogContentId)
    {
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $content = $contentsTable->find()->where([
            'Contents.entity_id' => $blogContentId,
            'Contents.type' => 'BlogContent'
        ])->first();
        $siteId = $content->site_id;
        $currentSiteId = 0;

        if (!empty($this->currentContent->alias_id)) {
            $content = $contentsTable->get($this->currentContent->alias_id);
            $currentSiteId = $content->site_id;
        } elseif ($this->_View->getRequest()->getAttribute('currentSite')->id) {
            $currentSiteId = $this->_View->getRequest()->getAttribute('currentSite')->id;
        }
        return ($currentSiteId == $siteId);
    }

    /**
     * ブログのカテゴリを取得する
     * - 例: $this->Blog->getBlogArchiveCategoryData($this->Blog->getCurrentBlogId());
     * 現在のページがカテゴリ一覧の場合、$categoryName は省略可
     *
     * @param int $blogContentId
     * @param string $categoryName
     * @param array $options
     * @return array
     */
    public function getCategoryByName($blogContentId, $categoryName = '', $options = [])
    {
        if (!$categoryName && $this->getBlogArchiveType() === 'category') {
            $pass = $this->_View->getRequest()->getParam('pass');
            $categoryName = $pass[count($pass) - 1];
        }
        return ClassRegistry::init('Blog.BlogCategory')->getByName($blogContentId, $categoryName, $options);
    }

    /**
     * 記事件数を取得する
     * 一覧でのみ利用可能
     *
     * @return false|mixed
     */
    public function getPostCount()
    {
        $params = $this->_View->Paginator->params('BlogPost');
        if (isset($params['count'])) {
            return $params['count'];
        }
        return false;
    }

    /**
     * 現在のブログタグアーカイブのブログタグ情報を取得する
     *
     * @return array
     */
    public function getCurrentBlogTag()
    {
        $blogTag = [];
        if ($this->isTag()) {
            $pass = $this->_View->getRequest()->getParam('pass');
            $name = isset($pass[1])? $pass[1] : '';
            $BlogTagModel = ClassRegistry::init('Blog.BlogTag');
            $blogTag = $BlogTagModel->getByName(rawurldecode($name));
        }
        return $blogTag;
    }

    /**
     * ブログ投稿者一覧ウィジェット用の View 変数を取得する
     *
     * @param int $blogContentId
     * @param bool $viewCount
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは実装しない
     */
    public function getViewVarsForBlogAuthorArchivesWidget(int $blogContentId, bool $viewCount)
    {
        /** @var BlogFrontService $blogFrontService */
        $blogFrontService = $this->getService(BlogFrontServiceInterface::class);
        return $blogFrontService->getViewVarsForBlogAuthorArchivesWidget($blogContentId, $viewCount);
    }

    /**
     * ブログカレンダーウィジェット用の View 変数を取得する
     *
     * @param int $blogContentId
     * @param string $year
     * @param string $month
     * @return array
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは実装しない
     */
    public function getViewVarsForBlogCalendarWidget(int $blogContentId, string $year = '', string $month = '')
    {
        /** @var BlogFrontService $blogFrontService */
        $blogFrontService = $this->getService(BlogFrontServiceInterface::class);
        return $blogFrontService->getViewVarsForBlogCalendarWidget($blogContentId, $year, $month);
    }

    /**
     * ブログカテゴリウィジェット用の View 変数を取得する
     *
     * @param int $blogContentId
     * @param bool $limit
     * @param bool $viewCount
     * @param int $depth
     * @param string|null $contentType
     * @return array
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは実装しない
     */
    public function getViewVarsForBlogCategoryArchivesWdget(
        int $blogContentId,
        bool $limit = false,
        bool $viewCount = false,
        int $depth = 1,
        string $contentType = null
    )
    {
        /** @var BlogFrontService $blogFrontService */
        $blogFrontService = $this->getService(BlogFrontServiceInterface::class);
        return $blogFrontService->getViewVarsForBlogCategoryArchivesWidget($blogContentId, $limit, $viewCount, $depth, $contentType);
    }

    /**
     * ブログ年別アーカイブウィジェット用の View 変数を取得する
     *
     * @param int $blogContentId
     * @param bool $limit
     * @param bool $viewCount
     * @return array
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは実装しない
     */
    public function getViewVarsForBlogYearlyArchivesWidget(int $blogContentId, bool $limit = false, bool $viewCount = false)
    {
        /** @var BlogFrontService $blogFrontService */
        $blogFrontService = $this->getService(BlogFrontServiceInterface::class);
        return $blogFrontService->getViewVarsForBlogYearlyArchivesWidget($blogContentId, $limit, $viewCount);
    }


    /**
     * ブログ月別アーカイブウィジェット用の View 変数を取得する
     *
     * @param int $blogContentId
     * @param int $limit
     * @param bool $viewCount
     * @return array
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは実装しない
     */
    public function getViewVarsBlogMonthlyArchivesWidget(
        int $blogContentId,
        int $limit = 12,
        bool $viewCount = false
    )
    {
        /** @var BlogFrontService $blogFrontService */
        $blogFrontService = $this->getService(BlogFrontServiceInterface::class);
        return $blogFrontService->getViewVarsBlogMonthlyArchivesWidget($blogContentId, $limit, $viewCount);
    }

    /**
     * 最近の投稿ウィジェット用 View 変数を取得する
     * @param int $blogContentId
     * @param int $limit
     * @return array
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは実装しない
     */
    public function getViewVarsRecentEntriesWidget(int $blogContentId, int $limit = 5)
    {
        /** @var BlogFrontService $blogFrontService */
        $blogFrontService = $this->getService(BlogFrontServiceInterface::class);
        return $blogFrontService->getViewVarsRecentEntriesWidget($blogContentId, $limit);
    }

}
