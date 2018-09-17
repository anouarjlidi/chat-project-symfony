<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserWebSiteRepository")
 */
class ForeignUserWebSite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userImage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserImage(): ?string
    {
        return $this->userImage;
    }

    public function setUserImage(?string $userImage): self
    {
        $this->userImage = $userImage;

        return $this;
    }
}
