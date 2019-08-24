<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BlogAppController', 'Blog.Controller');

/**
 * ブログ記事コントローラー
 *
 * @package Blog.Controller
 * @property BlogContent $BlogContent
 * @property BlogCategory $BlogCategory
 * @property BlogPost $BlogPost
 * @property BcAuthComponent $BcAuth
 * @property CookieComponent $Cookie
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcContentsComponent $BcContents
 * @property Content $Content
 */
class BlogController extends BlogAppController {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'Blog';

/**
 * モデル
 *
 * @var array
 */
	public $uses = ['Blog.BlogCategory', 'Blog.BlogPost', 'Blog.BlogContent'];

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = ['BcText', 'BcTime', 'BcFreeze', 'BcArray', 'Paginator', 'Blog.Blog'];

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'RequestHandler', 'BcEmail', 'Security', 'BcContents' => ['type' => 'Blog.BlogContent', 'useViewCache' => false]];

/**
 * ぱんくずナビ
 *
 * @var array
 */
	public $crumbs = [];

/**
 * サブメニューエレメント
 *
 * @var array
 */
	public $subMenuElements = [];

/**
 * ブログデータ
 *
 * @var array
 */
	public $blogContent = [];

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		/* 認証設定 */
		$this->BcAuth->allow(
			'index', 'mobile_index', 'smartphone_index', 'archives', 'mobile_archives', 'smartphone_archives',
			'posts', 'mobile_posts', 'smartphone_posts', 'get_calendar', 'get_categories', 'get_posted_months',
			'get_posted_years', 'get_recent_entries', 'get_authors', 'tags'
		);
		$blogContentId = null;

		if(preg_match('/tags$/', $this->request->params['action'])) {
			$Content = ClassRegistry::init('Content');
			$currentSite = BcSite::findCurrent(true);
			$url = '/';
			if($this->request->params['action'] != 'tags') {
				$prefix = str_replace('_tags', '', $this->request->params['action']);
				if($prefix == $currentSite->name) {
					$url = '/' . $currentSite->alias . '/';
					$this->request->params['action'] = 'tags';
					$this->action = 'tags';
				}
			}
			$content = $Content->find('first', ['conditions' => ['Content.url' => $url], 'recursive' => 0]);
			$this->request->params['Content'] = $content['Content'];
			$this->request->params['Site'] = $content['Site'];
		} else {
			if(!empty($this->request->params['entityId'])) {
				$blogContentId = $this->request->params['entityId'];
			} elseif(!empty($this->request->params['pass'])) {
				// 後方互換の為 pass もチェック
				$blogContentId = $this->request->params['pass'];
			}
			$this->BlogContent->recursive = -1;
			if ($this->contentId) {
				$this->blogContent = $this->BlogContent->read(null, $this->contentId);
			} else {
				$this->blogContent = $this->BlogContent->read(null, $blogContentId);
				$this->contentId = $blogContentId;
			}
		}

		if(empty($this->request->params['Content'])) {
			// ウィジェット系の際にコンテンツ管理上のURLでないので自動取得できない
			$content = $this->BcContents->getContent($blogContentId);
			if($content) {
				$this->request->params['Content'] = $content['Content'];
				$this->request->params['Site'] = $content['Site'];
			}
		}

		if(!empty($this->blogContent['BlogContent']['id'])) {
			$this->BlogPost->setupUpload($this->blogContent['BlogContent']['id']);
		}

		$this->subMenuElements = ['default'];

		// ページネーションのリンク対策
		// コンテンツ名を変更している際、以下の設定を行わないとプラグイン名がURLに付加されてしまう
		// Viewで $paginator->options = array('url' => $this->passedArgs) を行う事が前提
		if (!isset($this->request->params['admin'])) {
			if(!empty($this->request->params['Content'])) {
				$this->passedArgs['controller'] = $this->request->params['Content']['name'];
				$this->passedArgs['plugin'] = $this->request->params['Content']['name'];
			}
			$this->passedArgs['action'] = $this->action;
		}

		// コメント送信用のトークンを出力する為にセキュリティコンポーネントを利用しているが、
		// 表示用のコントローラーなのでポストデータのチェックは必要ない
		$this->Security->validatePost = false;
		$this->Security->csrfCheck = false;
	}

