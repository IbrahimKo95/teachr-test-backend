<?php

namespace App\Controller\API;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    #[Route('/api/products', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, CategoryRepository $categoryRepository, ValidatorInterface $validator)
    {
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $product->setCreatedAt(new \DateTime());
        $category = $categoryRepository->find($request->toArray()['category_id']);
        $product->setCategory($category);
        $em->persist($product);
        $em->flush();
        return $this->json($product, 201, [], ['groups' => 'products']);
    }

}