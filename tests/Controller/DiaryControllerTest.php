<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DiaryControllerTest extends WebTestCase
{
    private $client;
    private $userRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneByEmail('constance.gros@live.com');

        $this->urlGenerator = $this->client->getContainer()->get('router.default');

        $this->client->loginUser($this->user);

    }

    /**
     * @covers DefaultController
     */

    public function testHomepageIsUpWhenUserIsConnected()
    {

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }
    /**
     * @covers DefaultController::index
     */

    public function testHomepageIsUpWhenUserIsNotConnected()
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_login'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
