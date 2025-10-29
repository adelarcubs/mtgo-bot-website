<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

/**
 * laminas-router route configuration
 *
 * @see https://docs.laminas.dev/laminas-router/
 *
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

/**
 * Register public routes
 */
$registerPublicRoutes = function (Application $app): void {
    $app->get('/', App\Handler\HomePageHandler::class, 'home');
    $app->get('/our-bots', App\Handler\OurBotsHandler::class, 'our-bots');
    
    // Auth routes
    $app->route('/login', [
        App\Handler\Auth\LoginHandler::class,
    ], ['GET', 'POST'], 'auth.login');
    
    // Cart routes
    $app->route('/cart/upload', [
        App\Handler\Cart\UploadDeckHandler::class,
    ], ['GET', 'POST'], 'cart.upload');
    
    // Registration route
    $app->route('/register', App\Handler\Auth\RegisterHandler::class, ['GET', 'POST'], 'auth.register');
};

/**
 * Register API routes
 */
$registerApiRoutes = function (Application $app): void {
    $app->get('/api/ping', App\Handler\PingHandler::class, 'api.ping');
    $app->get('/api/orders/:id', App\Handler\GetOrderHandler::class, 'api.orders.get');
};

/**
 * Register authenticated routes
 */
$registerAuthenticatedRoutes = function (Application $app): void {
    $app->get('/my-account', App\Handler\MyAccountHandler::class, 'my-account');
    $app->get('/my-rent', App\Handler\MyRentHandler::class, 'my-rent');
    $app->get('/my-collection', App\Handler\MyCollectionHandler::class, 'my-collection');
    $app->get('/logout', App\Handler\Auth\LogoutHandler::class, 'logout');

    $app->get('/cart', [App\Handler\Cart\GetCartHandler::class], 'cart');
};

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) use ($registerPublicRoutes, $registerApiRoutes, $registerAuthenticatedRoutes): void {
    // Register all route groups
    $registerPublicRoutes($app);
    $registerApiRoutes($app);
    $registerAuthenticatedRoutes($app);
};
