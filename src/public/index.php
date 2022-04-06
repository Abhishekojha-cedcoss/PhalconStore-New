<?php
session_start();
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Logger\AdapterFactory;
use Phalcon\Logger\LoggerFactory;
use Phalcon\Config\ConfigFactory;
use Phalcon\Http\Response\Cookies;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use App\Components\Locale;
use Phalcon\Cache\Adapter\Memory;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
//URLROOT (Dynamic links)
define('URLROOT', 'http://localhost:8080/');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->register();

$loader->registerNamespaces(
    [
    "App\Components"=>APP_PATH."/components"
    ]
);

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$application = new Application($container);

//................................<Locale Translation>......................................//

$container->set('locale', (new Locale())->getTranslator());

//................................<Locale Translationt>......................................//



//................................<ACL Event>......................................//
$application = new Application($container);
$eventsManager = new EventsManager();


$eventsManager->attach(
    'application:beforeHandleRequest',
    new App\Components\listener($application)
);

$container->set(
    'EventsManager',
    $eventsManager
);

$application->setEventsManager($eventsManager);
//................................<ACL Event>......................................//





//.......................................<Logger>........................................//
$container->set( 
    'logger',
    function() {
        $adapters = [
            "main"  => new \Phalcon\Logger\Adapter\Stream(APP_PATH."/storage/log/main.log")
        ];
        $adapterFactory = new AdapterFactory();
        $loggerFactory  = new LoggerFactory($adapterFactory);
        
        return $loggerFactory->newInstance('prod-logger', $adapters);
    }, 
    true
 );
//.......................................<Logger>........................................//




//.......................................<SESSION>........................................//
$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;
    },
    true
);
//.......................................<SESSION>........................................//



//.......................................<DATABASE>........................................//
$container->set(
    'db',
    function () {
        $config = $this->getConfig();
        return new Mysql(
            [
                'host'     => $config->path('db.host'),
                'username' => $config->path('db.username'),
                'password' => $config->path('db.password'),
                'dbname'   => $config->path('db.dbname'),
                ]
        );
        }
);
//.......................................<DATABSE>........................................//



//.......................................<Config>........................................//
$container->set( 
    'config',
    function() {
    $fileName = APP_PATH.'/etc/config.php';
    $factory  = new ConfigFactory();
    return $factory->newInstance('php', $fileName);
    }, 
    true
 );
//.......................................<Config>........................................//

//....................................<Cache DI start>.........................................//

$container->set(
    'cache',
    function () {
        $serializerFactory = new SerializerFactory();

        $options = [
            'defaultSerializer' => 'Php',
            'lifetime'          => 7200,
            'storageDir'        =>  APP_PATH."/storage/cache"
        ];
        
        $adapter = new Phalcon\Cache\Adapter\Stream($serializerFactory, $options);

        $cache = new Cache($adapter);

        return $cache;
    }
);

//....................................<Cache Di ends>.........................................//

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
