<?php

namespace App\Handler;

use App\Command\LocalizeVehicleCommand;
use Domain\Repository\FleetRepositoryInterface;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\ValueObject\Location;

class LocalizeVehicleHandler
{
    private FleetRepositoryInterface $fleetRepository;
    private VehicleRepositoryInterface $vehicleRepository;

    public function __construct(FleetRepositoryInterface $fleetRepository, VehicleRepositoryInterface $vehicleRepository)
    {
        $this->fleetRepository = $fleetRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function handle(LocalizeVehicleCommand $command)
    {
        $fleet = $this->fleetRepository->findById($command->getFleetId());
        $vehicle = $this->vehicleRepository->findByPlateNumber($command->getVehiclePlateNumber());
        $fleet->hasVehicle($vehicle);
        $location = new Location($command->getLatitude(), $command->getLongitude(), $command->getAltitude());
        $vehicle->park($location);
        $this->vehicleRepository->save($vehicle);
    }
}