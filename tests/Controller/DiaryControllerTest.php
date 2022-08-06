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
        // // dd(static::getContainer()->get(UserRepository::class)->findOneByEmail('user-test@gmail.com'));
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneByEmail('constance.gros@live.com');
        // // dd($this->user);
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        // // dd($this->user);
        $this->client->loginUser($this->user);

    }
    public function testHomepageIsUp()
    {
        // $this->client->request('GET', '/');
        // $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }
    public function test2()
    {

    }

}
