<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider;

use DI\ContainerBuilder;
use OAuth2\Server;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\UserCredentials;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\RefreshToken;
use OAuth2\Scope;
use Psr\Container\ContainerInterface;
use Tranquillity\Application\Service\CreateAccessToken\CreateAccessTokenService;
use Tranquillity\Application\Service\FindAccessTokenByToken\FindAccessTokenByTokenService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Application\Service\FindClientByName\FindClientByNameService;
use Tranquillity\Application\Service\FindUserByUsername\FindUserByUsernameService;
use Tranquillity\Domain\Model\Auth\AccessTokenRepository;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Tranquillity\Domain\Model\Auth\UserRepository;
use Tranquillity\Domain\Service\Auth\VerifyClientCredentialsService;
use Tranquillity\Domain\Service\Auth\VerifyUserCredentialsService;
use Tranquillity\Domain\Service\Auth\HashingService;
use Tranquillity\Infrastructure\Authentication\OAuth\AccessTokenProvider;
use Tranquillity\Infrastructure\Authentication\OAuth\ClientProvider;
use Tranquillity\Infrastructure\Authentication\OAuth\UserProvider;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth\AccessTokenDataTransformer;
use Tranquillity\Infrastructure\Domain\Service\Auth\NativePhpHashingService;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth\ClientDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth\UserDataTransformer;

class AuthenticationServiceProvider extends AbstractServiceProvider
{
    /**
     * Registers the service with the application container
     *
     * @return void
     */
    public function register(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addDefinitions([
            // Register password hashing and verification service
            HashingService::class => function (ContainerInterface $c) {
                // Get connection and options from config
                $config = $c->get('config');
                $algorithm = $config->get('auth.password_algorithm', PASSWORD_DEFAULT);
                $options = $config->get('auth.password_options', []);

                return new NativePhpHashingService($algorithm, $options);
            },

            // Register providers for OAuth entities
            ClientProvider::class => function (ContainerInterface $c) {
                $repository = $c->get(ClientRepository::class);
                $viewService = new FindClientByNameService($repository, new ClientDataTransformer());
                $verifyService = new VerifyClientCredentialsService($repository, $c->get(HashingService::class));

                return new ClientProvider($viewService, $verifyService);
            },
            UserProvider::class => function (ContainerInterface $c) {
                $repository = $c->get(UserRepository::class);
                $viewService = new FindUserByUsernameService($repository, new UserDataTransformer());
                $verifyService = new VerifyUserCredentialsService($repository, $c->get(HashingService::class));

                return new UserProvider($viewService, $verifyService);
            },
            AccessTokenProvider::class => function (ContainerInterface $c) {
                $tokenRepository = $c->get(AccessTokenRepository::class);
                $clientRepository = $c->get(ClientRepository::class);
                $userRepository = $c->get(UserRepository::class);
                $viewService = new FindAccessTokenByTokenService($tokenRepository, new AccessTokenDataTransformer());
                $createService = new CreateAccessTokenService($tokenRepository, $clientRepository, $userRepository, new AccessTokenDataTransformer());
                $txnService = $c->get(TransactionalSession::class);

                return new AccessTokenProvider($viewService, $createService, $txnService);
            },

            // Register OAuth2 server with the container
            Server::class => function (ContainerInterface $c) {
                // Get entities used to represent OAuth objects
                $clientStorage = $c->get(ClientProvider::class);
                $userStorage = $c->get(UserProvider::class);
                $accessTokenStorage = $c->get(AccessTokenProvider::class);
                /*$refreshTokenStorage = $em->getRepository(RefreshTokenEntity::class);
                $authorisationCodeStorage = $em->getRepository(AuthorisationCodeEntity::class);
                $scopeStorage = $em->getRepository(ScopeEntity::class);*/

                // Create OAuth2 server
                $storage = [
                    'client_credentials' => $clientStorage,
                    'user_credentials'   => $userStorage,
                    'access_token'       => $accessTokenStorage
                    //'refresh_token'      => $refreshTokenStorage,
                    //'authorization_code' => $authorisationCodeStorage
                ];
                $server = new Server($storage, ['auth_code_lifetime' => 30, 'refresh_token_lifetime' => 30]);

                // Create scope storage manager
                //$scope = new Scope($scopeStorage);
                //$server->setScopeUtil($scope);

                // Add grant types
                $server->addGrantType(new ClientCredentials($clientStorage));
                $server->addGrantType(new UserCredentials($userStorage));
                //$server->addGrantType(new AuthorizationCode($authorisationCodeStorage));
                //$server->addGrantType(new RefreshToken($refreshTokenStorage, ['always_issue_new_refresh_token' => true]));

                return $server;
            }
        ]);
    }
}
