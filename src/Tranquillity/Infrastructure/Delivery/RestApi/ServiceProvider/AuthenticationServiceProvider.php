<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider;

use DateInterval;
use DI\ContainerBuilder;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use Psr\Container\ContainerInterface;
use Tranquillity\Application\Service\CreateAccessToken\CreateAccessTokenService;
use Tranquillity\Application\Service\CreateAuthorizationCode\CreateAuthorizationCodeService;
use Tranquillity\Application\Service\CreateRefreshToken\CreateRefreshTokenService;
use Tranquillity\Application\Service\DeleteAccessToken\DeleteAccessTokenService;
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
use Tranquillity\Infrastructure\Authentication\OAuth\Repository\AccessTokenRepository as OAuthAccessTokenRepository;
use Tranquillity\Infrastructure\Authentication\OAuth\Repository\AuthorizationCodeRepository as OAuthAuthorizationCodeRepository;
use Tranquillity\Infrastructure\Authentication\OAuth\Repository\ClientRepository as OAuthClientRepository;
use Tranquillity\Infrastructure\Authentication\OAuth\Repository\RefreshTokenRepository as OAuthRefreshTokenRepository;
use Tranquillity\Infrastructure\Authentication\OAuth\Repository\ScopeRepository as OAuthScopeRepository;
use Tranquillity\Infrastructure\Authentication\OAuth\Repository\UserRepository as OAuthUserRepository;
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
            OAuthClientRepository::class => function (ContainerInterface $c): OAuthClientRepository {
                $repository = $c->get(ClientRepository::class);
                $viewService = new FindClientByNameService($repository, new ClientDataTransformer());
                $verifyService = new VerifyClientCredentialsService($repository, $c->get(HashingService::class));

                return new OAuthClientRepository($viewService, $verifyService);
            },
            OAuthUserRepository::class => function (ContainerInterface $c): OAuthUserRepository {
                $repository = $c->get(UserRepository::class);
                $viewService = new FindUserByUsernameService($repository, new UserDataTransformer());
                $verifyService = new VerifyUserCredentialsService($repository, $c->get(HashingService::class));

                return new OAuthUserRepository($viewService, $verifyService);
            },
            OAuthAccessTokenRepository::class => function (ContainerInterface $c): OAuthAccessTokenRepository {
                $tokenRepository = $c->get(AccessTokenRepository::class);
                $clientRepository = $c->get(ClientRepository::class);
                $userRepository = $c->get(UserRepository::class);
                $viewService = new FindAccessTokenByTokenService($tokenRepository, new AccessTokenDataTransformer());
                $createService = new CreateAccessTokenService($tokenRepository, $clientRepository, $userRepository, new AccessTokenDataTransformer());
                $deleteService = new DeleteAccessTokenService($tokenRepository, new EmptyDataTransformer());
                $txnService = $c->get(TransactionalSession::class);

                return new OAuthAccessTokenRepository($viewService, $createService, $deleteService, $txnService);
            },
            OAuthRefreshTokenRepository::class => function (ContainerInterface $c): OAuthRefreshTokenRepository {
                $tokenRepository = $c->get(RefreshTokenRepository::class);
                $clientRepository = $c->get(ClientRepository::class);
                $userRepository = $c->get(UserRepository::class);
                $viewService = new FindRefreshTokenByTokenService($tokenRepository, new RefreshTokenDataTransformer());
                $createService = new CreateRefreshTokenService($tokenRepository, $clientRepository, $userRepository, new RefreshTokenDataTransformer());
                $deleteService = new DeleteRefreshTokenService($tokenRepository, new EmptyDataTransformer());
                $txnService = $c->get(TransactionalSession::class);

                return new OAuthRefreshTokenRepository($viewService, $createService, $deleteService, $txnService);
            },
            OAuthAuthorizationCodeRepository::class => function (ContainerInterface $c): OAuthAuthorizationCodeRepository {
                $codeRepository = $c->get(AuthorizationCodeRepository::class);
                $clientRepository = $c->get(ClientRepository::class);
                $userRepository = $c->get(UserRepository::class);
                $viewService = new FindAuthorizationCodeByCodeService($codeRepository, new AuthorizationCodeDataTransformer());
                $createService = new CreateAuthorizationCodeService($codeRepository, $clientRepository, $userRepository, new AuthorizationCodeDataTransformer());
                $deleteService = new DeleteAuthorizationCodeService($codeRepository, new EmptyDataTransformer());
                $txnService = $c->get(TransactionalSession::class);

                return new OAuthAuthorizationCodeRepository($viewService, $createService, $deleteService, $txnService);
            },
            OAuthScopeRepository::class => function (ContainerInterface $c): OAuthScopeRepository {
                return new OAuthScopeRepository();
            },

            // Register OAuth2 authorisation server with the container
            AuthorizationServer::class => function (ContainerInterface $c) {
                // Get options from config
                $config = $c->get('config')->get('auth');

                // Get OAuth entity repositories
                $clientRepository = $c->get(OAuthClientRepository::class);
                $userRepository = $c->get(OAuthUserRepository::class);
                $accessTokenRepository = $c->get(OAuthAccessTokenRepository::class);
                $refreshTokenRepository = $c->get(OAuthRefreshTokenRepository::class);
                $authorizationCodeRepository = $c->get(OAuthAuthorizationCodeRepository::class);
                $scopeRepository = $c->get(OAuthScopeRepository::class);

                // Get security keys
                $encryptionKey = $config['oauth_encryption_key'];
                $privateKeyPath = realpath($config['oauth_private_key_path']);
                $privateKey = new CryptKey($privateKeyPath, null, false);

                // Create OAuth2 server
                $server = new AuthorizationServer(
                    $clientRepository,
                    $accessTokenRepository,
                    $scopeRepository,
                    $privateKey,
                    $encryptionKey
                );

                // Set token lifetimes
                $accessTokenLifetime = new DateInterval('PT' . ($config['oauth_token_access_lifetime'] ?? 3600) . 'S');
                $refreshTokenLifetime = new DateInterval('PT' . ($config['oauth_token_refresh_lifetime'] ?? 30) . 'S');
                $authCodeLifetime = new DateInterval('PT' . ($config['oauth_auth_code_lifetime'] ?? 600) . 'S');

                // Enable client credentials grant (for trusted 3rd party applications)
                $grantClientCredentials = new ClientCredentialsGrant();
                $server->enableGrantType(
                    $grantClientCredentials,
                    $accessTokenLifetime
                );

                // Enable password credentials grant (only for trusted 1st party server applications)
                $grantPasswordCredentials = new PasswordGrant(
                    $userRepository,
                    $refreshTokenRepository
                );
                $grantPasswordCredentials->setRefreshTokenTTL($refreshTokenLifetime);
                $server->enableGrantType(
                    $grantPasswordCredentials,
                    $accessTokenLifetime
                );

                // Enable authorization code grant (for 1st and 3rd party server applications)
                $grantAuthorizationCode = new AuthCodeGrant(
                    $authorizationCodeRepository,
                    $refreshTokenRepository,
                    $authCodeLifetime
                );
                $grantAuthorizationCode->setRefreshTokenTTL($refreshTokenLifetime);
                $server->enableGrantType(
                    $grantAuthorizationCode,
                    $accessTokenLifetime
                );

                // Enable refresh token grant
                $grantRefreshToken = new RefreshTokenGrant(
                    $refreshTokenRepository
                );
                $grantRefreshToken->setRefreshTokenTTL($refreshTokenLifetime);
                $server->enableGrantType(
                    $grantRefreshToken,
                    $accessTokenLifetime
                );

                // Return configured authorisation server
                return $server;
            },

            // Register OAuth2 authorisation server with the container
            ResourceServer::class => function (ContainerInterface $c): ResourceServer {
                // Get options from config
                $config = $c->get('config')->get('auth');

                // Get OAuth entity repositories
                $accessTokenRepository = $c->get(OAuthAccessTokenRepository::class);

                // Get security keys
                $publicKeyPath = realpath($config['oauth_public_key_path']);
                $publicKey = new CryptKey($publicKeyPath, null, false);

                // Return configured resource server
                return new ResourceServer($accessTokenRepository, $publicKey);
            }
        ]);
    }
}
