<?php
declare(strict_types=1);

namespace App;

use App\Repository\UserRepository;

class Context
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }
}