/**
 * beforeRender
 *
 * @return void
 */
	public function beforeRender() {
		parent::beforeRender();
		$this->set('blogContent', $this->blogContent);
		if (!empty($this->blogContent['BlogContent']['widget_area'])) {
			$this->set('widgetArea', $this->blogContent['BlogContent']['widget_area']);
		}
	}

/**
 * [PUBLIC] ブログを一覧表示する
 *
 * @return void
 */
	public function index() {
		if($this->BcContents->preview == 'default' && $this->request->data) {
			$this->blogContent['BlogContent'] = $this->request->data['BlogContent'];
		}
		if ($this->RequestHandler->isRss()) {
			Configure::write('debug', 0);
			if($this->blogContent) {
				$channel = [
					'title' => h($this->request->params['Content']['title'] . '｜' . $this->siteConfigs['name']),
					'description' => h(strip_tags($this->blogContent['BlogContent']['description']))
				];
				$listCount = $this->blogContent['BlogContent']['feed_count'];
			} else {
				$channel = [
					'title' => $this->siteConfigs['name'],
					'description' => $this->siteConfigs['description']
				];
				// TODO 暫定的に一番最初に登録したブログコンテンツの表示件数を利用
				// BlogConfig で設定できるようにする
				$blogContent = $this->BlogContent->find('first', ['order' => 'BlogContent.id', 'recirsive' => -1]);
				$listCount = $blogContent['BlogContent']['feed_count'];
				$this->blogContent = $blogContent;
			}
			$this->set('channel', $channel);
			$this->layout = 'default';
			$template = 'index';
		} else {
			if($this->request->url == 'rss/index') {
				$this->notFound();
			}
			$template = $this->blogContent['BlogContent']['template'] . DS . 'index';
			$listCount = $this->blogContent['BlogContent']['list_count'];
		}

		$datas = $this->_getBlogPosts(['num' => $listCount]);
		if (BcUtil::loginUser('admin')) {
			$this->set('editLink', ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents', 'action' => 'edit', $this->blogContent['BlogContent']['id']]);
		}
		$this->set('posts', $datas);
		$this->set('single', false);
		$this->pageTitle = $this->request->params['Content']['title'];
		$this->render($template);
	}

/**
 * [MOBILE] ブログ記事を一覧表示する
 *
 * @return void
 */
	public function mobile_index() {
		$this->setAction('index');
	}

/**
 * [SMARTPHONE] ブログ記事を一覧表示する
 *
 * @return void
 */
	public function smartphone_index() {
		$this->setAction('index');
	}

/**
 * [PUBLIC] ブログアーカイブを表示する
 *
 * @return void
 */
	public function archives() {

		// パラメーター処理
		$pass = $this->request->params['pass'];
		$type = $year = $month = $day = $id = '';
		$crumbs = $posts = [];
		$single = false;
		$posts = [];

		if ($pass[0] == 'category') {
			$type = 'category';
		} elseif ($pass[0] == 'author') {
			$type = 'author';
		} elseif ($pass[0] == 'tag') {
			$type = 'tag';
		} elseif ($pass[0] == 'date') {
			$type = 'date';
		}

		$crumbs[] = ['name' => $this->request->params['Content']['title'], 'url' => $this->request->params['Content']['url']];
		
		switch ($type) {

			/* カテゴリ一覧 */
			case 'category':

				$category = $pass[count($pass) - 1];
				if (empty($category)) {
					//$this->notFound();
				}

				// ナビゲーションを設定
				$categoryId = $this->BlogCategory->field('id', [
					'BlogCategory.blog_content_id' => $this->contentId,
					'BlogCategory.name' => urlencode($category)
				]);

				if (!$categoryId) {
					$this->notFound();
				}

				// 記事を取得
				$posts = $this->_getBlogPosts(['category' => urlencode($category)]);
				$blogCategories = $this->BlogCategory->getPath($categoryId, ['name', 'title']);
				if (count($blogCategories) > 1) {
					foreach ($blogCategories as $key => $blogCategory) {
						if ($key < count($blogCategories) - 1) {
							$crumbs[] = ['name' => $blogCategory['BlogCategory']['title'], 'url' => $this->request->params['Content']['url'] . '/archives/category/' . $blogCategory['BlogCategory']['name']];
						}
					}
				}
				$this->pageTitle = $blogCategories[count($blogCategories) - 1]['BlogCategory']['title'];
				$template = $this->blogContent['BlogContent']['template'] . DS . 'archives';

				$this->set('blogArchiveType', $type);

				break;

			case 'author':
				$author = h($pass[count($pass) - 1]);
				$posts = $this->_getBlogPosts(['author' => $author]);
				$data = $this->BlogPost->User->find('first', ['fields' => ['real_name_1', 'real_name_2', 'nickname'], 'conditions' => ['User.name' => $author]]);
				App::uses('BcBaserHelper', 'View/Helper');
				$BcBaser = new BcBaserHelper(new View());
				$this->pageTitle = $BcBaser->getUserName($data);
				$template = $this->blogContent['BlogContent']['template'] . DS . 'archives';
				$this->set('blogArchiveType', $type);
				break;

			/* タグ別記事一覧 */
			case 'tag':

				$tag = h($pass[count($pass) - 1]);
				if (empty($this->blogContent['BlogContent']['tag_use']) || empty($tag)) {
					$this->notFound();
				}
				$posts = $this->_getBlogPosts(['tag' => $tag]);
				$this->pageTitle = urldecode($tag);
				$template = $this->blogContent['BlogContent']['template'] . DS . 'archives';

				$this->set('blogArchiveType', $type);

				break;

			/* 月別アーカイブ一覧 */
			case 'date':

				$year = h($pass[1]);
				$month = h(@$pass[2]);
				$day = h(@$pass[3]);
				if (!$year && !$month && !$day) {
					$this->notFound();
				}
				$posts = $this->_getBlogPosts(['year' => $year, 'month' => $month, 'day' => $day]);
				if ($day) {
					$this->pageTitle = sprintf(__('%s年%s月%s日'), $year, $month, $day);
				} elseif($month) {
					$this->pageTitle = sprintf(__('%s年%s月'), $year, $month);
				} else {
					$this->pageTitle = sprintf(__('%s年'), $year);
				}
				$template = $this->blogContent['BlogContent']['template'] . DS . 'archives';

				if ($day) {
					$this->set('blogArchiveType', 'daily');
				} elseif ($month) {
					$this->set('blogArchiveType', 'monthly');
				} else {
					$this->set('blogArchiveType', 'yearly');
				}

				break;

			/* 単ページ */
			default:

				if (!empty($pass[0])) {
					$id = $pass[0];
				}
				// プレビュー
				if ($this->BcContents->preview) {
					if (!empty($this->request->data['BlogPost'])) {
						$this->request->data = $this->BlogPost->saveTmpFiles($this->request->data, mt_rand(0, 99999999));
						$post = $this->BlogPost->createPreviewData($this->request->data);

					} else {
						$post = $this->_getBlogPosts(['preview' => true, 'no' => $id]);
						if (isset($post[0])) {
							$post = $post[0];
							if ($this->BcContents->preview == 'draft') {
								$post['BlogPost']['detail'] = $post['BlogPost']['detail_draft'];
							}
						}
					}
					
				} else {
					if (empty($pass[0])) {
						$this->notFound();
					}

					// コメント送信
					if (isset($this->request->data['BlogComment'])) {
						$this->add_comment($id);
					}

					$post = $this->_getBlogPosts(['no' => $id]);
					if (!empty($post[0])) {
						$post = $post[0];
					} else {
						$this->notFound();
					}
					
					// 一覧系のページの場合、時限公開の記事が存在し、キャッシュがあると反映できないが、
					// 詳細ページの場合は、記事の終了期間の段階でキャッシュが切れる前提となる為、キャッシュを利用する
					// プレビューでは利用しない事。
					// コメント送信時、キャッシュはクリアされるが、モバイルの場合、このメソッドに対してデータを送信する為、
					// キャッシュがあるとデータが処理されないので、キャッシュは全く作らない設定とする
					if(BcSite::findCurrent()->device != 'mobile') {
						$this->BcContents->useViewCache = true;
					}

				}
				
				if (BcUtil::loginUser('admin')) {
					$this->set('editLink', ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'edit', $post['BlogPost']['blog_content_id'], $post['BlogPost']['id']]);
				}

				// ナビゲーションを設定
				if (!empty($post['BlogPost']['blog_category_id'])) {
					$blogCategories = $this->BlogCategory->getPath($post['BlogPost']['blog_category_id'], ['name', 'title']);
					if ($blogCategories) {
						foreach ($blogCategories as $blogCategory) {
							$crumbs[] = ['name' => $blogCategory['BlogCategory']['title'], 'url' => $this->request->params['Content']['url'] . '/archives/category/' . $blogCategory['BlogCategory']['name']];
						}
					}
				}
				$this->pageTitle = $post['BlogPost']['name'];
				$single = true;
				$template = $this->blogContent['BlogContent']['template'] . DS . 'single';
				if ($this->BcContents->preview) {
					$this->blogContent['BlogContent']['comment_use'] = false;
				}
				$this->set('post', $post);
		}

		// 表示設定
		$this->crumbs = array_merge($this->crumbs, $crumbs);
		$this->set('single', $single);
		$this->set('posts', $posts);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->render($template);
	}

