<?php

namespace Guilro\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Guilro\TestBundle\Entity\TestObject;
use Guilro\TestBundle\Form\TestObjectType;

/**
 * TestObject controller.
 *
 */
class TestObjectController extends Controller
{

    /**
     * Lists all TestObject entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GuilroTestBundle:TestObject')->findAll();

        return $this->render('GuilroTestBundle:TestObject:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TestObject entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TestObject();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $securityContext = $this->get('security.context');
//            $user = $securityContext->getToken()->getUser();
//            $sid = $securityIdentity = UserSecurityIdentity::fromAccount($user);

            $fieldName = 'aFieldName';
            // trying code following issue #9433
            // step 2 works
            $aclProvider = $this->get('security.acl.provider');
            $oid = ObjectIdentity::FromDomainObject($entity);
            $acl = $aclProvider->createAcl($oid);

            $roleUser  = new RoleSecurityIdentity('ROLE_USER');
            $mask      = new MaskBuilder(4); // 4 = EDIT
            $acl->insertobjectFieldAce($fieldName, $roleUser, $mask->get());

            $aclProvider->updateAcl($acl);

            // step 3 works
            $acl  = $aclProvider->findAcl($oid);
            $roleUser  = new RoleSecurityIdentity('ROLE_FOO');
            $mask      = new MaskBuilder(4); // 4 = EDIT

            $acl->insertobjectFieldAce($fieldName, $roleUser, $mask->get());
            $aclProvider->updateAcl($acl);

            // step 4 ?
            $acl  = $aclProvider->findAcl($oid);
            $roleUser  = new RoleSecurityIdentity('ROLE_BAR');
            $mask      = new MaskBuilder(4); // 4 = EDIT

            $acl->insertobjectFieldAce($fieldName, $roleUser, $mask->get());
            $aclProvider->updateAcl($acl);

            $entities = $em->getRepository('GuilroTestBundle:TestObject')->findAll();
            return $this->render('GuilroTestBundle:TestObject:index.html.twig', array(
                'entities' => $entities,
            ));
        }

        return $this->render('GuilroTestBundle:TestObject:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a TestObject entity.
    *
    * @param TestObject $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TestObject $entity)
    {
        $form = $this->createForm(new TestObjectType(), $entity, array(
            'action' => $this->generateUrl('testobject_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TestObject entity.
     *
     */
    public function newAction()
    {
        $entity = new TestObject();
        $form   = $this->createCreateForm($entity);

        return $this->render('GuilroTestBundle:TestObject:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TestObject entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GuilroTestBundle:TestObject')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TestObject entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('GuilroTestBundle:TestObject:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing TestObject entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GuilroTestBundle:TestObject')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TestObject entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('GuilroTestBundle:TestObject:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TestObject entity.
    *
    * @param TestObject $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TestObject $entity)
    {
        $form = $this->createForm(new TestObjectType(), $entity, array(
            'action' => $this->generateUrl('testobject_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TestObject entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GuilroTestBundle:TestObject')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TestObject entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('testobject_edit', array('id' => $id)));
        }

        return $this->render('GuilroTestBundle:TestObject:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a TestObject entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GuilroTestBundle:TestObject')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TestObject entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('testobject'));
    }

    /**
     * Creates a form to delete a TestObject entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('testobject_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
