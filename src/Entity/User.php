<?php

namespace App\Entity;

use App\Traits\SoftdeleteableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends BaseUser
{
    use TimestampableTrait;
    use SoftdeleteableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /** @ORM\Column(length=2, nullable=true) */
    private $locale;

    /**
     * @ORM\ManyToMany(targetEntity="WebSite", inversedBy="users", cascade={"persist"})
     */
    private $webSites;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->webSites = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @param WebSite $webSite
     */
    public function addWebSite(WebSite $webSite)
    {
        $this->webSites[] = $webSite;
        $webSite->addUser($this);
    }

    /**
     * @param WebSite $webSite
     */
    public function removeWebSite(WebSite $webSite)
    {
        $this->webSites->removeElement($webSite);
        $webSite->removeUser($this);
    }

    /**
     * @return mixed
     */
    public function getWebSites()
    {
        return $this->chatRooms;
    }
}
