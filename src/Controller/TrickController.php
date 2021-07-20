<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        return $this->render('trick/_Trick.html.twig', [
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
        // dd($form);
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
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('sucess', 'Trick successfully updated !');
            return $this->redirectToRoute('trick_index', ["id" => $trick->getId()], Response::HTTP_SEE_OTHER);
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
}