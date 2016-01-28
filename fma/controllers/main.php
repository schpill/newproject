<?php
    /**
    * Thin is a swift Framework for PHP 5.4+
    *
    * @package    Thin
    * @version    1.0
    * @author     Gerald Plusquellec
    * @license    BSD License
    * @copyright  1996 - 2016 Gerald Plusquellec
    * @link       http://github.com/schpill/thin
    */

    namespace Thin;

    class MainController extends ControllerProject
    {
        public function getHome()
        {
            $this->title = 'Accueil';
        }

        public function getTest()
        {
            $this->title = 'Accueil';
        }

        public function getIs404()
        {
            header("HTTP/1.0 404 Not Found");
            $this->title = 'Erreur 404';
        }
    }
