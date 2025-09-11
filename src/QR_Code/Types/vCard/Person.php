<?php

namespace QR_Code\Types\vCard;

use QR_Code\Contracts\VCardItem;

/**
 * Class Person
 *
 * QR Code Generator for PHP is distributed under MIT
 * Copyright (C) 2018 Bruno Vaula Werneck <brunovaulawerneck at gmail dot com>
 */
class Person implements VCardItem
{
    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $org;

    /**
     * @var string
     */
    protected $orgTitle;

    /**
     * Person constructor.
     */
    public function __construct(string $firstName, string $lastName, string $title, string $email, string $org = '', string $orgTitle = '')
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->title = $title;
        $this->email = $email;
        $this->org = $org;
        $this->orgTitle = $orgTitle;
    }

    public function getEmailStr(): string
    {
        return "EMAIL:{$this->email}\n";
    }

    protected function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    protected function getName(): string
    {
        $response = "N:{$this->lastName};{$this->firstName};;";
        if ($this->title) {
            $response .= "{$this->title};";
        }

        return $response;
    }

    /**
     * Gets vCard Item Text
     */
    public function __toString(): string
    {
        $response = "{$this->getName()}\n";
        $response .= "FN:{$this->getFullName()}\n";
        $response .= "ORG:{$this->org}\n";
        $response .= "TITLE:{$this->orgTitle}\n";

        return $response;
    }
}
