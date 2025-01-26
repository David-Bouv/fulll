<?php

namespace Tests\Integration\Handler;

use PHPUnit\Framework\TestCase;
use App\Handler\LocalizeVehicleHandler;
use App\Command\LocalizeVehicleCommand;
use Domain\Repository\FleetRepositoryInterface;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\Entity\Fleet;
use Domain\Entity\User;
use Domain\Entity\Vehicle;
use Domain\Repository\UserRepositoryInterface;
use Domain\ValueObject\FleetId;
use Domain\ValueObject\UserId;
use Domain\ValueObject\VehicleId;
use Infra\Database\DatabaseConnection;
use Infra\Repository\FactoryRepository;

class LocalizeVehicleHandlerTest extends TestCase
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

    public function testHandle_LocalizeVehicle()
    {
        // Arrange
        $user = new User(new UserId('user-id-123'), 'John Doe');
        $fleet = new Fleet(new FleetId('fleet-id-456'), $user, 'Test Fleet');
        $vehicle = new Vehicle(new VehicleId('vehicle-id-789'), 'ABC-123', 'Car');

        // Save test data in the database
        $this->userRepository->save($user);
        $this->fleetRepository->save($fleet);
        $this->vehicleRepository->save($vehicle);

        // Associate the vehicle with the fleet
        $this->dbConnection->getConnection()->exec("
            INSERT INTO FleetVehicles (fleetId, vehicleId)
            VALUES ('fleet-id-456', 'vehicle-id-789')
        ");

        $handler = new LocalizeVehicleHandler($this->fleetRepository, $this->vehicleRepository);

        $command = new LocalizeVehicleCommand(
            fleetId: 'fleet-id-456',
            vehiclePlateNumber: 'ABC-123',
            latitude: 48.8566,
            longitude: 2.3522,
            altitude: 35.0
        );

        // Act
        $handler->handle($command);

        // Assert
        $savedVehicle = $this->vehicleRepository->findById('vehicle-id-789');
        $this->assertNotNull($savedVehicle->getLocation());
        $this->assertEquals(48.8566, $savedVehicle->getLocation()->getLatitude());
        $this->assertEquals(2.3522, $savedVehicle->getLocation()->getLongitude());
        $this->assertEquals(35.0, $savedVehicle->getLocation()->getAltitude());
    }
}