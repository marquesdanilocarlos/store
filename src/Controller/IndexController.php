<?php

namespace App\Controller;

use App\Entity\Product;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $product = new Product(
            'Primeiro produto',
            'Descrição do primeiro produto',
            'Informações do primeiro produto',
            39.90,
            'primeiro-produto',
            new DateTimeImmutable('now')
        );

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $name = "Danilo Marques";
        return $this->render('index.html.twig', compact('name'));
    }

    #[Route('/product/{slug}', name: 'product_single')]
    public function product(string $slug): Response
    {
        return $this->render('product/single.html.twig', compact('slug'));
    }
}
