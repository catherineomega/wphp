<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Form\RoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Role controller.
 *
 * @Route("/role")
 */
class RoleController extends Controller {

    /**
     * Lists all Role entities.
     *
     * @Route("/", name="role_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Role e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $roles = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'roles' => $roles,
        );
    }

    /**
     * @param Request $request
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/typeahead", name="role_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeaheadAction(Request $request) {
        $q = $request->query->get('q');
        if( ! $q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(role::class);
        $data = [];
        foreach($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => $result->getName(),
            ];
        }
        
        return new JsonResponse($data);
    }   
    /**
     * Creates a new Role entity.
     *
     * @Route("/new", name="role_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            $this->addFlash('success', 'The new role was created.');
            return $this->redirectToRoute('role_show', array('id' => $role->getId()));
        }

        return array(
            'role' => $role,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Role entity.
     *
     * @Route("/{id}", name="role_show")
     * @Method("GET")
     * @Template()
     * @param Role $role
     */
    public function showAction(Role $role) {

        return array(
            'role' => $role,
        );
    }
    
    /**
     * Displays a form to edit an existing Role entity.
     *
     * @Route("/{id}/edit", name="role_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Template()
     * @param Request $request
     * @param Role $role
     */
    public function editAction(Request $request, Role $role) {
        $editForm = $this->createForm(RoleType::class, $role);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The role has been updated.');
            return $this->redirectToRoute('role_show', array('id' => $role->getId()));
        }

        return array(
            'role' => $role,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Role entity.
     *
     * @Route("/{id}/delete", name="role_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @param Request $request
     * @param Role $role
     */
    public function deleteAction(Request $request, Role $role) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($role);
        $em->flush();
        $this->addFlash('success', 'The role was deleted.');

        return $this->redirectToRoute('role_index');
    }

}
