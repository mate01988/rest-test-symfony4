<?php declare(strict_types=1);

namespace App\Service\Interfaces;


use App\Entity\Post;
use App\Entity\User;

interface PostServiceInterface
{

    public function create(Post $post, User $user);

}