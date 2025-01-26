<?php

namespace Tests\Features\bootstrap;

use App\Command\CreateFleetCommand;
use App\Command\LocalizeVehicleCommand;
use Behat\Behat\Context\Context;
use App\Command\RegisterVehicleCommand;
use App\Handler\RegisterVehicleHandler;
use App\Handler\CreateFleetHandler;
use App\Handler\LocalizeVehicleHandler;
use Domain\Entity\Vehicle;
use Domain\ValueObject\VehicleId;
use Domain\Repository\FleetRepositoryInterface;
use Domain\Repository\UserRepositoryInterface;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\ValueObject\Location;
use Infra\Repository\InMemoryUserRepository;
use Infra\Repository\InMemoryFleetRepository;
use Infra\Repository\InMemoryVehicleRepository;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Shared //////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private FleetRepositoryInterface $fleetRepository;
    private VehicleRepositoryInterface $vehicleRepository;
    private UserRepositoryInterface $userRepository;
    private string $fleetId = '';
    private Vehicle $vehicle;
    private string $vehicleId = '';

    public function __construct()
    {
        $this->fleetRepository = new InMemoryFleetRepository();
        $this->vehicleRepository = new InMemoryVehicleRepository();
        $this->userRepository = new InMemoryUserRepository();
    }

    /**
     * @Given my fleet
     */
    public function myFleet()
    {
        $command = new CreateFleetCommand('user1');
        $handler = new CreateFleetHandler($this->fleetRepository, $this->userRepository);
        $this->fleetId = $handler->handle($command);
    }

    /**
     * @Given a vehicle
     */
    public function aVehicle()
    {
        $this->vehicle = new Vehicle(new VehicleId (), 'licensePlate1', 'type1');
    }

    /**
     * @Given I have registered this vehicle into my fleet
     */
    public function iHaveRegisteredThisVehicleIntoMyFleet()
    {
        $command = new RegisterVehicleCommand($this->fleetId, $this->vehicle->getLicensePlate(), $this->vehicle->getType());
        $handler = new RegisterVehicleHandler($this->fleetRepository, $this->vehicleRepository);
        $this->vehicleId = $handler->handle($command);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Register Vehicle ////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private $registrationException;
    private string $otherFleetId;

    /**
     * @When I register this vehicle into my fleet
     */
    public function iRegisterThisVehicleIntoMyFleet()
    {
        $command = new RegisterVehicleCommand($this->fleetId, $this->vehicle->getLicensePlate(), $this->vehicle->getType());
        $handler = new RegisterVehicleHandler($this->fleetRepository, $this->vehicleRepository);
        $this->vehicleId = $handler->handle($command);
    }

    /**
     * @Then this vehicle should be part of my vehicle fleet
     */
    public function thisVehicleShouldBePartOfMyVehicleFleet()
    {
        $fleet = $this->fleetRepository->findById($this->fleetId);
        $fleet->getVehicle($this->vehicleId);
    }

    /**
     * @When I try to register this vehicle into my fleet
     */
    public function iTryToRegisterThisVehicleIntoMyFleet()
    {
        try {
            $command = new RegisterVehicleCommand($this->fleetId, $this->vehicle->getLicensePlate(), $this->vehicle->getType());
            $handler = new RegisterVehicleHandler($this->fleetRepository, $this->vehicleRepository);
            $this->vehicleId = $handler->handle($command);
        } catch (\Exception $e) {
            $this->registrationException = $e;
        }
    }

    /**
     * @Then I should be informed that this vehicle has already been registered into my fleet
     */
    public function iShouldBeInformedThatThisVehicleHasAlreadyBeenRegisteredIntoMyFleet()
    {
        if (!$this->registrationException instanceof \Exception) {
            throw new \Exception('No exception was thrown for duplicate registration');
        }
        
        // Check if the exception message is the expected one
        if ($this->registrationException->getMessage() !== 'Vehicle already registered in this fleet.') {
            throw new \Exception('Unexpected exception message: ' . $this->registrationException->getMessage());
        }
    }

    /**
     * @Given the fleet of another user
     */
    public function theFleetOfAnotherUser(): void
    {
        $command = new CreateFleetCommand('user2');
        $handler = new CreateFleetHandler($this->fleetRepository, $this->userRepository);
        $this->otherFleetId = $handler->handle($command);
    }

    /**
     * @Given this vehicle has been registered into the other user's fleet
     */
    public function thisVehicleHasBeenRegisteredIntoTheOtherUserFleet(): void
    {
        $command = new RegisterVehicleCommand($this->otherFleetId, $this->vehicle->getLicensePlate(), $this->vehicle->getType());
        $handler = new RegisterVehicleHandler($this->fleetRepository, $this->vehicleRepository);
        $this->vehicleId = $handler->handle($command);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Park Vehicle ////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private Location $location;
    private $parkException;

    /**
     * @Given a location
     */
    public function aLocation()
    {
        $this->location = new Location(43.296482, 5.36978, 28);
    }

    /**
     * @When I park my vehicle at this location
     */
    public function iParkMyVehicleAtThisLocation()
    {
        $command = new LocalizeVehicleCommand($this->fleetId, $this->vehicle->getLicensePlate(), $this->location->getLatitude(), $this->location->getLongitude(), $this->location->getAltitude());
        $handler = new LocalizeVehicleHandler($this->fleetRepository, $this->vehicleRepository);
        $handler->handle($command);
    }

    /**
     * @Then the known location of my vehicle should verify this location
     */
    public function theKnownLocationOfMyVehicleShouldVerifyThisLocation()
    {
        $vehicle = $this->vehicleRepository->findById($this->vehicleId);
        if (!$vehicle->getLocation()->equals($this->location)) {
            throw new \Exception('Location of the vehicle does not match.');
        }
    }

    /**
     * @Given my vehicle has been parked into this location
     */
    public function myVehicleHasBeenParkedIntoThisLocation()
    {
        $command = new LocalizeVehicleCommand($this->fleetId, $this->vehicle->getLicensePlate(), $this->location->getLatitude(), $this->location->getLongitude(), $this->location->getAltitude());
        $handler = new LocalizeVehicleHandler($this->fleetRepository, $this->vehicleRepository);
        $handler->handle($command);
    }

    /**
     * @When I try to park my vehicle at this location
     */
    public function iTryToParkMyVehicleAtThisLocation()
    {
        try {
            $command = new LocalizeVehicleCommand($this->fleetId, $this->vehicle->getLicensePlate(), $this->location->getLatitude(), $this->location->getLongitude(), $this->location->getAltitude());
            $handler = new LocalizeVehicleHandler($this->fleetRepository, $this->vehicleRepository);
            $handler->handle($command);
        } catch (\Exception $e) {
            $this->parkException = $e;
        }
    }

    /**
     * @Then I should be informed that my vehicle is already parked at this location
     */
    public function iShouldBeInformedThatMyVehicleIsAlreadyParkedAtThisLocation()
    {
        if (!$this->parkException instanceof \Exception) {
            throw new \Exception('No exception was thrown for duplicate registration');
        }
        
        // Check if the exception message is the expected one
        if ($this->parkException->getMessage() !== 'Vehicle is already parked at this location.') {
            throw new \Exception('Unexpected exception message: ' . $this->parkException->getMessage());
        }
    }
}
