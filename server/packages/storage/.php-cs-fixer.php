<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor', 'tests']); // 合并exclude

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12'                            => true,
        '@Symfony'                          => true,
        'strict_param'                      => true,
        'modernize_types_casting'           => true,
        // 精简后的规则（移除重复/冲突项）
        'array_syntax'                      => ['syntax' => 'short'],
        'ordered_imports'                   => ['sort_algorithm' => 'alpha'],
        'no_unused_imports'                 => true,
        'not_operator_with_successor_space' => true,
        'trailing_comma_in_multiline'       => true,
        'phpdoc_scalar'                     => true,
        'unary_operator_spaces'             => true,
        'binary_operator_spaces'            => true,
        'blank_line_before_statement'       => [
            'statements' => ['return', 'throw', 'try'],
        ],
    ])
    ->setFinder($finder);
