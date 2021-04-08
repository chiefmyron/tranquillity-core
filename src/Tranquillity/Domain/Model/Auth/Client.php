<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Auth;

use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Event\Auth\ClientCreated;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\DomainEntity;
use Tranquillity\Domain\Validation\Notification;

class Client extends DomainEntity
{
    private ClientId $id;
    private string $name;
    private HashedPassword $password;
    private string $redirectUri;

    /**
     * Constructor
     *
     * @param ClientId $id
     * @param string $name
     * @param HashedPassword $secret
     * @param string $redirectUri
     */
    public function __construct(
        ClientId $id,
        string $name,
        HashedPassword $password,
        string $redirectUri
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->password = $password;
        $this->redirectUri = $redirectUri;

        // Ensure entity is valid after creation
        $errors = $this->validate();
        if ($errors->hasErrors() === true) {
            throw new ValidationException(
                "Validation errors occurred while creating instance of " . self::class,
                $errors->getErrors(),
                422
            );
        }

        // Publish client creation
        DomainEventPublisher::instance()->publish(
            new ClientCreated($this->id)
        );
    }

    // ****************************************************
    // Internal setter functions
    // ****************************************************

    private function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    private function setPassword(HashedPassword $password): self
    {
        $this->password = $password;
        return $this;
    }

    private function setRedirectUri(string $redirectUri): self
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    // ****************************************************
    // Public getter functions
    // ****************************************************

    public function getIdValue(): string
    {
        return $this->id()->id();
    }

    public function getEntityType(): string
    {
        return EntityTypeEnum::OAUTH_CLIENT;
    }

    public function id(): ClientId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function password(): HashedPassword
    {
        return $this->password;
    }

    public function redirectUri(): string
    {
        return $this->redirectUri;
    }

    // ****************************************************
    // Public changer functions
    // ****************************************************

    public function changeName(string $name): self
    {
        return $this->setName($name);
    }

    public function changePassword(HashedPassword $password): self
    {
        return $this->setPassword($password);
    }

    public function changeRedirectUri(string $redirectUri): self
    {
        return $this->setRedirectUri($redirectUri);
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
        $mandatoryFields = ['name'];
        foreach ($mandatoryFields as $fieldName) {
            if (trim($this->$fieldName) == '') {
                $notification->addItem(ErrorCodeEnum::FIELD_VALIDATION_MANDATORY_VALUE_MISSING, "Mandatory field '{$fieldName}' has not been provided", 'user', $fieldName);
            }
        }

        return $notification;
    }
}
