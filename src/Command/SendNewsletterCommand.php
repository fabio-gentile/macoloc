<?php

namespace App\Command;

use App\Service\NewsletterService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-newsletter',
    description: 'Add a short description for your command',
)]
class SendNewsletterCommand extends Command
{
    public function __construct(private readonly NewsletterService $service)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->service->sendNewsletter();

        $io->success('Newsletter sent successfully.');

        return Command::SUCCESS;
    }
}
