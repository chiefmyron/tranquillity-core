<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\Person;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\User;

class RouteLoader
{
    /**
     * Defines HTTP routes to be handled by the application
     *
     * Note to future self - DON'T TRY TO GET CLEVER WITH AUTO-GENERATING ROUTES
     * See: https://phil.tech/php/2013/07/23/beware-the-route-to-evil/
     * "routes.php is documentation"
     *
     * @param App $app
     * @return void
     */
    public static function load(App $app)
    {
        // Version 1 API routes (unauthenticated)
        $app->get('/', function ($request, $response, array $args) {
            echo "hello world!";
        });
        //$app->post('/v1/auth/token', AuthController::class.':token');

        // Version 1 API route group (authenticated)
        $routeGroup = $app->group('/v1', function (RouteCollectorProxy $group) {
            // Tag resource
            // $group->get('/tags', TagController::class.':list')->setName('tag-list');
            // $group->post('/tags', TagController::class.':create');
            // $group->get('/tags/{id}', TagController::class.':show')->setName('tag-detail');
            // $group->patch('/tags/{id}', TagController::class.':update');
            // $group->delete('/tags/{id}', TagController::class.':delete');
            // $group->get('/tags/{id}/{resource}', TagController::class.':showRelated')->setName('tag-related');
            // $group->get('/tags/{id}/relationships/{resource}', TagController::class.':showRelationship')->setName('tag-relationships');
            // $group->post('/tags/{id}/relationships/{resource}', TagController::class.':addRelationship');
            // $group->patch('/tags/{id}/relationships/{resource}', TagController::class.':updateRelationship');
            // $group->delete('/tags/{id}/relationships/{resource}', TagController::class.':deleteRelationship');

            // // User resource
            $group->get('/users', User\ListUsersAction::class)->setName('user-list')->setArgument('auth-scope', 'users:read');
            $group->post('/users', User\CreateUserAction::class)->setArgument('auth-scope', 'users:write');
            $group->get('/users/{id}', Person\ViewPersonAction::class)->setName('user-detail')->setArgument('auth-scope', 'users:read');
            // $group->patch('/users/{id}', UserController::class.':update')->setArgument('auth-scope', 'users:write');
            // $group->delete('/users/{id}', UserController::class.':delete')->setArgument('auth-scope', 'users:write');
            // $group->get('/users/{id}/{resource}', UserController::class.':showRelated')->setName('user-related')->setArgument('auth-scope', 'users:write');;
            // $group->get('/users/{id}/relationships/{resource}', UserController::class.':showRelationship')->setName('user-relationships')->setArgument('auth-scope', 'users:write');;
            // $group->post('/users/{id}/relationships/{resource}', UserController::class.':addRelationship')->setArgument('auth-scope', 'users:write');
            // $group->patch('/users/{id}/relationships/{resource}', UserController::class.':updateRelationship')->setArgument('auth-scope', 'users:write');
            // $group->delete('/users/{id}/relationships/{resource}', UserController::class.':deleteRelationship')->setArgument('auth-scope', 'users:write');

            // People resource
            $group->get('/people', Person\ListPeopleAction::class)->setName('person-list');
            $group->post('/people', Person\CreatePersonAction::class);
            $group->get('/people/{id}', Person\ViewPersonAction::class)->setName('person-detail');
            $group->patch('/people/{id}', Person\UpdatePersonAction::class);
            /*$group->delete('/people/{id}', PersonController::class . ':delete');
            $group->get('/people/{id}/{resource}', PersonController::class . ':showRelated')->setName('person-related');
            $group->get('/people/{id}/relationships/{resource}', PersonController::class . ':showRelationship')->setName('person-relationships');
            $group->post('/people/{id}/relationships/{resource}', PersonController::class . ':addRelationship');
            $group->patch('/people/{id}/relationships/{resource}', PersonController::class . ':updateRelationship');
            $group->delete('/people/{id}/relationships/{resource}', PersonController::class . ':deleteRelationship');*/

            // // Accounts resource
            // $group->get('/accounts', AccountController::class.':list')->setName('accounts-list');
            // $group->post('/accounts', AccountController::class.':create');
            // $group->get('/accounts/{id}', AccountController::class.':show');
            // $group->patch('/accounts/{id}', AccountController::class.':update');
            // $group->delete('/accounts/{id}', AccountController::class.':delete');
        });

        // Version 1 API route group (authenticated) middleware
        $routeMiddleware = [
            //AuthenticationMiddleware::class,
            //JsonApiRequestValidatorMiddleware::class
        ];
        foreach ($routeMiddleware as $middleware) {
            $routeGroup->add($middleware);
        }
    }
}
