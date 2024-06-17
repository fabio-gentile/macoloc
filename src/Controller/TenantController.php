<?php

namespace App\Controller;

use App\Repository\TenantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\Uuid;

#[Route('/tenant')]
class TenantController extends AbstractController
{
    public function __construct(
        private TenantRepository $tenantRepository
    ) {}

    #[Route('/{id}', name: 'app_tenant', requirements: ['id' => Requirement::UUID_V4])]
    public function index(Uuid $id): Response
    {
        $tenant = $this->tenantRepository->find($id);

        if (!$tenant) {
            throw $this->createNotFoundException('Locataire non trouvé.');
        }

        return $this->render('tenant/index.html.twig', [
            'tenant' => $tenant
        ]);
    }
}
