<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\CategoryRepository;
use App\Repository\MovieRepository;
use App\Service\movieAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/films")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/", name="movie_index", methods={"GET"})
     */
    public function index(MovieRepository $movieRepository, CategoryRepository $categoryRepository): Response
    {
        $movies = $movieRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/{id}", name="movie_show", methods={"GET"})
     */
    public function show(Movie $movie): Response
    {
        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/categorie/{category}", name="movie_by_category", methods={"GET"})
     */
    public function movieByCategory(string $category, MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAllByCategory($category);

   

        return $this->render('movie/byCategory.html.twig', [
            'movies' => $movies,
        ]);
    }



    
}
