<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/video")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/{id}/edit", name="video_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Video $video): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUrl = '';
            $url = $form->get('url')->getData();

            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $youtubeMatch);
            preg_match('%(?:https?://)?(?:www\.)?(?:dai\.ly/|dailymotion\.com(?:/video/|/embed/|/embed/video/))([^^&?/ ]{7})%i', $url, $videoDailymotion);
            if (!empty($youtubeMatch) || !empty($videoDailymotion)) {
                if (!empty($youtubeMatch)) {
                    $newUrl = "https://www.youtube.com/embed/$youtubeMatch[1]";
                } elseif (!empty($videoDailymotion)) {
                    $newUrl = "https://www.dailymotion.com/embed/video/$videoDailymotion[1]";
                }
                $video->setUrl($url);
            }

            $video->setUrl($newUrl);
            $this->getDoctrine()
            ->getManager()
            ->flush();
        
            return $this->redirectToRoute('trick_edit', ["id" => $video->getTrick()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    /**
     * @Route("video/delete/{id}", name="trick_video_delete")
     */
    public function deleteVideo(Video $video)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($video);
        $entityManager->flush();
        $slug = $video->getTrick()->getId();

        return $this->redirectToRoute('trick_edit', ["id" => $video->getTrick()->getId()], Response::HTTP_SEE_OTHER);
    }
}