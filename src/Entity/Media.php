<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Media
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="medias", cascade={"persist"})
     * @ORM\JoinColumn(name="trick_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $trick;

    /**
     * @ORM\Column(type="string")
     */
    private $extension;

    private $file;
    private $tempFilename;

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function PostUpload(){
        if($this->getFile()!=null){
            $this->getFile()->move($this->getUploadRootDir(),$this->tempFilename.'.'.$this->getExtension());
        }
    }

    public function getUploadDir()
    {
        return 'uploads/img';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../public/'.$this->getUploadDir();
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->getIdentif();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (file_exists($this->tempFilename)) {
            unlink($this->tempFilename);
        }
    }


    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        if(!preg_match("#jpeg$|jpg$#",$file->guessExtension())){
            return null;
        }
        $this->file = $file;
        $this->setExtension($this->file->guessExtension());
        $this->tempFilename = uniqid();
        $this->setIdentif($this->tempFilename);

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdentif(): ?string
    {
        return $this->identif;
    }

    public function setIdentif(string $identif): self
    {
        $this->identif = $identif;

        return $this;
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

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getUrl()
    {
        return 'uploads/img/'.$this->getIdentif().'.'.$this->getExtension();
    }
}