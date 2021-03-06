<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Person;

use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Event\Person\PersonCreated;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\DomainEntity;
use Tranquillity\Domain\Validation\Notification;

class Person extends DomainEntity
{
    private PersonId $id;
    private string $firstName;
    private string $lastName;
    private string $jobTitle;
    private string $emailAddress;

    /**
     * Constructor
     *
     * @param PersonId  $id           Entity identifier
     * @param string   $firstName    Person first name
     * @param string   $lastName     Person last name
     * @param string   $jobTitle     Job title
     * @param string   $emailAddress Email address
     */
    public function __construct(
        PersonId $id,
        string $firstName,
        string $lastName,
        string $jobTitle,
        string $emailAddress
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->jobTitle = $jobTitle;
        $this->emailAddress = $emailAddress;

        // Ensure entity is valid after creation
        $errors = $this->validate();
        if ($errors->hasErrors() === true) {
            throw new ValidationException(
                "Validation errors occurred while creating instance of " . self::class,
                $errors->getErrors(),
                422
            );
        }

        // Publish person creation
        DomainEventPublisher::instance()->publish(
            new PersonCreated($this->id)
        );
    }

    // ****************************************************
    // Internal setter functions
    // ****************************************************

    private function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    private function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    private function setJobTitle(string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;
        return $this;
    }

    private function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;
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
        return EntityTypeEnum::PERSON;
    }

    public function id(): PersonId
    {
        return $this->id;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function jobTitle(): string
    {
        return $this->jobTitle;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }

    // ****************************************************
    // Public changer functions
    // ****************************************************

    public function changeFirstName(string $firstName): self
    {
        return $this->setFirstName($firstName);
    }

    public function changeLastName(string $lastName): self
    {
        return $this->setLastName($lastName);
    }

    public function changeJobTitle(string $jobTitle): self
    {
        return $this->setJobTitle($jobTitle);
    }

    public function changeEmailAddress(string $emailAddress): self
    {
        return $this->setEmailAddress($emailAddress);
    }

    /**
     * Validate the Person entity
     *
     * @return Notification
     */
    public function validate(): Notification
    {
        $notification = new Notification();

        // Check mandatory fields
        $mandatoryFields = ['firstName', 'lastName', 'emailAddress'];
        foreach ($mandatoryFields as $fieldName) {
            if (trim($this->$fieldName) == '') {
                $notification->addItem(ErrorCodeEnum::FIELD_VALIDATION_MANDATORY_VALUE_MISSING, "Mandatory field '{$fieldName}' has not been provided", 'person', $fieldName);
            }
        }

        // Validate email address
        if (filter_var($this->emailAddress, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE) === false) {
            $notification->addItem(ErrorCodeEnum::FIELD_VALIDATION_EMAIL_FORMAT, "Email address '{$this->emailAddress}' is not valid", 'person', 'emailAddress');
        }

        return $notification;
    }
}
