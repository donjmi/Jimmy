<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/picture")
 */
class PictureController extends AbstractController
{
    /**
     * @Route("/", name="picture_index", methods={"GET"})
     */
    public function index(PictureRepository $pictureRepository): Response
    {
        return $this->render('picture/index.html.twig', [
            'pictures' => $pictureRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="picture_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute('picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="picture_show", methods={"GET"})
     */
    public function show(Picture $picture): Response
    {
        return $this->render('picture/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="picture_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Picture $picture): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="picture_delete", methods={"POST"})
     */
    public function delete(Request $request, Picture $picture): Response
    {
        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('picture_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete/{id}-{mode}", name="picture_reset_main", methods={"GET"})
     */
    public function resetFieldMainImage(Request $request, Picture $picture, String $mode, trick $trick): Response
    {
        $picture->setStatut(false);
        $this->getDoctrine()->getManager()->flush();

        switch ($mode) {
            case 'set':
                $route = 'picture_edit';
                $id = $picture->getId();
                break;

            default:
                $route = 'trick_edit';
                $id = $trick->getId();
                break;
        }

        return $this->redirectToRoute($route, ["id" => $id], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/update/{id}-{mode}", name="picture_set_main", methods={"GET"})
     */
    public function pictureSetMmain(Request $request, Picture $picture, String $mode): Response
    {

        $picture->setStatut(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('trick_edit', ["id" => $picture->getTricks()->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/list/{id}", name="picture_list", methods={"GET"})
     */
    public function pictureList(PictureRepository $pictureRepository, Trick $trick): Response
    {
        return $this->render('picture/index2.html.twig', [
            'pictures' => $pictureRepository->findByTricks($trick),
        ]);
    }
}