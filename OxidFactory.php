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

class OxidFactory extends OxidAware
{
    public function getFromRegistry($serviceName)
    {
        return \oxRegistry::get($serviceName);
    }
}
