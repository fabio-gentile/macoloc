<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

final class RegistrationRedirectListener
{
    public function __construct(private readonly Security $security, private readonly RouterInterface $router)
    {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $currentRoute = $request->attributes->get('_route');
        if (in_array($currentRoute, ['app_logout'])) {
            return;
        }

        /* @var $user User */
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        if (!$user->isVerified()) {
            if (!in_array($currentRoute, ['app_register_verify'])) {
                $response = new RedirectResponse($this->router->generate('app_register_verify'));
                $event->setResponse($response);
            }

            return;
        }

        if ($this->security->isGranted('ROLE_REGISTRATION_WAITING')) {
            if (!in_array($currentRoute, ['app_register_profile', 'app_logout'])) {
                $response = new RedirectResponse($this->router->generate('app_register_profile'));
                $event->setResponse($response);
            }
        }
    }
}
