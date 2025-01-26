<?php

namespace Console\Command;

use App\Command\RegisterVehicleCommand;
use App\Handler\RegisterVehicleHandler;
use Infra\Database\DatabaseConnection;
use Infra\Repository\FactoryRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterVehicleCLI extends Command
{
    protected function configure()
    {
        $this
            ->setName('fleet:register-vehicle')
            ->setDescription('Register a vehicle in the fleet.')
            ->addArgument('fleetId', InputArgument::REQUIRED, 'The ID of the fleet')
            ->addArgument('vehicleLicensePlate', InputArgument::REQUIRED, 'The vehicle license plate number');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dbConnection = new DatabaseConnection();
        $fleetId = $input->getArgument('fleetId');
        $vehicleLicensePlate = $input->getArgument('vehicleLicensePlate');

        $handler = new RegisterVehicleHandler(
            FactoryRepository::create($dbConnection, 'fleet'),
            FactoryRepository::create($dbConnection, 'vehicle')
        );

        try {
            $handler->handle(new RegisterVehicleCommand($fleetId, $vehicleLicensePlate, null));

            $output->writeln("Vehicle {$vehicleLicensePlate} has been successfully registered in the fleet {$fleetId}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Error: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }
    }
}