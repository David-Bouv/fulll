<?php

namespace Tests\Unit\Handler;

use PHPUnit\Framework\TestCase;
use App\Handler\CreateFleetHandler;
use App\Command\CreateFleetCommand;
use Domain\Repository\FleetRepositoryInterface;
use Domain\Repository\UserRepositoryInterface;
use Domain\Entity\Fleet;
use Domain\Entity\User;
use Domain\ValueObject\FleetId;
use Domain\ValueObject\UserId;
use PHPUnit\Framework\MockObject\MockObject;

class CreateFleetHandlerTest extends TestCase
{
    private CreateFleetHandler $handler;
    private MockObject|FleetRepositoryInterface $fleetRepository;
    private MockObject|UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        // Mock dependencies
        $this->fleetRepository = $this->createMock(FleetRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        // Creation of the handler with mocks
        $this->handler = new CreateFleetHandler($this->fleetRepository, $this->userRepository);
    }

    public function testHandleCreatesFleetAndUser()
    {
        // Arrange: Create a CreateFleetCommand
        $command = new CreateFleetCommand('user-id-123');

        // Create a User and Fleet object with the order data
        $userId = new UserId('user-id-123');
        $user = new User($userId, 'Default Name');
        $fleet = new Fleet(new FleetId(), $user, 'Default Fleet');

        // Configure expectations for mocks
        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($user); // Verify that save is called with the created user

        $this->fleetRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($fleet) {
                // Check that the Fleet object has the expected properties, except for the ID
                return $fleet instanceof Fleet && 
                    $fleet->getOwner()->getId()->__toString() === 'user-id-123' && 
                    $fleet->getName() === 'Default Fleet';
            })); // Verify that save is called with the created fleet, ignoring the FleetId

        // Act: Call handle method
        $fleetId = $this->handler->handle($command);

        // Assert: Verify that fleet ID is returned
        $this->assertInstanceOf(FleetId::class, $fleetId);
    }

    public function testHandleCreatesFleetWithDefaultValues()
    {
        // Arrange: Create a CreateFleetCommand with a userId
        $command = new CreateFleetCommand('user-id-123');

        // Create a User and Fleet object with default values
        $userId = new UserId('user-id-123');
        $user = new User($userId, 'Default Name');
        $fleet = new Fleet(new FleetId(), $user, 'Default Fleet');

        // Configure expectations for mocks
        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($user); // Verify that save is called with the created user

        $this->fleetRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($fleet) {
                // Check that the Fleet object has the expected properties, except for the ID
                return $fleet instanceof Fleet &&
                    $fleet->getOwner()->getId()->__toString() === 'user-id-123' && // Check user ID
                    $fleet->getName() === 'Default Fleet'; // Check fleet name
            })); // Verify that save is called with the created fleet, ignoring the FleetId

        // Act: Call handle method
        $fleetId = $this->handler->handle($command);

        // Assert: Check that the fleet has an ID of type FleetId
        $this->assertInstanceOf(FleetId::class, $fleetId);
    }
}