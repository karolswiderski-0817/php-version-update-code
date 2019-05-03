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

namespace PhpCsFixer\Tests\FixerDefinition;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerDefinition\FixerDefinition
 */
final class FixerDefinitionTest extends TestCase
{
    public function testGetSummary()
    {
        $definition = new FixerDefinition('Foo', []);

        static::assertSame('Foo', $definition->getSummary());
    }

    public function testGetCodeSamples()
    {
        $definition = new FixerDefinition('', ['Bar', 'Baz']);

        static::assertSame(['Bar', 'Baz'], $definition->getCodeSamples());
    }

    public function testGetDescription()
    {
        $definition = new FixerDefinition('', []);

        static::assertNull($definition->getDescription());

        $definition = new FixerDefinition('', [], 'Foo');

        static::assertSame('Foo', $definition->getDescription());
    }

    public function testGetRiskyDescription()
    {
        $definition = new FixerDefinition('', []);

        static::assertNull($definition->getRiskyDescription());

        $definition = new FixerDefinition('', [], null, 'Foo');

        static::assertSame('Foo', $definition->getRiskyDescription());
    }
}
