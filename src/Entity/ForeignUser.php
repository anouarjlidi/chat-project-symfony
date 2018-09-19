<?php

namespace App\Entity;

use App\Traits\PublicableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ForeignUserRepository")
 */
class ForeignUser
{
    use PublicableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $tempId;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userImage;

    /**
     * @ORM\ManyToMany(targetEntity="ChatRoom", mappedBy="foreignUsers")
     */
    private $chatRooms;

    /**
     * @ORM\ManyToOne(targetEntity="WebSite", inversedBy="foreignUsers")
     */
    private $webSite;

    public function __construct() {
        $this->chatRooms = new ArrayCollection();
    }

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

    /**
     * @param ChatRoom $chatRoom
     */
    public function addChatRooms(ChatRoom $chatRoom)
    {
        $this->chatRooms[] = $chatRoom;
    }

    /**
     * @param ChatRoom $chatRoom
     */
    public function removeChatRooms(ChatRoom $chatRoom)
    {
        $this->chatRooms->removeElement($chatRoom);
    }

    /**
     * @return ArrayCollection
     */
    public function getChatRooms()
    {
        return $this->chatRooms;
    }

    /**
     * @return mixed
     */
    public function getWebSite()
    {
        return $this->webSite;
    }

    /**
     * @param mixed $webSite
     */
    public function setWebSite($webSite): void
    {
        $this->webSite = $webSite;
    }

    /**
     * @return mixed
     */
    public function getTempId()
    {
        return $this->tempId;
    }

    /**
     * @param mixed $tempId
     */
    public function setTempId($tempId): void
    {
        $this->tempId = $tempId;
    }
}
