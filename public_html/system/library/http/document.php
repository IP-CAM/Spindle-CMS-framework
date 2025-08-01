<?php
/*
 * Spindle CMS
 * Copyright (c) 2025. All rights reserved.
 *
 * This file is part of the Spindle CMS project — a lightweight, modular PHP content framework derived from OpenCart.
 *
 * @license GNU General Public License v3.0 (GPL-3.0-or-later)
 * @link    https://github.com/RandomCoderTinker/Spindle
 */

namespace Spindle\System\Library\Http;

/**
 * Class Document
 */
class Document
{

	// Basic meta properties
	private string $title = '';
	private string $description = '';
	private string $keywords = '';
	private string $canonical = '';
	private string $robots = 'index, follow';

	// Open Graph properties
	private string $ogTitle = '';
	private string $ogDescription = '';
	private string $ogUrl = '';
	private string $ogType = 'website';
	private string $ogImage = CDN_LOCATION . 'images/logo_spindle.webp';
	private string $ogSiteName = '';
	private string $ogLocale = 'en_GB';

	// Twitter Card properties
	private string $twitterCard = 'summary_large_image';
	private string $twitterTitle = DEFAULT_TITLE;
	private string $twitterDescription = DEFAULT_DESCRIPTION;
	private string $twitterImage = DEFAULT_OGIMAGE;
	private string $twitterSite = DEFAULT_TWITTER;

	// Additional assets
	private array $links = [];
	private array $styles = [];
	private array $scripts = [];

	// Nonce for including scripts/styles
	private string $nonce = '';

	public function __construct ($nonce)
	{
		$this->setNonce($nonce->getNonce());
	}

	public function setNonce ($newNonce): void
	{
		$this->nonce = $newNonce;
	}

	private function getNonceAttr (): string
	{
		return $this->nonce ? " nonce='{$this->nonce}'" : '';
	}

	// Setters and getters for basic meta

	public function getTitle (): string
	{
		if (empty($this->title)) {
			return DEFAULT_TITLE;
		}

		return $this->title;
	}

	public function setTitle (string $title): void
	{
		$this->title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
	}

	public function getDescription (): string
	{
		if (empty($this->description)) {
			return DEFAULT_DESCRIPTION;
		}

		return $this->description;
	}

	public function setDescription (string $description): void
	{
		$this->description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
	}

	public function getKeywords (): string
	{
		if (empty($this->keywords)) {
			return DEFAULT_KEYWORDS;
		}

		return $this->keywords;
	}

	public function setKeywords (string $keywords): void
	{
		$this->keywords = htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8');
	}

	public function getCanonical (): string
	{
		if (empty($this->canonical)) {
			return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}

		return $this->canonical;
	}

	public function setCanonical (string $url = ''): void
	{
		$this->canonical = $url;
	}

	public function getRobots (): string
	{
		return $this->robots;
	}

	public function setRobots (string $robots): void
	{
		$this->robots = $robots;
	}

	// Setters and getters for Open Graph

	public function getOgTitle (): string
	{
		return $this->ogTitle;
	}

	public function setOgTitle (string $ogTitle): void
	{
		$this->ogTitle = $ogTitle;
	}

	public function getOgDescription (): string
	{
		return $this->ogDescription;
	}

	public function setOgDescription (string $ogDescription): void
	{
		$this->ogDescription = $ogDescription;
	}

	public function getOgUrl (): string
	{
		if (empty($this->ogUrl)) {
			return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}

		return $this->ogUrl;
	}

	public function setOgUrl (string $ogUrl): void
	{
		$this->ogUrl = $ogUrl;
	}

	public function getOgType (): string
	{
		return $this->ogType;
	}

	public function setOgType (string $ogType): void
	{
		$this->ogType = $ogType;
	}

	public function getOgImage (): string
	{
		if (empty($this->ogImage)) {
			return DEFAULT_OGIMAGE;
		}

		return $this->ogImage;
	}

	public function setOgImage (string $ogImage): void
	{
		$this->ogImage = $ogImage;
	}

	public function getOgSiteName (): string
	{
		return $this->ogSiteName;
	}

	public function setOgSiteName (string $ogSiteName): void
	{
		$this->ogSiteName = $ogSiteName;
	}

	public function getOgLocale (): string
	{
		return $this->ogLocale;
	}

	public function setOgLocale (string $ogLocale): void
	{
		$this->ogLocale = $ogLocale;
	}

	// Setters and getters for Twitter Cards

	public function getTwitterCard (): string
	{
		return $this->twitterCard;
	}

	public function setTwitterCard (string $twitterCard): void
	{
		$this->twitterCard = $twitterCard;
	}

	public function getTwitterTitle (): string
	{
		return $this->twitterTitle;
	}

	public function setTwitterTitle (string $twitterTitle): void
	{
		$this->twitterTitle = htmlspecialchars($twitterTitle, ENT_QUOTES, 'UTF-8');
	}

	public function getTwitterDescription (): string
	{
		return $this->twitterDescription;
	}

	public function setTwitterDescription (string $twitterDescription): void
	{
		$this->twitterDescription = htmlspecialchars($twitterDescription, ENT_QUOTES, 'UTF-8');
	}

	public function getTwitterImage (): string
	{
		return $this->twitterImage;
	}

	public function setTwitterImage (string $twitterImage): void
	{
		$this->twitterImage = $twitterImage;
	}

	public function getTwitterSite (): string
	{
		return $this->twitterSite;
	}

	public function setTwitterSite (string $twitterSite): void
	{
		$this->twitterSite = $twitterSite;
	}

	// Methods for managing links, styles, and scripts

	public function addLink (string $href, string $rel): void
	{
		$this->links[$href] = [
			'href' => $href,
			'rel' => $rel,
		];
	}

