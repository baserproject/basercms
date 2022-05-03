<?php

namespace BaserCore\Service;

use Cake\ORM\TableRegistry;
use Cake\Http\ServerRequest;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Utility\BcContainerTrait;
use Cake\Http\Exception\NotFoundException;
use BaserCore\Model\Validation\BcValidation;

class PagesDisplayService implements PagesDisplayServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Pageservice constructor.
     */
    public function __construct()
    {
        $this->Pages = TableRegistry::getTableLocator()->get('BaserCore.Pages');
        $this->Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $this->ContentsService = $this->getService(ContentsServiceInterface::class);
    }

    /**
     * プレビュー用のデータを取得する
     *
     * @param  mixed $request
     * @return array
     * @throws NotFoundException
     * @checked
     * @noTodo
     * @unitTest
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
                    $previewData = $request->getData('Page');
                    break;
                case 'draft':
                    if (!BcValidation::containsScript($request->getData('Page.draft'))) {
                        throw new NotFoundException(__d('baser', '本稿欄でスクリプトの入力は許可されていません。'));
                    }
                    $content = $this->Contents->saveTmpFiles($request->getData('Page.content'), mt_rand(0, 99999999));
                    $request = $request->withData('Page.content', $content);
                    $request = $request->withData('Page.contents', $request->getData('Page.contents_tmp'));
                    if (!empty($request->getData('Page.draft'))) {
                        $request = $request->withData('Page.contents', $request->getData('Page.draft'));
                    }
                    $previewData = $request->getData('Page');
                    break;
            }
        } else {
            switch($request->getQuery('preview')) {
                case 'default':
                case 'draft':
                    $parseUrl = $this->ContentsService->encodeParsedUrl($request->getQuery('url'));
                    $content = $this->Contents->findByUrl($parseUrl['path'], true, false, false, !empty($parseUrl['subDomain']));
                    $previewData = $content ? $this->Pages->get($content->entity_id, ['contain' => ['Contents' => ['Sites']]])->ToArray() : [];
                    break;
            }
        }
        if (empty($previewData)) throw new NotFoundException(__d('baser', 'プレビューが適切ではありません。'));
        return $previewData;
    }

}
