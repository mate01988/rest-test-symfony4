<?php declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends Controller
{
    const RESPONSE_SUCCESS = "ok";
    const RESPONSE_ERROR = "error";

    protected function responseJson($data = [], array $context = [])
    {
        $responseCode = 200;

        if (is_array($data)) {

            if (!isset($data['status'])) {
                $data['status'] = self::RESPONSE_SUCCESS;
            }

            if ($data['status'] === self::RESPONSE_ERROR) {
                if (isset($data['form'])) {
                    $data['error']['form'] = $this->getErrorsFromForm($data['form']);
                    $data['error']['fields'] = $this->getErrorsFromFormFields($data['form']);

                    if (!empty($data['error']['form'])) {
                        $data['error']['message'] = 'Invalid form.';
                    } elseif (!empty($data['error']['fields'])) {
                        $data['error']['message'] = 'Invalid form fields.';
                    }

                    unset($data['form']);
                }

                if (isset($data['error']['code'])) {
                    $responseCode = $data['error']['code'];
                } else {
                    $responseCode = 400;
                }
            }else{
                if(!isset($data['data'])){
                    $data = [
                        'data' => $data,
                        'status' => self::RESPONSE_SUCCESS
                    ];
                    unset($data['data']['status']);
                }
            }
        } elseif (is_object($data)) {
            $data = [
                'data' => $data,
                'status' => self::RESPONSE_SUCCESS
            ];
        }

        //unset($data['status']);
        return $this->json($data, $responseCode, [], $context);
    }

    protected function responseJsonError($message, $code = 400)
    {

        if (empty($code)) {
            $code = 400;
        }

        return $this->responseJson([
            'status' => self::RESPONSE_ERROR,
            'error' => [
                'message' => $message,
                'code' => $code
            ]
        ]);
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    public function getErrorsFromForm(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    protected function getErrorsFromFormFields(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

}
