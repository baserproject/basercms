<?php

namespace BaserCore\Service;

use Cake\Http\ServerRequest;

class PagesDisplayService implements PagesDisplayServiceInterface
{
    /**
     * プレビュー用のデータを取得する
     *
     * @param  mixed $request
     * @return array
     */
    public function getPreviewData(ServerRequest $request): array
    {
        return [];
    }

}
