<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentCreateType;
use App\Form\PostCreateType;
use App\Repository\PostRepository;
use App\Service\Interfaces\PostServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 *
 * @SWG\Tag(
 *     name="Posts"
 * )
 *
 * @Security("has_role('ROLE_USER')")
 *
 * @Route("/api")
 *
 * @package Api\Controller\Api
 */
class PostController extends BaseController
{

    /**
     * @param Request $request
     * @param PostRepository $postRepository
     * @return Post|null
     */
    private function getPost(Request $request, PostRepository $postRepository): ?Post
    {
        $id = $request->get('id', false);

        if (false === $id) {
            return null;
        }

        try {
            return $postRepository->find(intval($id));

        } catch (\Exception $e) {

            return null;
        }

        return null;
    }

    /**
     * Create a post
     *
     * @Route("/posts", methods={"POST"})
     *
     * @SWG\Parameter(
     *         name="title",
     *         in="formData",
     *         type="string",
     *         description="Title post",
     *         maxLength=255,
     *         minLength=3,
     *         required=true
     * ),
     *
     * @SWG\Parameter(
     *         name="content",
     *         in="formData",
     *         type="string",
     *         description="Content post",
     *         required=false
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when successful"
     * )
     *
     * @param Request $request
     * @param PostServiceInterface $postService
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function createAction(Request $request, PostServiceInterface $postService): JsonResponse
    {

        $post = new Post();
        $form = $this->createForm(PostCreateType::class, $post);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            try {

                $post = $postService->create($post, $this->getUser());

            } catch (\Exception $e) {
                return $this->responseJsonError($e->getMessage(), $e->getCode());

            }

            return $this->responseJson($post, ['groups' => ['postList', 'postUser', 'userList']]);
        } else {

            return $this->responseJson([
                'status' => self::RESPONSE_ERROR,
                'form' => $form
            ]);
        }
    }

    /**
     * Returns a list of posts
     *
     * @Route("/posts", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list"
     * )
     *
     * @param PostRepository $postRepository
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function listAction(PostRepository $postRepository): JsonResponse
    {

        try {
            $posts = $postRepository->findBy([], ['id' => 'DESC']);

        } catch (\Exception $e) {

            return $this->responseJsonError($e->getMessage(), $e->getCode());
        }

        return $this->responseJson($posts, ['groups' => ['postList', 'postUser', 'userList']]);
    }

    /**
     * Return the post
     *
     * @Route("/posts/{id}", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list"
     * )
     *
     * @param Request $request
     * @param PostRepository $postRepository
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function readAction(Request $request, PostRepository $postRepository): JsonResponse
    {

        $post = $this->getPost($request, $postRepository);

        if (null === $post) {
            return $this->responseJsonError('The post does not exist.', 404);
        }

        return $this->responseJson($post, ['groups' => ['postDetails', 'postUser', 'userList', 'postComments', 'commentList', 'commentUser']]);
    }

    /**
     * Create a post comment
     *
     * @Route("/posts/{id}/comments", methods={"POST"})
     *
     * @SWG\Parameter(
     *         name="content",
     *         in="formData",
     *         type="string",
     *         description="Content comment",
     *         required=true
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when successful"
     * )
     *
     * @param Request $request
     * @param PostServiceInterface $postService
     * @param PostRepository $postRepository
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function createCommentAction(Request $request, PostRepository $postRepository, PostServiceInterface $postService): JsonResponse
    {

        $post = $this->getPost($request, $postRepository);

        if (null === $post) {
            return $this->responseJsonError('The post does not exist.', 404);
        }

        $comment = new Comment();
        $form = $this->createForm(CommentCreateType::class, $comment);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            try {

                $comment = $postService->addComment($post, $comment, $this->getUser());

            } catch (\Exception $e) {
                return $this->responseJsonError($e->getMessage(), $e->getCode());

            }

            return $this->responseJson($comment, ['groups' => ['commentList', 'commentUser', 'userList']]);
        } else {

            return $this->responseJson([
                'status' => self::RESPONSE_ERROR,
                'form' => $form
            ]);
        }
    }

    /**
     * Remove the post
     *
     * @Route("/posts/{id}", methods={"DELETE"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when successful"
     * )
     *
     * @param Request $request
     * @param PostRepository $postRepository
     * @param PostServiceInterface $postService
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function removeAction(Request $request, PostRepository $postRepository, PostServiceInterface $postService): JsonResponse
    {

        $post = $this->getPost($request, $postRepository);

        if (null === $post) {
            return $this->responseJsonError('The post does not exist.', 404);
        }

        if ($post->getUser() !== $this->getUser()) {
            return $this->responseJsonError('The post does not exist.', 404);
        }

        try {
            $postService->remove($post);

        } catch (\Exception $e) {
            return $this->responseJsonError($e->getMessage(), $e->getCode());

        }

        return $this->responseJson(['deleted' => true]);
    }

}
