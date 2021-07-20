<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'trick_first' => $trickRepository->findBy([], ['id' => 'DESC'], 10, 0),
            'trick_last' => $trickRepository->findBy([], ['id' => 'DESC'], 50, 10),
        ]);
    }
}