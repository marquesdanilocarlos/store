<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;


#[Route("/admin/users", name: "admin_users_")]
class UserController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route("/", name: "index")]
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('admin/user/index.html.twig', compact('users'));
    }

    #[Route("/create", name: "create")]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Usuário criado com sucesso!');

            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route("/edit/{user}", name: "edit")]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $em
    ) {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Usuário atualizado com sucesso!');

            return $this->redirectToRoute('admin_users_edit', ['user' => $user->getId()]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route("/remove/{user}", name: "remove")]
    public function remove(User $user)
    {
        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Usuário removido com sucesso!');

            return $this->redirectToRoute('admin_users_index');
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}