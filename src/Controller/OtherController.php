<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OtherController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('other/contact.html.twig');
    }

    #[Route('/privacy', name: 'app_privacy')]
    public function about(): Response
    {
        return $this->render('other/privacy.html.twig');
    }
    #[Route('/terms', name: 'app_terms')]
    public function terms(): Response
    {
        return $this->render('other/terms.html.twig');
    }
}
