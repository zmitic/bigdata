<?php

namespace App\Controller;

use App\Service\Admin;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     * @Method("GET")
     */
    public function admin(): Response
    {
        return $this->render('admin/base.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/{segment}", name="admin_segment")
     */
    public function list(Request $request, Admin $admin, string $segment): Response
    {
        $page = $request->query->getInt('page', 1);
        $config = $admin->getConfigForSegment($segment);
        $columns = $config->getColumnsList();
        $pager = $config->getPager($page);

        return $this->render('admin/list.html.twig', [
            'columns' => $columns,
            'pager' => $pager,
        ]);
    }

    public function navLeft(Admin $admin): Response
    {
        $segments = $admin->getSegmentNames();

        return $this->render('admin/navigation_left.html.twig', [
            'segments' => $segments,
        ]);
    }
}
