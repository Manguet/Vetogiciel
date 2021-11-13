<?php

namespace App\Interfaces\Socials;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface SocialInterface
{
    /**
     * @return string|null
     */
    public function getFacebook(): ?string;

    /**
     * @param string|null $facebook
     *
     * @return $this
     */
    public function setFacebook(?string $facebook): self;

    /**
     * @return string|null
     */
    public function getInstagram(): ?string;

    /**
     * @param string|null $instagram
     *
     * @return $this
     */
    public function setInstagram(?string $instagram): self;

    /**
     * @return string|null
     */
    public function getLinkedin(): ?string;

    /**
     * @param string|null $linkedin
     *
     * @return $this
     */
    public function setLinkedin(?string $linkedin): self;

    /**
     * @return string|null
     */
    public function getTwitter(): ?string;

    /**
     * @param string|null $twitter
     *
     * @return $this
     */
    public function setTwitter(?string $twitter): self;

    /**
     * @return string|null
     */
    public function getYoutube(): ?string;

    /**
     * @param string|null $youtube
     *
     * @return $this
     */
    public function setYoutube(?string $youtube): self;
}