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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Config;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 * @group covers-nothing
 */
final class ProjectFixerConfigurationTest extends TestCase
{
    public function testCreate()
    {
        $config = $this->loadConfig();

        static::assertInstanceOf(\PhpCsFixer\Config::class, $config);
        static::assertEmpty($config->getCustomFixers());
        static::assertNotEmpty($config->getRules());

        // call so the fixers get configured to reveal issue (like deprecated configuration used etc.)
        $resolver = new ConfigurationResolver(
            $config,
            [],
            __DIR__,
            new ToolInfo()
        );

        $resolver->getFixers();
    }

    public function testRuleDefinedAlpha()
    {
        $rules = $rulesSorted = array_keys($this->loadConfig()->getRules());
        sort($rulesSorted);
        static::assertSame($rulesSorted, $rules, 'Please sort the "rules" in `.php-cs-fixer.dist.php` of this project.');
    }

    /**
     * @return Config
     */
    private function loadConfig()
    {
        return require __DIR__.'/../../.php-cs-fixer.dist.php';
    }
}
