<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PACKAGE_DIRECTORIES, [
        // custom
        __DIR__ . '/plugins',
    ]);

    $parameters->set(Option::PACKAGE_DIRECTORIES_EXCLUDES, [
        __DIR__ . '/plugins/BcSpaSample',
        __DIR__ . '/plugins/bc-blog',
        __DIR__ . '/plugins/bc-mail',
        __DIR__ . '/plugins/bc-uploader',
    ]);

    // for "merge" command
    $parameters->set(Option::DATA_TO_APPEND, [
        ComposerJsonSection::REQUIRE_DEV => [
            'phpunit/phpunit' => '^9.5',
        ],
    ]);
};
