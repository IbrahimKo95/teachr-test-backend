<?php

namespace App\Controller\API;

use App\Entity\Category;
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

class CategoryController extends AbstractController
{

    #[Route('/api/categories', methods: ['GET'])]
    public function index(CategoryRepository $repository)
    {
        $categories = $repository->findAll();
        return $this->json($categories, 200, [], ['groups' => 'categories']);
    }

    #[Route('/api/categories/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Category $categories)
    {
        return $this->json($categories, 200, [], ['groups' => 'categories']);
    }

    #[Route('/api/categories', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $category = $serializer->deserialize($request->getContent(), Category::class, 'json');

        $errors = $validator->validate($category);
        if (count($errors) > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $em->persist($category);
        $em->flush();
        return $this->json($category, 201, [], ['groups' => 'categories']);
    }

    #[Route('/api/categories/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(Category $categories, EntityManagerInterface $em)
    {
        $em->remove($categories);
        $em->flush();
        return new JsonResponse(null, 204);
    }

    #[Route('/api/categories/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['PUT'])]
    public function update(Request $request, Category $currentCategory, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $updateCategory = $serializer->deserialize($request->getContent(), Category::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCategory]);

        $errors = $validator->validate($updateCategory);
        if (count($errors) > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $em->persist($updateCategory);
        $em->flush();
        return $this->json($updateCategory, 201, [], ['groups' => 'categories']);
    }

}