<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController extends AbstractController
{
    public const SUCCESS = 'success';
    public const ERROR = 'error';

    protected function respond(string $message, $data = [], int $httpCode = JsonResponse::HTTP_OK, string $status = self::SUCCESS): JsonResponse
    {
        return new JsonResponse([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $httpCode);
    }

    protected function respondWithError(string $message, $data = [], int $httpCode = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->respond($message, $data, $httpCode, self::ERROR);
    }

    /**
     * @return JsonResponse|array
     */
    protected function testJson(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        return $data ?? $this->respondWithError('incorrect_json');
    }

    protected function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }
}