	public function getLinks (): array
	{
		return $this->links;
	}

	public function addStyle (string $href, string $rel = 'stylesheet', string $media = 'screen', int $sortOrder = 1): void
	{
		$this->styles[$href] = [
			'href' => $href,
			'rel' => $rel,
			'media' => $media,
			'sort_order' => $sortOrder,
		];
	}

	public function getStyles (): array
	{
		return $this->styles;
	}

	public function addScript (string $src, string $position = 'header'): void
	{
		$this->scripts[$position][$src] = $src;
	}

	/**
	 * Returns the current full URL, using HTTPS if available.
	 *
	 * @return string
	 */
	public function getCurrentUrl (): string
	{
		$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

		return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	public function renderHead (): string
	{
		// Fallback values based on basic meta
		$finalTitle = $this->title;
		$finalDescription = $this->description;
		$finalKeywords = $this->keywords;
		$finalCanonical = $this->getCurrentUrl();

		// Open Graph fallback: use basic meta if specific OG values are empty
		$finalOgTitle = $this->ogTitle ?: $finalTitle;
		$finalOgDescription = $this->ogDescription ?: $finalDescription;
		$finalOgUrl = $this->getCurrentUrl();
		$finalOgType = $this->ogType; // default is already set to 'website'
		$finalOgImage = $this->ogImage; // optionally you can add a hardcoded default image here
		$finalOgSiteName = $this->ogSiteName ?: $finalTitle;
		$finalOgLocale = $this->ogLocale ?: 'en_GB';
		$ogLogo = CDN_LOCATION . 'images/logo_spindle.webp';

		// Twitter Card fallback: use OG or basic meta values if empty
		$finalTwitterCard = $this->twitterCard ?: DEFAULT_TWITTER;
		$finalTwitterTitle = $this->twitterTitle ?: $finalTitle;
		$finalTwitterDescription = $this->twitterDescription ?: $finalDescription;
		$finalTwitterImage = $this->twitterImage ?: $finalOgImage;
		$finalTwitterSite = $this->twitterSite;

		// Build the head content
		$head = "<meta charset=\"UTF-8\">\n";
		$head .= "<title>{$finalTitle}</title>\n";
		$head .= "<meta http-equiv=\"x-ua-compatible\" content=\"ie=edge\">\n";
		$head .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, shrink-to-fit=no\">\n";
		$head .= "<meta name=\"theme-color\" content=\"#000000\">";

		// Add the manifest data
		$head .= "<link rel=\"manifest\" href=\"/data/manifest.json\">\n";
		$head .= "<link rel=\"icon\" type=\"image/png\" sizes=\"512x512\" href=\"/data/icon-512x512.png\">\n";
		$head .= "<link rel=\"apple-touch-icon\" sizes=\"512x512\" href=\"/data/icon-512x512.png\">\n";

		if ($finalDescription) {
			$head .= "<meta name=\"description\" content=\"{$finalDescription}\">\n";
		}
		if ($finalKeywords) {
			$head .= "<meta name=\"keywords\" content=\"{$finalKeywords}\">\n";
		}
		if ($finalCanonical) {
			$head .= "<link rel=\"canonical\" href=\"{$finalCanonical}\">\n";
		}
		$head .= "<meta name=\"robots\" content=\"{$this->robots}\">\n";

		// Open Graph tags
		$head .= "<meta property=\"og:title\" content=\"{$finalOgTitle}\">\n";
		if ($finalOgDescription) {
			$head .= "<meta property=\"og:description\" content=\"{$finalOgDescription}\">\n";
		}
		$head .= "<meta property=\"og:url\" content=\"{$finalOgUrl}\">\n";
		$head .= "<meta property=\"og:type\" content=\"{$finalOgType}\">\n";
		if ($finalOgImage) {
			$head .= "<meta property=\"og:image\" content=\"{$finalOgImage}\">\n";
		}
		$head .= "<meta property=\"og:site_name\" content=\"{$finalOgSiteName}\">\n";
		$head .= "<meta property=\"og:locale\" content=\"{$finalOgLocale}\">\n";
		$head .= "<meta property=\"og:logo\" content=\"{$ogLogo}\" />";

		// Twitter Card tags
		$head .= "<meta name=\"twitter:card\" content=\"{$finalTwitterCard}\">\n";
		$head .= "<meta name=\"twitter:title\" content=\"{$finalTwitterTitle}\">\n";
		$head .= "<meta name=\"twitter:description\" content=\"{$finalTwitterDescription}\">\n";
		if ($finalTwitterImage) {
			$head .= "<meta name=\"twitter:image\" content=\"{$finalTwitterImage}\">\n";
		}
		if ($finalTwitterSite) {
			$head .= "<meta name=\"twitter:site\" content=\"{$finalTwitterSite}\">\n";
		}

		// Additional links and styles with nonce
		foreach ($this->links as $link) {
			$head .= "<link {$this->getNonceAttr()} rel=\"{$link['rel']}\" href=\"{$link['href']}\">\n";
		}

		// Sort the style by sort order
		$styles = $this->styles;
		usort($styles, function($a, $b) {
			return $a['sort_order'] <=> $b['sort_order'];
		});

		foreach ($styles as $style) {
			$head .= "<link {$this->getNonceAttr()} rel=\"{$style['rel']}\" href=\"{$style['href']}\" media=\"{$style['media']}\">\n";
		}

		// Header scripts with nonce
		foreach ($this->getScripts('header') as $script) {
			$head .= "<script {$this->getNonceAttr()} src=\"{$script}\"></script>\n";
		}

		return $head;
	}

	// Render the full head section with fallback defaults

	public function getScripts (string $position = 'header'): array
	{
		return $this->scripts[$position] ?? [];
	}

}
