<?php
namespace Devture\Bundle\TranslationBundle\Model;

class SourceResource implements ResourceInterface {

	private $name;
	private $path;
	private $localeKey;
	private $localizedResources = array();
	private $pack;

	public function __construct($name, $path, $localeKey) {
		$this->name = $name;
		$this->path = $path;
		$this->localeKey = $localeKey;
	}

	public function getId() {
		return $this->getName();
	}

	public function getName() {
		return $this->name;
	}

	public function getPath() {
		return $this->path;
	}

	public function getLocaleKey() {
		return $this->localeKey;
	}

	public function addLocalizedResource(LocalizedResource $localizedResource) {
		$this->localizedResources[] = $localizedResource;
	}

	/**
	 * @return LocalizedResource[]
	 */
	public function getLocalizedResources() {
		return $this->localizedResources;
	}

	/**
	 * @param string $localeKey
	 * @return LocalizedResource|NULL
	 */
	public function getLocalizedResourceByLocaleKey($localeKey) {
		foreach ($this->getLocalizedResources() as $localizedResource) {
			if ($localizedResource->getLocaleKey() === $localeKey) {
				return $localizedResource;
			}
		}
		return null;
	}

	public function isSource() {
		return true;
	}

	public function setTranslationPack(TranslationPack $pack) {
		$this->pack = $pack;
	}

	/**
	 * @return TranslationPack
	 */
	public function getTranslationPack() {
		return $this->pack;
	}

}