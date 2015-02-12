<?php

/*
 * This file is part of the OXID package.
 *
 * (c) Eligijus Vitkauskas <eligijusvitkauskas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ellis\Oxid\Bundle\FrameworkBundle;

use Ellis\Oxid\Bundle\FrameworkBundle\Bootstrapper\BootstrapperInterface;

abstract class OxidAware implements OxidAwareInterface
{
    /**
     * @var BootstrapperInterface
     */
    protected $bootstrapper;

    /**
     * {@inheritdoc}
     */
    public function setOxidBootstrapper(BootstrapperInterface $bootstrapper)
    {
        $this->bootstrapper = $bootstrapper;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrapOxid()
    {
        if (!$this->bootstrapper->isBootstrapped()) {
            $this->bootstrapper->bootstrap();
        }
    }
}