/**
 * コメントを送信する
 *
 * @param int $id
 * @return void
 */
	public function add_comment($id) {
		// blog_post_idを取得
		$conditions = [
			'BlogPost.no' => $id,
			'BlogPost.blog_content_id' => $this->contentId
		];
		$conditions = am($conditions, $this->BlogPost->getConditionAllowPublish());

		// 毎秒抽出条件が違うのでキャッシュしない
		$data = $this->BlogPost->find('first', [
			'conditions' => $conditions,
			'fields' => ['BlogPost.id'],
			'cache' => false,
			'recursive' => -1
		]);
		$postId = null;
		if (empty($data['BlogPost']['id'])) {
			$this->notFound();
		} else {
			$postId = $data['BlogPost']['id'];
		}

		if ($this->BlogPost->BlogComment->add($this->request->data, $this->contentId, $postId, $this->blogContent['BlogContent']['comment_approve'])) {
			$content = $this->BlogPost->BlogContent->Content->findByType('Blog.BlogContent', $this->blogContent['BlogContent']['id']);
			$this->request->data['Content'] = $content['Content'];
			$this->_sendCommentAdmin($postId, $this->request->data);
			// コメント承認機能を利用していない場合は、公開されているコメント投稿者にアラートを送信
			if (!$this->blogContent['BlogContent']['comment_approve']) {
				$this->_sendCommentContributor($postId, $this->request->data);
			}
			if ($this->blogContent['BlogContent']['comment_approve']) {
				$commentMessage = __('送信が完了しました。送信された内容は確認後公開させて頂きます。');
			} else {
				$commentMessage = __('コメントの送信が完了しました。');
			}
			$this->request->data = null;
		} else {

			$commentMessage = __('コメントの送信に失敗しました。');
		}
		clearViewCache();
		$this->set('commentMessage', $commentMessage);
	}

