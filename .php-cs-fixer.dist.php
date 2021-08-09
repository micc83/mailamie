<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('public')
    ->in(__DIR__);

return (new PhpCsFixer\Config)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);
