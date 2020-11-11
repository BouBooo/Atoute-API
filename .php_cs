<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src/')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'trailing_comma_in_multiline_array' => false,
        'single_blank_line_at_eof' => false,
        'concat_space' => 'one'
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/.php_cs.cache')
;
