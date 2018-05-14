<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/suggestions")
 */
class SuggestionController extends Controller
{
    /**
     * @Route("/categories", name="suggestions_categories")
     */
    public function index(Request $request, CategoryRepository $repository): JsonResponse
    {
        $q = $request->query->get('q');
        $categories = $repository->getResults(
            $repository->whereNameLike($q),
            $repository->setMaxResults(20)
        );

        $results = array_map(function (Category $category) {
            return [
                'id' => $category->getId(),
                'text' => (string) $category,
            ];
        }, $categories);

        return $this->json($results);
    }
}
