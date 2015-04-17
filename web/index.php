<?php

use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

// Configuration
try {
    $configuration = new \Netdudes\Branchio\Configuration(__DIR__.'/../config/config.yml');
} catch (\Exception $e) {
    echo "Configuration missing, non existent or invalid.";
    echo '<pre>' . $e->getMessage() . '</pre>';
    exit();
};

// Providers
$app->register(
    new Silex\Provider\TwigServiceProvider(),
    [
        'twig.path' => __DIR__ . '/../src/views',
    ]
);

// Services
$app['git'] = $app->share(
    function () use ($configuration) {
        return new \Netdudes\Branchio\Git(
            $configuration->get('git-directory'),
            $configuration->get('git-remote'),
            $configuration->get('git-private-key')
        );
    }
);
$app['url_builder'] = $app->share(
    function () use ($configuration) {
        return new \Netdudes\Branchio\UrlBuilder(
            $configuration->get('url-pattern')
        );
    }
);
$app['sites'] = $app->share(
    function () use ($configuration) {
        return new \Netdudes\Branchio\Sites(
            $configuration->get('sites-directory')
        );
    }
);

// Routes
$app->get('/', 'Netdudes\\Branchio\\Controller\\Main::listAction');
$app->get('/refresh', 'Netdudes\\Branchio\\Controller\\Main::refreshAction');


// Error handling
$app->error(function (\Exception $e, $code) {
    switch ($code) {
        case 404:
            $message = 'Not Found :(';
            break;
        default:
            $message = $e->getMessage();
    }

    return new Response($message);
});

$app->run();