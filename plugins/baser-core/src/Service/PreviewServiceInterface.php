<?php

namespace BaserCore\Service;

use Cake\Http\ServerRequest;

interface PreviewServiceInterface {

    public function getPreviewData(ServerRequest $request): array;

}
