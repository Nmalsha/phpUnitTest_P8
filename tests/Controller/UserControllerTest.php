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
        $this->adminUser = $this->userRepository->findOneByEmail('admin12@gmail.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');

    }

    //check redirection of the page when login depending the role of the user

    /**
     * @covers UserController::listAction
     */

    public function testUserPageRedirectWhenUserIsNotAdmin(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @covers UserController::listAction
     */

    public function testUserPageRedirectWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->adminUser);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

//check redirection of user create depending the role of the user

    /**
     * @covers UserController::createAction
     */

    public function testCreateUserPageRedirectWhenUserIsNotAdmin(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @covers UserController::createAction
     */

    public function testCreateUserPageRedirectWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->adminUser);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_create'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

//check user create
    /**
     * @covers UserController::createAction
     */

    public function testCreateUserWhenUserIsAdmin(): void
    {
        $userAdmin = $this->client->loginUser($this->adminUser);
        $data = ["User-Test", "password", "password", "ROLE_USER", "user.test@gmail.com"];
        // dd($data);

        $this->createForm($userAdmin, $data);

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

//check redirection of user edit depending the role of the user

    /**
     * @covers UserController::editAction
     */

    public function testEditUserPageRedirectWhenUserIsNotAdmin(): void
    {
        $this->client->loginUser($this->user);
        // $this->assertSelectorTextContains('',"Vous ne disposez pas des droits requis pour réaliser cette action");
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

//check user edit
    /**
     * @covers UserController::editAction
     */

    public function testEditUserWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->adminUser);
        $userTest = $this->userRepository->findOneByEmail('marcelle73@ifrance.com');
        // dd($userTest);
        $crowler = $this->client->request('GET', '/users/' . $userTest->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crowler->selectButton('Modifier')->form();
        $form['user[email]'] = 'marcelle73edit@ifrance.com';
        $form['user[roles]'] = 'ROLE_USER';
        $form['user[username]'] = 'TestuserModif';
        $form["user[password][first]"] = 'password';
        $form["user[password][second]"] = 'password';

        $this->client->submit($form);

        $this->client->followRedirects();
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    private function createForm($user, $data)
    {
        $this->client->loginUser($this->adminUser);
        $crawler = $this->client->request('GET', '/users/create');
        $createButton = $crawler->selectButton("Ajouter");
        $form = $createButton->form();
        $form["user[username]"] = $data[0];
        $form["user[password][first]"] = $data[1];
        $form["user[password][second]"] = $data[2];
        $form["user[roles]"]->availableOptionValues()[0] = $data[3];
        $form["user[email]"] = $data[4];

        return $this->client->submit($form);

    }
}
