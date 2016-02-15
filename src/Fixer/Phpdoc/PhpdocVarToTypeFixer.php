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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractPhpdocTagsFixer;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
final class PhpdocVarToTypeFixer extends AbstractPhpdocTagsFixer
{
    /**
     * {@inheritdoc}
     */
    protected static $search = array('var');

    /**
     * {@inheritdoc}
     */
    protected static $replace = 'type';

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@var should always be written as @type.';
    }
}
