<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Auth;

use DateTimeImmutable;
use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Event\Auth\AuthorizationCodeCreated;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\DomainEntity;
use Tranquillity\Domain\Model\Auth\UserId;
use Tranquillity\Domain\Validation\Notification;

class AuthorizationCode extends DomainEntity
{
    private AuthorizationCodeId $id;
    private string $code;
    private Client $client;
    private ?User $user;
    private DateTimeImmutable $expires;
    private string $redirectUri;
    private ?string $scope;

    /**
     * Constructor
     *
     * @param AuthorizationCodeId $id
     * @param string $code
     * @param Client $client
     * @param User $user
     * @param DateTimeImmutable $expires
     * @param array $scopes
     */
    public function __construct(
        AuthorizationCodeId $id,
        string $code,
        Client $client,
        ?User $user,
        DateTimeImmutable $expires,
        string $redirectUri,
        array $scopes
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->client = $client;
        $this->user = $user;
        $this->expires = $expires;
        $this->redirectUri = $redirectUri;
        $this->setScopes($scopes);

        // Ensure entity is valid after creation
        $errors = $this->validate();
        if ($errors->hasErrors() === true) {
            throw new ValidationException(
                "Validation errors occurred while creating instance of " . self::class,
                $errors->getErrors(),
                422
            );
        }

        // Publish authorization code creation
        DomainEventPublisher::instance()->publish(
            new AuthorizationCodeCreated($this->id)
        );
    }

    // ****************************************************
    // Internal setter functions
    // ****************************************************

    private function setScopes(array $scopes): self
    {
        // Convert scopes into a space-delimited string
        if (count($scopes) > 0) {
            $this->scope = join(" ", $scopes);
        } else {
            $this->scope = null;
        }
        return $this;
    }

    // ****************************************************
    // Public getter functions
    // ****************************************************

    public function getIdValue(): string
    {
        return $this->id()->id();
    }

    public function getClientIdValue(): string
    {
        return $this->client->getIdValue();
    }

    public function getClientName(): string
    {
        return $this->client->name();
    }

    public function getUserIdValue(): string
    {
        if (is_null($this->user) == false) {
            return $this->user->getIdValue();
        }
        return '';
    }

    public function getUserUsername(): string
    {
        if (is_null($this->user) == false) {
            return $this->user->username();
        }
        return '';
    }

    public function getEntityType(): string
    {
        return EntityTypeEnum::OAUTH_CODE_AUTHORIZATION;
    }

    public function id(): AuthorizationCodeId
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function clientId(): ClientId
    {
        return $this->client->id();
    }

    public function userId(): ?UserId
    {
        return $this->user->id();
    }

    public function expires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function redirectUri(): string
    {
        return $this->redirectUri();
    }

    public function scopes(): array
    {
        if ($this->scope == null) {
            return [];
        }
        return explode(' ', $this->scope);
    }

    // ****************************************************
    // Public changer functions
    // ****************************************************

    public function changeScope(array $scopes): self
    {
        return $this->setScopes($scopes);
    }

    /**
     * Validate the User entity
     *
     * @return Notification
     */
    public function validate(): Notification
    {
        $notification = new Notification();

        // Check mandatory fields
        $mandatoryFields = ['code'];
        foreach ($mandatoryFields as $fieldName) {
            if (trim($this->$fieldName) == '') {
                $notification->addItem(ErrorCodeEnum::FIELD_VALIDATION_MANDATORY_VALUE_MISSING, "Mandatory field '{$fieldName}' has not been provided", 'user', $fieldName);
            }
        }

        return $notification;
    }
}
