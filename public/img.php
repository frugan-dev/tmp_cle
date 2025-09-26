<?php

/**
 * Resize and crop images on the fly, store generated images in a cache.
 *
 * This version uses mos/cimage via Composer and framework bootstrap.
 *
 * @author  Mikael Roos mos@dbwebb.se
 * @example http://dbwebb.se/opensource/cimage
 * @link    https://github.com/mosbth/cimage
 */

require_once __DIR__ . '/wscms/include/bootstrap.php';

/**
 * IMPORTANT: Configuration Override Mechanism
 *
 * The original vendor/mos/cimage/webroot/img.php file looks for its own configuration
 * file (img_config.php) in the vendor directory using __DIR__, which takes precedence
 * over any $config array we define here.
 *
 * To use our custom configuration instead of the vendor's default, we implemented
 * a composer post-install/post-update hook that automatically removes the vendor
 * config file after each composer install/update operation.
 *
 * Required composer.json scripts:
 * {
 *   "scripts": {
 *     "post-install-cmd": ["@post-cmd"],
 *     "post-update-cmd": ["@post-cmd"],
 *     "post-cmd": "rm -f vendor/mos/cimage/webroot/img_config.php || true"
 *   }
 * }
 *
 * This approach ensures:
 * - Our $config array below is always used
 * - Updates don't break the configuration
 * - No modification of vendor files is needed
 * - Clean separation between vendor code and our configuration
 */

/**
 * Change configuration details in the array below or create a separate file
 * where you store the configuration details.
 *
 * The configuration file should be named the same name as this file and then
 * add '_config.php'. If this file is named 'img.php' then name the
 * config file should be named 'img_config.php'.
 *
 * The settings below are only a few of the available ones. Check the file in
 * webroot/img_config.php for a complete list of configuration options.
 *
 * @see https://cimage.se/doc/configure
 */
$config = [
    // 'production', 'development', 'strict'
    'mode'          => 'production',

    'image_path'    =>  __DIR__ . '/uploads/',
    'cache_path'    =>  __DIR__ . '/uploads/cache/',
    'alias_path'    =>  __DIR__ . '/uploads/',

    // A bundle does not need the autoloader since it has all code in one script.
    // But, when using img.php you need to point to the file containing the autoloader.
    'autoloader'    =>  __DIR__ . '/vendor/autoload.php',

    // 'password'      => false,
];

$imgPath = __DIR__ . '/vendor/mos/cimage/webroot/img.php';

if (!is_file($imgPath)) {
    $message = 'CImage img.php not found in vendor directory';

    if (isset($config['mode']) && $config['mode'] === 'development') {
        die($message);
    }

    Logger::error($message);

    header('HTTP/1.0 500 Internal Server Error');
    exit;
}

require $imgPath;
