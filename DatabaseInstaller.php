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

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ExecutableFinder;

/**
 * OXID database installer
 */
class DatabaseInstaller
{
    /**
     * @var string
     */
    protected $databaseHost;

    /**
     * @var string
     */
    protected $databaseName;

    /**
     * @var string
     */
    protected $databaseUser;

    /**
     * @var string
     */
    protected $databasePassword;

    /**
     * @var FileLocatorInterface
     */
    protected $fileLocator;

    /**
     * @var \mysqli
     */
    protected $connection;

    /**
     * @var string Mysql executable path
     */
    private $mysqlPath;

    /**
     * Constructor.
     *
     * @param string $databaseHost
     * @param string $databaseName
     * @param string $databaseUser
     * @param string $databasePassword
     * @param FileLocatorInterface $fileLocator
     * @param \mysqli $connection
     */
    public function __construct(
        $databaseHost,
        $databaseName,
        $databaseUser,
        $databasePassword,
        FileLocatorInterface $fileLocator,
        \mysqli $connection
    ) {
        $this->databaseHost = $databaseHost;
        $this->databaseName = $databaseName;
        $this->databaseUser = $databaseUser;
        $this->databasePassword = $databasePassword;
        $this->fileLocator = $fileLocator;
        $this->connection = $connection;
    }

    /**
     * Check whenever the database is installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        return $this->connection->select_db($this->databaseName);
    }

    /**
     * Install OXID database
     */
    public function install()
    {
        $this->createDatabase($this->databaseName);
        $this->importDump($this->databaseName, 'sql/database.sql');
    }

    /**
     * Install demodata
     */
    public function installDemodata()
    {
        $this->importDump($this->databaseName, 'sql/demodata.sql');
    }

    /**
     * Import sql dump into database
     *
     * @param string $database Database name
     * @param string $filename File name
     * @param int    $timeout  Timeout in seconds
     *
     * @throws \RuntimeException
     */
    protected function importDump($database, $filename, $timeout = 180)
    {
        $path = $this->fileLocator->locate($filename);
        $cmd = escapeshellarg(
            $this->getMysql() . ' -u'
            . $this->databaseUser
            . ' --password=' . $this->databasePassword
            . ' --host=' . $this->databaseHost
            . ' ' . $database
        ) . ' < ' . $path;

        $process = new Process($cmd, null, null, null, $timeout);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('An error occurred on importing database dump.');
        }
    }

    /**
     * Create database
     *
     * @param string $database Database name
     */
    protected function createDatabase($database)
    {
        $this->connection->query(
            'CREATE DATABASE `'
            . $this->connection->escape_string($database)
            . '`'
        );
    }

    /**
     * Get mysql executable path
     *
     * @return string
     */
    protected function getMysql()
    {
        if (null === $this->mysqlPath) {
            $finder = new ExecutableFinder();
            $this->mysqlPath = $finder->find('mysql', 'mysql');
        }

        return $this->mysqlPath;
    }
}
