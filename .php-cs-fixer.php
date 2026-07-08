<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12'                        => true,
        '@Symfony'                      => true,
        'array_syntax'                  => ['syntax' => 'short'],
        'ordered_imports'               => ['sort_algorithm' => 'alpha'],
        'no_unused_imports'             => true,
        'trailing_comma_in_multiline'   => true,
        'phpdoc_order'                  => true,
        'declare_strict_types'          => false,
        'concat_space'                  => ['spacing' => 'one'],
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache');
