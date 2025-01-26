<?php

namespace Tests\Unit\Repository;

use PHPUnit\Framework\TestCase;
use Domain\Entity\Fleet;
use Domain\ValueObject\FleetId;
use Infra\Repository\InMemoryFleetRepository;

class InMemoryFleetRepositoryTest extends TestCase
{
    private InMemoryFleetRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryFleetRepository();
    }

    public function testFindByIdWhenFleetExists()
    {
        // Setup: Create a fleet and add it to the repository
        $fleetId = new FleetId('fleet-123');
        $fleet = $this->createMock(Fleet::class);
        $fleet->method('getId')->willReturn($fleetId);
        $this->repository->save($fleet);

        // Test: Find the fleet by ID
        $foundFleet = $this->repository->findById('fleet-123');

        $this->assertSame($fleet, $foundFleet);
    }

    public function testFindByIdWhenFleetDoesNotExist()
    {
        // Test: Try to find a fleet that does not exist
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Fleet not found.');

        $this->repository->findById('non-existent-fleet');
    }

    public function testSaveNewFleet()
    {
        // Setup: Create a fleet
        $fleetId = new FleetId('fleet-123');
        $fleet = $this->createMock(Fleet::class);
        $fleet->method('getId')->willReturn($fleetId);

        // Test: Save the fleet to the repository
        $this->repository->save($fleet);

        // Verify: The fleet is saved in the repository
        $savedFleet = $this->repository->findById('fleet-123');
        $this->assertSame($fleet, $savedFleet);
    }

    public function testSaveOverwritesExistingFleet()
    {
        // Setup: Create a fleet
        $fleetId = new FleetId('fleet-123');
        $fleet = $this->createMock(Fleet::class);
        $fleet->method('getId')->willReturn($fleetId);
        
        // Save the first fleet
        $this->repository->save($fleet);

        // Create and save a new fleet with the same ID (overwrite)
        $newFleet = $this->createMock(Fleet::class);
        $newFleet->method('getId')->willReturn($fleetId);
        $this->repository->save($newFleet);

        // Verify: The fleet in the repository is now the new fleet
        $savedFleet = $this->repository->findById('fleet-123');
        $this->assertSame($newFleet, $savedFleet);
    }
}