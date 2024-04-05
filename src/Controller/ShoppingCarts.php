<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ShoppingCart;
use App\Entity\ShoppingCartProduct;
use App\Repository\ShoppingCartRepository;
use App\Service\ProductValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/v1/shopping_carts', name: 'shopping_carts_', format: 'json')]
class ShoppingCarts extends AbstractController
{
    #[Route('', name: 'cors_root', methods: ['OPTIONS'])]
    #[Route('/{id}', name: 'cors_entity', requirements: ['id' => Requirement::UID_RFC4122], methods: ['OPTIONS'])]
    #[Route(
        '/{id}/products/{productID}',
        name: 'cors_shopping_cart_products',
        requirements: [
            'id' => Requirement::UID_RFC4122,
            'productID' => Requirement::UID_RFC4122,
        ],
        methods: ['OPTIONS']
    )]
    public function cors(): JsonResponse
    {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*'); //TODO should be configured
        $response->headers->set('Access-Control-Allow-Methods', 'GET, PATCH, POST, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        return $response;
    }

    /** List shopping carts
     * @param ShoppingCartRepository<ShoppingCart> $repository
     **/
    #[Route(name: "collection", methods: ['GET'])]
    public function collection(ShoppingCartRepository $repository): JsonResponse
    {
        $all = $repository->findAll();

        $shoppingCards = [
            'total' => $repository->count(),
            'results' => count($all),
            'data' => $all,
        ];

        return $this->json($shoppingCards, context: $this->buildObjectNormalizerContext('shop:list'));
    }

    /**
     * @param ShoppingCartRepository<ShoppingCart> $repository
     * @return JsonResponse
     */
    #[Route(name: "create", methods: ['POST'])]
    public function create(ShoppingCartRepository $repository): JsonResponse
    {
        $context = $this->buildObjectNormalizerContext('shop:item');

        $shoppingCart = $repository->createShoppingCart();

        return $this->json($shoppingCart, status: Response::HTTP_CREATED, context: $context);
    }

    /**
     * @param ShoppingCartRepository<ShoppingCart> $repository
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: "entity", requirements: ['id' => Requirement::UID_RFC4122], methods: ['GET'])]
    public function entity(ShoppingCartRepository $repository, string $id): JsonResponse
    {
        $context = $this->buildObjectNormalizerContext('shop:item');

        $shoppingCart = $repository->find($id);
        if ($shoppingCart === null) {
            return $this->handleShoppingCartNotFound();
        }

        $data = [
            'results' => 1,
            'data' => $shoppingCart,
        ];

        return $this->json($data, context: $context);
    }

    /**
     * @param ShoppingCartRepository<ShoppingCart> $repository
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: "delete", requirements: ['id' => Requirement::UID_RFC4122], methods: ['DELETE'])]
    public function delete(ShoppingCartRepository $repository, string $id): JsonResponse
    {
        try {
            $repository->removeByID($id);

        } catch (NotFoundHttpException) {
            return $this->handleShoppingCartNotFound();

        } catch (OptimisticLockException|ORMException $e) {
            return $this->handleError($e);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param ShoppingCartRepository<ShoppingCart> $repository
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/{id}/products', name: 'products_collection', requirements: ['id' => Requirement::UID_RFC4122], methods: ['GET'])]
    public function shoppingCartProductsCollection(ShoppingCartRepository $repository, string $id): JsonResponse
    {
        /** @var ?ShoppingCart $shoppingCart */
        $shoppingCart = $repository->find($id);
        if ($shoppingCart === null) {
            return $this->handleShoppingCartNotFound();
        }

        /** @var ArrayCollection<int,ShoppingCartProduct> $products */
        $products = $shoppingCart->getShoppingCartItems();
        $data = [
            'results' => $products->count(),
            'data' => $products,
        ];

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * Add a Product and return the list of the currently assigned products
     *
     * @param EntityManagerInterface $entityManager
     * @param string $id
     * @param string $productID
     * @return JsonResponse
     */
    #[Route(
        '/{id}/products/{productID}',
        name: "add_one_product",
        requirements: [
            'id' => Requirement::UID_RFC4122,
            'productID' => Requirement::UID_RFC4122,
        ],
        methods: ['POST']
    )]
    public function addProduct(EntityManagerInterface $entityManager, string $id, string $productID): JsonResponse
    {
        try {
            /* @var ?ShoppingCart $shoppingCart */
            $shoppingCart = $entityManager->find(ShoppingCart::class, $id);
            if ($shoppingCart === null) {
                return $this->handleShoppingCartNotFound();
            }

            $product = $entityManager->find(Product::class, $productID);
            if ($product === null) {
                return $this->handleProductNotFound();
            }

            $shoppingCart->addOneProduct($product);
            $entityManager->flush();

            /** @var ArrayCollection<int,ShoppingCartProduct> $products */
            $products = $shoppingCart->getShoppingCartItems();
            $data = [
                'results' => $products->count(),
                'data' => $shoppingCart,
            ];

            return $this->json($data, context: $this->buildObjectNormalizerContext('shop:item'));
        } catch (OptimisticLockException|ORMException $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Remove one Product and return the list of the currently assigned products
     *
     * @param EntityManagerInterface $entityManager
     * @param string $id
     * @param string $productID
     * @return JsonResponse
     */
    #[Route(
        '/{id}/products/{productID}',
        name: "remove_one_product",
        requirements: [
            'id' => Requirement::UID_RFC4122,
            'productID' => Requirement::UID_RFC4122,
        ],
        methods: ['DELETE']
    )]
    public function removeOneProduct(EntityManagerInterface $entityManager, string $id, string $productID): JsonResponse
    {
        try {
            /* @var ?ShoppingCart $shoppingCart */
            $shoppingCart = $entityManager->find(ShoppingCart::class, $id);
            if ($shoppingCart === null) {
                return $this->handleShoppingCartNotFound();
            }

            $product = $entityManager->find(Product::class, $productID);
            if ($product === null) {
                return $this->handleProductNotFound();
            }

            $shoppingCartProduct = $shoppingCart->removeOneProduct($product);
            if ($shoppingCartProduct instanceof ShoppingCartProduct) {
                //make sure to delete a ShoppingCartProduct without items
                $entityManager->remove($shoppingCartProduct);
            }

            $entityManager->flush();

            /** @var ArrayCollection<int,ShoppingCartProduct> $products */
            $products = $shoppingCart->getShoppingCartItems();
            $data = [
                'results' => $products->count(),
                'data' => $products,
            ];

            return $this->json($data, context: $this->buildObjectNormalizerContext('shop:item'));
        } catch (OptimisticLockException|ORMException $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Edit an ShoppingCartProduct
     *
     * @param EntityManagerInterface $entityManager
     * @param string $id
     * @param string $productID
     * @param Request $request
     * @param ProductValidator $validator
     * @return JsonResponse
     */
    #[Route(
        '/{id}/products/{productID}',
        name: "edit_one_product",
        requirements: [
            'id' => Requirement::UID_RFC4122,
            'productID' => Requirement::UID_RFC4122,
        ],
        methods: ['PATCH']
    )]
    public function editProduct(EntityManagerInterface $entityManager, string $id, string $productID, Request $request, ProductValidator $validator): JsonResponse
    {
        $requestData = $request->toArray();

        //fetch storage errors
        try {
            /* @var ?ShoppingCart $shoppingCart */
            $shoppingCart = $entityManager->find(ShoppingCart::class, $id);
            $product = $entityManager->find(Product::class, $productID);
        } catch (OptimisticLockException|ORMException $e) {
            return $this->handleError($e);
        }

        //validate input
        if ($shoppingCart === null) {
            return $this->handleShoppingCartNotFound();
        }

        if ($product === null) {
            return $this->handleProductNotFound();
        }

        $errors = $validator->validateUpdate($requestData);
        if (count($errors) > 0) {
            return $this->handleValidationErrors($errors);
        }


        //patch data
        if (isset($requestData['product']['name'])) {
            $product->setName($requestData['product']['name']);
        }
        if (isset($requestData['product']['price'])) {
            $product->setPrice($requestData['product']['price']);
        }
        if (isset($requestData['amount'])) {
            $shoppingCart->setAmountOnProduct($requestData['amount'], $product);
        }

        $entityManager->flush();

        //return results
        /** @var ArrayCollection<int,ShoppingCartProduct> $products */
        $products = $shoppingCart->getShoppingCartItems();
        $data = [
            'results' => $products->count(),
            'data' => $products,
        ];

        return $this->json($data, context: $this->buildObjectNormalizerContext('shop:item'));
    }
}
