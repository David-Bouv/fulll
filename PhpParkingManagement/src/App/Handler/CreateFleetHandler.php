<?php

namespace App\Handler;

use App\Command\CreateFleetCommand;
use Domain\Repository\FleetRepositoryInterface;
use Domain\Entity\Fleet;
use Domain\ValueObject\FleetId;
use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;
use Domain\ValueObject\UserId;

class CreateFleetHandler
{
    private FleetRepositoryInterface $fleetRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(FleetRepositoryInterface $fleetRepository, UserRepositoryInterface $userRepository)
    {
        $this->fleetRepository = $fleetRepository;
        $this->userRepository = $userRepository;
    }

    public function handle(CreateFleetCommand $command)
    {
        $user = new User(new UserId($command->getUserId()), 'Default Name');
        $fleet = new Fleet(new FleetId(), $user, 'Default Fleet');
        
        $this->userRepository->save($user);
        $this->fleetRepository->save($fleet);
        
        return $fleet->getId();
    }
}