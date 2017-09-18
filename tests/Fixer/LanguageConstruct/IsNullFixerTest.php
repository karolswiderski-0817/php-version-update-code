<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer
 */
final class IsNullFixerTest extends AbstractFixerTestCase
{
    public function testConfigurationWrongOption()
    {
        $fixer = new IsNullFixer();

        $this->setExpectedException(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '[is_null] Invalid configuration: The option "yoda" does not exist.'
        );
        $fixer->configure(array('yoda' => true));
    }

    public function testConfigurationWrongValue()
    {
        $fixer = new IsNullFixer();

        $this->setExpectedException(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '[is_null] Invalid configuration: The option "use_yoda_style" with value -1 is expected to be of type "bool", but is of type "integer".'
        );
        $fixer->configure(array('use_yoda_style' => -1));
    }

    public function testCorrectConfiguration()
    {
        $fixer = new IsNullFixer();
        $fixer->configure(array('use_yoda_style' => false));

        $configuration = static::getObjectAttribute($fixer, 'configuration');
        static::assertFalse($configuration['use_yoda_style']);
    }

    /**
     * @dataProvider provideYodaFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testYodaFix($expected, $input = null)
    {
        $this->fixer->configure(array('use_yoda_style' => true));
        $this->doTest($expected, $input);
    }

    public function provideYodaFixCases()
    {
        $multiLinePatternToFix = <<<'FIX'
<?php $x =
is_null

(
    json_decode
    (
        $x
    )

)

;
FIX;
        $multiLinePatternFixed = <<<'FIXED'
<?php $x =
null === json_decode
    (
        $x
    )

;
FIXED;

        return array(
            array('<?php $x = "is_null";'),

            array('<?php $x = ClassA::is_null(json_decode($x));'),
            array('<?php $x = ScopeA\\is_null(json_decode($x));'),
            array('<?php $x = namespace\\is_null(json_decode($x));'),
            array('<?php $x = $object->is_null(json_decode($x));'),

            array('<?php $x = new \\is_null(json_decode($x));'),
            array('<?php $x = new is_null(json_decode($x));'),
            array('<?php $x = new ScopeB\\is_null(json_decode($x));'),

            array('<?php is_nullSmth(json_decode($x));'),
            array('<?php smth_is_null(json_decode($x));'),

            array('<?php "SELECT ... is_null(json_decode($x)) ...";'),
            array('<?php "SELECT ... is_null(json_decode($x)) ...";'),
            array('<?php "test" . "is_null" . "in concatenation";'),

            array('<?php $x = null === json_decode($x);', '<?php $x = is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = !is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = ! is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = ! is_null( json_decode($x) );'),

            array('<?php $x = null === json_decode($x);', '<?php $x = \\is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = !\\is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = ! \\is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = ! \\is_null( json_decode($x) );'),

            array('<?php $x = null === json_decode($x).".dist";', '<?php $x = is_null(json_decode($x)).".dist";'),
            array('<?php $x = null !== json_decode($x).".dist";', '<?php $x = !is_null(json_decode($x)).".dist";'),
            array('<?php $x = null === json_decode($x).".dist";', '<?php $x = \\is_null(json_decode($x)).".dist";'),
            array('<?php $x = null !== json_decode($x).".dist";', '<?php $x = !\\is_null(json_decode($x)).".dist";'),

            array($multiLinePatternFixed, $multiLinePatternToFix),
            array(
                '<?php $x = /**/null === /**/ /** x*//**//** */json_decode($x)/***//*xx*/;',
                '<?php $x = /**/is_null/**/ /** x*/(/**//** */json_decode($x)/***/)/*xx*/;',
            ),
            array(
                '<?php $x = null === (null === $x ? z(null === $y) : z(null === $z));',
                '<?php $x = is_null(is_null($x) ? z(is_null($y)) : z(is_null($z)));',
            ),
            array(
                '<?php $x = null === ($x = array());',
                '<?php $x = is_null($x = array());',
            ),
            array(
                '<?php null === a(null === a(null === a(null === b())));',
                '<?php \is_null(a(\is_null(a(\is_null(a(\is_null(b())))))));',
            ),
            array(
                '<?php $d= null === ($a)/*=?*/?>',
                "<?php \$d= is_null(\n(\$a)/*=?*/\n)?>",
            ),
            array(
                '<?php is_null()?>',
            ),

            // edge cases: is_null wrapped into a binary operations
            array(
                '<?php $result = (false === (null === $a)); ?>',
                '<?php $result = (false === is_null($a)); ?>',
            ),
            array(
                '<?php $result = ((null === $a) === false); ?>',
                '<?php $result = (is_null($a) === false); ?>',
            ),
            array(
                '<?php $result = (false !== (null === $a)); ?>',
                '<?php $result = (false !== is_null($a)); ?>',
            ),
            array(
                '<?php $result = ((null === $a) !== false); ?>',
                '<?php $result = (is_null($a) !== false); ?>',
            ),
            array(
                '<?php $result = (false == (null === $a)); ?>',
                '<?php $result = (false == is_null($a)); ?>',
            ),
            array(
                '<?php $result = ((null === $a) == false); ?>',
                '<?php $result = (is_null($a) == false); ?>',
            ),
            array(
                '<?php $result = (false != (null === $a)); ?>',
                '<?php $result = (false != is_null($a)); ?>',
            ),
            array(
                '<?php $result = ((null === $a) != false); ?>',
                '<?php $result = (is_null($a) != false); ?>',
            ),
            array(
                '<?php $result = (false <> (null === $a)); ?>',
                '<?php $result = (false <> is_null($a)); ?>',
            ),
            array(
                '<?php $result = ((null === $a) <> false); ?>',
                '<?php $result = (is_null($a) <> false); ?>',
            ),
            array(
                '<?php if (null === $x) echo "foo"; ?>',
                '<?php if (is_null($x)) echo "foo"; ?>',
            ),
            // check with logical operator
            array(
                '<?php if (null === $x && $y) echo "foo"; ?>',
                '<?php if (is_null($x) && $y) echo "foo"; ?>',
            ),
            array(
                '<?php if (null === $x || $y) echo "foo"; ?>',
                '<?php if (is_null($x) || $y) echo "foo"; ?>',
            ),
            array(
                '<?php if (null === $x xor $y) echo "foo"; ?>',
                '<?php if (is_null($x) xor $y) echo "foo"; ?>',
            ),
            array(
                '<?php if (null === $x and $y) echo "foo"; ?>',
                '<?php if (is_null($x) and $y) echo "foo"; ?>',
            ),
            array(
                '<?php if (null === $x or $y) echo "foo"; ?>',
                '<?php if (is_null($x) or $y) echo "foo"; ?>',
            ),
            array(
                '<?php if ((null === $u or $v) and ($w || null === $x) xor (null !== $y and $z)) echo "foo"; ?>',
                '<?php if ((is_null($u) or $v) and ($w || is_null($x)) xor (!is_null($y) and $z)) echo "foo"; ?>',
            ),
        );
    }

    /**
     * @dataProvider provideNonYodaFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testNonYodaFix($expected, $input)
    {
        $this->fixer->configure(array('use_yoda_style' => false));
        $this->doTest($expected, $input);
    }

    public function provideNonYodaFixCases()
    {
        return array(
            array(
                '<?php $x = $y === null;', '<?php $x = is_null($y);',
            ),
            array(
                '<?php $b = a(a(a(b() === null) === null) === null) === null;',
                '<?php $b = \is_null(a(\is_null(a(\is_null(a(\is_null(b())))))));',
            ),
            array(
              '<?php if ($x === null && $y) echo "foo";',
              '<?php if (is_null($x) && $y) echo "foo";',
            ),
            array(
                '<?php $x = ($x = array()) === null;',
                '<?php $x = is_null($x = array());',
            ),
            array(
                '<?php while (($nextMaxId = $myTimeline->getNextMaxId()) === null);',
                '<?php while (is_null($nextMaxId = $myTimeline->getNextMaxId()));',
            ),
        );
    }
}
