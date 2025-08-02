<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class HomeController extends AbstractController {

   /**
    * @Route("/", name="admin_home")
    *
    */
   public function home () {

      return $this->render('admin/home/home.html.twig');
   }
}
