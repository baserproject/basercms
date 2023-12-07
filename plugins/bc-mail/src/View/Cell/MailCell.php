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

namespace BcMail\View\Cell;

use BaserCore\Utility\BcContainerTrait;
use BcMail\Service\Front\MailFrontServiceInterface;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailMessagesServiceInterface;
use Cake\Http\ServerRequest;
use Cake\View\Cell;

/**
 * MailCell
 */
class MailCell extends Cell
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * display
     *
     * MailController::index() と同じ処理を行うが、
     * Cell であるための処理を追加している
     *
     * @param ServerRequest $request
     * @return void
     */
    public function display(ServerRequest $request)
    {
        $service = $this->getService(MailFrontServiceInterface::class);
        $mailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);

        // メールフォーム用のパスに切り替えるため、cell のパスを上書き
        $this->viewBuilder()->setTemplatePath('Mail');
        $this->viewBuilder()->setLayout('empty');

        $mailContent = $mailContentsService->get($request->getParam('entityId'));
        if (!$service->isAccepting($mailContent)) {
            // cell のパスを setTemplatePath で指定しているので、こちら側は削る
            $template = $service->getUnpublishTemplate($mailContent);
            $this->viewBuilder()->setTemplate(preg_replace('/^Mail\//', '', $template));
            return;
        }

        if ($request->is(['post', 'put'])) {
            $mailMessage = $mailMessagesService->getNew($mailContent->id, $request->getQueryParams());
        } else {
            $mailMessage = $mailMessagesService->getNew($mailContent->id, $request->getData());
        }

        $request->getSession()->write('BcMail.valid', true);
        $this->set($service->getViewVarsForIndex($mailContent, $mailMessage));
        // cell のパスを setTemplatePath で指定しているので、こちら側は削る
        $template = $service->getIndexTemplate($mailContent);
        $this->viewBuilder()->setTemplate(preg_replace('/^Mail\//', '', $template));
    }

}
