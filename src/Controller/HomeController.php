<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\TvShowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TvShowRepository $tvShowRepository, MovieRepository $movieRepository): Response
    {
        //$tvShows = $tvShowRepository->findAll();
        $tvShows = $tvShowRepository->findTenLast();
        $movies = $movieRepository->findTenLast();

        return $this->render('home/index.html.twig', [
            'tvShows' => $tvShows,
            'movies' => $movies
        ]);
    }
}
