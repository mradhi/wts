<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Point;
use App\Entity\PointVote;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\ExportType;
use App\Form\ImportPointsType;
use App\Form\PointType;
use App\Form\PostType;
use App\Repository\CommentRepository;
use App\Repository\PointRepository;
use App\Repository\PointVoteRepository;
use App\Repository\PostRepository;
use App\Request\ExportPoints;
use App\Request\ImportPoints;
use App\Service\PointFileReader;
use App\Service\PointFileWriter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app", name="app_idea_")
 */
class AppController extends AbstractController
{
    /**
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param PostRepository         $postRepository
     *
     * @return Response
     *
     * @Route("/home", name="home")
     */
    public function home(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository): Response
    {
        $form = $this->createForm(PostType::class, $post = new Post());
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($user);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_idea_home');
        }

        return $this->render('home.html.twig', [
            'form' => $form->createView(),
            'posts' => $postRepository->findRecentByUser($user)
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/map", name="map")
     */
    public function map(): Response
    {
        return $this->render('map.html.twig');
    }

    /**
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param CommentRepository      $commentRepository
     *
     * @return Response
     *
     * @Route("/forum", name="forum")
     */
    public function forum(Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {
        $form = $this->createForm(CommentType::class, $comment = new Comment());
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_idea_forum');
        }

        return $this->render('forum.html.twig', [
            'form' => $form->createView(),
            'comments' => $commentRepository->findRecent()
        ]);
    }

    /**
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param PointFileReader        $fileReader
     *
     * @return Response
     *
     * @Route("/import", name="import")
     */
    public function import(Request $request, EntityManagerInterface $entityManager, PointFileReader $fileReader): Response
    {
        $pointForm = $this->createForm(PointType::class, $point = new Point());
        $pointForm->handleRequest($request);

        if ($pointForm->isSubmitted() && $pointForm->isValid()) {
            $point->setUser($this->getUser());

            $entityManager->persist($point);
            $entityManager->flush();

            $this->addFlash('success', 'Point importé avec succés.');

            return $this->redirectToRoute('app_idea_map');
        }

        $importForm = $this->createForm(ImportPointsType::class, $importRequest = new ImportPoints());
        $importForm->handleRequest($request);

        if ($importForm->isSubmitted() && $importForm->isValid()) {
            // Parse the csv file
            // and create points from file data
            $rows = $fileReader->read($importRequest->getFile());

            foreach ($rows as $row) {
                $point = new Point();
                $point->setUser($this->getUser());
                $point->setIdentifier($row['identifier']);
                $point->setX((float)$row['x']);
                $point->setY((float)$row['y']);
                $point->setZ((float)$row['z']);

                $entityManager->persist($point);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Points importé avec succés.');

            return $this->redirectToRoute('app_idea_map');
        }

        return $this->render('import.html.twig', [
            'point_form' => $pointForm->createView(),
            'import_form' => $importForm->createView()
        ]);
    }

    /**
     * @param Request         $request
     * @param PointRepository $pointRepository
     * @param PointFileWriter $fileWriter
     *
     * @return Response
     *
     * @Route("/export", name="export")
     */
    public function export(Request $request, PointRepository $pointRepository, PointFileWriter $fileWriter): Response
    {
        $exportForm = $this->createForm(ExportType::class, $exportRequest = new ExportPoints());
        $exportForm->handleRequest($request);

        if ($exportForm->isSubmitted() && $exportForm->isValid()) {
            $response = new Response($fileWriter->write($exportRequest->getPoints()));
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="points.csv"');

            return $response;
        }

        return $this->render('export.html.twig', [
            'points' => $pointRepository->findRecent(),
            'export_form' => $exportForm->createView()
        ]);
    }

    /**
     * @param Point                  $point
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     *
     * @Route("/points/{point}/edit", name="points_edit")
     */
    public function editPoint(Point $point, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PointType::class, $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Point sauvegardé avec succés.');

            return $this->redirectToRoute('app_idea_export');
        }

        return $this->render('edit_point.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Point                  $point
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     *
     * @Route("/points/{point}/delete", name="points_delete")
     */
    public function deletePoint(Point $point, EntityManagerInterface $entityManager): Response
    {
        $identifier = $point->getIdentifier();

        $entityManager->remove($point);
        $entityManager->flush();

        $this->addFlash('success', "Point [$identifier] supprimé avec succés.");

        return $this->redirectToRoute('app_idea_export');
    }

    /**
     * @param Request                $request
     * @param Point                  $point
     * @param EntityManagerInterface $entityManager
     * @param PointVoteRepository    $voteRepository
     *
     * @return Response
     *
     * @Route("/vote/{point}/up", name="upvote")
     */
    public function upVote(Request $request, Point $point, EntityManagerInterface $entityManager, PointVoteRepository $voteRepository): Response
    {
        // Search a vote from the same user for that point
        $user = $this->getUser();

        if (null === $vote = $voteRepository->findOneByUser($user, $point)) {
            $vote = new PointVote();
        }

        $vote->doUpVote($user, $point);

        if (null === $vote->getId()) {
            $entityManager->persist($vote);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Merci pour votre vote.');

        return $this->redirect(
            $request->headers->get('referer')
        );
    }

    /**
     * @param Request                $request
     * @param Point                  $point
     * @param EntityManagerInterface $entityManager
     * @param PointVoteRepository    $voteRepository
     *
     * @return Response
     *
     * @Route("/vote/{point}/down", name="downvote")
     */
    public function downVote(Request $request, Point $point, EntityManagerInterface $entityManager, PointVoteRepository $voteRepository): Response
    {
        // Search a vote from the same user for that point
        $user = $this->getUser();

        if (null === $vote = $voteRepository->findOneByUser($user, $point)) {
            $vote = new PointVote();
        }

        $vote->doDownVote($user, $point);

        if (null === $vote->getId()) {
            $entityManager->persist($vote);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Merci pour votre vote.');

        return $this->redirect(
            $request->headers->get('referer')
        );
    }

    /**
     * @param PointRepository $pointRepository
     *
     * @return Response
     *
     * @Route("/points", name="api_points")
     */
    public function jsonPoints(PointRepository $pointRepository): Response
    {
        $result = [];

        foreach ($pointRepository->findRecent() as $point) {
            $vote = $point->getUserVote($this->getUser());
            $upVoted = false;
            $downVoted = false;
            $voted = false;

            if (null !== $vote) {
                $voted = true;

                if ($vote->isPositive()) {
                    $upVoted = true;
                } else {
                    $downVoted = true;
                }
            }


            $result[] = [
                'id' => $point->getId(),
                'identifier' => $point->getIdentifier(),
                'x' => $point->getX(),
                'y' => $point->getY(),
                'z' => $point->getZ(),
                'reliability' => $point->getReliability(),
                'voted' => $voted,
                'user' => $point->getUser()->getFullName(),
                'up_voted' => $upVoted,
                'down_voted' => $downVoted,
                'can_vote' => $this->getUser() !== $point->getUser()
            ];
        }

        return $this->json($result);
    }
}