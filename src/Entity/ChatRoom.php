<?php

namespace App\Entity;

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

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $chatType;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $needFriend;

    /**
     * @ORM\ManyToOne(targetEntity="WebSite", inversedBy="chatRooms")
     */
    private $website;

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
     * @return bool|null
     */
    public function getNeedFriend(): ?bool
    {
        return $this->needFriend;
    }

    /**
     * @param bool|null $needFriend
     * @return ChatRoom
     */
    public function setNeedFriend(?bool $needFriend): self
    {
        $this->needFriend = $needFriend;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $webSite
     */
    public function setWebsite($webSite): void
    {
        $this->website = $webSite;
    }
}