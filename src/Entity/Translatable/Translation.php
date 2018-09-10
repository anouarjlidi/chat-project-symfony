<?php

namespace App\Entity\Translatable;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping\Entity;
use Gedmo\Translatable\Entity\Translation as MainTranslation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Gedmo\Translatable\Entity\Translation
 *
 * @Table(
 *         name="ext_translations",
 *         options={"row_format":"DYNAMIC"},
 *         indexes={@Index(name="translations_lookup_idx", columns={
 *             "locale", "object_class", "foreign_key"
 *         })},
 *         uniqueConstraints={@UniqueConstraint(name="lookup_unique_idx", columns={
 *             "locale", "object_class", "field", "foreign_key"
 *         })}
 * )
 * @Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 */
class Translation extends MainTranslation
{
    /**
     * @var string $objectClass
     *
     * @ORM\Column(name="object_class", type="string", length=191)
     */
    protected $objectClass;
}
