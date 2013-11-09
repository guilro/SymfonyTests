<?php

namespace Guilro\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
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
            $entity2 = new TestObject();
            $entity2->setTestField($entity->getTestField() . '_copy');
            $em->persist($entity2);
            $em->flush();

            $securityContext = $this->get('security.context');
            $user = $securityContext->getToken()->getUser();
            $sid = $securityIdentity = UserSecurityIdentity::fromAccount($user);

            $aclProvider = $this->get('security.acl.provider');
            $o1 = ObjectIdentity::FromDomainObject($entity);
            $o2 = ObjectIdentity::FromDomainObject($entity2);
            $aclProvider->createAcl($o1);
            $aclProvider->createAcl($o2);

            //code given in issue #9239

            $acl1 = $aclProvider->findAcl($o1);
            $acl2 = $aclProvider->findAcl($o2);

            $acl1->insertClassAce($sid, 4);
            $aclProvider->updateAcl($acl1);
            // Both acls see the class ace - OK
            var_dump($acl1->getClassAces()[0]->getMask()); // prints 4.
            var_dump($acl2->getClassAces()[0]->getMask()); // prints 4.

            $acl2->updateClassAce(0, 42);
            // Both acls see the updated class ace - OK
            var_dump($acl2->getClassAces()[0]->getMask()); // prints 42.
            var_dump($acl1->getClassAces()[0]->getMask()); // prints 42.

            $aclProvider->updateAcl($acl2); // [!!] Changes will not be saved to database
            //end code given in issue #9239

            return $this->redirect($this->generateUrl('testobject_show', array('id' => $entity->getId())));
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
