<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/products', name: 'admin_')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private SluggerInterface $slugger
    ) {
    }

    #[Route('/', name: 'index_products', methods: ['GET'])]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();
        return $this->render('admin/product/index.html.twig', compact('products'));
    }

    #[Route('/create', name: 'create_products', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('admin/product/create.html.twig');
    }

    #[Route('/store', name: 'store_products', methods: ['POST'])]
    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $request->request->all();
            $product = new Product(
                $data['name'],
                $data['description'],
                $data['body'],
                $data['price'],
                $this->slugger->slug($data['name']),
                new \DateTimeImmutable('now')
            );

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Produto criado com sucesso!');
        } catch (Exception $e) {
            throw $e;
        }

        return $this->redirectToRoute('admin_index_products');
    }

    #[Route('/edit/{product}', name: 'edit_products', methods: ['GET'])]
    public function edit(Product $product): Response
    {
        return $this->render('admin/product/edit.html.twig', compact('product'));
    }

    #[Route('/update/{product}', name: 'update_products', methods: ['PUT'])]
    public function update(Product $product, Request $request): RedirectResponse
    {
        try {
            $data = $request->request->all();
            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setBody($data['body']);
            $product->setPrice($data['price']);
            $product->setUpdatedAt(new \DateTimeImmutable('now'));
            $this->entityManager->flush();

            $this->addFlash('success', 'Produto criado com sucesso!');
        } catch (Exception $e) {
            throw $e;
        }

        return $this->redirectToRoute('admin_edit_products', ['product' => $product->getId()]);
    }

    #[Route('/remove/{product}', name: 'remove_products', methods: ['DELETE'])]
    public function remove(Product $product): RedirectResponse
    {
        try {
            $this->entityManager->remove($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Produto criado com sucesso!');
        } catch (Exception $e) {
            throw $e;
        }

        return $this->redirectToRoute('admin_index_products');
    }
}
