<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\ProductPhoto;
use App\Event\ProductCreated;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/products', name: 'admin_products_')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private UploadService $uploader
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();
        return $this->render('admin/product/index.html.twig', compact('products'));
    }

    #[Route('/create', name: 'create', methods: ['GET'])]
    public function create(Request $request): Response
    {
        $productForm = $this->createForm(ProductType::class);
        return $this->render('admin/product/create.html.twig', compact('productForm'));
    }

    #[Route('/create', name: 'store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        try {
            $productForm = $this->createForm(ProductType::class);
            $productForm->handleRequest($request);

            if (!$productForm->isSubmitted() || !$productForm->isValid()) {
                return $this->render('admin/product/create.html.twig', compact('productForm'));
            }

            $product = $productForm->getData();
            $photos = $productForm->get('photos')->getData();

            if ($photos) {
                $photos = $this->uploader->upload($photos, 'products');
                $photos = $this->createProductPhotos($photos);
                /**
                 * @var Product $product
                 */
                $product->addManyProductPhotos($photos);
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $dispatcher = new EventDispatcher();
            $event = new ProductCreated($product);
            $dispatcher->dispatch($event, ProductCreated::NAME);
        } catch (Exception $e) {
            throw $e;
        }
        $this->addFlash('success', 'Produto criado com sucesso!');
        return $this->redirectToRoute('admin_products_index');
    }

    #[Route('/edit/{product}', name: 'edit', methods: ['GET'])]
    public function edit(Product $product): Response
    {
        $productForm = $this->createForm(ProductType::class, $product, ['isEdit' => true]);
        return $this->render('admin/product/edit.html.twig', [
            'productForm' => $productForm,
            'productPhotos' => $product->getProductPhotos()
        ]);
    }

    #[Route('/edit/{product}', name: 'update', methods: ['PUT'])]
    public function update(Product $product, Request $request): Response
    {
        try {
            $productForm = $this->createForm(ProductType::class, $product, ['isEdit' => true]);
            $productForm->handleRequest($request);

            if (!$productForm->isSubmitted()) {
                return $this->render('admin/product/edit.html.twig', compact('productForm'));
            }

            $photos = $productForm->get('photos')->getData();
            if ($photos) {
                $photos = $this->uploader->upload($photos, 'products');
                $photos = $this->createProductPhotos($photos);
                /**
                 * @var Product $product
                 */
                $product->addManyProductPhotos($photos);
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'Produto editado com sucesso!');
        } catch (Exception $e) {
            throw $e;
        }

        return $this->redirectToRoute('admin_products_edit', ['product' => $product->getId()]);
    }

    #[Route('/remove/{product}', name: 'remove', methods: ['DELETE'])]
    public function remove(Product $product): RedirectResponse
    {
        try {
            $this->entityManager->remove($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Produto criado com sucesso!');
        } catch (Exception $e) {
            throw $e;
        }

        return $this->redirectToRoute('admin_products_index');
    }

    #[Route('/upload', name:'upload', methods: ['POST'])]
    public function upload(Request $request)
    {
        $photos = $request->files->get('photos');
        $this->uploader->upload($photos, 'products');

        return new Response('Upload...');
    }

    private function createProductPhotos(array $photos): array
    {
        $newProductPhotos = [];
        foreach ($photos as $photo) {
            $newProductPhotos[] = new ProductPhoto($photo);
        }
        return $newProductPhotos;
    }
}
