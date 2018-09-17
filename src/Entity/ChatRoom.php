<?php

namespace App\Entity;

use App\Traits\PublicableTrait;
use App\Traits\SoftdeleteableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChatRoomRepository")
 */
class ChatRoom
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
     * @ORM\Column(type="string", length=10)
     */
    private $chatType;

    /**
     * @ORM\ManyToOne(targetEntity="WebSite", inversedBy="chatRooms")
     */
    private $webSite;

    /**
     * @ORM\OneToOne(targetEntity="ForeignUserWebSite", cascade={"persist"})
     */
    private $userWebSiteForAdmin;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $userWebSiteForAdminUserId;

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
    public function getChatType(): ?string
    {
        return $this->chatType;
    }

    /**
     * @param string $chatType
     * @return ChatRoom
     */
    public function setChatType(string $chatType): self
    {
        $this->chatType = $chatType;

        return $this;
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
    public function getUserWebSiteForAdmin()
    {
        return $this->userWebSiteForAdmin;
    }

    /**
     * @param mixed $userWebSiteForAdmin
     */
    public function setUserWebSiteForAdmin($userWebSiteForAdmin): void
    {
        $this->userWebSiteForAdmin = $userWebSiteForAdmin;
    }

    /**
     * @return mixed
     */
    public function getUserWebSiteForAdminUserId()
    {
        return $this->userWebSiteForAdminUserId;
    }

    /**
     * @param mixed $userWebSiteForAdminUserId
     */
    public function setUserWebSiteForAdminUserId($userWebSiteForAdminUserId): void
    {
        $this->userWebSiteForAdminUserId = $userWebSiteForAdminUserId;
    }
}
