<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RectorPrefix20210618\Symfony\Component\EventDispatcher\Debug;

use RectorPrefix20210618\Psr\EventDispatcher\StoppableEventInterface;
use RectorPrefix20210618\Psr\Log\LoggerInterface;
use RectorPrefix20210618\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use RectorPrefix20210618\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use RectorPrefix20210618\Symfony\Component\HttpFoundation\Request;
use RectorPrefix20210618\Symfony\Component\HttpFoundation\RequestStack;
use RectorPrefix20210618\Symfony\Component\Stopwatch\Stopwatch;
use RectorPrefix20210618\Symfony\Contracts\Service\ResetInterface;
/**
 * Collects some data about event listeners.
 *
 * This event dispatcher delegates the dispatching to another one.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TraceableEventDispatcher implements \RectorPrefix20210618\Symfony\Component\EventDispatcher\EventDispatcherInterface, \RectorPrefix20210618\Symfony\Contracts\Service\ResetInterface
{
    protected $logger;
    protected $stopwatch;
    private $callStack;
    private $dispatcher;
    private $wrappedListeners;
    private $orphanedEvents;
    private $requestStack;
    private $currentRequestHash = '';
    public function __construct(\RectorPrefix20210618\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher, \RectorPrefix20210618\Symfony\Component\Stopwatch\Stopwatch $stopwatch, \RectorPrefix20210618\Psr\Log\LoggerInterface $logger = null, \RectorPrefix20210618\Symfony\Component\HttpFoundation\RequestStack $requestStack = null)
    {
        $this->dispatcher = $dispatcher;
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;
        $this->wrappedListeners = [];
        $this->orphanedEvents = [];
        $this->requestStack = $requestStack;
    }
    /**
     * {@inheritdoc}
     */
    public function addListener(string $eventName, $listener, int $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);
    }
    /**
     * {@inheritdoc}
     */
    public function addSubscriber(\RectorPrefix20210618\Symfony\Component\EventDispatcher\EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
    }
    /**
     * {@inheritdoc}
     */
    public function removeListener(string $eventName, $listener)
    {
        if (isset($this->wrappedListeners[$eventName])) {
            foreach ($this->wrappedListeners[$eventName] as $index => $wrappedListener) {
                if ($wrappedListener->getWrappedListener() === $listener) {
                    $listener = $wrappedListener;
                    unset($this->wrappedListeners[$eventName][$index]);
                    break;
                }
            }
        }
        return $this->dispatcher->removeListener($eventName, $listener);
    }
    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(\RectorPrefix20210618\Symfony\Component\EventDispatcher\EventSubscriberInterface $subscriber)
    {
        return $this->dispatcher->removeSubscriber($subscriber);
    }
    /**
     * {@inheritdoc}
     */
    public function getListeners(string $eventName = null)
    {
        return $this->dispatcher->getListeners($eventName);
    }
    /**
     * {@inheritdoc}
     */
    public function getListenerPriority(string $eventName, $listener)
    {
        // we might have wrapped listeners for the event (if called while dispatching)
        // in that case get the priority by wrapper
        if (isset($this->wrappedListeners[$eventName])) {
            foreach ($this->wrappedListeners[$eventName] as $index => $wrappedListener) {
                if ($wrappedListener->getWrappedListener() === $listener) {
                    return $this->dispatcher->getListenerPriority($eventName, $wrappedListener);
                }
            }
        }
        return $this->dispatcher->getListenerPriority($eventName, $listener);
    }
    /**
     * {@inheritdoc}
     */
    public function hasListeners(string $eventName = null)
    {
        return $this->dispatcher->hasListeners($eventName);
    }
    /**
     * {@inheritdoc}
     * @param string|null $eventName
     * @param object $event
     * @return object
     */
    public function dispatch($event, $eventName = null)
    {
        $eventName = $eventName ?? \get_class($event);
        if (null === $this->callStack) {
            $this->callStack = new \SplObjectStorage();
        }
        $currentRequestHash = $this->currentRequestHash = $this->requestStack && ($request = $this->requestStack->getCurrentRequest()) ? \spl_object_hash($request) : '';
        if (null !== $this->logger && $event instanceof \RectorPrefix20210618\Psr\EventDispatcher\StoppableEventInterface && $event->isPropagationStopped()) {
            $this->logger->debug(\sprintf('The "%s" event is already stopped. No listeners have been called.', $eventName));
        }
        $this->preProcess($eventName);
        try {
            $this->beforeDispatch($eventName, $event);
            try {
                $e = $this->stopwatch->start($eventName, 'section');
                try {
                    $this->dispatcher->dispatch($event, $eventName);
                } finally {
                    if ($e->isStarted()) {
                        $e->stop();
                    }
                }
            } finally {
                $this->afterDispatch($eventName, $event);
            }
        } finally {
            $this->currentRequestHash = $currentRequestHash;
            $this->postProcess($eventName);
        }
        return $event;
    }
    /**
     * @return array
     */
    public function getCalledListeners(\RectorPrefix20210618\Symfony\Component\HttpFoundation\Request $request = null)
    {
        if (null === $this->callStack) {
            return [];
        }
        $hash = $request ? \spl_object_hash($request) : null;
        $called = [];
        foreach ($this->callStack as $listener) {
            [$eventName, $requestHash] = $this->callStack->getInfo();
            if (null === $hash || $hash === $requestHash) {
                $called[] = $listener->getInfo($eventName);
            }
        }
        return $called;
    }
    /**
     * @return array
     */
    public function getNotCalledListeners(\RectorPrefix20210618\Symfony\Component\HttpFoundation\Request $request = null)
    {
        try {
            $allListeners = $this->getListeners();
        } catch (\Exception $e) {
            if (null !== $this->logger) {
                $this->logger->info('An exception was thrown while getting the uncalled listeners.', ['exception' => $e]);
            }
            // unable to retrieve the uncalled listeners
            return [];
        }
        $hash = $request ? \spl_object_hash($request) : null;
        $calledListeners = [];
        if (null !== $this->callStack) {
            foreach ($this->callStack as $calledListener) {
                [, $requestHash] = $this->callStack->getInfo();
                if (null === $hash || $hash === $requestHash) {
                    $calledListeners[] = $calledListener->getWrappedListener();
                }
            }
        }
        $notCalled = [];
        foreach ($allListeners as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                if (!\in_array($listener, $calledListeners, \true)) {
                    if (!$listener instanceof \RectorPrefix20210618\Symfony\Component\EventDispatcher\Debug\WrappedListener) {
                        $listener = new \RectorPrefix20210618\Symfony\Component\EventDispatcher\Debug\WrappedListener($listener, null, $this->stopwatch, $this);
                    }
                    $notCalled[] = $listener->getInfo($eventName);
                }
            }
        }
        \uasort($notCalled, [$this, 'sortNotCalledListeners']);
        return $notCalled;
    }
    public function getOrphanedEvents(\RectorPrefix20210618\Symfony\Component\HttpFoundation\Request $request = null) : array
    {
        if ($request) {
            return $this->orphanedEvents[\spl_object_hash($request)] ?? [];
        }
        if (!$this->orphanedEvents) {
            return [];
        }
        return \array_merge(...\array_values($this->orphanedEvents));
    }
    public function reset()
    {
        $this->callStack = null;
        $this->orphanedEvents = [];
        $this->currentRequestHash = '';
    }
    /**
     * Proxies all method calls to the original event dispatcher.
     *
     * @param string $method    The method name
     * @param array  $arguments The method arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->dispatcher->{$method}(...$arguments);
    }
    /**
     * Called before dispatching the event.
     * @param object $event
     */
    protected function beforeDispatch(string $eventName, $event)
    {
    }
    /**
     * Called after dispatching the event.
     * @param object $event
     */
    protected function afterDispatch(string $eventName, $event)
    {
    }
    private function preProcess(string $eventName) : void
    {
        if (!$this->dispatcher->hasListeners($eventName)) {
            $this->orphanedEvents[$this->currentRequestHash][] = $eventName;
            return;
        }
        foreach ($this->dispatcher->getListeners($eventName) as $listener) {
            $priority = $this->getListenerPriority($eventName, $listener);
            $wrappedListener = new \RectorPrefix20210618\Symfony\Component\EventDispatcher\Debug\WrappedListener($listener instanceof \RectorPrefix20210618\Symfony\Component\EventDispatcher\Debug\WrappedListener ? $listener->getWrappedListener() : $listener, null, $this->stopwatch, $this);
            $this->wrappedListeners[$eventName][] = $wrappedListener;
            $this->dispatcher->removeListener($eventName, $listener);
            $this->dispatcher->addListener($eventName, $wrappedListener, $priority);
            $this->callStack->attach($wrappedListener, [$eventName, $this->currentRequestHash]);
        }
    }
    private function postProcess(string $eventName) : void
    {
        unset($this->wrappedListeners[$eventName]);
        $skipped = \false;
        foreach ($this->dispatcher->getListeners($eventName) as $listener) {
            if (!$listener instanceof \RectorPrefix20210618\Symfony\Component\EventDispatcher\Debug\WrappedListener) {
                // #12845: a new listener was added during dispatch.
                continue;
            }
            // Unwrap listener
            $priority = $this->getListenerPriority($eventName, $listener);
            $this->dispatcher->removeListener($eventName, $listener);
            $this->dispatcher->addListener($eventName, $listener->getWrappedListener(), $priority);
            if (null !== $this->logger) {
                $context = ['event' => $eventName, 'listener' => $listener->getPretty()];
            }
            if ($listener->wasCalled()) {
                if (null !== $this->logger) {
                    $this->logger->debug('Notified event "{event}" to listener "{listener}".', $context);
                }
            } else {
                $this->callStack->detach($listener);
            }
            if (null !== $this->logger && $skipped) {
                $this->logger->debug('Listener "{listener}" was not called for event "{event}".', $context);
            }
            if ($listener->stoppedPropagation()) {
                if (null !== $this->logger) {
                    $this->logger->debug('Listener "{listener}" stopped propagation of the event "{event}".', $context);
                }
                $skipped = \true;
            }
        }
    }
    private function sortNotCalledListeners(array $a, array $b)
    {
        if (0 !== ($cmp = \strcmp($a['event'], $b['event']))) {
            return $cmp;
        }
        if (\is_int($a['priority']) && !\is_int($b['priority'])) {
            return 1;
        }
        if (!\is_int($a['priority']) && \is_int($b['priority'])) {
            return -1;
        }
        if ($a['priority'] === $b['priority']) {
            return 0;
        }
        if ($a['priority'] > $b['priority']) {
            return -1;
        }
        return 1;
    }
}
