<?php

namespace App\Service;

use App\Entity\TvShow;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class movieAPI {

   private $apiKey = "6eb5f8ed1c2cfe9dfa26b1168ff86684";
   private $apiUrl = "https://api.themoviedb.org/3/search/";
   private $client;

   public function __construct(HttpClientInterface $client)
   {
      $this->client = $client;
   }

   public function fetchTvshow ($title) {

      $query = $this->apiUrl . 'tv?api_key=' . $this->apiKey . '&language=fr-FR&query=' . $title;
      $response = $this->client->request('GET', $query);

      return $response->toArray();
   }

   public function fetchMovie ($title) {

      $query = $this->apiUrl . 'movie?api_key=' . $this->apiKey . '&language=fr-FR&query=' . $title;
      $response = $this->client->request('GET', $query);

      return $response->toArray();
   }

   public function fetchSeasons ($title) {

      // get tvShow id
      $getTvShowId = $this->fetchTvshow($title);
      $tvShowId = $getTvShowId['results'][0]['id'];
    
      // get number of season
      $query = 'https://api.themoviedb.org/3/tv/' . $tvShowId . '?api_key=6eb5f8ed1c2cfe9dfa26b1168ff86684&language=fr-FR';
      $response = $this->client->request('GET', $query)->toArray();
      $seasonNumber = $response['number_of_seasons'];
     
      // make a query to get all the seasons details
      for($i = 1 ; $i <= $seasonNumber ; $i++) {
         
         $q = 'https://api.themoviedb.org/3/tv/' . $tvShowId . '/season/' . $i . '?api_key=6eb5f8ed1c2cfe9dfa26b1168ff86684&language=fr-FR';
         $r = $this->client->request('GET', $q)->toArray();

         $seasons[] = $r;
      }
      
      return $seasons;
   }


}