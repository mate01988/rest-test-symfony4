<?php declare(strict_types=1);

namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Interfaces\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserService implements UserServiceInterface
{

    private $em;
    private $passwordEncoder;
    private $userRepository;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManagerInterface $em,
                                UserPasswordEncoderInterface $passwordEncoder,
                                UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     * @return User
     * @throws \Exception
     */
    public function login(User $user): User
    {
        $token = Uuid::uuid4();

        $user->setApiToken($token->toString());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param User $user
     * @return User
     */
    public function create(User $user): User
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

}