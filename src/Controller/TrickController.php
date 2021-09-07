<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Video;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Form\PictureType;
use App\Repository\TrickRepository;
use App\Repository\PictureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    /**
     * @Route("/", name="trick_index", methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictures = $form->get('picture')->getData();

            foreach ($pictures as $picture) {
                $file = md5(uniqid()) . '.' . $picture->guessExtension();
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $file
                );

                $pic = new Picture();
                $pic->setName($file);
                $pic->setStatut(false);
                $trick->addPicture($pic);
            }

            $videos = new Video();
            $newUrl = "";
            $url = $form->get('video')->getData();

            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $videoYoutube);
            preg_match('%(?:https?://)?(?:www\.)?(?:dai\.ly/|dailymotion\.com(?:/video/|/embed/|/embed/video/))([^^&?/ ]{7})%i', $url, $videoDailymotion);
            if (!empty($videoYoutube) || !empty($videoDailymotion)) {
                if (!empty($videoYoutube)) {
                    $newUrl = "https://www.youtube.com/embed/$videoYoutube[1]";
                } elseif (!empty($videoDailymotion)) {
                    $newUrl = "https://www.dailymotion.com/embed/video/$videoDailymotion[1]";
                }
            }
            $videos->setUrl($newUrl);
            $trick->addVideo($videos);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Trick successfully created');
            return $this->redirectToRoute('trick_show', ["id" => $trick->getId()]);
        }

        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="trick_show")
     */
    public function show(Trick $trick, Request $request): Response
    {
        $comment = new Comment();
        $comment->setTrick($trick);
        $form = $this->createForm(CommentType::class, $comment)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setUser($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('sucess', 'Comment successfully created !');
            return $this->redirectToRoute('trick_show', ["id" => $trick->getId()]);
        }

        $Trickvideos = $trick->getVideos();
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'videos' => $Trickvideos,
            'formComment' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictures = $form->get('picture')->getData();
            foreach ($pictures as $picture) {
                $file = md5(uniqid()) . '.' . $picture->guessExtension();
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $file
                );

                $pic = new Picture();
                $pic->setName($file);
                $pic->setStatut(0);
                $trick->addPicture($pic);
                $trick->setUpdatedAt(new DateTimeImmutable());
            }

            $videos = new Video();
            $newUrl = "";
            $url = $form->get('video')->getData();

            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $youtubeMatch);
            preg_match('%(?:https?://)?(?:www\.)?(?:dai\.ly/|dailymotion\.com(?:/video/|/embed/|/embed/video/))([^^&?/ ]{7})%i', $url, $videoDailymotion);
            if (!empty($youtubeMatch) || !empty($videoDailymotion)) {
                if (!empty($youtubeMatch)) {
                    $newUrl = "https://www.youtube.com/embed/$youtubeMatch[1]";
                } elseif (!empty($videoDailymotion)) {
                    $newUrl = "https://www.dailymotion.com/embed/video/$videoDailymotion[1]";
                }
                $videos->setUrl($newUrl);
                $trick->addVideo($videos);
            }

            $trick->setUpdatedAt(new DateTimeImmutable());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Trick successfully updated !');
            return $this->redirectToRoute('trick_edit', ["id" => $trick->getId()], Response::HTTP_SEE_OTHER);
        }

        $Trickvideos = $trick->getVideos();
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'videos' => $Trickvideos
        ]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"POST"})
     */
    public function delete(Request $request, Trick $trick): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
            
            $this->addFlash('info', 'Trick successfully deleted');
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete/picture/{id}", name="tricks_delete_picture", methods={"DELETE"})
     */
    public function deletePicture(Picture $picture, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])) {
            // On récupère le nom de l'image
            $nom = $picture->getName();
            // On supprime le fichier
            unlink($this->getParameter('pictures_directory') . '/' . $nom);

            // On supprime l'entrée de la base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();
            
            $this->addFlash('info', 'Picture successfully deleted');

            // On répond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
