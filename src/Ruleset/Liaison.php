<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config\Ruleset;

/**
 * Rules for the `Liaison` namespace.
 */
class Liaison extends BaseRuleset
{
    protected $name = 'Liaison (PHP 7.3)';

    protected $rules = [
        '@PSR2'                   => true,
        'align_multiline_comment' => true,
        'array_indentation'       => false,
        'array_syntax'            => [
            'syntax' => 'short',
        ],
        'backtick_to_shell_exec' => true,
        'binary_operator_spaces' => [
            'default' => 'align_single_space',
        ],
        'blank_line_after_opening_tag' => true,
        'blank_line_before_return'     => false, // deprecated
        'blank_line_before_statement'  => [
            'statements' => [
                'return',
                'throw',
            ],
        ],
        'braces' => [
            'allow_single_line_closure'                   => true,
            'position_after_anonymous_constructs'         => 'same',
            'position_after_control_structures'           => 'same',
            'position_after_functions_and_oop_constructs' => 'next',
        ],
        'cast_spaces'                 => true,
        'class_attributes_separation' => [
            'elements' => [
                'method',
            ],
        ],
        'class_definition'           => true,
        'class_keyword_remove'       => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'combine_nested_dirname'     => true, // risky
        'comment_to_phpdoc'          => true, // risky
        'compact_nullable_typehint'  => true,
        'concat_space'               => [
            'spacing' => 'one',
        ],
        'date_time_immutable'                    => false,
        'declare_equal_normalize'                => true,
        'declare_strict_types'                   => false,
        'dir_constant'                           => true, // risky
        'doctrine_annotation_array_assignment'   => false,
        'doctrine_annotation_braces'             => false,
        'doctrine_annotation_indentation'        => false,
        'doctrine_annotation_spaces'             => false,
        'ereg_to_preg'                           => true, // risky
        'error_suppression'                      => true, // risky
        'escape_implicit_backslashes'            => true,
        'explicit_indirect_variable'             => true,
        'explicit_string_variable'               => true,
        'final_class'                            => false, // risky
        'final_internal_class'                   => true, // risky
        'final_public_method_for_abstract_class' => false, // risky
        'final_static_access'                    => true,
        'fopen_flag_order'                       => true, // risky
        'fopen_flags'                            => [
            'b_mode' => true, // risky
        ],
        'fully_qualified_strict_types' => true,
        'function_to_constant'         => [
            'functions' => [ // risky
                'get_called_class',
                'get_class',
                'get_class_this',
                'php_sapi_name',
                'phpversion',
                'pi',
            ],
        ],
        'function_typehint_space'          => true,
        'general_phpdoc_annotation_remove' => [
            'annotations' => [
                'author',
                'package',
                'subpackage',
            ],
        ],
        'global_namespace_import' => true,
        'hash_to_slash_comment'   => false, // deprecated
        'header_comment'          => false,
        'heredoc_indentation'     => true,
        'heredoc_to_nowdoc'       => true,
        'implode_call'            => true, // risky
        'include'                 => true,
        'increment_style'         => [
            'style' => 'post',
        ],
        'is_null'                     => true, // risky
        'linebreak_after_opening_tag' => true,
        'list_syntax'                 => [
            'syntax' => 'short',
        ],
        'logical_operators'          => true, // risky
        'lowercase_cast'             => true,
        'lowercase_constants'        => false, // deprecated
        'lowercase_static_reference' => true,
        'magic_constant_casing'      => true,
        'magic_method_casing'        => true,
        'mb_str_functions'           => true, // risky
        'method_argument_space'      => [
            'after_heredoc'                    => true,
            'ensure_fully_multiline'           => true,
            'keep_multiple_spaces_after_comma' => false,
            'on_multiline'                     => 'ensure_fully_multiline',
        ],
        'method_chaining_indentation'            => true,
        'method_separation'                      => false, // deprecated
        'modernize_types_casting'                => true, // risky
        'multiline_comment_opening_closing'      => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'new_line_for_chained_calls',
        ],
        'native_constant_invocation' => [ // risky
            'fix_built_in' => false,
            'include'      => [
                'DIRECTORY_SEPARATOR',
                'PHP_SAPI',
                'PHP_VERSION_ID',
            ],
            'scope' => 'namespaced',
        ],
        'native_function_casing'     => true,
        'native_function_invocation' => [ // risky
            'include' => [
                '@compiler_optimized',
            ],
            'scope'  => 'namespaced',
            'strict' => true,
        ],
        'native_function_type_declaration_casing' => true,
        'new_with_braces'                         => true,
        'no_alias_functions'                      => true, // risky
        'no_alternative_syntax'                   => false,
        'no_binary_string'                        => true,
        'no_blank_lines_after_class_opening'      => true,
        'no_blank_lines_after_phpdoc'             => true,
        'no_blank_lines_before_namespace'         => false,
        'no_empty_comment'                        => true,
        'no_empty_phpdoc'                         => true,
        'no_empty_statement'                      => true,
        'no_extra_blank_lines'                    => [
            'tokens' => [
                'extra',
                'break',
                'continue',
                'curly_brace_block',
                'parenthesis_brace_block',
                'square_brace_block',
                'return',
                'throw',
                'use',
                'use_trait',
                'switch',
                'case',
                'default',
            ],
        ],
        'no_extra_consecutive_blank_lines' => false, // deprecated
        'no_homoglyph_names'               => true, // risky
        'no_leading_import_slash'          => true,
        'no_leading_namespace_whitespace'  => true,
        'no_mixed_echo_print'              => [
            'use' => 'echo',
        ],
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_multiline_whitespace_before_semicolons'   => false, // deprecated
        'no_null_property_initialization'             => true,
        'no_php4_constructor'                         => true, // risky
        'no_short_bool_cast'                          => true,
        'no_short_echo_tag'                           => false,
        'no_singleline_whitespace_before_semicolons'  => true,
        'no_spaces_after_function_name'               => true,
        'no_spaces_around_offset'                     => [
            'positions' => [
                'inside',
                'outside',
            ],
        ],
        'no_spaces_inside_parenthesis'                     => true,
        'no_superfluous_elseif'                            => true,
        'no_superfluous_phpdoc_tags'                       => false,
        'no_trailing_comma_in_list_call'                   => true,
        'no_trailing_comma_in_singleline_array'            => true,
        'no_unneeded_control_parentheses'                  => true,
        'no_unneeded_curly_braces'                         => true,
        'no_unneeded_final_method'                         => true, // risky
        'no_unreachable_default_argument_value'            => true, // risky
        'no_unset_cast'                                    => true,
        'no_unset_on_property'                             => true, // risky
        'no_unused_imports'                                => true,
        'no_useless_else'                                  => true,
        'no_useless_return'                                => true,
        'no_whitespace_before_comma_in_array'              => true,
        'no_whitespace_in_blank_line'                      => true,
        'non_printable_character'                          => true, // risky
        'normalize_index_brace'                            => true,
        'not_operator_with_space'                          => false,
        'not_operator_with_successor_space'                => false,
        'nullable_type_declaration_for_default_null_value' => true,
        'object_operator_without_whitespace'               => true,
        'ordered_class_elements'                           => true,
        'ordered_imports'                                  => true,
        'ordered_interfaces'                               => false, // risky
        'php_unit_construct'                               => true, // risky
        'php_unit_dedicate_assert'                         => ['target' => 'newest'], // risky
        'php_unit_dedicate_assert_internal_type'           => ['target' => 'newest'], // risky
        'php_unit_expectation'                             => true, // risky
        'php_unit_fqcn_annotation'                         => true,
        'php_unit_internal_class'                          => true,
        'php_unit_method_casing'                           => true,
        'php_unit_mock'                                    => true, // risky
        'php_unit_mock_short_will_return'                  => true, // risky
        'php_unit_namespaced'                              => true, // risky
        'php_unit_no_expectation_annotation'               => false, // risky
        'php_unit_ordered_covers'                          => true,
        'php_unit_set_up_tear_down_visibility'             => true, // risky
        'php_unit_size_class'                              => true,
        'php_unit_strict'                                  => true, // risky
        'php_unit_test_annotation'                         => false, // risky
        'php_unit_test_case_static_method_calls'           => [ // risky
            'call_type' => 'this',
        ],
        'php_unit_test_class_requires_covers'           => false,
        'phpdoc_add_missing_param_annotation'           => ['only_untyped' => false],
        'phpdoc_align'                                  => true,
        'phpdoc_annotation_without_dot'                 => true,
        'phpdoc_indent'                                 => true,
        'phpdoc_inline_tag'                             => true,
        'phpdoc_line_span'                              => true,
        'phpdoc_no_access'                              => true,
        'phpdoc_no_alias_tag'                           => true,
        'phpdoc_no_empty_return'                        => false,
        'phpdoc_no_package'                             => true,
        'phpdoc_no_useless_inheritdoc'                  => true,
        'phpdoc_order'                                  => true,
        'phpdoc_return_self_reference'                  => true,
        'phpdoc_scalar'                                 => true,
        'phpdoc_separation'                             => true,
        'phpdoc_single_line_var_spacing'                => true,
        'phpdoc_summary'                                => true,
        'phpdoc_to_comment'                             => true,
        'phpdoc_to_param_type'                          => true, // risky
        'phpdoc_to_return_type'                         => false, // risky
        'phpdoc_trim'                                   => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types'                                  => true,
        'phpdoc_types_order'                            => true,
        'phpdoc_var_annotation_correct_order'           => true,
        'phpdoc_var_without_name'                       => true,
        'pow_to_exponentiation'                         => true,
        'pre_increment'                                 => false, // deprecated
        'protected_to_private'                          => false,
        'psr0'                                          => false,
        'psr4'                                          => true,
        'random_api_migration'                          => true,
        'return_assignment'                             => true,
        'return_type_declaration'                       => true,
        'self_accessor'                                 => true, // risky
        'self_static_accessor'                          => true,
        'semicolon_after_instruction'                   => true,
        'set_type_to_cast'                              => true, // risky
        'short_scalar_cast'                             => true,
        'silenced_deprecation_error'                    => false, // deprecated
        'simple_to_complex_string_variable'             => true,
        'simplified_null_return'                        => true,
        'single_blank_line_before_namespace'            => true,
        'single_class_element_per_statement'            => true,
        'single_import_per_statement'                   => true,
        'single_line_after_imports'                     => true,
        'single_line_comment_style'                     => true,
        'single_line_throw'                             => false,
        'single_quote'                                  => true,
        'single_trait_insert_per_statement'             => true,
        'space_after_semicolon'                         => true,
        'standardize_increment'                         => true,
        'standardize_not_equals'                        => true,
        'static_lambda'                                 => true, // risky
        'strict_comparison'                             => true, // risky
        'strict_param'                                  => true, // risky
        'string_line_ending'                            => true, // risky
        'switch_case_semicolon_to_colon'                => true,
        'switch_case_space'                             => true,
        'ternary_operator_spaces'                       => true,
        'ternary_to_null_coalescing'                    => true,
        'trailing_comma_in_multiline_array'             => true,
        'trim_array_spaces'                             => true,
        'unary_operator_spaces'                         => true,
        'visibility_required'                           => [
            'elements' => [
                'const',
                'method',
                'property',
            ],
        ],
        'void_return'                     => false, // risky
        'whitespace_after_comma_in_array' => true,
        'yoda_style'                      => true,
    ];

    protected $requiredPHPVersion = 70300;

    protected $autoActivateIsRiskyAllowed = true;
}
