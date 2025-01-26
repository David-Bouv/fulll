<?php

namespace Tests\Integration\Handler;

use PHPUnit\Framework\TestCase;
use App\Handler\RegisterVehicleHandler;
use App\Command\RegisterVehicleCommand;
use Domain\Repository\FleetRepositoryInterface;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\Entity\Fleet;
use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;
use Domain\ValueObject\FleetId;
use Domain\ValueObject\UserId;
use Infra\Database\DatabaseConnection;
use Infra\Repository\FactoryRepository;

class RegisterVehicleHandlerTest extends TestCase
{
    private DatabaseConnection $dbConnection;
    private UserRepositoryInterface $userRepository;
    private FleetRepositoryInterface $fleetRepository;
    private VehicleRepositoryInterface $vehicleRepository;

    protected function setUp(): void
    {
        $config = include(__DIR__ . '/../../../config.php');
        $this->dbConnection = new DatabaseConnection($config);
        $this->fleetRepository = FactoryRepository::create($this->dbConnection, 'fleet');
        $this->userRepository = FactoryRepository::create($this->dbConnection, 'user');
        $this->vehicleRepository = FactoryRepository::create($this->dbConnection, 'vehicle');

        if($this->dbConnection->getUseDatabase()){
            $this->dbConnection->getConnection()->beginTransaction();
        }
    }

    protected function tearDown(): void
    {
        if($this->dbConnection->getUseDatabase()){
            $this->dbConnection->getConnection()->rollBack();
        }
    }

    public function testHandle_RegisterVehicle()
    {
        // Arrange
        $user = new User(new UserId('user-id-123'), 'John Doe');
        $fleet = new Fleet(new FleetId('fleet-id-456'), $user, 'Test Fleet');

        // Save test data in the database
        $this->userRepository->save($user);
        $this->fleetRepository->save($fleet);

        $handler = new RegisterVehicleHandler($this->fleetRepository, $this->vehicleRepository);

        $command = new RegisterVehicleCommand(
            fleetId: 'fleet-id-456',
            vehicleLicensePlate: 'XYZ-987',
            vehicleType: 'Truck'
        );

        // Act
        $handler->handle($command);

        // Assert
        $savedVehicle = $this->vehicleRepository->findByPlateNumber('XYZ-987');
        $this->assertNotNull($savedVehicle);
        $this->assertEquals('XYZ-987', $savedVehicle->getLicensePlate());
        $this->assertEquals('Truck', $savedVehicle->getType());

        $savedFleet = $this->fleetRepository->findById('fleet-id-456');
        $this->assertCount(1, $savedFleet->getVehicles());
        $this->assertEquals('XYZ-987', $savedFleet->getVehicles()[$savedVehicle->getId()->__toString()]->getLicensePlate());
    }
}