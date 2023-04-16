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

namespace BaserCore\Service;

use Cake\Http\ServerRequest;

/**
 * PreviewServiceInterface
 */
interface PreviewServiceInterface {

    /**
     * プレビューで利用するデータを取得する
     * 
     * @param ServerRequest $request
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPreviewData(ServerRequest $request): array;

}
