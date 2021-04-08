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
use Tranquillity\Application\Service\CreateAuthorizationCode\CreateAuthorizationCodeService;
use Tranquillity\Application\Service\CreateRefreshToken\CreateRefreshTokenService;
use Tranquillity\Application\Service\DeleteAuthorizationCode\DeleteAuthorizationCodeService;
use Tranquillity\Application\Service\DeleteRefreshToken\DeleteRefreshTokenService;
use Tranquillity\Application\Service\FindAccessTokenByToken\FindAccessTokenByTokenService;
use Tranquillity\Application\Service\FindAuthorizationCodeByCode\FindAuthorizationCodeByCodeService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Application\Service\FindClientByName\FindClientByNameService;
use Tranquillity\Application\Service\FindRefreshTokenByToken\FindRefreshTokenByTokenService;
use Tranquillity\Application\Service\FindUserByUsername\FindUserByUsernameService;
use Tranquillity\Domain\Model\Auth\AccessTokenRepository;
use Tranquillity\Domain\Model\Auth\AuthorizationCodeRepository;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Tranquillity\Domain\Model\Auth\RefreshTokenRepository;
use Tranquillity\Domain\Model\Auth\UserRepository;
use Tranquillity\Domain\Service\Auth\VerifyClientCredentialsService;
use Tranquillity\Domain\Service\Auth\VerifyUserCredentialsService;
use Tranquillity\Domain\Service\Auth\HashingService;
use Tranquillity\Infrastructure\Authentication\OAuth\AccessTokenProvider;
use Tranquillity\Infrastructure\Authentication\OAuth\AuthorizationCodeProvider;
use Tranquillity\Infrastructure\Authentication\OAuth\ClientProvider;
use Tranquillity\Infrastructure\Authentication\OAuth\RefreshTokenProvider;
use Tranquillity\Infrastructure\Authentication\OAuth\UserProvider;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth\AccessTokenDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth\AuthorizationCodeDataTransformer;
use Tranquillity\Infrastructure\Domain\Service\Auth\NativePhpHashingService;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth\ClientDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth\EmptyDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth\RefreshTokenDataTransformer;
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
                // Get options from config
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
            RefreshTokenProvider::class => function (ContainerInterface $c) {
                $tokenRepository = $c->get(RefreshTokenRepository::class);
                $clientRepository = $c->get(ClientRepository::class);
                $userRepository = $c->get(UserRepository::class);
                $viewService = new FindRefreshTokenByTokenService($tokenRepository, new RefreshTokenDataTransformer());
                $createService = new CreateRefreshTokenService($tokenRepository, $clientRepository, $userRepository, new RefreshTokenDataTransformer());
                $deleteService = new DeleteRefreshTokenService($tokenRepository, new EmptyDataTransformer());
                $txnService = $c->get(TransactionalSession::class);

                return new RefreshTokenProvider($viewService, $createService, $deleteService, $txnService);
            },
            AuthorizationCodeProvider::class => function (ContainerInterface $c) {
                $codeRepository = $c->get(AuthorizationCodeRepository::class);
                $clientRepository = $c->get(ClientRepository::class);
                $userRepository = $c->get(UserRepository::class);
                $viewService = new FindAuthorizationCodeByCodeService($codeRepository, new AuthorizationCodeDataTransformer());
                $createService = new CreateAuthorizationCodeService($codeRepository, $clientRepository, $userRepository, new AuthorizationCodeDataTransformer());
                $deleteService = new DeleteAuthorizationCodeService($codeRepository, new EmptyDataTransformer());
                $txnService = $c->get(TransactionalSession::class);

                return new AuthorizationCodeProvider($viewService, $createService, $deleteService, $txnService);
            },

            // Register OAuth2 server with the container
            Server::class => function (ContainerInterface $c) {
                // Get options from config
                $config = $c->get('config');
                $options = [
                    'auth_code_lifetime' => $config->get('auth.oauth_auth_code_lifetime', 30), // 30 seconds
                    'refresh_token_lifetime' => $config->get('auth.oauth_token_refresh_lifetime', 1209600) // 14 days
                ];

                // Get entities used to represent OAuth objects
                $clientStorage = $c->get(ClientProvider::class);
                $userStorage = $c->get(UserProvider::class);
                $accessTokenStorage = $c->get(AccessTokenProvider::class);
                $refreshTokenStorage = $c->get(RefreshTokenProvider::class);
                $authorisationCodeStorage = $c->get(AuthorizationCodeProvider::class);
                /*$scopeStorage = $em->getRepository(ScopeEntity::class);*/

                // Create OAuth2 server
                $storage = [
                    'client_credentials' => $clientStorage,
                    'user_credentials'   => $userStorage,
                    'access_token'       => $accessTokenStorage,
                    'refresh_token'      => $refreshTokenStorage,
                    'authorization_code' => $authorisationCodeStorage
                ];
                $server = new Server($storage, $options);

                // Create scope storage manager
                //$scope = new Scope($scopeStorage);
                //$server->setScopeUtil($scope);

                // Add grant types
                $server->addGrantType(new UserCredentials($userStorage));
                $server->addGrantType(new AuthorizationCode($authorisationCodeStorage));
                $server->addGrantType(
                    new ClientCredentials(
                        $clientStorage,
                        ['allow_credentials_in_request_body' => $config->get('auth.oauth_client_allow_credentials_in_body', true)]
                    )
                );
                $server->addGrantType(
                    new RefreshToken(
                        $refreshTokenStorage,
                        ['always_issue_new_refresh_token' => $config->get('auth.oauth_token_refresh_always_issue_new', true)]
                    )
                );

                return $server;
            }
        ]);
    }
}
