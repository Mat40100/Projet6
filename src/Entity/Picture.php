<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 */
class Picture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $identif;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $alt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="picture")
     */
    private $user;

    /**
     * @Assert\Regex(
     *     pattern="/^jpg|jpeg|png/",
     *     message="Le format réquis est jpeg, jpg, png"
     * )
     * @ORM\Column(type="string")
     */
    private $extension;

    /**
     * @Assert\NotNull()
     */
    private $file;

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        $this->setExtension($this->file->guessExtension());
        $this->setIdentif(md5(uniqid()).'.'.$this->getExtension());

        $this->file->move(__DIR__.'/../../public/uploads/pic', $this->getIdentif());
    }

    /**
     * @return mixed
     */
    public function getIdentif()
    {
        return $this->identif;
    }

    /**
     * @param mixed $identif
     */
    public function setIdentif($identif): void
    {
        $this->identif = $identif;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $extension
     */
    public function setExtension($extension): void
    {
        $this->extension = $extension;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getUrl()
    {
        return 'uploads/pic/'.$this->getIdentif();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDefault()
    {
        return 'uploads/pic/default-user.png';
    }
}
