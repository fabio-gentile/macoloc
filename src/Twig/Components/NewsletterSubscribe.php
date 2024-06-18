<?php

namespace App\Twig\Components;

use App\Entity\NewsletterSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use App\Form\NewsletterType;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(csrf: true)]
final class NewsletterSubscribe extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?NewsletterSubscriber $initialFormData = null;

    #[LiveProp]
    public bool $isSubmittedSuccessfully = false;

    #[LiveProp]
    public ?string $registeredEmail = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(NewsletterType::class, $this->initialFormData);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager): void
    {
        // Submit the form! If validation fails, an exception is thrown
        // and the component is automatically re-rendered with the errors
        $this->submitForm();

        $email = $this->getForm()->get('email')->getData();

        $newsletter = new NewsletterSubscriber();

        $newsletter->setEmail($email)
            ->setVerified(true);

        $entityManager->persist($newsletter);
        $entityManager->flush();

        $this->isSubmittedSuccessfully = true;
        $this->registeredEmail = $email;
    }
}
