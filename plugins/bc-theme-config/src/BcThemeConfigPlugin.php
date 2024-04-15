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

namespace BcThemeConfig;

use BaserCore\BcPlugin;
use BaserCore\Utility\BcUtil;
use BcThemeConfig\ServiceProvider\BcThemeConfigServiceProvider;
use Cake\Core\ContainerInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class Plugin
 */
class BcThemeConfigPlugin extends BcPlugin
{

    /**
     * services
     * @param ContainerInterface $container
     * @noTodo
     * @checked
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcThemeConfigServiceProvider());
    }

    /**
     * テーマ用初期データのダウンロードCSVを修正する
     * @param $path
     * @return true
     * @checked
     * @noTodo
     */
    public function modifyDownloadDefaultData($path)
    {
        $path .= 'theme_configs.csv';
        $targets = [
            'logo',
            'main_image_1',
            'main_image_2',
            'main_image_3',
            'main_image_4',
            'main_image_5'
        ];
        $fp = fopen($path, 'a+');
        $records = [];
        while(($record = BcUtil::fgetcsvReg($fp, 10240)) !== false) {
            if (in_array($record[1], $targets)) {
                $record[2] = '';
            }
            $records[] = '"' . implode('","', $record) . '"';
        }
        ftruncate($fp, 0);
        fwrite($fp, implode("\n", $records));
        return true;
    }

}
