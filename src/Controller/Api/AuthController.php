<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\UserCreateType;
use App\Repository\UserRepository;
use App\Service\Interfaces\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AuthController
 *
 * @SWG\Tag(
 *     name="Auth"
 * )
 *
 * @Route("/api")
 *
 * @package Api\Controller\Api
 */
class AuthController extends BaseController
{

    /**
     * User login
     *
     * @Route("/login", name="auth_login", methods={"POST"})
     *
     * consumes={"multipart/form-data"},
     *
     * @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         description="email",
     *         type="string",
     *         required=true
     *     ),
     *
     * @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="password",
     *         type="string",
     *         required=true
     *     ),
     *
     *
     * @SWG\Response(
     *         response=200,
     *         description="Success",
     *     )
     *
     * @SWG\Tag(name="Auth")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     * @param UserServiceInterface $userService
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function login(Request $request,
                          UserPasswordEncoderInterface $encoder,
                          EntityManagerInterface $em,
                          UserServiceInterface $userService,
                          UserRepository $userRepository): JsonResponse
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->findOneBy(['email' => $user->getEmail()]);

            if (null === $user) {
                return $this->responseJsonError("This user not exists.", 401);
            }

            if (false === $encoder->isPasswordValid($user, $form->get('password')->getData())) {
                return $this->responseJsonError("Incorrect login or password.", 401);
            }

            try {

                $responseData = $userService->login($user);

            } catch (\Exception $e) {
                return $this->responseJsonError($e->getMessage(), 400);
            }


            $response = $this->responseJson([
                'data' => $responseData
            ], ['groups' => ['user', 'userAuth']]);


            return $response;

        } else {
            return $this->responseJson([
                'status' => self::RESPONSE_ERROR,
                'form' => $form
            ]);
        }
    }

    /**
     * Register a User
     *
     * @Route("/register", methods={"POST"})
     *
     * @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         type="string",
     *         description="User name",
     *         maxLength=255,
     *         minLength=3,
     *         required=true
     * ),
     *
     * * @SWG\Parameter(
     *         name="lastname",
     *         in="formData",
     *         type="string",
     *         description="User lastname",
     *         maxLength=255,
     *         minLength=3,
     *         required=true
     * ),
     *
     * @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         type="string",
     *         description="User email address",
     *         maxLength=255,
     *         minLength=3,
     *         required=true
     * ),
     *
     * @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         type="string",
     *         description="User password",
     *         maxLength=255,
     *         minLength=6,
     *         required=true
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when successful"
     * )
     * @SWG\Tag(name="Auth")
     *
     * @param Request $request
     * @param UserServiceInterface $userService
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function registerAction(Request $request, UserServiceInterface $userService): JsonResponse
    {
        $user = new User();
        $form = $this->createForm(UserCreateType::class, $user);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $user = $userService->create($form->getData());

            } catch (\Exception $exception) {
                return $this->responseJsonError($exception->getMessage(), 400);
            }

            return $this->responseJson([
                'data' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'username' => $user->getUsername()
                ]
            ]);
        } else {
            return $this->responseJson([
                'status' => self::RESPONSE_ERROR,
                'form' => $form
            ]);
        }
    }

}
