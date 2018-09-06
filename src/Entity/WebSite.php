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
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
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
     * @ORM\ManyToMany(targetEntity="User", mappedBy="websites")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $adminUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adminTempUser;

    /**
     * WebSite constructor.
     */
    public function __construct()
    {
        $this->chatRooms = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
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

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->chatRooms;
    }

    /**
     * @return mixed
     */
    public function getAdminUser()
    {
        return $this->adminUser;
    }

    /**
     * @param User $adminUser
     */
    public function setAdminUser(User $adminUser): void
    {
        $this->adminUser = $adminUser;
    }

    /**
     * @return mixed
     */
    public function getAdminTempUser()
    {
        return $this->adminTempUser;
    }

    /**
     * @param mixed $adminTempUser
     */
    public function setAdminTempUser($adminTempUser): void
    {
        $this->adminTempUser = $adminTempUser;
    }
}
