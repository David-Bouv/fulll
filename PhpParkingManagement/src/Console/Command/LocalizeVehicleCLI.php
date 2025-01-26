<?php

namespace Console\Command;

use App\Command\LocalizeVehicleCommand;
use App\Handler\LocalizeVehicleHandler;
use Infra\Database\DatabaseConnection;
use Infra\Repository\FactoryRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LocalizeVehicleCLI extends Command
{
    protected function configure()
    {
        $this
            ->setName('fleet:localize-vehicle')
            ->setDescription('Localize a vehicle in the fleet.')
            ->addArgument('fleetId', InputArgument::REQUIRED, 'The ID of the fleet')
            ->addArgument('vehiclePlateNumber', InputArgument::REQUIRED, 'The vehicle license plate number')
            ->addArgument('lat', InputArgument::REQUIRED, 'Latitude')
            ->addArgument('lng', InputArgument::REQUIRED, 'Longitude')
            ->addArgument('alt', InputArgument::OPTIONAL, 'Altitude');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dbConnection = new DatabaseConnection();
        $fleetId = $input->getArgument('fleetId');
        $vehiclePlateNumber = $input->getArgument('vehiclePlateNumber');
        $lat = $input->getArgument('lat');
        $lng = $input->getArgument('lng');
        $alt = $input->getArgument('alt') ?: null;

        $handler = new LocalizeVehicleHandler(
            FactoryRepository::create($dbConnection, 'fleet'),
            FactoryRepository::create($dbConnection, 'vehicle')
        );

        try {
            $handler->handle(new LocalizeVehicleCommand($fleetId, $vehiclePlateNumber, $lat, $lng, $alt));

            $output->writeln("Vehicle {$vehiclePlateNumber} has been localized at {$lat}, {$lng}" . ($alt ? " with altitude {$alt}" : ""));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Error: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }
    }
}