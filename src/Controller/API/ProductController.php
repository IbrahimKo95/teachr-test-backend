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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['price']) || !isset($data['category_id']) || !isset($data['description'])) {
            return new JsonResponse(['error' => 'Veuillez remplir tous les champs !'], JsonResponse::HTTP_BAD_REQUEST);
        }

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

    #[Route('/api/products/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(Product $product, EntityManagerInterface $em)
    {
        $em->remove($product);
        $em->flush();
        return new JsonResponse(null, 204);
    }

    #[Route('/api/products/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['PUT'])]
    public function update(Request $request, Product $currentProduct, SerializerInterface $serializer, EntityManagerInterface $em, CategoryRepository $categoryRepository, ValidatorInterface $validator)
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['price']) || !isset($data['category_id']) || !isset($data['description'])) {
            return new JsonResponse(['error' => 'Veuillez remplir tous les champs !'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $updateProduct = $serializer->deserialize($request->getContent(), Product::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentProduct]);

        $errors = $validator->validate($updateProduct);
        if (count($errors) > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $category = $categoryRepository->find($request->toArray()['category_id']);
        $updateProduct->setCategory($category);
        $em->persist($updateProduct);
        $em->flush();
        return $this->json($updateProduct, 201, [], ['groups' => 'products']);
    }

    #[Route('/api/products/voiture', methods: ['GET'])]
    public function getProductWithVoiture(ProductRepository $repository, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->findBy(["name"=> "Voiture"]);
        if(!$category || !isset($category)){
            return new JsonResponse(['error' => 'Catégorie voiture non trouvée !'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $products = $repository->findBy(["category" => $category[0]]);
        return $this->json($products, 200, [], ['groups' => 'products']);
    }


}