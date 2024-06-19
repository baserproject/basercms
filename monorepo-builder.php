<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([__DIR__ . '/plugins']);
    $version = (!empty($_SERVER['argv'][2]))?: null;
    if(!$version) return;
    $mbConfig->packageDirectoriesExcludes([
        __DIR__ . '/plugins/BcThemeSample',
        __DIR__ . '/plugins/BcPluginSample',
    ]);
    // for "merge" command
    $mbConfig->dataToAppend([
        ComposerJsonSection::REQUIRE_DEV => [
            'phpunit/phpunit' => '^9.5',
            'symplify/monorepo-builder' => '^11.2',
        ],
    ]);

    if(preg_match('/^[0-9]+\.[0-9]+\.[0-9]+$/', $version)) {
		/**
         * 正式リリース
         * タグの送信とマスタの送信
         */
		$mbConfig->defaultBranch('master');
		$mbConfig->workers([
			UpdateReplaceReleaseWorker::class,
			SetCurrentMutualDependenciesReleaseWorker::class,
			AddTagToChangelogReleaseWorker::class,
			SetNextMutualDependenciesReleaseWorker::class,
			UpdateBranchAliasReleaseWorker::class,
			PushNextDevReleaseWorker::class
		]);
    } elseif(preg_match('/^[0-9]+\.[0-9]+\.[0-9]+-(alpha|beta|rc)/', $version)) {
    	/**
         * alpha / beta / rc
         * タグの送信のみ
         */
    	$mbConfig->defaultBranch('dev');
    }
};
