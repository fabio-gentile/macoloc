<?php

namespace App\Command;

use App\Entity\FrenchCity;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-french-cities',
    description: 'Load french cities in the database.',
)]
class LoadFrenchCitiesCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '1G');

        $io = new SymfonyStyle($input, $output);
        $reader = Reader::createFromPath('french_cities.csv', 'r');
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();
        $progressBar = new ProgressBar($output, iterator_count($records));
        $progressBar->start();
        foreach ($records as $offset => $record) {
            $existingCity = $this->manager->getRepository(FrenchCity::class)->findOneBy([
                'city' => $record['city'],
                'postalCode' => $record['postal_code']
            ]);

            if (!$existingCity) {
                $city = new FrenchCity();
                $city->setCity($record['city'])
                    ->setPostalCode($record['postal_code'])
                    ->setLatitude($record['latitude'])
                    ->setLongitude($record['longitude'])
                    ->setDepartment($record['department'])
                ;

                $this->manager->persist($city);
            }

            $progressBar->advance();
        }

        $this->manager->flush();
        $progressBar->finish();
        $io->success('French cities have been loaded in the database.');
        return Command::SUCCESS;
    }
}
