<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private ProductRepository $productRepository,
        private EntityManagerInterface $em
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('index.html.twig', compact('user'));
    }

    #[Route('/product/{slug}', name: 'product_single')]
    public function product(string $slug): Response
    {
        return $this->render('product/single.html.twig', compact('slug'));
    }
}
