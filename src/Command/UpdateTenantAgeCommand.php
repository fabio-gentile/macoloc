<?php

namespace App\Command;

use App\Entity\Tenant;
use App\Repository\TenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-tenant-age',
    description: 'Update all the tenants age in the database.',
)]
class UpdateTenantAgeCommand extends Command
{
    public function __construct(private readonly TenantRepository $tenantRepository,private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tenants = $this->tenantRepository->findAll();

        $progressBar = new ProgressBar($output, count($tenants) - 1);
        $progressBar->start();

        $tenantsUpdated = 0;
        $i = 0;
        while ($i++ < count($tenants) - 1) {
            /** @var Tenant $tenant */
            $user = $tenants[$i]->getUser();
            $newAge = $user->getDateOfBirth()->diff(new \DateTime())->y;
            if ($tenants[$i]->getAge() !== $newAge) {
                $tenantsUpdated++;
                $tenants[$i]->setAge($newAge);
                $this->entityManager->persist($tenants[$i]);
            }

            $progressBar->advance();
        }

        $this->entityManager->flush();
        $progressBar->finish();
        $io->success('Tenant ages updated successfully. ' . $tenantsUpdated . ' tenants updated.');

        return Command::SUCCESS;
    }
}
