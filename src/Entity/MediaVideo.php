<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaVideoRepository")
 * @ORM\Table(name="media_video")
 */
class MediaVideo
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $identif;

    /**
     * @Assert\Regex(
     *     pattern="#^(http|https)://(www.youtube.com|www.dailymotion.com|vimeo.com)/#",
     *     match=true,
     *     message="L'url doit correspondre à l'url d'une vidéo Youtube, DailyMotion ou Vimeo"
     * )
     */
    private $url;

    /**
     * @var Trick
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="videos", cascade={"persist"})
     * @ORM\JoinColumn(name="trick_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $trick;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getUrl(){

        $control = $this->getType();  // on récupère le type de la vidéo
        $id = strip_tags($this->getIdentif()); // on récupère son identifiant

        if($control == 'youtube')
        {
            $embed = "https://www.youtube-nocookie.com/embed/".$id;
            return $embed;
        }
        else if ($control == 'dailymotion')
        {
            $embed = "https://www.dailymotion.com/embed/video/".$id;
            return $embed;
        }
        else if($control == 'vimeo')
        {
            $embed = "https://player.vimeo.com/video/".$id;
            return $embed;
        }
    }

    public function setUrl($url)
    {
        $this->url = $url;
        if($url!=null){
            $this->extractIdentif($url);
        }
        return $this;
    }

    private function youtubeId($url)
    {
        $tableaux = explode("=", $url);  // découpe l’url en deux  avec le signe ‘=’

        $this->setIdentif($tableaux[1]);  // ajoute l’identifiant à l’attribut identif
        $this->setType('youtube');  // signale qu’il s’agit d’une video youtube et l’inscrit dans l’attribut $type
    }

    private function dailymotionId($url)
    {
        $cas = explode("/", $url); // On sépare la première partie de l'url des 2 autres

        $idb = $cas[4];  // On récupère la partie qui nous intéressent

        $bp = explode("_", $idb);  // On sépare l'identifiant du reste

        $id = $bp[0]; // On récupère l'identifiant

        $this->setIdentif($id);  // ajoute l’identifiant à l’attribut identif

        $this->setType('dailymotion'); // signale qu’il s’agit d’une video dailymotion et l’inscrit dans l’attribut $type
    }

    public function extractIdentif($url)
    {
        if (preg_match("#^(http|https)://www.youtube.com/#", $url))  // Si c’est une url Youtube on execute la fonction correspondante
        {
            $this->youtubeId($url);
        }
        else if((preg_match("#^(http|https)://www.dailymotion.com/#", $url))) // Si c’est une url Dailymotion on execute la fonction correspondante
        {
            $this->dailymotionId($url);
        }
        else{
            $this->setUrl(null);
        }
    }

    public function video()
    {
        $video = "<iframe class='vignettes' width='100%' height='100%' src='".$this->getUrl()."'  frameborder='0'  allowfullscreen></iframe>";
        return $video;
    }
}
