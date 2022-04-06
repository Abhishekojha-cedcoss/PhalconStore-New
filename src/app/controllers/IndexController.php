<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

/**
 * IndexController class
 */
class IndexController extends Controller
{
    /**
     * indexAction function
     *
     * redirects to the login page
     * @return void
     */
    public function indexAction()
    {

        $response= new Response();
        $response->redirect("login");
        $response->send();


    }
}