/**
 * ブログ記事を取得する
 *
 * @param array $options
 * @return array
 */
	protected function _getBlogPosts($options = []) {
		$contentId = $listDirection = $listCount = null;
		if(!empty($this->blogContent['BlogContent']['list_direction'])) {
			$listDirection = $this->blogContent['BlogContent']['list_direction'];
		}
		if(!empty($this->blogContent['BlogContent']['list_direction'])) {
			$listCount = $this->blogContent['BlogContent']['list_count'];
		}
		if ($this->contentId) {
			$contentId = $this->contentId;
		}
		$options = array_merge([
			'findType' => 'customParams',
			'direction' => $listDirection,
			'listCount' => $listCount,		// @deprecated 5.0.0 since 4.0.0
			'num' => null,
			'contentId' => $contentId,
			'page' => 1,
			'cache' => false,
		], $options);

		$named = [];
		if (!empty($this->request->params['named'])) {
			$named = $this->request->params['named'];
		}
		if($named) {
			if (!empty($named['direction'])) $options['direction'] = $named['direction'];
			if (!empty($named['num'])) $options['num'] = $named['num'];
			if (!empty($named['contentId'])) $options['contentId'] = $named['contentId'];
			if (!empty($named['category'])) $options['category'] = $named['category'];
			if (!empty($named['tag'])) $options['tag'] = $named['tag'];
			if (!empty($named['year'])) $options['year'] = $named['year'];
			if (!empty($named['month'])) $options['month'] = $named['month'];
			if (!empty($named['day'])) $options['day'] = $named['day'];
			if (!empty($named['id'])) $options['id'] = $named['id'];
			if (!empty($named['no'])) $options['no'] = $named['no'];
			if (!empty($named['keyword'])) $options['keyword'] = $named['keyword'];
			if (!empty($named['author'])) $options['author'] = $named['author'];
		}

		if($options['listCount'] && !$options['num']) {
			$options['num'] = $options['listCount'];
		}
		if($options['num']) {
			$options['limit'] = $options['num'];
		}
		unset($options['listCount'], $options['num']);

		$this->paginate = $options;
		return $this->paginate('BlogPost');
	}

