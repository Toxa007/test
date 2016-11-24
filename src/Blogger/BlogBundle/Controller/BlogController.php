<?php
// src/Blogger/BlogBundle/Controller/BlogController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Comment;
use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Form\BlogType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Blog controller.
 */
class BlogController extends Controller
{
    /**
     * Show a blog entry
     */
    public function showAction($id, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        $comments = $em->getRepository('BloggerBlogBundle:Comment')
            ->getCommentsForBlog($blog->getId());

        return $this->render('BloggerBlogBundle:Blog:show.html.twig', array(
            'blog'      => $blog,
            'comments'  => $comments
        ));
    }

    public function newAction()
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);

        return $this->render('BloggerBlogBundle:Blog:create.html.twig', array(
            'blog' => $blog,
            'form' => $form->createView()
        ));
    }

    public function createAction(Request $request)
    {
        $blog = new Blog();
        $form    = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()
                ->getManager();
            $em->persist($blog);
            $em->flush();

            return $this->redirect($this->generateUrl('BloggerBlogBundle_homepage', array()));
        }

        return $this->render('BloggerBlogBundle:Blog:create.html.twig', array(
            'blog' => $blog,
            'form' => $form->createView()
        ));

    }
}