<?php namespace collectiv\controllers;

use collectiv\core\View;

class HomeController extends BaseController {

    /**
     * This route shows our basic index page.
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index() {
        return View::render('home.twig');
    }

    protected function routeName(): string {
        return '';
    }
}