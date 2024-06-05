<?php

namespace App\Service;

use App\Repository\TenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

readonly class UpdateTenantAgeService {
    public function __construct(
        private TenantRepository       $tenantRepository,
        private EntityManagerInterface $entityManager)
    {
    }

    /**
     * Update all the tenants age in the database.
     * If an OutputInterface is provided, a progress bar will be displayed.
     * @param OutputInterface|null $output
     * @return void
     */
    public function updateTenantAge(OutputInterface $output = null): void
    {
        $tenants = $this->tenantRepository->findAll();
        $tenantsUpdated = 0;
        $progressBar = null;

        if ($output) {
            $progressBar = new ProgressBar($output, count($tenants));
            $progressBar->start();
        }

        foreach ($tenants as $tenant) {
            $user = $tenant->getUser();
            $newAge = $user->getDateOfBirth()->diff(new \DateTime())->y;
            if ($tenant->getAge() !== $newAge) {
                $tenant->setAge($newAge);
                $this->entityManager->persist($tenant);
                $tenantsUpdated++;
            }

            $progressBar?->advance();
        }
        $this->entityManager->flush();
        $progressBar?->finish();

        if ($output) {
            $output->writeln('');
            $output->writeln('Tenants ages updated successfully. ' . $tenantsUpdated . ' tenants updated.');
        }
    }
}
