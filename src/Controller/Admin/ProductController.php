<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/products', name: 'admin_')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private UploadService $uploader
    ) {
    }

    #[Route('/', name: 'index_products', methods: ['GET'])]
    public function index(): Response
    {
        //$uploader = $this->container->get('uploader');
        dump($this->uploader->upload());
        dump($this->getParameter('upload_dir'));

        $products = $this->productRepository->findAll();
        return $this->render('admin/product/index.html.twig', compact('products'));
    }

    #[Route('/create', name: 'create_products', methods: ['GET'])]
    public function create(Request $request): Response
    {
        $productForm = $this->createForm(ProductType::class);
        return $this->render('admin/product/create.html.twig', compact('productForm'));
    }

    #[Route('/create', name: 'store_products', methods: ['POST'])]
    public function store(Request $request): Response
    {
        try {
            $productForm = $this->createForm(ProductType::class);
            $productForm->handleRequest($request);

            if (!$productForm->isSubmitted() || !$productForm->isValid()) {
                return $this->render('admin/product/create.html.twig', compact('productForm'));
            }

            $product = $productForm->getData();
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw $e;
        }
        $this->addFlash('success', 'Produto criado com sucesso!');
        return $this->redirectToRoute('admin_index_products');
    }

    #[Route('/edit/{product}', name: 'edit_products', methods: ['GET'])]
    public function edit(Product $product): Response
    {
        $productForm = $this->createForm(ProductType::class, $product, ['isEdit' => true]);
        return $this->render('admin/product/edit.html.twig', compact('productForm'));
    }

    #[Route('/edit/{product}', name: 'update_products', methods: ['PUT'])]
    public function update(Product $product, Request $request): Response
    {
        try {
            $productForm = $this->createForm(ProductType::class, $product, ['isEdit' => true]);
            $productForm->handleRequest($request);

            if (!$productForm->isSubmitted()) {
                return $this->render('admin/product/edit.html.twig', compact('productForm'));
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'Produto editado com sucesso!');
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

    #[Route('/upload', methods: ['POST'])]
    public function upload(Request $request)
    {
        $photos = $request->files->get('photos');
        $this->uploader->upload($photos, 'products');

        return new Response('Upload...');
    }
}
