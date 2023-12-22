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

namespace BcUploader\Model\Table;

use BaserCore\Model\Table\AppTable;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * ファイルアップローダー設定モデル
 *
 */
class UploaderConfigsTable extends AppTable
{

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcKeyValue');
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @noTodo
     * @checked
     * @unitTest
     */
    public function validationKeyValue(Validator $validator): Validator
    {
        $validator
            ->scalar('large_width')
            ->naturalNumber('large_width', __d('baser_core', 'PCサイズ（大）[幅]に不正な値が利用されています。'))
            ->notEmptyString('large_width', __d('baser_core', 'PCサイズ（大）[幅] を入力してください。'));
        $validator
            ->scalar('large_height')
            ->naturalNumber('large_height', __d('baser_core', 'PCサイズ（大）[高さ]に不正な値が利用されています。'))
            ->notEmptyString('large_height', __d('baser_core', 'PCサイズ（大）[高さ] を入力してください。'));
        $validator
            ->scalar('midium_width')
            ->naturalNumber('midium_width', __d('baser_core', 'PCサイズ（中）[幅]に不正な値が利用されています。'))
            ->notEmptyString('midium_width', __d('baser_core', 'PCサイズ（中）[幅] を入力してください。'));
        $validator
            ->scalar('midium_height')
            ->naturalNumber('midium_height', __d('baser_core', 'PCサイズ（中）[高さ]に不正な値が利用されています。'))
            ->notEmptyString('midium_height', __d('baser_core', 'PCサイズ（中）[高さ] を入力してください。'));
        $validator
            ->scalar('small_width')
            ->naturalNumber('small_width', __d('baser_core', 'PCサイズ（小）[幅]に不正な値が利用されています。'))
            ->notEmptyString('small_width', __d('baser_core', 'PCサイズ（小）[幅] を入力してください。'));
        $validator
            ->scalar('small_height')
            ->naturalNumber('small_height', __d('baser_core', 'PCサイズ（小）[高さ]に不正な値が利用されています。'))
            ->notEmptyString('small_height', __d('baser_core', 'PCサイズ（小）[高さ] を入力してください。'));
        $validator
            ->scalar('mobile_large_width')
            ->naturalNumber('mobile_large_width', __d('baser_core', 'モバイルサイズ（小）[幅]に不正な値が利用されています。'))
            ->notEmptyString('mobile_large_width', __d('baser_core', 'モバイルサイズ（大）[幅] を入力してください。'));
        $validator
            ->scalar('mobile_large_height')
            ->naturalNumber('mobile_large_height', __d('baser_core', 'モバイルサイズ（大）[高さ]に不正な値が利用されています。'))
            ->notEmptyString('mobile_large_height', __d('baser_core', 'モバイルサイズ（大）[高さ] を入力してください。'));
        $validator
            ->scalar('mobile_small_width')
            ->naturalNumber('mobile_small_width', __d('baser_core', 'モバイルサイズ（小）[幅]に不正な値が利用されています。'))
            ->notEmptyString('mobile_small_width', __d('baser_core', 'モバイルサイズ（小）[幅] を入力してください。'));
        $validator
            ->scalar('mobile_small_height')
            ->naturalNumber('mobile_small_height', __d('baser_core', 'モバイルサイズ（小）[幅]に不正な値が利用されています。'))
            ->notEmptyString('mobile_small_height', __d('baser_core', 'モバイルサイズ（小）[幅] を入力してください。'));
        return $validator;
    }

}
