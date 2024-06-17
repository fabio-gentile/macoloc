<?php

namespace App\Controller;

use App\Repository\HousingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\UuidV4;

#[Route('/housing')]
class HousingController extends AbstractController
{
    public function __construct(
        private HousingRepository $housingRepository
    )
    {
    }

    #[Route('/{id}', name: 'app_housing', requirements: ['id' => Requirement::UUID_V4])]
    public function index(UuidV4 $id): Response
    {
        return $this->render('housing/index.html.twig', [
            'housing' => $this->housingRepository->find($id),
        ]);
    }
}
