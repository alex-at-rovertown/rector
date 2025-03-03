<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RectorPrefix20220202\Symfony\Contracts\Tests\Service;

use RectorPrefix20220202\PHPUnit\Framework\TestCase;
use RectorPrefix20220202\Psr\Container\ContainerInterface;
use RectorPrefix20220202\Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\OtherDir\Component1\Dir1\Service1;
use RectorPrefix20220202\Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\OtherDir\Component1\Dir2\Service2;
use RectorPrefix20220202\Symfony\Contracts\Service\Attribute\SubscribedService;
use RectorPrefix20220202\Symfony\Contracts\Service\ServiceLocatorTrait;
use RectorPrefix20220202\Symfony\Contracts\Service\ServiceSubscriberInterface;
use RectorPrefix20220202\Symfony\Contracts\Service\ServiceSubscriberTrait;
use RectorPrefix20220202\Symfony\Contracts\Tests\Fixtures\TestServiceSubscriberUnion;
class ServiceSubscriberTraitTest extends \RectorPrefix20220202\PHPUnit\Framework\TestCase
{
    /**
     * @group legacy
     */
    public function testLegacyMethodsOnParentsAndChildrenAreIgnoredInGetSubscribedServices()
    {
        $expected = [\RectorPrefix20220202\Symfony\Contracts\Tests\Service\LegacyTestService::class . '::aService' => '?' . \RectorPrefix20220202\Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\OtherDir\Component1\Dir2\Service2::class];
        $this->assertEquals($expected, \RectorPrefix20220202\Symfony\Contracts\Tests\Service\LegacyChildTestService::getSubscribedServices());
    }
    /**
     * @requires PHP 8
     */
    public function testMethodsOnParentsAndChildrenAreIgnoredInGetSubscribedServices()
    {
        $expected = [\RectorPrefix20220202\Symfony\Contracts\Tests\Service\TestService::class . '::aService' => \RectorPrefix20220202\Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\OtherDir\Component1\Dir2\Service2::class, \RectorPrefix20220202\Symfony\Contracts\Tests\Service\TestService::class . '::nullableService' => '?' . \RectorPrefix20220202\Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\OtherDir\Component1\Dir2\Service2::class];
        $this->assertEquals($expected, \RectorPrefix20220202\Symfony\Contracts\Tests\Service\ChildTestService::getSubscribedServices());
    }
    public function testSetContainerIsCalledOnParent()
    {
        $container = new class([]) implements \RectorPrefix20220202\Psr\Container\ContainerInterface
        {
            use ServiceLocatorTrait;
        };
        $this->assertSame($container, (new \RectorPrefix20220202\Symfony\Contracts\Tests\Service\TestService())->setContainer($container));
    }
    /**
     * @requires PHP 8
     * @group legacy
     */
    public function testMethodsWithUnionReturnTypesAreIgnored()
    {
        $expected = [\RectorPrefix20220202\Symfony\Contracts\Tests\Fixtures\TestServiceSubscriberUnion::class . '::method1' => 'RectorPrefix20220202\\?Symfony\\Contracts\\Tests\\Fixtures\\Service1'];
        $this->assertEquals($expected, \RectorPrefix20220202\Symfony\Contracts\Tests\Fixtures\TestServiceSubscriberUnion::getSubscribedServices());
    }
}
class ParentTestService
{
    public function aParentService() : \RectorPrefix20220202\Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\OtherDir\Component1\Dir1\Service1
    {
    }
    public function setContainer(\RectorPrefix20220202\Psr\Container\ContainerInterface $container)
    {
        return $container;
    }
}
class LegacyTestService extends \RectorPrefix20220202\Symfony\Contracts\Tests\Service\ParentTestService implements \RectorPrefix20220202\Symfony\Contracts\Service\ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;
    public function aService() : \RectorPrefix20220202\Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\OtherDir\Component1\Dir2\Service2
    {
    }
}
class LegacyChildTestService extends \RectorPrefix20220202\Symfony\Contracts\Tests\Service\LegacyTestService
{
    public function aChildService() : \RectorPrefix20220202\Symfony\Contracts\Tests\Service\Service3
    {
    }
}
class TestService extends \RectorPrefix20220202\Symfony\Contracts\Tests\Service\ParentTestService implements \RectorPrefix20220202\Symfony\Contracts\Service\ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;
    #[SubscribedService]
    public function aService() : \RectorPrefix20220202\Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\OtherDir\Component1\Dir2\Service2
    {
    }
    #[SubscribedService]
    public function nullableService() : ?\RectorPrefix20220202\Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\OtherDir\Component1\Dir2\Service2
    {
    }
}
class ChildTestService extends \RectorPrefix20220202\Symfony\Contracts\Tests\Service\TestService
{
    #[SubscribedService]
    public function aChildService() : \RectorPrefix20220202\Symfony\Contracts\Tests\Service\Service3
    {
    }
}
class Service3
{
}
