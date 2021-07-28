<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Form\PictureType;
use App\Repository\TrickRepository;
use App\Repository\PictureRepository;
use DateTimeImmutable;
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
     * @Route("/list", name="trick_list", methods={"GET"})
     */
    public function TrickList(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/edit.html.twig', [
            'tricks' => $trickRepository->findAll(),
            'trick_first' => $trickRepository->findBy([], ['id' => 'DESC'], 10, 0),
            'trick_last' => $trickRepository->findBy([], ['id' => 'DESC'], 50, 10),
        ]);
    }

    /**
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictures = $form->get('picture')->getData();

            // On boucle sur les images
            foreach ($pictures as $picture) {
                // On génère un nouveau nom de fichier
                $file = md5(uniqid()) . '.' . $picture->guessExtension();

                // On copie le fichier dans le dossier uploads
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $file
                );

                // On crée l'image dans la base de données
                $pic = new Picture();
                $pic->setName($file);
                $trick->addPicture($pic);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('sucess', 'Trick successfully created');
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

            return $this->redirectToRoute('trick_show', ["id" => $trick->getId()]);
        }
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'formComment' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $pictures = $form->get('picture')->getData();

            // On boucle sur les images
            foreach ($pictures as $picture) {
                // On génère un nouveau nom de fichier
                $file = md5(uniqid()) . '.' . $picture->guessExtension();

                // On copie le fichier dans le dossier uploads
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $file
                );

                // On crée l'image dans la base de données
                $pic = new Picture();
                $pic->setName($file);
                $pic->setStatut(0);
                $trick->addPicture($pic);
                $trick->setUpdatedAt(new DateTimeImmutable());
            }
            $trick->setUpdatedAt(new DateTimeImmutable());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('sucess', 'Trick successfully updated !');
            return $this->redirectToRoute('trick_edit', ["id" => $trick->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
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

        return $this->redirectToRoute('trick_index', [], Response::HTTP_SEE_OTHER);
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

            // On répond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}