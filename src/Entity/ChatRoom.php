<?php

namespace App\Entity;

use App\Traits\PublicableTrait;
use App\Traits\SoftdeleteableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\ManyToMany(targetEntity="ForeignUser", inversedBy="chatRooms", cascade={"persist"})
     */
    private $foreignUsers;

    public function __construct()
    {
        $this->foreignUsers = new ArrayCollection();
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
     * @param ForeignUser $foreignUser
     */
    public function addForeignUsers(ForeignUser $foreignUser)
    {
        $this->foreignUsers[] = $foreignUser;
        $foreignUser->addChatRooms($this);
    }

    /**
     * @param ForeignUser $foreignUser
     */
    public function removeForeignUsers(ForeignUser $foreignUser)
    {
        $this->foreignUsers->removeElement($foreignUser);
        $foreignUser->removeChatRooms(null);
    }

    /**
     * @return ArrayCollection
     */
    public function getForeignUsers()
    {
        return $this->foreignUsers;
    }
}
