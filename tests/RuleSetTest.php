<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests;

use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class RuleSetTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $ruleSet = RuleSet::create();

        $this->assertInstanceOf('PhpCsFixer\RuleSet', $ruleSet);
    }

    /**
     * @dataProvider provideAllRulesFromSets
     */
    public function testIfAllRulesInSetsExists($rule)
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixers = array();

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        $this->assertArrayHasKey($rule, $fixers);
    }

    public function provideAllRulesFromSets()
    {
        $cases = array();
        foreach (RuleSet::create()->getSetDefinitionNames() as $setName) {
            $cases = array_merge($cases, RuleSet::create(array($setName => true))->getRules());
        }

        return array_map(
            function ($item) {
                return array($item);
            },
            array_keys($cases)
        );
    }

    /**
     * @expectedException        \UnexpectedValueException
     * @expectedExceptionMessage Set "@foo" does not exist.
     */
    public function testResolveRulesWithInvalidSet()
    {
        RuleSet::create(array(
            '@foo' => true,
        ));
    }

    public function testResolveRulesWithSet()
    {
        $ruleSet = RuleSet::create(array(
            'strict' => true,
            '@PSR1' => true,
            'braces' => true,
            'encoding' => false,
            'unix_line_endings' => true,
        ));

        $this->assertSameRules(
            array(
                'strict' => true,
                'full_opening_tag' => true,
                'braces' => true,
                'unix_line_endings' => true,
            ),
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithNestedSet()
    {
        $ruleSet = RuleSet::create(array(
            '@PSR2' => true,
            'strict' => true,
        ));

        $this->assertSameRules(
            array(
                'encoding' => true,
                'full_opening_tag' => true,
                'unix_line_endings' => true,
                'no_tab_indentation' => true,
                'no_trailing_whitespace' => true,
                'no_closing_tag' => true,
                'elseif' => true,
                'visibility_required' => true,
                'lowercase_keywords' => true,
                'single_line_after_imports' => true,
                'switch_case_space' => true,
                'switch_case_semicolon_to_colon' => true,
                'no_spaces_inside_parenthesis' => true,
                'single_import_per_statement' => true,
                'no_spaces_after_function_name' => true,
                'method_argument_space' => true,
                'function_declaration' => true,
                'lowercase_constants' => true,
                'blank_line_after_namespace' => true,
                'braces' => true,
                'class_definition' => true,
                'single_blank_line_at_eof' => true,
                'strict' => true,
            ),
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithDisabledSet()
    {
        $ruleSet = RuleSet::create(array(
            '@PSR2' => true,
            '@PSR1' => false,
            'encoding' => true,
        ));

        $this->assertSameRules(
            array(
                'encoding' => true,
                'unix_line_endings' => true,
                'no_tab_indentation' => true,
                'no_trailing_whitespace' => true,
                'no_closing_tag' => true,
                'elseif' => true,
                'visibility_required' => true,
                'lowercase_keywords' => true,
                'single_line_after_imports' => true,
                'switch_case_space' => true,
                'switch_case_semicolon_to_colon' => true,
                'no_spaces_inside_parenthesis' => true,
                'single_import_per_statement' => true,
                'no_spaces_after_function_name' => true,
                'method_argument_space' => true,
                'function_declaration' => true,
                'lowercase_constants' => true,
                'blank_line_after_namespace' => true,
                'braces' => true,
                'class_definition' => true,
                'single_blank_line_at_eof' => true,
            ),
            $ruleSet->getRules()
        );
    }

    private function assertSameRules(array $expected, array $actual, $message = '')
    {
        ksort($expected);
        ksort($actual);

        $this->assertSame($expected, $actual, $message);
    }
}
