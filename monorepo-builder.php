<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([__DIR__ . '/plugins']);
    $version = (!empty($_SERVER['argv'][2]))? $_SERVER['argv'][2] : '';
    if(!$version) return;

    // for "merge" command
    $mbConfig->dataToAppend([
        ComposerJsonSection::REQUIRE_DEV => [
            'phpunit/phpunit' => '^10.1.0',
            'symplify/monorepo-builder' => '^11.2',
        ],
    ]);

    if(preg_match('/^([0-9]+\.[0-9]+\.[0-9]+|patch)$/', $version)) {
		/**
         * 正式リリース
         * タグの送信とマスタの送信
         */
		$mbConfig->defaultBranch('master');
		$mbConfig->workers([
			UpdateReplaceReleaseWorker::class,
			SetCurrentMutualDependenciesReleaseWorker::class,
		]);
    } elseif(preg_match('/^[0-9]+\.[0-9]+\.[0-9]+-(alpha|beta|rc)/', $version)) {
    	/**
         * alpha / beta / rc
         * タグの送信のみ
         */
    	$mbConfig->defaultBranch('dev-5');
    }
};
