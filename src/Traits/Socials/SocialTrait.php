<?php

namespace App\Traits\Socials;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait to add socials links in entities
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
trait SocialTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $facebook;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $instagram;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $linkedin;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $twitter;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $youtube;

    /**
     * @return string|null
     */
    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    /**
     * @param string|null $facebook
     *
     * @return $this
     */
    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    /**
     * @param string|null $instagram
     *
     * @return $this
     */
    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    /**
     * @param string|null $linkedin
     *
     * @return $this
     */
    public function setLinkedin(?string $linkedin): self
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    /**
     * @param string|null $twitter
     *
     * @return $this
     */
    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    /**
     * @param string|null $youtube
     *
     * @return $this
     */
    public function setYoutube(?string $youtube): self
    {
        $this->youtube = $youtube;

        return $this;
    }
}