<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__,
    ]);

    // https://getrector.com/documentation/ignoring-rules-or-paths
    $rectorConfig->skip([
        __DIR__.'/filemanager',
        __DIR__.'/uploads',
        __DIR__.'/var',
        __DIR__.'/vendor',
    ]);

    // https://github.com/rectorphp/rector/issues/7323
    $rectorConfig->parallel(240);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_84,
    ]);
};
