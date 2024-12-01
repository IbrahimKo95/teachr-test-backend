<?php

namespace App\Controller\API;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{

    #[Route('/api/products', methods: ['GET'])]
    public function index(ProductRepository $repository)
    {
        $products = $repository->findAll();
        return $this->json($products, 200, [], ['groups' => 'products']);
    }

    #[Route('/api/products/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Product $product)
    {
        return $this->json($product, 200, [], ['groups' => 'products']);
    }


}