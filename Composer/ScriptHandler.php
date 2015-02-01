<?php

/*
 * This file is part of the OXID package.
 *
 * (c) Eligijus Vitkauskas <eligijusvitkauskas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ellis\Oxid\Bundle\FrameworkBundle\Composer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\CommandEvent;

/**
 * Composer scripts handler
 */
class ScriptHandler
{
    private static $options = array(
        'app-dir' => 'app'
    );

    /**
     * Clears the application cache.
     *
     * @param CommandEvent $event
     */
    public static function clearCache(CommandEvent $event)
    {
        $options = self::getOptions($event);

        static::executeCommand($event, 'cache:clear', $options['process-timeout']);
    }

    /**
     * Installs OXID database.
     *
     * @param CommandEvent $event
     */
    public static function installDatabase(CommandEvent $event)
    {
        $options = self::getOptions($event);

        static::executeCommand($event, 'database:install', $options['process-timeout']);
    }

    /**
     * @author Jordi Boggiano <j.boggiano@seld.be>
     */
    protected static function executeCommand(CommandEvent $event, $cmd, $timeout = 300)
    {
        $options = self::getOptions($event);
        $php = escapeshellarg(self::getPhp(false));
        $phpArgs = implode(' ', array_map('escapeshellarg', self::getPhpArguments()));
        $console = escapeshellarg($options['app-dir'].'/console');
        if ($event->getIO()->isDecorated()) {
            $console .= ' --ansi';
        }

        $process = new Process($php.($phpArgs ? ' '.$phpArgs : '').' '.$console.' '.$cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) use ($event) { $event->getIO()->write($buffer, false); });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', escapeshellarg($cmd)));
        }
    }

    /**
     * @author Jordi Boggiano <j.boggiano@seld.be>
     */
    protected static function getOptions(CommandEvent $event)
    {
        $options = array_merge(self::$options, $event->getComposer()->getPackage()->getExtra());

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }

    /**
     * @author Jordi Boggiano <j.boggiano@seld.be>
     */
    protected static function getPhp($includeArgs = true)
    {
        $phpFinder = new PhpExecutableFinder();
        if (!$phpPath = $phpFinder->find($includeArgs)) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        return $phpPath;
    }

    /**
     * @author Jordi Boggiano <j.boggiano@seld.be>
     */
    protected static function getPhpArguments()
    {
        $arguments = array();

        $phpFinder = new PhpExecutableFinder();
        if (method_exists($phpFinder, 'findArguments')) {
            $arguments = $phpFinder->findArguments();
        }

        if (false !== $ini = php_ini_loaded_file()) {
            $arguments[] = '--php-ini='.$ini;
        }

        return $arguments;
    }
}
