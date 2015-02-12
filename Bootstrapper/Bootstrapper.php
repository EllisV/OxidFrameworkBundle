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
 * OXID Bootstrapper.
 */
class Bootstrapper implements BootstrapperInterface
{
    /**
     * @var string
     */
    protected $webDir;

    /**
     * Constructor.
     *
     * @param string $webDir
     */
    public function __construct($webDir)
    {
        $this->webDir = $webDir;
    }

    /**
     * {@inheritdoc}
     */
    public function isBootstrapped()
    {
        return class_exists('\oxConfig');
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap()
    {
        require_once $this->webDir.'/bootstrap.php';
    }
}
