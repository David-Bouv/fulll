<?php

namespace App\Handler;

use App\Command\RegisterVehicleCommand;
use Domain\Entity\Vehicle;
use Domain\Repository\FleetRepositoryInterface;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\ValueObject\VehicleId;

class RegisterVehicleHandler
{
    private FleetRepositoryInterface $fleetRepository;
    private VehicleRepositoryInterface $vehicleRepository;

    public function __construct(FleetRepositoryInterface $fleetRepository, VehicleRepositoryInterface $vehicleRepository)
    {
        $this->fleetRepository = $fleetRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function handle(RegisterVehicleCommand $command)
    {
        $vehicle = $this->vehicleRepository->findByPlateNumber($command->getVehicleLicensePlate());
        if(!$vehicle){
            $vehicle = new Vehicle(new VehicleId(), $command->getVehicleLicensePlate(), $command->getVehicleType());
            $this->vehicleRepository->save($vehicle);
        }

        $fleet = $this->fleetRepository->findById($command->getFleetId());
        $fleet->addVehicle($vehicle);
        $this->fleetRepository->save($fleet);

        return $vehicle->getId()->__toString();
    }
}