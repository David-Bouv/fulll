<?php

namespace Tests\Unit\Handler;

use PHPUnit\Framework\TestCase;
use App\Handler\LocalizeVehicleHandler;
use App\Command\LocalizeVehicleCommand;
use Domain\Repository\FleetRepositoryInterface;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\ValueObject\Location;
use Domain\Entity\Fleet;
use Domain\Entity\Vehicle;
use PHPUnit\Framework\MockObject\MockObject;

class LocalizeVehicleHandlerTest extends TestCase
{
    private LocalizeVehicleHandler $handler;
    private MockObject|FleetRepositoryInterface $fleetRepository;
    private MockObject|VehicleRepositoryInterface $vehicleRepository;

    protected function setUp(): void
    {
        // Mock dependencies
        $this->fleetRepository = $this->createMock(FleetRepositoryInterface::class);
        $this->vehicleRepository = $this->createMock(VehicleRepositoryInterface::class);

        // Creation of the handler with mocks
        $this->handler = new LocalizeVehicleHandler($this->fleetRepository, $this->vehicleRepository);
    }

    public function testHandleSuccessfullyLocalizesVehicle()
    {
        // Arrange: Create a LocalizeVehicleCommand
        $command = new LocalizeVehicleCommand('fleet-id-123', 'vehicle-plate-123', 48.8566, 2.3522, 35.0);

        // Create a mock of Fleet and Vehicle
        $fleet = $this->createMock(Fleet::class);
        $vehicle = $this->createMock(Vehicle::class);

        // Configure expectations for mocks
        $this->fleetRepository->expects($this->once())
            ->method('findById')
            ->with('fleet-id-123')
            ->willReturn($fleet); // Returns the mock of the fleet

        $this->vehicleRepository->expects($this->once())
            ->method('findByPlateNumber')
            ->with('vehicle-plate-123')
            ->willReturn($vehicle); // Returns the vehicle mock

        $fleet->expects($this->once())
            ->method('hasVehicle')
            ->with($vehicle); // Verifies that the fleet has the vehicle

        $vehicle->expects($this->once())
            ->method('park')
            ->with(new Location(48.8566, 2.3522, 35.0)); // Verifies that the park method is called with the correct location

        $this->vehicleRepository->expects($this->once())
            ->method('save')
            ->with($vehicle); // Verify that the vehicle is backed up

        // Act: Call handle method
        $this->handler->handle($command);

        // Assert: No explicit assertions here, as we check interactions via expectations set on mocks
    }

    public function testHandleFailsIfVehicleNotInFleet()
    {
        // Arrange: Create a LocalizeVehicleCommand
        $command = new LocalizeVehicleCommand('fleet-id-123', 'vehicle-plate-123', 48.8566, 2.3522, 35.0);

        // Create a mock of Fleet and Vehicle
        $fleet = $this->createMock(Fleet::class);
        $vehicle = $this->createMock(Vehicle::class);

        // Configure expectations for mocks
        $this->fleetRepository->expects($this->once())
            ->method('findById')
            ->with('fleet-id-123')
            ->willReturn($fleet); // Returns the mock of the fleet

        $this->vehicleRepository->expects($this->once())
            ->method('findByPlateNumber')
            ->with('vehicle-plate-123')
            ->willReturn($vehicle); // Returns the vehicle mock

        $fleet->expects($this->once())
            ->method('hasVehicle')
            ->with($vehicle)
            ->willThrowException(new \Exception("Vehicle not in fleet")); // Throw an exception if the vehicle is not in the fleet

        // Act & Assert: Ensure exception is thrown if vehicle is not in fleet
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Vehicle not in fleet");
        $this->handler->handle($command);
    }
}