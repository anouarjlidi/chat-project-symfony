<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WebSiteRepository")
 */
class WebSite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isOnline;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hadAdminChat;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasPrivateChat;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWordpress;

    /**
     * @ORM\OneToMany(targetEntity="ChatRoom", mappedBy="website", cascade={"persist"})
     */
    private $chatRooms;

    /**
     * WebSite constructor.
     */
    public function __construct()
    {
        $this->chatRooms = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param null|string $url
     * @return WebSite
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    /**
     * @param bool|null $isOnline
     * @return WebSite
     */
    public function setIsOnline(?bool $isOnline): self
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHadAdminChat(): ?bool
    {
        return $this->hadAdminChat;
    }

    /**
     * @param bool $hadAdminChat
     * @return WebSite
     */
    public function setHadAdminChat(bool $hadAdminChat): self
    {
        $this->hadAdminChat = $hadAdminChat;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasPrivateChat(): ?bool
    {
        return $this->hasPrivateChat;
    }

    /**
     * @param bool $hasPrivateChat
     * @return WebSite
     */
    public function setHasPrivateChat(bool $hasPrivateChat): self
    {
        $this->hasPrivateChat = $hasPrivateChat;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsWordpress(): ?bool
    {
        return $this->isWordpress;
    }

    /**
     * @param bool|null $isWordpress
     * @return WebSite
     */
    public function setIsWordpress(?bool $isWordpress): self
    {
        $this->isWordpress = $isWordpress;

        return $this;
    }

    /**
     * @param ChatRoom $chatRoom
     */
    public function addCategory(ChatRoom $chatRoom)
    {
        $this->chatRooms[] = $chatRoom;
        $chatRoom->setWebSite($this);
    }

    /**
     * @param ChatRoom $chatRoom
     */
    public function removeCategory(ChatRoom $chatRoom)
    {
        $this->chatRooms->removeElement($chatRoom);
        $chatRoom->setWebSite(null);
    }

    /**
     * @return ArrayCollection
     */
    public function getChatRooms()
    {
        return $this->chatRooms;
    }
}
