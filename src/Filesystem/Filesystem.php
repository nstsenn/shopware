<?php declare(strict_types=1);

namespace Shopware\Filesystem;

use Shopware\Filesystem\DependencyInjection\FilesystemAdapterCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class Filesystem extends Bundle
{
    protected $name = 'Filesystem';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('services.xml');

        $container->addCompilerPass(new FilesystemAdapterCompilerPass());
    }
}