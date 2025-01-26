<?php
namespace Console\Command;

use App\Command\CreateFleetCommand;
use App\Handler\CreateFleetHandler;
use Infra\Database\DatabaseConnection;
use Infra\Repository\FactoryRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateFleetCLI extends Command
{
    protected function configure()
    {
        
        $this
            ->setName('fleet:create')
            ->setDescription('Create a fleet for a user.')
            ->addArgument('userId', InputArgument::REQUIRED, 'The ID of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dbConnection = new DatabaseConnection();
        $userId = $input->getArgument('userId');
        
        $handler = new CreateFleetHandler(
            FactoryRepository::create($dbConnection, 'fleet'),
            FactoryRepository::create($dbConnection,'user')
        );

        try {
            $fleetId = $handler->handle(New CreateFleetCommand($userId));

            $output->writeln("Fleet created with ID: " . $fleetId);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Error: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }
    }
}