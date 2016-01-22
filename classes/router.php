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

    class RouterProject
    {
        private $method, $request, $page, $controller, $action, $render;

        public function __construct()
        {
            $this->page     = isAke($_REQUEST, 'page', 'home');
            $this->request  = core('request');
            $this->method   = $this->request->method();

            $routes         = include path('config') . DS . 'routes.php';

            $routes         = isAke($routes, $this->method, []);

            $route          = isAke($routes, $this->page, null);

            if (is_callable($route)) {
                $args = $route();

                if (!is_array($args)) {
                    $args = ['main', $args, true];
                }

                if (count($args) == 2) {
                    $args[] = true;
                }

                $this->controller   = current($args);
                $this->action       = $args[1];
                $this->render       = end($args);
            } else {
                $this->controller   = 'main';
                $this->action       = 'is404';
                $this->render       = true;
            }

            $this->boot();
        }

        private function boot($first = true)
        {
            $controllerFile = path('module') . DS . 'controllers' . DS . $this->controller . '.php';

            if (!is_file($controllerFile) && $first) {
                $this->controller   = 'main';
                $this->action       = 'is404';
                $this->render       = true;
                $this->boot(false);
            }

            if (!is_file($controllerFile) && !$first) {
                throw new Exception('You must define a valid route to process.');
            }

            loaderCore('controller');

            require_once $controllerFile;

            $class = '\\Thin\\' . ucfirst(Inflector::lower($this->controller)) . 'Controller';

            $actions    = get_class_methods($class);
            $father     = get_parent_class($class);

            if ($father == 'Thin\ControllerCore') {
                $a      = $this->action;
                $method = $this->method;

                $action = Inflector::lower($method) . ucfirst(
                    Inflector::camelize(
                        strtolower($a)
                    )
                );

                $controller         = new $class;
                $controller->_name  = $this->controller;
                $controller->action = $a;

                if (in_array('boot', $actions)) {
                    $controller->boot();
                }

                if (in_array($action, $actions)) {
                    $controller->$action();
                } else {
                    $this->controller   = 'main';
                    $this->action       = 'is404';
                    $this->render       = true;
                    $this->boot(false);
                }

                if (in_array('unboot', $actions)) {
                    $controller->unboot();
                }
            }

            if (true === $this->render) {
                $this->render($controller);
            }
        }

        private function render($controller)
        {
            $tpl = path('module') . DS . 'views' . DS . $controller->_name . DS . $controller->action . '.phtml';

            if (File::exists($tpl)) {
                $content = File::read($tpl);

                $content = str_replace(
                    '$this->partial(\'',
                    '\\Thin\\RouterProject::partial($controller, \'' . path('module') . DS . 'views' . DS . 'partials' . DS,
                    $content
                );

                $content = str_replace(
                    '$this->',
                    '$controller->',
                    $content
                );

                $content = str_replace(['%%=', '%%'], ['<?php echo ', '; ?>'], $content);

                $file = path('cache') . DS . sha1($content) . '.display';

                File::put($file, $content);

                ob_start();

                include $file;

                $html = ob_get_contents();

                ob_end_clean();

                File::delete($file);

                echo $html;
            } else {
                echo '<h1>Error 404</h1>';
            }
        }

        public static function partial($controller, $partial)
        {
            if (File::exists($partial)) {
                $content = File::read($partial);

                $content = str_replace(
                    '$this->partial(\'',
                    '\\Thin\\RouterProject::partial($controller, \'' . path('module') . DS . 'views' . DS . 'partials' . DS,
                    $content
                );

                $content = str_replace(
                    '$this->',
                    '$controller->',
                    $content
                );

                $content = str_replace(['%%=', '%%'], ['<?php echo ', '; ?>'], $content);

                $tab        = explode(DS, $partial);
                $last       = str_replace('.phtml', '', array_pop($tab));
                $beforeLast = array_pop($tab);
                $partialKey = "$beforeLast.$last";

                $file = path('cache') . DS . sha1($content) . '.display';

                File::put($file, $content);

                ob_start();

                include $file;

                $html = ob_get_contents();

                ob_end_clean();

                File::delete($file);

                echo $html;
            } else {
                echo '';
            }
        }
    }
