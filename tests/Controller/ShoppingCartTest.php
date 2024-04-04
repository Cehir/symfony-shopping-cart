<?php

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\ProductFactory;
use App\Factory\ShoppingCartFactory;

use App\Factory\ShoppingCartProductFactory;
use DateTimeImmutable;
use Symfony\Component\Uid\UuidV4;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ShoppingCartTest extends ApiTestCase
{
    protected const string SHOPPING_CARTS_API_ENDPOINT = '/api/v1/shopping_carts';

    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'test',
            'debug' => false,
        ]);
    }

    /**
     * @param string $method
     * @param string|null $subPath
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public static function request(string $method = 'GET', ?string $subPath = null): ResponseInterface
    {
        if ($subPath === null) {
            return static::createClient()->request($method, self::SHOPPING_CARTS_API_ENDPOINT);
        }

        return static::createClient()->request($method, self::SHOPPING_CARTS_API_ENDPOINT . "/" . $subPath);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testListHavingAnyShoppingCarts(): void
    {
        self::request();

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['results' => 0]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testListHavingAShoppingCart(): void
    {
        //create one shopping cart database entry
        $shoppingCart = ShoppingCartFactory::createOne();
        $uuid = $shoppingCart->getId();
        $this->assertNotNull($uuid);
        $this->assertInstanceOf(DateTimeImmutable::class, $shoppingCart->getCreatedAt());

        static::ensureKernelShutdown(); // creating factories boots the kernel; shutdown before creating the client
        self::request();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'data' => [
                [
                    'id' => $uuid->toRfc4122(),
                ],
            ],
            'results' => 1,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetHavingAnInvalidUuid(): void
    {
        $willNotBeFound = new UuidV4();
        self::request(subPath: $willNotBeFound->toRfc4122());

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function testCreatingShoppingCart(): void
    {
        $response = self::request(method: 'POST');
        $responseData = $response->toArray();

        // check http response
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertArrayHasKey("id", $responseData);
        $this->assertArrayHasKey("created_at", $responseData);

        // find in database
        ShoppingCartFactory::assert()->exists([
            'id' => $responseData['id'],
            'createdAt' => new DateTimeImmutable($responseData['created_at']),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteShoppingCart(): void
    {
        $shoppingCart = ShoppingCartFactory::createOne();
        static::ensureKernelShutdown(); // creating factories boots the kernel; shutdown before creating the client

        $uuid = $shoppingCart->getId();
        self::request('DELETE', $uuid?->toRfc4122());

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteNoneExistingShoppingCart(): void
    {
        $uuid = new UuidV4();
        self::request('DELETE', $uuid->toRfc4122());

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteWithAssociatedData(): void
    {
        $product = ProductFactory::createOne();
        $shoppingCart = ShoppingCartFactory::createOne();
        ShoppingCartProductFactory::createOne(['product' => $product, 'shoppingCart' => $shoppingCart]);

        //count entities before deletion
        ProductFactory::assert()->count(1);
        ShoppingCartFactory::assert()->count(1);
        ShoppingCartProductFactory::assert()->count(1);

        // trigger delete action
        $shoppingCartUUID = $shoppingCart->getId()?->toRfc4122();
        self::request('DELETE', $shoppingCartUUID);

        // delete action should be successful
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        // shopping cart & shopping cart products should be deleted
        ShoppingCartFactory::assert()->count(0);
        ShoppingCartProductFactory::assert()->count(0);

        // the associated product should still exist
        ProductFactory::assert()->count(1);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testAddProduct(): void
    {
        [$firstProduct, $secondProduct] = ProductFactory::createMany(2);
        $shoppingCart = ShoppingCartFactory::createOne();

        self::request('POST', $shoppingCart->getId()?->toRfc4122() . '/products/' . $firstProduct->getId()?->toRfc4122());
        self::request('POST', $shoppingCart->getId()?->toRfc4122() . '/products/' . $firstProduct->getId()?->toRfc4122());
        self::request('POST', $shoppingCart->getId()?->toRfc4122() . '/products/' . $secondProduct->getId()?->toRfc4122());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $shoppingCartProducts = ShoppingCartProductFactory::repository()->findBy(['shoppingCart' => $shoppingCart]);

        //shopping cart should contain to products
        $this->assertCount(2, $shoppingCartProducts);
        $this->assertEquals($firstProduct->getId(), $shoppingCartProducts[0]->getProduct()->getId());
        $this->assertEquals($secondProduct->getId(), $shoppingCartProducts[1]->getProduct()->getId());

        //the amount of the first product should be two and for the second 1
        $this->assertEquals(2, $shoppingCartProducts[0]->getAmount());
        $this->assertEquals(1, $shoppingCartProducts[1]->getAmount());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testRemoveProduct(): void
    {
        $product = ProductFactory::createOne();
        $shoppingCart = ShoppingCartFactory::createOne();
        ShoppingCartProductFactory::createOne([
            'amount' => 2,
            'product' => $product,
            'shoppingCart' => $shoppingCart,
        ]);

        $productId = $product->getId()?->toRfc4122();
        $shoppingCartId = $shoppingCart->getId()?->toRfc4122();

        //remove one product
        self::request('DELETE', $shoppingCartId . '/products/' . $productId);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertJsonContains([
            'results' => 1,
            'data' => [
                [
                    'amount' => 1,
                    'product' => [
                        'id' => $productId,
                    ],
                ],
            ],
        ]);

        //remove the last product
        self::request('DELETE', $shoppingCartId . '/products/' . $productId);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertJsonContains([
            'results' => 0,
            'data' => [],
        ]);

        // check that the product is no longer associated with the shopping cart
        $shoppingCartProducts = ShoppingCartProductFactory::repository()->findBy(['shoppingCart' => $shoppingCart]);
        $this->assertCount(0, $shoppingCartProducts);

        // the shopping cart should still exist
        ShoppingCartFactory::assert()->count(1);

        // the product should still exist
        ProductFactory::assert()->count(1);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateShoppingCartProductWithInvalidData(): void
    {
        $product = ProductFactory::createOne();
        $shoppingCart = ShoppingCartFactory::createOne();
        ShoppingCartProductFactory::createOne([
            'product' => $product,
            'shoppingCart' => $shoppingCart,
        ]);

        $productId = $product->getId()?->toRfc4122();
        $shoppingCartId = $shoppingCart->getId()?->toRfc4122();

        $response = static::createClient()->request(
            'PUT',
            self::SHOPPING_CARTS_API_ENDPOINT . "/" . $shoppingCartId . '/products/' . $productId,
            ['body' => json_encode([
                'name' => 'newName',
            ])]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            'status' => Response::$statusTexts[Response::HTTP_BAD_REQUEST],
            'message' => 'Validation failed',
            'errors' => [
                [
                    "property" => "[name]",
                    "message" => "This field was not expected."
                ],
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateShoppingCartProduct(): void
    {
        $product = ProductFactory::createOne();
        $shoppingCart = ShoppingCartFactory::createOne();
        ShoppingCartProductFactory::createOne([
            'product' => $product,
            'shoppingCart' => $shoppingCart,
        ]);

        $productId = $product->getId()?->toRfc4122();
        $shoppingCartId = $shoppingCart->getId()?->toRfc4122();

        static::createClient()->request(
            'PUT',
            self::SHOPPING_CARTS_API_ENDPOINT . "/" . $shoppingCartId . '/products/' . $productId,
            ['body' => json_encode([
                'product' => [
                    'name' => 'newName',
                    'price' => 'newPrice',
                ]
            ])]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertEquals('newName', $product->getName());
        self::assertEquals('newPrice', $product->getPrice());
    }
}
