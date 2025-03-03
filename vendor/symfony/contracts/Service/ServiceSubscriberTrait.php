<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RectorPrefix20220202\Symfony\Contracts\Service;

use RectorPrefix20220202\Psr\Container\ContainerInterface;
use RectorPrefix20220202\Symfony\Contracts\Service\Attribute\SubscribedService;
/**
 * Implementation of ServiceSubscriberInterface that determines subscribed services from
 * method return types. Service ids are available as "ClassName::methodName".
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait ServiceSubscriberTrait
{
    /** @var ContainerInterface */
    protected $container;
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices() : array
    {
        static $services;
        if (null !== $services) {
            return $services;
        }
        $services = \is_callable(['parent', __FUNCTION__]) ? parent::getSubscribedServices() : [];
        $attributeOptIn = \false;
        if (\PHP_VERSION_ID >= 80000) {
            foreach ((new \ReflectionClass(self::class))->getMethods() as $method) {
                if (self::class !== $method->getDeclaringClass()->name) {
                    continue;
                }
                if (!($attribute = (\method_exists($method, 'getAttributes') ? $method->getAttributes(\RectorPrefix20220202\Symfony\Contracts\Service\Attribute\SubscribedService::class) : [])[0] ?? null)) {
                    continue;
                }
                if ($method->isStatic() || $method->isAbstract() || $method->isGenerator() || $method->isInternal() || $method->getNumberOfRequiredParameters()) {
                    throw new \LogicException(\sprintf('Cannot use "%s" on method "%s::%s()" (can only be used on non-static, non-abstract methods with no parameters).', \RectorPrefix20220202\Symfony\Contracts\Service\Attribute\SubscribedService::class, self::class, $method->name));
                }
                if (!($returnType = $method->getReturnType())) {
                    throw new \LogicException(\sprintf('Cannot use "%s" on methods without a return type in "%s::%s()".', \RectorPrefix20220202\Symfony\Contracts\Service\Attribute\SubscribedService::class, $method->name, self::class));
                }
                $serviceId = $returnType instanceof \ReflectionNamedType ? $returnType->getName() : (string) $returnType;
                if ($returnType->allowsNull()) {
                    $serviceId = '?' . $serviceId;
                }
                $services[$attribute->newInstance()->key ?? self::class . '::' . $method->name] = $serviceId;
                $attributeOptIn = \true;
            }
        }
        if (!$attributeOptIn) {
            foreach ((new \ReflectionClass(self::class))->getMethods() as $method) {
                if ($method->isStatic() || $method->isAbstract() || $method->isGenerator() || $method->isInternal() || $method->getNumberOfRequiredParameters()) {
                    continue;
                }
                if (self::class !== $method->getDeclaringClass()->name) {
                    continue;
                }
                if (!($returnType = $method->getReturnType()) instanceof \ReflectionNamedType) {
                    continue;
                }
                if ($returnType->isBuiltin()) {
                    continue;
                }
                if (\PHP_VERSION_ID >= 80000) {
                    trigger_deprecation('symfony/service-contracts', '2.5', 'Using "%s" in "%s" without using the "%s" attribute on any method is deprecated.', \RectorPrefix20220202\Symfony\Contracts\Service\ServiceSubscriberTrait::class, self::class, \RectorPrefix20220202\Symfony\Contracts\Service\Attribute\SubscribedService::class);
                }
                $services[self::class . '::' . $method->name] = '?' . ($returnType instanceof \ReflectionNamedType ? $returnType->getName() : $returnType);
            }
        }
        return $services;
    }
    /**
     * @required
     *
     * @return ContainerInterface|null
     */
    public function setContainer(\RectorPrefix20220202\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
        if (\is_callable(['parent', __FUNCTION__])) {
            return parent::setContainer($container);
        }
        return null;
    }
}
