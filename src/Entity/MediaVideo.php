<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
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
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=155)
     * @Assert\Regex(
     *     pattern="/^https:\/\/www\.(youtube|dailymotion)\.com/",
     *     message="Le format de la vidéo n'est pas correct."
     * )
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $videoId;

    /**
     * @var Trick
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="videos", cascade={"persist"})
     * @ORM\JoinColumn(name="trick_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $trick;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        $this->extractIdentif($url);

        return $this;
    }

    private function youtubeId($url)
    {
        $tableaux = explode('=', $url);

        $this->setVideoId($tableaux[1]);
        $this->setType('youtube');
    }

    private function dailymotionId($url)
    {
        $cas = explode('/', $url);

        $idb = $cas[4];

        $urlTable = explode('_', $idb);

        $id = $urlTable[0];

        $this->setVideoId($id);

        $this->setType('dailymotion');
    }

    public function extractIdentif($url)
    {
        if (preg_match('#^(http|https)://www.youtube.com/#', $url)) {
            $this->youtubeId($url);
        } elseif ((preg_match('#^(http|https)://www.dailymotion.com/#', $url))) {
            $this->dailymotionId($url);
        }
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

    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    public function setVideoId(string $videoId): self
    {
        $this->videoId = $videoId;

        return $this;
    }

    public function getEmbedUrl()
    {
        $control = $this->getType();
        $tags = strip_tags($this->getVideoId());

        if ('youtube' == $control) {
            $embed = 'https://www.youtube.com/embed/'.$tags;

            return $embed;
        } elseif ('dailymotion' == $control) {
            $embed = 'https://www.dailymotion.com/embed/video/'.$tags;

            return $embed;
        } elseif ('vimeo' == $control) {
            $embed = 'https://player.vimeo.com/video/'.$tags;

            return $embed;
        }
    }

    public function video()
    {
        $video = "<iframe class='vignettes' width='100%' height='100%' src='".$this->getEmbedUrl()."'  frameborder='0'  allowfullscreen></iframe>";

        return $video;
    }
}
