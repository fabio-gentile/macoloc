<?php

namespace App\Controller\Admin;

use App\Entity\NewsletterSubscriber;
use App\Repository\NewsletterSubscriberRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/newsletter')]
class AdminNewsletterController extends AbstractController
{
    public function __construct(
        private NewsletterSubscriberRepository $newsletterSubscriberRepository
    ) {}

    #[Route('/', name: 'admin_newsletter')]
    public function index(
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $LIMIT = 10;
        return $this->render('admin/newsletter/index.html.twig', [
            'newsletter' => $paginator->paginate(
                $this->newsletterSubscriberRepository->findAll(),
                $request->query->getInt('page', 1),
                $LIMIT
            ),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_newsletter_delete')]
    public function delete(NewsletterSubscriber $newsletterSubscriber): Response
    {
        $email = $newsletterSubscriber->getEmail();
        $this->newsletterSubscriberRepository->delete($newsletterSubscriber);
        $this->addFlash('success', 'La newsletter appartenant à ' . $email . ' bien été supprimée.');
        return $this->redirectToRoute('admin_newsletter');
    }
}
