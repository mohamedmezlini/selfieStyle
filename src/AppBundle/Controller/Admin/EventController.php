<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\GPage;
use AppBundle\Form\GPageType;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Form\CommentType;
use AppBundle\Form\PostType;
use AppBundle\Entity\Event;
use AppBundle\Form\EventType;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller used to manage blog contents in the backend.
 *
 * Please note that the application backend is developed manually for learning
 * purposes. However, in your real Symfony application you should use any of the
 * existing bundles that let you generate ready-to-use backends without effort.
 * See http://knpbundles.com/keyword/admin
 *
 * @Route("/admin/event")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class EventController extends Controller
{
   



//---------------------------------add Mayssa-------------------------
    /**
     * Lists all Event entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'admin_event_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     *
     * 
     * @ Route("/", name="admin_event")
     * @ Route("/", name="admin_event_index")
     * @ Method("GET")
     */
    public function indexEventAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $events = $entityManager->getRepository(Event::class)->findBy([], ['publishedAt' => 'DESC']);

        return $this->render('admin/event/index.html.twig', ['events' => $events]);
    }

    /**
     * Creates a new Event entity.
     *
     * @Route("/new", name="admin_event_new")
     * @Route("/", name="admin_event")
     * @Route("/", name="admin_event_index")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function newEventAction(Request $request)
    {
        $event = new Event();
        $event->setAuthorEmail($this->getUser()->getEmail()); 
       
        
        $entityManager = $this->getDoctrine()->getManager();
        $gpages = $entityManager->getRepository(GPage::class)->findBy([], ['publishedAt' => 'DESC']);
        $events = $entityManager->getRepository(Event::class)->findBy([], ['publishedAt' => 'DESC']);
        

        // See http://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(EventType::class, $event)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See http://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {
            $event->setSlug($this->get('slugger')->slugify($event->getTitle()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See http://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'event.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_event_new');
            }

            return $this->redirectToRoute('admin_event_new');
        }

        return $this->render('admin/event/new.html.twig', [
            'gpages' => $gpages,
            'event' => $event,
            'events' => $events,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="admin_event_show")
     * @Method("GET")
     */
    public function showEventAction(Event $event)
    {
        // This security check can also be performed:
        //   1. Using an annotation: @Security("event.isAuthor(user)")
        //   2. Using a "voter" (see http://symfony.com/doc/current/cookbook/security/voters_data_permission.html)
        if (null === $this->getUser() || !$event->isAuthor($this->getUser())) {
            throw $this->createAccessDeniedException('Pages can only be shown to their authors.');
        }

        $deleteForm = $this->createDeleteFormPage($event);

        return $this->render('admin/event/show.html.twig', [
            'event'        => $event,
            'delete_form' => $deleteForm->createView(),
        ]);
    }










    /**
     * Displays a form to edit an existing Event entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="admin_event_edit")
     * @Method({"GET", "POST"})
     */
    public function editEventAction(Event $event, Request $request)
    {
        if (null === $this->getUser() || !$event->isAuthor($this->getUser())) {
            throw $this->createAccessDeniedException('Events can only be edited by their authors.');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(EventType::class, $event);
        $deleteForm = $this->createDeleteFormPage($event);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $event->setSlug($this->get('slugger')->slugify($event->getTitle()));
            $entityManager->flush();

            $this->addFlash('success', 'Evnet.updated_successfully');

            return $this->redirectToRoute('admin_event_edit', ['id' => $event->getId()]);
        }

        return $this->render('admin/event/edit.html.twig', [
            'event'        => $event,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Event entity.
     *
     * @Route("/{id}", name="admin_event_delete")
     * @Method("DELETE")
     * @Security("event.isAuthor(user)")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     * The isAuthor() method is defined in the AppBundle\Entity\Event entity.
     */
    public function deleteEventAction(Request $request, Event $event)
    {
        $form = $this->createDeleteFormPage($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($event);
            $entityManager->flush();

            $this->addFlash('success', 'event.deleted_successfully');
        }

        return $this->redirectToRoute('admin_event_new');
    }

    /**
     * Creates a form to delete a Event entity by id.
     *
     * This is necessary because browsers don't support HTTP methods different
     * from GET and POST. Since the controller that removes the page events expects
     * a DELETE method, the trick is to create a simple form that *fakes* the
     * HTTP DELETE method.
     * See http://symfony.com/doc/current/cookbook/routing/method_parameters.html.
     *
     * @param Event $event The event object
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteFormPage(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_event_delete', ['id' => $event->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


 /**
     * This controller is called directly via the render() function in the
     * blog/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     *
     * @ param Post $post
     *
     * @return Response
     */
    public function eventFormAction()
    {
        $form = $this->createForm(EventType::class);

        return $this->render('admin/event/_form.html.twig', [
            //'post' => $post,
            'form' => $form->createView(),
        ]);
    }


//----------------------------------End Add-----------------------------


}
