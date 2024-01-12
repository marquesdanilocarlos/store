<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        /*$user = new User(
            'Danilo',
            'Marques',
            'marquesdanilocarlos@gmail.com',
            'a654321'
        );
        $this->entityManager->persist($user);

        $address = new Address(
            'QA 13 MR Casa',
            '08',
            'Setor Sul',
            'Planaltina',
            'GO',
            '73753-113'
        );

        $address->setUser($user);
        $this->entityManager->persist($address);
        $this->entityManager->flush();*/

        $user = $this->userRepository->find(3);

        $name = "Danilo Marques";
        return $this->render('index.html.twig', compact('name', 'user'));
    }

    #[Route('/product/{slug}', name: 'product_single')]
    public function product(string $slug): Response
    {
        return $this->render('product/single.html.twig', compact('slug'));
    }
}
