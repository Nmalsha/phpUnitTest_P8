<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {

        if ($this->getUser()->getRoles()[0] == "ROLE_ADMIN") {
            return $this->render('user/list.html.twig', ['users' => $this->getDoctrine()->getRepository(User::class)->findAll()]);
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
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator) {

        // dd($this->getUser()->getRoles());
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($this->getUser()->getRoles()[0]);
            if ($this->getUser()->getRoles()[0] == "ROLE_ADMIN") {

                //encode the plain password

                $password = $userPasswordHasher->hashPassword($user, $user->getPassword());

                $user->setPassword($password);
                $user->setRoles($form->get('roles')->getData());
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', "L'utilisateur a bien été ajouté.");

                return $this->redirectToRoute('user_list');
            } else {
                $this->addFlash('error', "Vous n'avez pas le doit de créer un user");

            }

        }
        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function editAction($id, User $user, Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        // dd($this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $id])->getroles()[0]);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            //   dd($form->get('roles')->getData());
            $user->setRoles($form->get('roles')->getData());

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);

    }

}
