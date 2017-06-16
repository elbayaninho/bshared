<?php

namespace Bshared\BsharedBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Bshared\BsharedBundle\Entity\Document;
use Bshared\BsharedBundle\Form\Type\DocumentType;
use Bshared\BsharedBundle\Form\Type\DocumentFilterType;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Document controller.
 *
 */
class DocumentController extends Controller
{
    /**
     * Lists all Document entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(DocumentFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'document', 'user_document'))) {
            return $response;
        }
        $qb = $em->getRepository('BsharedBsharedBundle:Document')->createQueryBuilder('d');
        $paginator = $this->filter($form, $qb, 'document');
        return $this->render('BsharedBsharedBundle:Document:index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }

    /**
     * Finds and displays a Document entity.
     *
     */
    public function showAction(Document $document)
    {
        $deleteForm = $this->createDeleteForm($document->getId(), 'user_document_delete');

        return $this->render('BsharedBsharedBundle:Document:show.html.twig', array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Document entity.
     *
     */
    public function newAction()
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);

        return $this->render('BsharedBsharedBundle:Document:new.html.twig', array(
            'document' => $document,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function createAction(Request $request)
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        if ($form->handleRequest($request)->isValid()) {
            // On recupere le document
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $document->getFile();
            // On recupere son extension
            $fileExtension = $file->getClientOriginalExtension();
            // On recupere la taille
            $fileSize = $file->getClientSize();
            // On lui attribue un nom
            $fileName = $file->getClientOriginalName();
            // Move the file to the directory where documents are stored
            $fileDir = $this->container->getParameter('document_directory');
            // On move le file
            $file->move($fileDir, $fileName);
            // Update the 'file' property to store the extension file name
            // instead of its contents
            $document->setName($fileName);
            $document->setSize($fileSize);
            $document->setExtension($fileExtension);
            $document->setFile($file);
            //
//            die(dump($document));
            $author = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $document->setAuthor($author);
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('user_document_show', array('id' => $document->getId())));
        }

        return $this->render('BsharedBsharedBundle:Document:new.html.twig', array(
            'document' => $document,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Document entity.
     *
     */
    public function editAction(Document $document)
    {
        $editForm = $this->createForm(DocumentType::class, $document, array(
            'action' => $this->generateUrl('user_document_update', array('id' => $document->getId())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($document->getId(), 'user_document_delete');

        return $this->render('BsharedBsharedBundle:Document:edit.html.twig', array(
            'document' => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Document entity.
     *
     */
    public function updateAction(Document $document, Request $request)
    {
        $editForm = $this->createForm(DocumentType::class, $document, array(
            'action' => $this->generateUrl('user_document_update', array('id' => $document->getId())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            // On recupere le document
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $document->getFile();
            // On recupere son extension
            $fileExtension = $file->getClientOriginalExtension();
            // On recupere la taille
            $fileSize = $file->getClientSize();
            // On lui attribue un nom
            $fileName = $file->getClientOriginalName();
            // Move the file to the directory where documents are stored
            $fileDir = $this->container->getParameter('document_directory');
            // On move le file
            $file->move($fileDir, $fileName);
            // Update the 'file' property to store the extension file name
            // instead of its contents
            $document->setName($fileName);
            $document->setSize($fileSize);
            $document->setExtension($fileExtension);
            $document->setFile($file);
            $author = $this->container->get('security.token_storage')->getToken()->getUser();
            $document->setAuthor($author);
            $document->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('user_document_edit', array('id' => $document->getId())));
        }
        $deleteForm = $this->createDeleteForm($document->getId(), 'user_document_delete');

        return $this->render('BsharedBsharedBundle:Document:edit.html.twig', array(
            'document' => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Save order.
     *
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('document', $field, $type);

        return $this->redirect($this->generateUrl('user_document'));
    }

    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->container->get('request_stack')->getCurrentRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->container->get('request_stack')->getCurrentRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Save filters
     *
     * @param  FormInterface $form
     * @param  string        $name   route/entity name
     * @param  string        $route  route name, if different from entity name
     * @param  array         $params possible route parameters
     * @return Response
     */
    protected function saveFilter(FormInterface $form, $name, $route = null, array $params = null)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $url = $this->generateUrl($route ?: $name, is_null($params) ? array() : $params);
        if ($request->query->has('submit-filter') && $form->handleRequest($request)->isValid()) {
            $request->getSession()->set('filter.' . $name, $request->query->get($form->getName()));

            return $this->redirect($url);
        } elseif ($request->query->has('reset-filter')) {
            $request->getSession()->set('filter.' . $name, null);

            return $this->redirect($url);
        }
    }

    /**
     * Filter form
     *
     * @param  FormInterface                                       $form
     * @param  QueryBuilder                                        $qb
     * @param  string                                              $name
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    protected function filter(FormInterface $form, QueryBuilder $qb, $name)
    {
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
            }
        }

        // possible sorting
        $this->addQueryBuilderSort($qb, $name);
        return $this->get('knp_paginator')->paginate($qb, $this->container->get('request_stack')->getCurrentRequest()->query->get('page', 1), 20);
    }

    /**
     * Get filters from session
     *
     * @param  string $name
     * @return array
     */
    protected function getFilter($name)
    {
        return $this->container->get('request_stack')->getCurrentRequest()->getSession()->get('filter.' . $name);
    }

    /**
     * Deletes a Document entity.
     *
     */
    public function deleteAction(Document $document, Request $request)
    {
        $form = $this->createDeleteForm($document->getId(), 'user_document_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            // On supprime le fichier associe a document
            $link = $this->container->getParameter('document_directory')."/".$document->getName();
//          // On recupere le chemin adsolu du fichier et on le passe a la methode unlink qui va supprimer le fichier
            if (isset($link)) {
                unlink($link);
            }
            // On flush
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user_document'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

}
