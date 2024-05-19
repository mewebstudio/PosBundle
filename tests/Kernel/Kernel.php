<?php

namespace Mews\PosBundle\Tests\Kernel;

use Mews\PosBundle\MewsPosBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        return [
           new MewsPosBundle(),
           new FrameworkBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/../config/services.yaml', 'yaml');
        $loader->load(__DIR__.'/../config/mews_pos.yaml', 'yaml');
    }
}
