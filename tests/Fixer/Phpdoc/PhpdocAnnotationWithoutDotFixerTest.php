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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class PhpdocAnnotationWithoutDotFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
    /**
     * Summary.
     *
     * Description.
     *
     * @param string $str        Some string
     * @param string $ellipsis1  Ellipsis is this: ...
     * @param string $ellipsis2  Ellipsis is this: 。。。
     * @param string $ellipsis3  Ellipsis is this: …
     * @param bool   $isStr      Is it a string?
     * @param int    $int        Some multiline
     *                           description. With many dots
     *
     * @return array Result array
     *
     * @SomeCustomAnnotation This is important sentence that must not be modified.
     */',
                '<?php
    /**
     * Summary.
     *
     * Description.
     *
     * @param string $str        Some string.
     * @param string $ellipsis1  Ellipsis is this: ...
     * @param string $ellipsis2  Ellipsis is this: 。。。
     * @param string $ellipsis3  Ellipsis is this: …
     * @param bool   $isStr      Is it a string?
     * @param int    $int        Some multiline
     *                           description. With many dots.
     *
     * @return array Result array。
     *
     * @SomeCustomAnnotation This is important sentence that must not be modified.
     */',
            ),
            array(
                // invalid char inside line won't crash the fixer
                '<?php
    /**
     * @var string This: '.chr(174).' is an odd character.
     * @var string This: '.chr(174).' is an odd character 2nd time。
     */',
            ),
        );
    }
}
