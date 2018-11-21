<?php

namespace App\Controller;

use App\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends Controller
{
    /**
     * Matches / exactly
     *
     * @Route("/", name="news_list")
     */
    public function list()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(News::class)->findAll();

        foreach ($posts as $key => $post) {
            $posts[$key]->link = $this->generateUrl('news_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('news/index.twig',  ['posts' => $posts]  );
    }

    /**
     * Matches /news/*
     *
     * @Route("/news/{slug}", name="news_show")
     */
    public function show(News $post)
    {
        $post->link = $this->generateUrl('news_show', ['slug' => $post->getSlug()]);
        return $this->render('news/post_show.twig', ['post' => $post]);
    }
}