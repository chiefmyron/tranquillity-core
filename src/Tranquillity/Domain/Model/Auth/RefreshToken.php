<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Auth;

use DateTime;
use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Event\Auth\RefreshTokenCreated;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\DomainEntity;
use Tranquillity\Domain\Model\Auth\UserId;
use Tranquillity\Domain\Validation\Notification;

class RefreshToken extends DomainEntity
{
    private RefreshTokenId $id;
    private string $token;
    private Client $client;
    private ?User $user;
    private DateTime $expires;
    private ?string $scope;

    /**
     * Constructor
     *
     * @param RefreshTokenId $id
     * @param string $token
     * @param Client $client
     * @param User $user
     * @param DateTime $expires
     * @param string|null $scope
     */
    public function __construct(
        RefreshTokenId $id,
        string $token,
        Client $client,
        ?User $user,
        DateTime $expires,
        ?string $scope
    ) {
        $this->id = $id;
        $this->token = $token;
        $this->client = $client;
        $this->user = $user;
        $this->expires = $expires;
        $this->scope = $scope;

        // Ensure entity is valid after creation
        $errors = $this->validate();
        if ($errors->hasErrors() === true) {
            throw new ValidationException(
                "Validation errors occurred while creating instance of " . self::class,
                $errors->getErrors(),
                422
            );
        }

        // Publish refresh token creation
        DomainEventPublisher::instance()->publish(
            new RefreshTokenCreated($this->id)
        );
    }

    // ****************************************************
    // Internal setter functions
    // ****************************************************

    private function setScope(string $scope): self
    {
        $this->scope = $scope;
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
        return EntityTypeEnum::OAUTH_TOKEN_ACCESS;
    }

    public function id(): RefreshTokenId
    {
        return $this->id;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function clientId(): ClientId
    {
        return $this->client->id();
    }

    public function userId(): ?UserId
    {
        return $this->user->id();
    }

    public function expires(): DateTime
    {
        return $this->expires;
    }

    public function scope(): ?string
    {
        return $this->scope;
    }

    // ****************************************************
    // Public changer functions
    // ****************************************************

    public function changeScope(string $scope): self
    {
        return $this->setScope($scope);
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
        $mandatoryFields = ['token'];
        foreach ($mandatoryFields as $fieldName) {
            if (trim($this->$fieldName) == '') {
                $notification->addItem(ErrorCodeEnum::FIELD_VALIDATION_MANDATORY_VALUE_MISSING, "Mandatory field '{$fieldName}' has not been provided", 'user', $fieldName);
            }
        }

        return $notification;
    }
}
