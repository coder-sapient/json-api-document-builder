<?php

declare(strict_types=1);

$finder = \PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/examples',
    ])
    ->append([
        __FILE__,
    ]);

$rules = [
    '@PSR12' => true,
    'binary_operator_spaces' => true,
    'blank_line_before_statement' => [
        'statements' => [
            'for',
            'foreach',
            'return',
            'throw',
            'try',
            'while',
        ],
    ],
    'braces' => [
        'allow_single_line_anonymous_class_with_empty_body' => true,
        'allow_single_line_closure' => true,
    ],
    'method_argument_space' => ['on_multiline' => 'ignore'],
    'concat_space' => ['spacing' => 'one'],
    'global_namespace_import' => true,
    'no_superfluous_phpdoc_tags' => ['allow_mixed' => false, 'allow_unused_params' => true, 'remove_inheritdoc' => true],
    'not_operator_with_successor_space' => true,
    'nullable_type_declaration_for_default_null_value' => true,
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'class_attributes_separation' => ['elements' => ['method' => 'one']],
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public_static',
            'property_protected_static',
            'property_private_static',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
            'destruct',
            'method_public_abstract_static',
            'method_public_static',
            'method_protected_abstract_static',
            'method_protected_static',
            'method_private_static',
            'method_public',
            'method_public_abstract',
            'method_protected_abstract',
            'method_protected',
            'method_private',
        ],
    ],
    'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
    'phpdoc_add_missing_param_annotation' => false,
    'phpdoc_align' => ['align' => 'left'],
    'phpdoc_separation' => false,
    'phpdoc_to_comment' => false,
    'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
    'phpdoc_to_param_type' => false,
    'phpdoc_to_property_type' => false,
    'phpdoc_to_return_type' => false,
    'return_assignment' => false,
    'semicolon_after_instruction' => false,
    'single_line_comment_style' => ['comment_types' => ['hash']],
    'static_lambda' => true,
    'increment_style' => ['style' => 'post'],
    'trailing_comma_in_multiline' => ['after_heredoc' => true, 'elements' => ['arrays', 'arguments', 'parameters']],
];

return (new \PhpCsFixer\Config())
    ->setCacheFile(__DIR__ . '/.php_cs.cache')
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules($rules);
