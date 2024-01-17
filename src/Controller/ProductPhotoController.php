<?php

namespace App\Controller;

use App\Entity\ProductPhoto;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[\Symfony\Component\Routing\Attribute\Route('/admin/products', name: 'admin_')]
class ProductPhotoController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UploadService $uploader
    ) {
    }

    #[Route('/photo/{productPhoto}', name: 'product_photo_remove', methods: ['DELETE'])]
    public function remove(ProductPhoto $productPhoto): Response
    {
        try {
            $photoFile = "{$this->getParameter('upload_dir')}/products/{$productPhoto->getPhoto()}";

            $this->entityManager->remove($productPhoto);
            $this->entityManager->flush();

            $this->uploader->remove($photoFile);

            return $this->redirectToRoute('admin_edit_products', ['product' => $productPhoto->getProduct()->getId()]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
