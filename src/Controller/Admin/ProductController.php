<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/products', name: 'admin_')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository
    ) {
    }

    #[Route('/', name: 'index_products', methods: ['GET'])]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();
        dd($products);
        return $this->render('admin/product/index.html.twig', compact('products'));
    }

    #[Route('/create', name: 'create_products', methods: ['GET'])]
    public function create()
    {
    }

    #[Route('/store', name: 'store_products', methods: ['POST'])]
    public function store()
    {
        $product = new Product(
            'Primeiro produto',
            'Descrição do primeiro produto',
            'Informações do primeiro produto',
            39.90,
            'primeiro-produto',
            new \DateTimeImmutable('now')
        );

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    #[Route('/edit/{product}', name: 'edit_products', methods: ['GET'])]
    public function edit(Product $product)
    {
        //$product = $this->productRepository->find(2);
    }

    #[Route('/update/{product}', name: 'update_products', methods: ['PUT'])]
    public function update(Product $product)
    {
        $product->setName('Primeiro produto com nome atualizado');
        $product->setUpdatedAt(new \DateTimeImmutable('now'));
        $this->entityManager->flush();
    }

    #[Route('/remove/{product}', name: 'remove_products', methods: ['DELETE'])]
    public function remove(Product $product)
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}
