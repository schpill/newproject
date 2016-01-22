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

    defined('SITE_NAME')        || define('SITE_NAME', getenv('SITE_NAME')              ? getenv('SITE_NAME')       : 'site');
    defined('APPLICATION_ENV')  || define('APPLICATION_ENV', getenv('APPLICATION_ENV')  ? getenv('APPLICATION_ENV') : 'development');

    define('STORAGE_PATH', session_save_path());
    define('APPLICATION_PATH', realpath(__DIR__));
    define('CLI', false);
    define('VENDORS_PATH', realpath(__DIR__ . '/vendor'));
    define('VENDOR_PATH', realpath(__DIR__ . '/vendor'));
    define('CACHE_PATH', session_save_path() . '/cache');

    require_once VENDOR_PATH    . '/autoload.php';
    require_once VENDOR_PATH    . '/schpill/standalone/init.php';
    require_once __DIR__        . '/helpers.php';

    class Bootstrap
    {
        public function __construct()
        {
            register_shutdown_function([&$this, 'finish']);

            Timer::start();

            lib('app');

            forever();

            $this->storage_dir = STORAGE_PATH;

            if (!is_writable(STORAGE_PATH)) {
                die('Please give 0777 right to ' . STORAGE_PATH);
            }

            if (!is_dir(CACHE_PATH)) {
                File::mkdir(CACHE_PATH);
            }

            $this->dir = __DIR__;

            Config::set('app.module.dir',           __DIR__);
            Config::set('mvc.dir',                  __DIR__);
            Config::set('app.module.dirstorage',    $this->dir . DS . 'storage');
            Config::set('app.module.assets',        $this->dir . DS . 'assets');
            Config::set('app.module.config',        $this->dir . DS . 'config');
            Config::set('dir.raw.store',            $this->storage_dir . DS . 'db');
            Config::set('dir.ardb.store',           $this->storage_dir . DS . 'db');
            Config::set('dir.ephemere',             $this->storage_dir . DS . 'ephemere');
            Config::set('dir.flight.store',         $this->storage_dir . DS . 'flight');
            Config::set('dir.flat.store',           $this->storage_dir . DS . 'flat');
            Config::set('dir.cache.store',          $this->storage_dir . DS . 'cache');
            Config::set('dir.nosql.store',          $this->storage_dir . DS . 'nosql');
            Config::set('dir.module.logs',          $this->storage_dir . DS . 'logs');

            path('module',  $this->dir);
            path('store',   $this->storage_dir);
            path('config',  Config::get('app.module.config'));
            path('cache',   CACHE_PATH);

            loaderProject('entity');
            loaderProject('system');

            Alias::facade('DB',     'EntityProject', 'Thin');
            Alias::facade('System', 'SystemProject', 'Thin');

            System::Db()->firstOrCreate(['name' => SITE_NAME]);

            require_once path('config') . DS . 'application.php';

            $this->router();
        }

        public function __set($k, $v)
        {
            $this->$k = $v;

            return $this;
        }

        public function __get($k)
        {
            return isset($this->$k) ? $this->$k : null;
        }

        public function __isset($k)
        {
            return isset($this->$k);
        }

        public function __unset($k)
        {
            unset($this->$k);
        }

        public function assetAged($asset)
        {
            $file = __DIR__ . $asset;

            if (file_exists($file)) {
                return sha1(filemtime($file) . $file);
            }

            return 1;
        }

        public function router()
        {
            libProject('router');
        }

        public function finish()
        {
            $time = Timer::get();
        }
    }

    new Bootstrap;
