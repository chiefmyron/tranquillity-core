<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Auth;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Locale;
use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Event\Auth\UserCreated;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\Auth\HashedPassword;
use Tranquillity\Domain\Model\DomainEntity;
use Tranquillity\Domain\Validation\Notification;

class User extends DomainEntity
{
    private UserId $id;
    private string $username;
    private HashedPassword $password;
    private string $timezoneCode;
    private string $localeCode;
    private bool $active;
    private DateTimeImmutable $registeredDateTime;

    /**
     * Constructor
     *
     * @param UserId $id
     * @param string $username
     * @param HashedPassword $password
     * @param string $timezoneCode
     * @param string $localeCode
     * @param boolean $active
     */
    public function __construct(
        UserId $id,
        string $username,
        HashedPassword $password,
        string $timezoneCode,
        string $localeCode,
        bool $active = true
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->changePassword($password);
        $this->timezoneCode = $timezoneCode;
        $this->localeCode = $localeCode;
        $this->active = $active;
        $this->registeredDateTime = new DateTimeImmutable();

        // Ensure entity is valid after creation
        $errors = $this->validate();
        if ($errors->hasErrors() === true) {
            throw new ValidationException(
                "Validation errors occurred while creating instance of " . self::class,
                $errors->getErrors(),
                422
            );
        }

        // Publish user creation
        DomainEventPublisher::instance()->publish(
            new UserCreated($this->id)
        );
    }

    // ****************************************************
    // Internal setter functions
    // ****************************************************

    private function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    private function setPassword(HashedPassword $password): self
    {
        $this->password = $password;
        return $this;
    }

    private function setTimezoneCode(string $timezoneCode): self
    {
        $this->timezoneCode = $timezoneCode;
        return $this;
    }

    private function setLocaleCode(string $localeCode): self
    {
        $this->localeCode = $localeCode;
        return $this;
    }

    private function setActive(bool $active): self
    {
        $this->active = $active;
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
        return EntityTypeEnum::USER;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): HashedPassword
    {
        return $this->password;
    }

    public function timezoneCode(): string
    {
        return $this->timezoneCode;
    }

    public function localeCode(): string
    {
        return $this->localeCode;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function registeredDateTime(): DateTimeImmutable
    {
        return $this->registeredDateTime;
    }

    // ****************************************************
    // Public changer functions
    // ****************************************************

    public function changePassword(HashedPassword $password): self
    {
        return $this->setPassword($password);
    }

    public function changeTimezoneCode(string $timezoneCode): self
    {
        return $this->setTimezoneCode($timezoneCode);
    }

    public function changeLocaleCode(string $localeCode): self
    {
        return $this->setLocaleCode($localeCode);
    }

    public function changeActive(bool $active): self
    {
        return $this->setActive($active);
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
        $mandatoryFields = ['username', 'timezoneCode', 'localeCode'];
        foreach ($mandatoryFields as $fieldName) {
            if (trim($this->$fieldName) == '') {
                $notification->addItem(ErrorCodeEnum::FIELD_VALIDATION_MANDATORY_VALUE_MISSING, "Mandatory field '{$fieldName}' has not been provided", 'user', $fieldName);
            }
        }

        // Validate timezone code
        try {
            $tz = new DateTimeZone($this->timezoneCode);
        } catch (Exception $e) {
            $notification->addItem(ErrorCodeEnum::FIELD_VALIDATION_TIMEZONE_INVALID, "Timezone '{$this->timezoneCode}' is not supported", 'user', 'timezoneCode');
        }

        // Validate lang-locale code
        $primary = \Locale::getPrimaryLanguage($this->localeCode);
        $region  = \Locale::getRegion($this->localeCode);

        if ($primary === null || $region === null || Locale::getDisplayLanguage($this->localeCode) == $this->localeCode) {
            $notification->addItem(ErrorCodeEnum::FIELD_VALIDATION_LOCALE_INVALID, "Timezone '{$this->timezoneCode}' is not supported", 'user', 'localeCode');
        }

        return $notification;
    }
}
