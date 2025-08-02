<?php

namespace App\Controller\Admin;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Service\movieAPI;
use App\Repository\MovieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 */
class MovieController extends AbstractController {


   /**
    * @Route("/films", name="movie_list")
    *
    */
   public function list (MovieRepository $movieRepository, Request $request, PaginatorInterface $paginator) {

    $moviesData = $movieRepository->findBy([], ['id' => 'desc']);

    $movies = $paginator->paginate(
        $moviesData, 
        $request->query->getInt('page', 1),
        10
    );

      return $this->render('admin/movie/list.html.twig', [
         'movies' => $movies
      ]);

   }

      /**
     * @Route("/film/new", name="movie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, MovieRepository $movieRepository, movieAPI $movieAPI, SluggerInterface $slug): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // get the title input value 
            $details = $request->request->all();
            $title = $details['movie']['title'];

            //slug the title
            $slugTitle = $slug->slug($title);

            // use movieAPI service to get data
            $result = $movieAPI->fetchMovie($slugTitle);

            $movieTitle = $result['results'][0]['title'];
            $moviePoster = $result['results'][0]['poster_path'];
            $movieSynopsis = $result['results'][0]['overview'];

            // set the different entity field with api data before flush
            $movie->setTitle($movieTitle);
            $movie->setPoster('https://image.tmdb.org/t/p/w500' . $moviePoster);
            $movie->setSynopsis($movieSynopsis);
            $movie->setSlug($slug->slug($movieTitle));

            $movieRepository->add($movie);
            return $this->redirectToRoute('movie_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/film/{id}/edit", name="movie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Movie $movie, MovieRepository $movieRepository): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movieRepository->add($movie);
            return $this->redirectToRoute('movie_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/film/{id}", name="movie_delete", methods={"POST"})
     */
    public function delete(Request $request, Movie $movie, MovieRepository $movieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $movieRepository->remove($movie);
        }

        return $this->redirectToRoute('movie_list', [], Response::HTTP_SEE_OTHER);
    }
}