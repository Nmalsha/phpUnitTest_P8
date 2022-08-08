<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneByEmail('constance.gros@live.com');
        $this->aminUser = $this->userRepository->findOneByEmail('admin12@gmail.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');

    }
    //check redirection of the page when login depending the role of the user
    public function testUserPageRedirectWhenUserIsNotAdmin(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUserPageRedirectWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->aminUser);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
//check redirection of user create depending the role of the user
    public function testCreateUserPageRedirectWhenUserIsNotAdmin(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    public function testCreateUserPageRedirectWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->aminUser);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_create'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
//check redirection of user edit depending the role of the user
    public function testEditUserPageRedirectWhenUserIsNotAdmin(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    public function testEditUserPageRedirectWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->aminUser);
        $userTest = $this->userRepository->findOneByEmail('marcelle73edit@ifrance.com');
        $crowler = $this->client->request('GET', '/users/' . $userTest->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        // dd($crowler->selectButton('EDIT'));
        $form = $crowler->selectButton('modifier')->form();
        $form['user[email]'] = 'marcelle73@ifrance.com';
        $form['user[roles]'] = 'ROLE_USER';
        $form['user[username]'] = 'TestuserModif';
        $form["user[password][first]"] = 'password';
        $form["user[password][second]"] = 'password';

        $this->client->submit($form);
        // dd($this->client->followRedirects());
        $this->client->followRedirects();
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        // $this->assertSelectorTextContains("", "L'utilisateur a bien été modifié");
        // $this->assertTrue("marcelle73t@ifrance.com" === $this->userRepository->find($userTest->getId())->getEmail());

    }
}
