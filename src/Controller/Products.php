<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

#[Route("/api/v1/products", name: "products_", methods: ["GET"], format: "json")]
class Products extends AbstractController
{
    /**
     * @param ProductRepository<Product> $repository
     * @return JsonResponse
     */
    #[Route(name: "collection")]
    function collection(ProductRepository $repository): JsonResponse
    {
        $context = (new ObjectNormalizerContextBuilder())->withGroups(['product:list'])->toArray();

        $data = $repository->findAll();

        return $this->json(data: [
            "results" => count($data),
            "data" => $data
        ], context: $context);
    }

    #[Route] #[Route("/{id}", name: "entity", requirements: ['id' => Requirement::UID_RFC4122])]
    function entity(Product $product): JsonResponse
    {
        $context = (new ObjectNormalizerContextBuilder())->withGroups(['product:item'])->toArray();

        return $this->json(data: [
            'results' => 1,
            'data' => $product,
        ], context: $context);
    }
}
