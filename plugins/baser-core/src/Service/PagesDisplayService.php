<?php

namespace BaserCore\Service;

use Cake\Http\ServerRequest;

class PagesDisplayService implements PagesDisplayServiceInterface
{
    public function getPreviewData(ServerRequest $request): array
    {
        return [];
    }

}
