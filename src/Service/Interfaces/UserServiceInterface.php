<?php declare(strict_types=1);

namespace App\Service\Interfaces;


use App\Entity\User;

interface UserServiceInterface
{

    public function create(User $user);

}