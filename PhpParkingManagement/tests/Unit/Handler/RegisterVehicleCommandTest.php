<?php

namespace Tests\Unit\Handler;

use PHPUnit\Framework\TestCase;
use App\Handler\RegisterVehicleHandler;
use App\Command\RegisterVehicleCommand;
use Domain\Repository\FleetRepositoryInterface;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\Entity\Fleet;
use Domain\Entity\Vehicle;
use PHPUnit\Framework\MockObject\MockObject;

class RegisterVehicleHandlerTest extends TestCase
{
    private RegisterVehicleHandler $handler;
    private MockObject|FleetRepositoryInterface $fleetRepository;
    private MockObject|VehicleRepositoryInterface $vehicleRepository;

    protected function setUp(): void
    {
        // Mock dependencies
        $this->fleetRepository = $this->createMock(FleetRepositoryInterface::class);
        $this->vehicleRepository = $this->createMock(VehicleRepositoryInterface::class);

        // Create handler with mocks
        $this->handler = new RegisterVehicleHandler($this->fleetRepository, $this->vehicleRepository);
    }

    public function testHandleSuccessfullyRegistersVehicle()
    {
        // Arrange: Create a RegisterVehicleCommand
        $command = new RegisterVehicleCommand('fleet-id-123', 'ABC-123', 'Car');

        // Create a mock of Fleet
        $fleet = $this->createMock(Fleet::class);
        // Create a mock of Vehicle
        $vehicle = $this->createMock(Vehicle::class);

        // Set expectations for mocks
        $this->fleetRepository->expects($this->once())
            ->method('findById')
            ->with('fleet-id-123')
            ->willReturn($fleet); // Returns the fleet mock

        $this->vehicleRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Vehicle::class)); // Checks that the vehicle is saved

        $fleet->expects($this->once())
            ->method('addVehicle')
            ->with($this->isInstanceOf(Vehicle::class)); // Checks that the addVehicle method is called with the correct vehicle

        $this->fleetRepository->expects($this->once())
            ->method('save')
            ->with($fleet); // Checks that the fleet is saved after the vehicle is added

        // Act: Call the handle method
        $this->handler->handle($command);

        // Assert: No explicit assertions here, as we verify interactions through the expectations set on the mocks
    }

    public function testHandleFailsIfFleetNotFound()
    {
        // Arrange: Create a RegisterVehicleCommand
        $command = new RegisterVehicleCommand('fleet-id-123', 'ABC-123', 'Car');

        // Set expectations for findById to throw an exception
        $this->fleetRepository->expects($this->once())
            ->method('findById')
            ->with('fleet-id-123')
            ->willThrowException(new \Exception("Fleet not found"));

        // Act & Assert: Ensure the exception is thrown when the fleet is not found
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Fleet not found");
        $this->handler->handle($command);
    }

    public function testHandleFailsIfVehicleAlreadyRegistered()
    {
        // Arrange: Create a RegisterVehicleCommand
        $command = new RegisterVehicleCommand('fleet-id-123', 'ABC-123', 'Car');

        // Create a mock of Fleet
        $fleet = $this->createMock(Fleet::class);

        // Create a mock of a Vehicle already in the fleet
        $vehicle = $this->createMock(Vehicle::class);

        // Set expectations for mocks
        $this->fleetRepository->expects($this->once())
            ->method('findById')
            ->with('fleet-id-123')
            ->willReturn($fleet); // Returns the fleet mock

        // Simulate that the vehicle is already added to the fleet
        $fleet->expects($this->once())
            ->method('addVehicle')
            ->with($this->isInstanceOf(Vehicle::class))
            ->willThrowException(new \Exception("Vehicle already registered in fleet"));

        // Act & Assert: Ensure the exception is thrown if the vehicle is already registered
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Vehicle already registered in fleet");
        $this->handler->handle($command);
    }
}