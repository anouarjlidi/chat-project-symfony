<?php

namespace App\Entity;

use App\Traits\PublicableTrait;
use App\Traits\SoftdeleteableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WebSiteRepository")
 */
class WebSite
{
    use TimestampableTrait;
    use SoftdeleteableTrait;
    use PublicableTrait;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isOnline;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasAdminChat;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasPrivateChat;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWordpress;

    /**
     * @ORM\OneToMany(targetEntity="ChatRoom", mappedBy="webSite", cascade={"persist"})
     */
    private $chatRooms;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="webSites")
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
     * @ORM\Column(type="boolean")
     */
    private $installed;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sourceCode;

    /**
     * @ORM\Column(type="text")
     */
    private $cssAdminChat;

    /**
     * @ORM\Column(type="text")
     */
    private $templateAdminChat;

    /**
     * WebSite constructor.
     */
    public function __construct()
    {
        $this->chatRooms = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->installed = false;
        $this->cssAdminChat = "";
        $this->templateAdminChat = "";
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
    public function getHasAdminChat(): ?bool
    {
        return $this->hasAdminChat;
    }

    /**
     * @param bool|null $hasAdminChat
     * @return WebSite
     */
    public function setHasAdminChat(?bool $hasAdminChat): self
    {
        $this->hasAdminChat = $hasAdminChat;

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
     * @param bool|null $hasPrivateChat
     * @return WebSite
     */
    public function setHasPrivateChat(?bool $hasPrivateChat): self
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
        return $this->users;
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

    /**
     * @return mixed
     */
    public function getInstalled()
    {
        return $this->installed;
    }

    /**
     * @param mixed $installed
     */
    public function setInstalled($installed): void
    {
        $this->installed = $installed;
    }

    /**
     * @return mixed
     */
    public function getSourceCode()
    {
        return $this->sourceCode;
    }

    /**
     * @param mixed $sourceCode
     */
    public function setSourceCode($sourceCode): void
    {
        $this->sourceCode = $sourceCode;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function __toString()
    {
        if ($this->name == null OR $this->name == "") {
            return $this->url;
        }
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getCssAdminChat()
    {
        return $this->cssAdminChat;
    }

    /**
     * @param mixed $cssAdminChat
     */
    public function setCssAdminChat($cssAdminChat): void
    {
        $this->cssAdminChat = $cssAdminChat;
    }

    /**
     * @return mixed
     */
    public function getTemplateAdminChat()
    {
        return $this->templateAdminChat;
    }

    /**
     * @param mixed $templateAdminChat
     */
    public function setTemplateAdminChat($templateAdminChat): void
    {
        $this->templateAdminChat = $templateAdminChat;
    }
}
