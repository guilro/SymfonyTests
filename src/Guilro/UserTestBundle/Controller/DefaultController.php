<?php

namespace Guilro\UserTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('GuilroTestBundle:Default:index.html.twig', array('name' => $name));
    }
}
