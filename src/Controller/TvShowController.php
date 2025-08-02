<?php

namespace App\Controller;

use App\Entity\TvShow;
use App\Form\TvShowType;
use App\Repository\CategoryRepository;
use App\Repository\TvShowRepository;
use App\Service\movieAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/series")
 */
class TvShowController extends AbstractController
{
    /**
     * @Route("/", name="tv_show", methods={"GET"})
     */
    public function index(TvShowRepository $tvShowRepository, CategoryRepository $categoryRepository): Response
    {

        return $this->render('tv_show/index.html.twig', [
            'tvShows' => $tvShowRepository->findAll(),
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}", name="tv_show_show", methods={"GET"})
     */
    public function show(TvShow $tvShow, movieAPI $movieAPI): Response
    {
       $title = $tvShow->getTitle();
       $seasonsDetails = $movieAPI->fetchSeasons($title);

        return $this->render('tv_show/show.html.twig', [
            'tvShow' => $tvShow,
            'seasons' => $seasonsDetails
        ]);
    }
    
    /**
     * @Route("/categorie/{category}", name="tvshow_by_category", methods={"GET"})
     */
    public function tvShowByCategory(string $category, TvShowRepository $tvShowRepository): Response
    {
       $tvShows = $tvShowRepository->findAllByCategory($category);

        return $this->render('tv_show/byCategory.html.twig', [
            'tvShows' => $tvShows,
        ]);
    }
    
    
}
