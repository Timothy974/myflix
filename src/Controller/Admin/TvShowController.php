<?php

namespace App\Controller\Admin;

use App\Entity\TvShow;
use App\Form\TvShowType;
use App\Service\movieAPI;
use App\Repository\TvShowRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 */
class TvShowController extends AbstractController {

   /**
    * @Route("/series", name="tvshow_list")
    *
    */
    public function list (TvShowRepository $tvShowRepository, Request $request, PaginatorInterface $paginator) {

      $tvShowData = $tvShowRepository->findBy([], ['id' => 'desc']);
  
      $tvShows = $paginator->paginate(
          $tvShowData, 
          $request->query->getInt('page', 1),
          10
      );
  
        return $this->render('admin/tvshow/list.html.twig', [
           'tvshows' => $tvShows
        ]);
  
     }

      /**
     * @Route("/serie/new", name="tv_show_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TvShowRepository $tvShowRepository, movieAPI $movieAPI, SluggerInterface $slug): Response
    {

        $tvShow = new TvShow();
        $form = $this->createForm(TvShowType::class, $tvShow);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            // get the title input value 
            $details = $request->request->all();
            $title = $details['tv_show']['title'];

            //slug the title
            $slugTitle = $slug->slug($title);

            // use movieAPI service to get data
            $result = $movieAPI->fetchTvshow($slugTitle);
            $tvTitle = $result['results'][0]['original_name'];
            $tvPoster = $result['results'][0]['poster_path'];
            $tvSynopsis = $result['results'][0]['overview'];

            // set the different entity field with api data before flush
            $tvShow->setTitle($tvTitle);
            $tvShow->setPoster('https://image.tmdb.org/t/p/w500' . $tvPoster);
            $tvShow->setSynopsis($tvSynopsis);
            $tvShow->setSlug($slug->slug($tvTitle));

            $tvShowRepository->add($tvShow);
            return $this->redirectToRoute('tvshow_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/tvshow/new.html.twig', [
            'tv_show' => $tvShow,
            'form' => $form,
        ]);
    }

     /**
     * @Route("/serie/{id}", name="tvshow_show", methods={"GET"})
     */
    public function show(TvShow $tvShow, movieAPI $movieAPI): Response
    {
       $title = $tvShow->getTitle();
       $seasonsDetails = $movieAPI->fetchSeasons($title);

        return $this->render('admin/tvshow/show.html.twig', [
            'tvshow' => $tvShow,
            'seasons' => $seasonsDetails
        ]);
    }


    /**
     * @Route("/serie/{id}/edit", name="tv_show_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TvShow $tvShow, TvShowRepository $tvShowRepository): Response
    {
        $form = $this->createForm(TvShowType::class, $tvShow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tvShowRepository->add($tvShow);
            return $this->redirectToRoute('tvshow_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/tvshow/edit.html.twig', [
            'tvShow' => $tvShow,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/serie/{id}", name="tv_show_delete", methods={"POST"})
     */
    public function delete(Request $request, TvShow $tvShow, TvShowRepository $tvShowRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tvShow->getId(), $request->request->get('_token'))) {
            $tvShowRepository->remove($tvShow);
        }

        return $this->redirectToRoute('tv_show', [], Response::HTTP_SEE_OTHER);
    }
}