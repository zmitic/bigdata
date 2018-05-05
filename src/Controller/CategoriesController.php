<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends Controller
{
    /**
     * @Route("/", name="default")
     */
    public function index(CategoryRepository $repo, Request $request): Response
    {
        $pager = $repo->paginate($request->query->get('page'), null,
            $repo->orX(
                $repo->whereName('Category_0979'),
                $repo->whereName('Category_0001')
            )
        );

        return $this->render('categories/list.html.twig', [
            'pager' => $pager,
        ]);
    }
}
