<?php

namespace PokemonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PokemonBundle:Default:index.html.twig');
    }
}
