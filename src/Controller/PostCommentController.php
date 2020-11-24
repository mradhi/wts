<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostCommentController extends AbstractController
{
    /**
     * @param Post                   $post
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     *
     * @Route("/app/posts/{post}/edit", name="app_posts_edit")
     */
    public function editPost(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_idea_home');
        }

        return $this->render('edit_post.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Comment                $comment
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     *
     * @Route("/app/comments/{comment}/edit", name="app_comments_edit")
     */
    public function editComment(Comment $comment, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_idea_forum');
        }

        return $this->render('edit_comment.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Post                   $post
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     *
     * @Route("/app/posts/{post}/delete", name="app_posts_delete")
     */
    public function deletePost(Post $post, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_idea_home');
    }

    /**
     * @param Comment                $comment
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     *
     * @Route("/app/comments/{comment}/delete", name="app_comments_delete")
     */
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_idea_forum');
    }
}