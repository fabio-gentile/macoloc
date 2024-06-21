<?php

namespace App\Controller;

use App\Entity\Chamber;
use App\Entity\FrenchCity;
use App\Entity\Housing;
use App\Entity\HousingImage;
use App\Entity\Tenant;
use App\Entity\TenantImage;
use App\Factory\FileUploaderFactory;
use App\Form\PublishHousingType;
use App\Form\PublishTenantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/publish')]
class PublishController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/housing', name: 'app_publish_housing')]
    public function housing(FileUploaderFactory $fileUploaderFactory, Request $request): Response
    {
        $form = $this->createForm(PublishHousingType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $images = $request->files->get('publish_housing')['images'];
            $chambers = $data['chambers'];

            /** @var FrenchCity $address */
            $address = $data['address'];

            $housing = new Housing();
            $housing
                ->setCommodity($data['commodity'])
                ->setOther($data['other'])
                ->setUser($this->getUser())
                ->setCity($address->getCity())
                ->setLatitude($address->getLatitude())
                ->setLongitude($address->getLongitude())
                ->setType($data['housing']['type_housing'])
                ->setSurfaceArea($data['housing']['surface_area'])
                ->setNumberOfRooms($data['housing']['number_of_rooms'])
                ->setTitle($data['description']['title'])
                ->setDescription($data['description']['description'])
            ;

            foreach ($chambers as $chamber) {
                /** @var Chamber $chamber */
                $chamberEntity = new Chamber();
                $chamberEntity
                    ->setSurfaceArea($chamber->getSurfaceArea())
                    ->setPrice($chamber->getPrice())
                    ->setFurnished($chamber->isFurnished())
                    ->setAvaibleAt($chamber->getAvaibleAt())
                    ->setCaution($chamber->getCaution())
                ;

                $housing->addChamber($chamberEntity);
            }

            foreach ($images as $image) {
                $imageEntity = new HousingImage();
                $fileUploader = $fileUploaderFactory->createUploader('housings'); // or 'housings'
                $result = $fileUploader->upload($image);
                $imageEntity
                    ->setHousing($housing)
                    ->setFilename($result['fileName'])
                    ->setOriginalFilename($result['originalFilename'])
                    ->setMimeType($result['mimeType'])
                ;

                $housing->addHousingImage($imageEntity);
            }

            $this->entityManager->persist($housing);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_housing', ['id' => $housing->getId(),], Response::HTTP_SEE_OTHER);
        }
        return $this->render('publish/housing.html.twig', [
            'form' => $form,
            'errors' => $form->getErrors(),
        ]);
    }

    #[Route('/tenant', name: 'app_publish_tenant')]
    public function tenant(Request $request, FileUploaderFactory $fileUploaderFactory): Response
    {
        $form = $this->createForm(PublishTenantType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $tenant = new Tenant();
            /** @var FrenchCity $address */
            $address = $data['address'];
            $image = $request->files->get('publish_tenant')['image'];
//            dd($data, $images, $address);

            $tenant
                ->setUser($this->getUser())
                ->setCity($address->getCity())
                ->setLatitude($address->getLatitude())
                ->setLongitude($address->getLongitude())
                ->setBudget($data['about']['budget'])
                ->setDescription($data['description'])
                ->setActivity($data['about']['activity'])
                ->setGender($this->getUser()->getGender())
            ;

            if ($image) {
                $fileUploader = $fileUploaderFactory->createUploader('tenants');
                $result = $fileUploader->upload($image);
                $tenantImage = new TenantImage();
                $tenantImage
                    ->setTenant($tenant)
                    ->setFilename($result['fileName'])
                    ->setOriginalFilename($result['originalFilename'])
                    ->setMimeType($result['mimeType'])
                ;

                $tenant->setTenantImage($tenantImage);
            }

            $this->entityManager->persist($tenant);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_tenant', ['id' => $tenant->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publish/tenant.html.twig', [
            'form' => $form,
            'errors' => $form->getErrors(),
        ]);
    }
}
