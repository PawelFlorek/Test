<?php

namespace OroInc\ChainCommandBundle\Tests\DependencyInjection;

use OroInc\ChainCommandBundle\DependencyInjection\ChainCommandExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ChainCommandExtensionTest extends \PHPUnit_Framework_TestCase {

    public function testChainCommandLoad()
    {
        $loader = new ChainCommandExtension();
        $config =  "";

        $loader->load(array($config), new ContainerBuilder());
    }
}