/**
 * [MOBILE] ブログアーカイブを表示する
 *
 * @return void
 */
	public function mobile_archives() {
		$this->setAction('archives');
	}

/**
 * [SMARTPHONE] ブログアーカイブを表示する
 *
 * @return void
 */
	public function smartphone_archives() {
		$this->setAction('archives');
	}

/**
 * ブログカレンダー用のデータを取得する
 *
 * @param int $id
 * @param int $year
 * @param int $month
 * @return array
 */
	public function get_calendar($id, $year = '', $month = '') {
		$year = h($year);
		$month = h($month);
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null, $id);
		$this->BlogPost->recursive = -1;
		$data['entryDates'] = $this->BlogPost->getEntryDates($id, $year, $month);

		if (!$year) {
			$year = date('Y');
		}
		if (!$month) {
			$month = date('m');
		}

		if ($month == 12) {
			$data['next'] = $this->BlogPost->existsEntry($id, $year + 1, 1);
		} else {
			$data['next'] = $this->BlogPost->existsEntry($id, $year, $month + 1);
		}
		if ($month == 1) {
			$data['prev'] = $this->BlogPost->existsEntry($id, $year - 1, 12);
		} else {
			$data['prev'] = $this->BlogPost->existsEntry($id, $year, $month - 1);
		}

		return $data;
	}

/**
 * カテゴリー一覧用のデータを取得する
 *
 * @param int $id
 * @param mixed $limit Number Or false Or '0'（制限なし）
 * @param mixed $viewCount
 * @param mixed $contentType year Or null
 * @return array
 */
	public function get_categories($id, $limit = false, $viewCount = false, $depth = 1, $contentType = null) {
		if ($limit === '0') {
			$limit = false;
		}
		$data = [];
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null, $id);
		$data['categories'] = $this->BlogCategory->getCategoryList($id, [
			'type' => $contentType,
			'limit' => $limit,
			'depth' => $depth,
			'viewCount' => $viewCount
		]);
		return $data;
	}

/**
 * 投稿者一覧ウィジェット用のデータを取得する
 * 
 * @param int $blogContentId
 * @param boolean $limit
 * @param int $viewCount 
 */
	public function get_authors($blogContentId, $viewCount = false) {
		$data = [];
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null, $blogContentId);
		$data['authors'] = $this->BlogPost->getAuthors($blogContentId, [
			'viewCount' => $viewCount
		]);
		return $data;
	}

