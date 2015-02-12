<?php

/*
 * This file is part of the OXID package.
 *
 * (c) Eligijus Vitkauskas <eligijusvitkauskas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ellis\Oxid\Bundle\FrameworkBundle\Bootstrapper;

/**
 * An interface to be implemented for OXID bootstrapper
 */
interface BootstrapperInterface
{
    /**
     * Is OXID bootstrapped?
     *
     * @return bool
     */
    public function isBootstrapped();

    /**
     * Bootstrap OXID if it hasn't been bootstrapped yet
     */
    public function bootstrap();
}
