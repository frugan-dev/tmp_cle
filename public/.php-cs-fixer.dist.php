<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Finder;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;

$header = <<<'EOF'
    This file is part of the WebSync framework.

    (ɔ) WebSync <info@websync.it>

    This source file is subject to the GNU GPLv3 license that is bundled
    with this source code in the file COPYING.
    EOF;

// exclude will work only for directories, so if you need to exclude file, try notPath
// directories passed as exclude() argument must be relative to the ones defined with the in() method
$finder = Finder::create()
    ->in([__DIR__])
    ->exclude(['uploads', 'var', 'vendor'])
;

$config = new Config()
    ->setCacheFile(sys_get_temp_dir().'/.php_cs.cache')
    ->setRiskyAllowed(false)
    ->setRules([
        // https://mlocati.github.io/php-cs-fixer-configurator
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'no_extra_blank_lines' => true,
        'trailing_comma_in_multiline' => true,
        'single_quote' => true,
        'global_namespace_import' => true,
        'fully_qualified_strict_types' => [
            'import_symbols' => true,
        ],
        //'header_comment' => ['header' => $header],
    ])
    ->setFinder($finder)
;

// special handling of fabbot.io service if it's using too old PHP CS Fixer version
if (false !== getenv('FABBOT_IO')) {
    try {
        FixerFactory::create()
            ->registerBuiltInFixers()
            ->registerCustomFixers($config->getCustomFixers())
            ->useRuleSet(new RuleSet($config->getRules()))
        ;
    } catch (InvalidConfigurationException $e) {
        $config->setRules([]);
    } catch (UnexpectedValueException $e) {
        $config->setRules([]);
    } catch (InvalidArgumentException $e) {
        $config->setRules([]);
    }
}

return $config;
