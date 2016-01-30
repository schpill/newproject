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

    class ControllerProject
    {
        public $title = '';

        public function url($url)
        {
            return core('request')->setUrl($url);
        }

        public function redirect($url)
        {
            $url = $this->url('/' . $url);
            header("Location: $url");
        }

        public function forward($url)
        {
            $url = $this->url('/' . $url);
            $_SERVER['REQUEST_URI'] = $url;

            new RouterProject();

            exit;
        }
    }