/**
 * 月別アーカイブ一覧用のデータを取得する
 *
 * @param int $id
 * @return mixed $limit Number Or false Or '0'（制限なし）
 */
	public function get_posted_months($id, $limit = 12, $viewCount = false) {
		if ($limit === '0') {
			$limit = false;
		}
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null, $id);
		$this->BlogPost->recursive = -1;
		$data['postedDates'] = $this->BlogPost->getPostedDates($id, [
			'type' => 'month',
			'limit' => $limit,
			'viewCount' => $viewCount
		]);
		return $data;
	}

/**
 * 年別アーカイブ一覧用のデータを取得する
 *
 * @param int $id
 * @param boolean $viewCount
 * @return mixed $count
 */
	public function get_posted_years($id, $limit = false, $viewCount = false) {
		if ($limit === '0') {
			$limit = false;
		}
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null, $id);
		$this->BlogPost->recursive = -1;
		$data['postedDates'] = $this->BlogPost->getPostedDates($id, [
			'type' => 'year',
			'limit' => $limit,
			'viewCount' => $viewCount
		]);
		return $data;
	}

/**
 * 最近の投稿用のデータを取得する
 *
 * @param int $id
 * @param mixed $count
 * @return array
 */
	public function get_recent_entries($id, $limit = 5) {
		if ($limit === '0') {
			$limit = false;
		}
		$data['blogContent'] = $this->BlogContent->find('first', ['conditions' => ['BlogContent.id' => $id], 'recursive' => 0]);
		$conditions = array_merge(['BlogPost.blog_content_id' => $id], $this->BlogPost->getConditionAllowPublish());

		/* BlogCategoryのBlogPostを外す */
		$this->BlogPost->BlogCategory->unbindModel(['hasMany' => ['BlogPost']]);
		/* UserのBlogPostとFavoriteを外す */
		$this->BlogPost->User->unbindModel(['hasMany' => ['BlogPost', 'Favorite']]);
		/* BlogContentのBlogPostとBlogCategoryを外す */
		$this->BlogPost->BlogContent->unbindModel(['hasMany' => ['BlogPost', 'BlogCategory']]);

		// 毎秒抽出条件が違うのでキャッシュしない
		$data['recentEntries'] = $this->BlogPost->find('all', [
			'conditions' => $conditions,
			'limit' => $limit,
			'order' => 'posts_date DESC',
			'recursive' => 2,
			'cache' => false
		]);
		return $data;
	}

/**
 * 記事リストを出力
 * requestAction用
 *
 * @param int $blogContentId
 * @param mixed $num
 */
	public function posts($blogContentId, $limit = 5) {
		if (!empty($this->params['named']['template'])) {
			$template = $this->request->params['named']['template'];
		} else {
			$template = 'posts';
		}
		unset($this->request->params['named']['template']);

		$this->layout = null;
		$this->contentId = $blogContentId;

		$datas = $this->_getBlogPosts(['num' => $limit]);

		$this->set('posts', $datas);

		$this->render($this->blogContent['BlogContent']['template'] . DS . $template);
	}

/**
 * [MOBILE] 記事リストを出力
 *
 * requestAction用
 *
 * @param int $blogContentId
 * @param mixed $num
 */
	public function mobile_posts($blogContentId, $limit = 5) {
		$this->setAction('posts', $blogContentId, $limit);
	}

/**
 * [SMARTPHONE] 記事リストを出力
 *
 * requestAction用
 *
 * @param int $blogContentId
 * @param mixed $num
 */
	public function smartphone_posts($blogContentId, $limit = 5) {
		$this->setAction('posts', $blogContentId, $limit);
	}

/**
 * 全体タグ一覧
 * @param $name
 */
	public function tags($name) {
		if (empty($name)) {
			$this->notFound();
		}
		$num = 10;
		if(!empty($this->request->params['named']['num'])) {
			$num = $this->request->params['named']['num'];
		}
		$tag = h($name);
		$posts = $this->_getBlogPosts([
			'tag' => $tag,
			'num' => $num
		]);
		$this->pageTitle = urldecode($tag);
		$this->set('posts', $posts);
	}

}
