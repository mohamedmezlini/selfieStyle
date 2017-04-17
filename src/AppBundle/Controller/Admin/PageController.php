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
 * @Route("/admin/page")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class PageController extends Controller
{
   



//---------------------------------add Mayssa-------------------------
    /**
     * Lists all GPage entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'admin_gpage_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     *
     * 
     * @Route("/", name="admin_gpage")
     * @Route("/", name="admin_gpage_index")
     * @Method({"GET", "POST"})
     */
    public function indexActionPage(Request $request )
    {
       
        $entityManager = $this->getDoctrine()->getManager();
        $gpages = $entityManager->getRepository(GPage::class)->findBy([], ['publishedAt' => 'DESC']);
        $posts = $entityManager->getRepository(Post::class)->findBy([], ['publishedAt' => 'DESC']);
         $events = $entityManager->getRepository(Event::class)->findBy([], ['publishedAt' => 'DESC']);
      

        $post = new Post();
        $post->setAuthorEmail($this->getUser()->getEmail());

        // See http://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(PostType::class, $post);



        $form->handleRequest($request);
        

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See http://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($this->get('slugger')->slugify($post->getContent()));
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See http://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'post.created_successfully');


            return $this->redirectToRoute('admin_gpage_index');

       /* return $this->render('admin/page/index.html.twig', [
            'gpages' => $gpages, 
            'posts' => $posts,
            'post' => $post,
            'form' => $form->createView(),
            ]);*/
        }

    






        return $this->render('admin/page/index.html.twig', [
            'gpages' => $gpages, 
            'posts' => $posts,
            'post' => $post,
            'events' => $events,
            'form' => $form->createView(),
            ]);
    }



    public function indexActionPageAction(){
         return $this->redirectToRoute('admin_gpage_index');
    }

    /**
     * Creates a new GPage entity.
     *
     * @Route("/new", name="admin_gpage_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function newActionPage(Request $request)

    {   $entityManager = $this->getDoctrine()->getManager();
        $gpages = $entityManager->getRepository(GPage::class)->findBy([], ['publishedAt' => 'DESC']);
       $events = $entityManager->getRepository(Event::class)->findBy([], ['publishedAt' => 'DESC']);
       
        $gpage = new GPage();
        $gpage->setAuthorEmail($this->getUser()->getEmail());

        // See http://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(GPageType::class, $gpage)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See http://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {
            $gpage->setSlug($this->get('slugger')->slugify($gpage->getTitle()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($gpage);
            $entityManager->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See http://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'page.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_gpage_new');
            }

            return $this->redirectToRoute('admin_gpage_index');
        }

        return $this->render('admin/page/new.html.twig', [
            'gpage' => $gpage,
            'gpages' => $gpages,
            'events' => $events,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a GPage entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="admin_gpage_show")
     * @Method("GET")
     */
    public function showActionPage(GPage $gpage)
    {
        // This security check can also be performed:
        //   1. Using an annotation: @Security("gpage.isAuthor(user)")
        //   2. Using a "voter" (see http://symfony.com/doc/current/cookbook/security/voters_data_permission.html)
        if (null === $this->getUser() || !$gpage->isAuthor($this->getUser())) {
            throw $this->createAccessDeniedException('Pages can only be shown to their authors.');
        }

        $deleteForm = $this->createDeleteFormPage($gpage);

        return $this->render('admin/page/show.html.twig', [
            'gpage'        => $gpage,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing GPage entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="admin_gpage_edit")
     * @Method({"GET", "POST"})
     */
    public function editActionPage(GPage $gpage, Request $request)
    {
        if (null === $this->getUser() || !$gpage->isAuthor($this->getUser())) {
            throw $this->createAccessDeniedException('GPages can only be edited by their authors.');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(GPageType::class, $gpage);
        $deleteForm = $this->createDeleteFormPage($gpage);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $gpage->setSlug($this->get('slugger')->slugify($gpage->getTitle()));
            $entityManager->flush();

            $this->addFlash('success', 'page.updated_successfully');

            return $this->redirectToRoute('admin_gpage_edit', ['id' => $gpage->getId()]);
        }

        return $this->render('admin/page/edit.html.twig', [
            'gpage'        => $gpage,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a GPage entity.
     *
     * @Route("/{id}", name="admin_gpage_delete")
     * @Method("DELETE")
     * @Security("gpage.isAuthor(user)")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     * The isAuthor() method is defined in the AppBundle\Entity\GPage entity.
     */
    public function deleteActionPage(Request $request, GPage $gpage)
    {
        $form = $this->createDeleteFormPage($gpage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($gpage);
            $entityManager->flush();

            $this->addFlash('success', 'gpage.deleted_successfully');
        }

        return $this->redirectToRoute('admin_gpage');
    }

    /**
     * Creates a form to delete a GPage entity by id.
     *
     * This is necessary because browsers don't support HTTP methods different
     * from GET and POST. Since the controller that removes the page gpages expects
     * a DELETE method, the trick is to create a simple form that *fakes* the
     * HTTP DELETE method.
     * See http://symfony.com/doc/current/cookbook/routing/method_parameters.html.
     *
     * @param GPage $gpage The gpage object
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteFormPage(GPage $gpage)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_gpage_delete', ['id' => $gpage->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }





//----------------------------------End Add-----------------------------



//----------------------------------- commentaire d'un post ----------------------

     /**
     * @Route("/comment/{postSlug}/new", name="comment_new")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     *
     * NOTE: The ParamConverter mapping is required because the route parameter
     * (postSlug) doesn't match any of the Doctrine entity properties (slug).
     * See http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#doctrine-converter
     */
    public function commentNewAction(Request $request, Post $post)
    {
        $form = $this->createForm(CommentType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setAuthorEmail($this->getUser()->getEmail());
            $comment->setPost($post);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('blog_post', ['slug' => $post->getSlug()]);
        }

        return $this->render('blog/comment_form_error.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * This controller is called directly via the render() function in the
     * blog/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     *
     * @param Post $post
     *
     * @return Response
     */
    public function commentFormAction(Post $post)
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('blog/_comment_form.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

//---------------------------------------   post    add           -----------------------


    /**
     * Creates a new Post entity.
     *
     * @Route("/post/new", name="admin_gpage_post_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function newActionPost(Request $request)
    {
        $post = new Post();
        $post->setAuthorEmail($this->getUser()->getEmail());

        // See http://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(PostType::class, $post)
            ->add('saveAndCreateNew', SubmitType::class);



        $form->handleRequest($request);
        

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See http://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($this->get('slugger')->slugify($post->getTitle()));
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See http://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'post.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_post_new');
            }

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/blog/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

}
