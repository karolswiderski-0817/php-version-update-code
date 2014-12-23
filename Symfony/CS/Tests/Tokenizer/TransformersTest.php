<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer;

use Symfony\CS\Tokenizer\Transformers;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class TransformersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCustomTokenPrefixCases
     */
    public function testCustomTokenPrefix($name)
    {
        $this->assertStringStartsWith('CT_', $name, 'Custom token should start with `CT_` prefix.');
    }

    public function provideCustomTokenPrefixCases()
    {
        $transformers = Transformers::create();

        $transformersReflection = new \ReflectionClass($transformers);
        $propertyReflection = $transformersReflection->getProperty('items');
        $propertyReflection->setAccessible(true);

        $items = $propertyReflection->getValue($transformers);

        $cases = array();

        foreach ($items as $item) {
            foreach ($item->getCustomTokenNames() as $name) {
                $cases[] = array($name);
            }
        }

        return $cases;
    }
}
