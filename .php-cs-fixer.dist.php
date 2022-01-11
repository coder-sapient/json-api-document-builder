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
    '@PHP80Migration' => true,
    '@PHP80Migration:risky' => true,
    '@PhpCsFixer' => true,
    '@PhpCsFixer:risky' => true,
    '@PHPUnit84Migration:risky' => true,
    '@PSR12' => true,
    '@PSR12:risky' => true,
    'binary_operator_spaces' => ['operators' => ['|' => null]],
    'blank_line_before_statement' => [
        'statements' => [
            'case',
            'continue',
            'declare',
            'exit',
            'for',
            'foreach',
            'default',
            'include',
            'include_once',
            'require',
            'require_once',
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
    'concat_space' => ['spacing' => 'one'],
    'comment_to_phpdoc' => ['ignored_tags' => ['fixme']],
    'date_time_immutable' => true,
    'fopen_flags' => ['b_mode' => true],
    'global_namespace_import' => true,
    'no_superfluous_phpdoc_tags' => ['allow_mixed' => false, 'allow_unused_params' => true, 'remove_inheritdoc' => true],
    'not_operator_with_successor_space' => true,
    'native_function_invocation' => false,
    'native_constant_invocation' => true,
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
            'phpunit',
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
    'php_unit_strict' => true,
    'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
    'php_unit_internal_class' => false,
    'php_unit_method_casing' => false,
    'php_unit_test_annotation' => false,
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
    'void_return' => false,
    'yoda_style' => false,
];

return (new \PhpCsFixer\Config())
    ->setCacheFile(__DIR__ . '/.php_cs.cache')
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules($rules);
