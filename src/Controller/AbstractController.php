<?php

namespace App\Controller;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * Prepare json serialization
     *
     * @param array<string>|null|string $groups
     * @return array<string|mixed>
     */
    protected function buildObjectNormalizerContext(array|null|string $groups): array
    {
        return (new ObjectNormalizerContextBuilder())->withGroups($groups)->toArray();
    }

    /**
     * @param ORMException|\Exception|OptimisticLockException $e
     * @return JsonResponse
     */
    protected function handleError(ORMException|\Exception|OptimisticLockException $e): JsonResponse
    {
        $errorStatusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        return $this->json([
            'status' => Response::$statusTexts[$errorStatusCode],
            'msg' => 'unable to find shopping cart',
            'error' => $e->getMessage()
        ], $errorStatusCode);
    }

    /**
     * @return JsonResponse
     */
    protected function handleProductNotFound(): JsonResponse
    {
        return $this->json([
            'status' => Response::$statusTexts[Response::HTTP_NOT_FOUND],
            'msg' => 'product not found'
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * @return JsonResponse
     */
    protected function handleShoppingCartNotFound(): JsonResponse
    {
        $notFound = Response::HTTP_NOT_FOUND;
        return $this->json([
            'status' => Response::$statusTexts[$notFound],
            'msg' => 'shopping cart not found'
        ], status: $notFound);
    }

    protected function handleValidationErrors(ConstraintViolationListInterface $errors): JsonResponse
    {
        $data = [];
        foreach ($errors as $error) {
            $data[] = [
                'property' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
            ];
        }

        return $this->json([
            'status' =>  Response::$statusTexts[Response::HTTP_BAD_REQUEST],
            'message' => 'Validation failed',
            'errors' => $data,
        ], Response::HTTP_BAD_REQUEST);
    }
}
