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
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private ProductRepository $productRepository
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $user = $this->userRepository->find(3);
        $orders = $user->getOrders();
        $product = $this->productRepository->find(2);
        $categories = $product->getCategories();

        $name = "Danilo Marques";
        return $this->render('index.html.twig', compact('name', 'user', 'orders', 'categories'));
    }

    #[Route('/product/{slug}', name: 'product_single')]
    public function product(string $slug): Response
    {
        return $this->render('product/single.html.twig', compact('slug'));
    }
}
