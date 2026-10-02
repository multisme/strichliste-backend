<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Exception\ParameterNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class BankService
{
    /**
     * @param array<string, mixed> $strichlisteSettings
     */
	public function __construct(
		private readonly SettingsService $settingsService,
		private readonly UserRepository $userRepository,
        	private readonly EntityManagerInterface $entityManager,
	){
    }

    /**
     * @return User
     */
    public function createUser(string $name): User
    {
        $user = new User();
	$user->setName($name);
        $user->setBalance(0); // Starting balance

        // 2. Persist and save to the database via the EntityManager
        $this->entityManager->persist($user);
	$this->entityManager->flush();
	return $user;
    }
    
    /**
     * @return User
     */
    public function getBankUser(): ?User
    {
	$bank_name = $this->settingsService->getOrDefault('bank.name', null);
	if ($bank_name === null ){
		return null;
	}
	$bank_user = $this->userRepository->findOneBy(['name' => $bank_name]);
	if ( $bank_user === null){
		return $this->createUser($bank_name);
	}
	return $bank_user;
    }

}
