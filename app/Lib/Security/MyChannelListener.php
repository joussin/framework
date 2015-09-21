<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lib\Security;

use Symfony\Component\Security\Http\AccessMapInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * ChannelListener switches the HTTP protocol based on the access control
 * configuration.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyChannelListener implements ListenerInterface
{
    private $map;
    private $authenticationEntryPoint;
    private $logger;

    public function __construct(AccessMapInterface $map, AuthenticationEntryPointInterface $authenticationEntryPoint, LoggerInterface $logger = null)
    {
        $this->map = $map;
        $this->authenticationEntryPoint = $authenticationEntryPoint;
        $this->logger = $logger;
    }

    /**
     * Handles channel management.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        list(, $channel) = $this->map->getPatterns($request);

        if (null !== $this->logger) {
            $this->logger->info('Redirecting to HTTP.');
        }

        $response = $this->authenticationEntryPoint->start($request);

        $event->setResponse($response);

    }
}
