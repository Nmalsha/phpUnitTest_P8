<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class UserController extends AbstractController
{

    private $userRepository;
    private $doctrine;
    private $cache;

    public function __construct(UserRepository $userRepository, ManagerRegistry $doctrine, CacheInterface $cache)
    {

        $this->userRepository = $userRepository;
        $this->doctrine = $doctrine;
        $this->cache = $cache;
    }

    /**
     * @Route("/users", name="user_list")
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {

        if ($this->getUser()->getRoles()[0] == "ROLE_ADMIN") {
            $users = $this->cache->get('user_list', function () {
                return $this->userRepository->findAll();
            });

            $userspag = $paginator->paginate(
                $users,
                $request->query->getInt('page', 1),
                5
            );

            return $this->render('user/list.html.twig', ['users' => $userspag]);
        }
        //if the current user is not the admin re direct to the task list
        $this->addFlash('error', "Vous n'pouvez pas accéder aux pages de gestion des utilisateurs ");
        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ) {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($this->getUser()->getRoles()[0] == "ROLE_ADMIN") {
            if ($form->isSubmitted() && $form->isValid()) {

                //encode the plain password

                $password = $userPasswordHasher->hashPassword($user, $user->getPassword());

                $user->setPassword($password);
                $user->setRoles($form->get('roles')->getData());
                $entityManager->persist($user);
                $entityManager->flush();
                // delete cache key
                $this->cache->delete('user_list');

                $this->addFlash('success', "L'utilisateur a bien été ajouté.");

                return $this->redirectToRoute('user_list');
            }

        } else {
            $this->addFlash('error', "Vous n'avez pas le doit de créer un user");
            return $this->redirectToRoute('task_list');

        }
        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function editAction(User $user, Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($this->getUser()->getRoles()[0] == "ROLE_ADMIN") {
            if ($form->isSubmitted() && $form->isValid()) {
                $password = $userPasswordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
                //   dd($form->get('roles')->getData());
                $user->setRoles($form->get('roles')->getData());
                // dd($this->doctrine);
                $this->doctrine->getManager()->flush();

                $this->addFlash('success', "L'utilisateur a bien été modifié");
                // delete cache key
                $this->cache->delete('user_list');

                return $this->redirectToRoute('user_list');
            }

        } else {
            $this->addFlash('error', "Vous n'avez pas le doit de editer un user");
            return $this->redirectToRoute('task_list');

        }

        return $this->render('user/create.html.twig', ['form' => $form->createView(), 'user' => $user]);

    }
}
