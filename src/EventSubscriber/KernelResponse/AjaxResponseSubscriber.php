<?php

namespace App\EventSubscriber\KernelResponse;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class AjaxResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->isXmlHttpRequest()) {
            return;
        }
        $response = $event->getResponse();
        if (!$response instanceof RedirectResponse) {
            return;
        }
        $newResponse = new Response();
        $newResponse->headers->set('redirect-to', $response->getTargetUrl());
        $event->setResponse($newResponse);
    }
}
