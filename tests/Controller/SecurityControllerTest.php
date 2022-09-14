<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function setUp(): void
    {

        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->testUser = $userRepository->findOneByEmail('constance.gros@live.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
    }

    /**
     * @covers \App\Controller\SecurityController::login
     */

    public function testConnexionWithBadCredentials(): void
    {
        $this->client->request('GET', '/login');
        // dd($this->client->request('GET', '/login'));
        $this->client->submitForm('Sign in', [
            'email' => 'false@Gmail.com',
            'password' => 'falsePassword',
        ]);
        $this->client->followRedirect();
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_login'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        // $this->assertSelectorTextContains('', 'Bad credentials');
        // $this->client->followRedirect();
        // $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_login'));

    }
    public function testConnexionWithGoodCredentials(): void
    {

        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'constance.gros@live.com',
            'password' => 'password',
        ]);
        $this->client->followRedirect();
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }
}
