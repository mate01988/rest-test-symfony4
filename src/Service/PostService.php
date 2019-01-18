<?php declare(strict_types=1);

namespace App\Service;


use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Service\Interfaces\PostServiceInterface;
use Doctrine\ORM\EntityManagerInterface;


class PostService implements PostServiceInterface
{

    private $em;
    private $postRepository;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $em
     * @param PostRepository $postRepository
     */
    public function __construct(EntityManagerInterface $em,
                                PostRepository $postRepository)
    {
        $this->em = $em;
        $this->postRepository = $postRepository;
    }

    /**
     * @param Post $post
     * @param User $user
     *
     * @return Post
     * @throws \Exception
     */
    public function create(Post $post, User $user): Post
    {
        $post->setCreatedAt(new \DateTime());
        $post->setUser($user);

        $this->em->persist($post);
        $this->em->flush();

        return $post;
    }

    public function addComment(Post $post, Comment $comment, User $user): Comment
    {

        $comment->setCreatedAt(new \DateTime());
        $comment->setUser($user);

        $this->em->persist($comment);

        $post->addComment($comment);

        $this->em->persist($post);
        $this->em->flush();

        return $comment;
    }

    public function remove(Post $post): bool
    {

        $this->em->remove($post);
        $this->em->flush();

        return true;
    }


}