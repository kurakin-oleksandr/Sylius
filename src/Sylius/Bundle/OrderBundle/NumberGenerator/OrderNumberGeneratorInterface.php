<?php

/*
 * This file is a part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\NumberGenerator;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface OrderNumberGeneratorInterface
{
    /**
     * @return string
     */
    public function generate();
}
