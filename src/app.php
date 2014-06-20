<?php

use Silex\Application;

use Symfony\Component\HttpFoundation\Request;

use Mozza\Core\Services as MozzaServices,
    Mozza\Core\Provider as MozzaProvider;

# Here we go

$rootdir = realpath(__DIR__ . '/..');
require_once $rootdir . '/vendor/autoload.php';

###############################################################################
# Initializing the application
###############################################################################

$app = new Application();
$app['version'] = '1.0.0';
$app['rootdir'] = $rootdir;
unset($rootdir); # from now on, we will only use the DI container's version

###############################################################################
# Loading environment
###############################################################################

$app['environment'] = $app->share(function() use ($app) {

    $env = Habitat\Habitat::getAll();

    $envfile = $app['rootdir'] . '/.env';
    if(is_file($app['rootdir'] . '/.env')) {
        $envloader = new MozzaServices\Context\DotEnvFileReaderService();
        $env = array_merge($envloader->read($app['rootdir'] . '/.env'), $env);
    }

    return new MozzaServices\Context\EnvironmentService(
        $env,
        $app['rootdir']
    );
});

# Debug ?
$app['debug'] = (bool)$app['environment']->getEnv('DEBUG');

###############################################################################
# Mounting platform (infrastructure services we rely upon)
###############################################################################

$platformprovider = $app['environment']->getEnv('PLATFORM');
if(!$platformprovider) {
    $platformprovider = 'Mozza\Core\Provider\Platform\ClassicPlatformServiceProvider';
}
$app->register(new $platformprovider());

# We now have:
#
# * an environment
# * a loaded app configuration (config.site)
# * a database connection
# * a persistent storage

###############################################################################
# Building platform independent or platform-abstracted services
###############################################################################

$app->register(new MozzaProvider\LowLevelServiceProvider());

###############################################################################
# Building business logic services
###############################################################################

$app->register(new MozzaProvider\BusinessLogicServiceProvider());

###############################################################################
# Building controller services
###############################################################################

$app->register(new MozzaProvider\ControllerProvider());

###############################################################################
# Handling cache
###############################################################################

$app->before(function(Request $req) use($app) {
    $app['post.cachehandler']->updateCacheIfNeeded();
});

# Serving the app
return $app;