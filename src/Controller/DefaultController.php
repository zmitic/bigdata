<?php

namespace App\Controller;

use App\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder('o')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        dump((string)$product->getid());die;
        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', [
            'path' => str_replace($this->getParameter('kernel.project_dir') . '/', '', __FILE__)
        ]);
    }

    /**
     * @Route("/product/{id}")
     * @param Product $product
     */
    public function showProduct(Product $product)
    {
        dump($product);die;
    }
}
