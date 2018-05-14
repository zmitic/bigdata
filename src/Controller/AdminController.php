<?php

namespace App\Controller;

use App\Service\Admin;
use App\Service\FiltersHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\IsTrue;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /** @var Admin */
    private $admin;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    /**
     * @Route("/admin", name="admin")
     * @Method("GET")
     */
    public function admin(): Response
    {
        return $this->render('admin/base.html.twig');
    }

    /**
     * @Route("/{segment}", name="admin_list")
     */
    public function list(Request $request, FiltersHandler $filtersHandler): Response
    {
        $page = $request->query->getInt('page', 1);
        $segment = $request->attributes->getAlpha('segment');
        $config = $this->admin->getConfigForSegment($segment);
        $columns = $config->getColumnsList();

        $formModel = $config->getFilterForm($filtersHandler);
        $form = $formModel->getForm($request);
        $filters = $form->getData();
        $pager = $config->getPager($page, $filters);

        return $this->render('admin/list.html.twig', [
            'columns' => $columns,
            'pager' => $pager,
            'filter_form' => $form->createView(),
            'segment' => $segment,
        ]);
    }

    /**
     * @Route("/{segment}/edit/{id}", name="admin_edit")
     * @Method(methods={"GET", "POST"})
     */
    public function edit(Request $request): Response
    {
        $segment = $request->attributes->getAlpha('segment');
        $id = $request->attributes->get('id');
        $config = $this->admin->getConfigForSegment($segment);
        $entity = $config->findOne($id);

        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $formBuilder = $this->createFormBuilder($entity);
        $config->setFormBuilder($formBuilder);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $config->updateOne($entity);
            $routeParams = array_merge(['segment' => $segment], $request->query->all());

            return $this->redirectToRoute('admin_list', $routeParams);
        }

        return $this->render('admin/edit.html.twig', [
            'segment' => $segment,
            'id' => $id,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{segment}/delete/{id}", name="admin_delete")
     * @Method(methods={"GET", "POST"})
     */
    public function delete(Request $request): Response
    {
        $segment = $request->attributes->getAlpha('segment');
        $id = $request->attributes->get('id');
        $config = $this->admin->getConfigForSegment($segment);
        $entity = $config->findOne($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder()
            ->add('confirm', CheckboxType::class, [
                'constraints' => [
                    new IsTrue(['message' => 'You must confirm deletion of object.']),
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $config->deleteOne($entity);
            $routeParams = array_merge(['segment' => $segment], $request->query->all());

            return $this->redirectToRoute('admin_list', $routeParams);
        }

        return $this->render('admin/delete.html.twig', [
            'segment' => $segment,
            'id' => $id,
            'form' => $form->createView(),
        ]);
    }

    public function navLeft(Admin $admin, RequestStack $requestStack): Response
    {
        $masterRequest = $requestStack->getMasterRequest();
        if (!$masterRequest) {
            throw new \LogicException('You must embed this action.');
        }
        $segments = $admin->getSegmentNames();

        return $this->render('admin/navigation_left.html.twig', [
            'segments' => $segments,
            'active' => $masterRequest->attributes->getAlpha('segment'),
        ]);
    }
}
