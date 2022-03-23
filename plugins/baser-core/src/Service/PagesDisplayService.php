<?php

namespace BaserCore\Service;

use Cake\ORM\TableRegistry;
use Cake\Http\ServerRequest;

class PagesDisplayService implements PagesDisplayServiceInterface
{

    /**
     * Pageservice constructor.
     */
    public function __construct()
    {
        $this->Pages = TableRegistry::getTableLocator()->get('BaserCore.Pages');
        $this->Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
    }

    /**
     * プレビュー用のデータを取得する
     *
     * @param  mixed $request
     * @return array
     */
    public function getPreviewData(ServerRequest $request): array
    {
        if ($request->getData()) {
            switch($request->getQuery('preview')) {
                case 'default':
                    $content = $this->Contents->saveTmpFiles($request->getData('Page.content'), mt_rand(0, 99999999));
                    $request = $request->withData('Page.content', $content);
                    if (!empty($request->getData('Page.contents_tmp'))) {
                        $request = $request->withData('Page.contents', $request->getData('Page.contents_tmp'));
                    }
                    return $request->getData('Page');
                    break;
                case 'draft':
                    break;
                default:
                    return [];
                    break;
            }
        } else {
            switch($request->getQuery('preview')) {
                case 'default':
                    // TODO ucmitz: site_idが1以外の場合もテストする
                    $content = $this->Contents->findByUrl("/" . $request->getParam('pass.0'));
                    $page = $this->Pages->get($content->entity_id, ['contain' => ['Contents' => ['Sites']]]);
                    return $page->ToArray();
                    break;
                case 'draft':
                    return [];
                    break;
                default:
                    return [];
                    break;
            }
        }
    }

}
