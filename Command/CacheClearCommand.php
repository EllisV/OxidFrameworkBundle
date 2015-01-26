<?php

/*
 * This file is part of the OXID package.
 *
 * (c) Eligijus Vitkauskas <eligijusvitkauskas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ellis\Oxid\Bundle\FrameworkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Clear the cache.
 *
 * @author Francis Besset <francis.besset@gmail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Eligijus Vitkauskas <eligijusvitkauskas@gmail.com>
 */
class CacheClearCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clears the cache')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command clears the application cache for a given environment
and debug mode:

  <info>php %command.full_name% --env=dev</info>
  <info>php %command.full_name% --env=prod --no-debug</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $realCacheDir = $this->getContainer()->getParameter('kernel.cache_dir');
        $oldCacheDir = $realCacheDir.'_old';
        $filesystem = $this->getContainer()->get('filesystem');

        if (!is_writable($realCacheDir)) {
            throw new \RuntimeException(sprintf('Unable to write in the "%s" directory', $realCacheDir));
        }

        if ($filesystem->exists($oldCacheDir)) {
            $filesystem->remove($oldCacheDir);
        }

        $kernel = $this->getContainer()->get('kernel');
        $output->writeln(sprintf('Clearing the cache for the <info>%s</info> environment with debug <info>%s</info>', $kernel->getEnvironment(), var_export($kernel->isDebug(), true)));
        $this->getContainer()->get('cache_clearer')->clear($realCacheDir);

        $filesystem->rename($realCacheDir, $oldCacheDir);

        if ($output->isVerbose()) {
            $output->writeln('  Removing old cache directory');
        }

        $filesystem->remove($oldCacheDir);

        if ($output->isVerbose()) {
            $output->writeln('  Done');
        }
    }

    /**
     * Set Container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get Container
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
