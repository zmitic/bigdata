<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends Controller
{
    /**
     * @Route("/", name="default")
     */
    public function index(ProductRepository $repo, Request $request): Response
    {
        $pager = $repo->paginate($request->query->get('page'), null);

        $products = $pager->getItems();
        $repo->optimizeJoinsOn($products);

        return $this->render('products/list.html.twig', [
            'pager' => $pager,
        ]);
    }
}
