<?php

namespace App\Command;

use App\Service\UpdateTenantAgeService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-tenant-age',
    description: 'Update all the tenants age in the database.',
)]
class UpdateTenantAgeCommand extends Command
{
    public function __construct(private readonly UpdateTenantAgeService $updateTenantAgeService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'no-progress',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will not display a progress bar'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $noProgress = $input->getOption('no-progress');

        if ($noProgress) {
            $this->updateTenantAgeService->updateTenantAge();
        } else {
            $this->updateTenantAgeService->updateTenantAge($output);
        }

        $io->success('Tenant ages update process completed.');

        return Command::SUCCESS;
    }
}
