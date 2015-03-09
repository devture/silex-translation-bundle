<?php
namespace Devture\Bundle\TranslationBundle\Model;

class LocalizedResource implements ResourceInterface {

	private $name;
	private $path;
	private $localeKey;
	private $pack;

	public function __construct($name, $path, $localeKey) {
		$this->name = $name;
		$this->path = $path;
		$this->localeKey = $localeKey;
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

	public function isSource() {
		return false;
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

	public function determineTranslationStatsAgainst(ResourceInterface $sourceResource) {
		$totalCount = 0;
		$translatedCount = 0;

		/* @var $sourceTranslationString TranslationString */
		foreach ($sourceResource->getTranslationPack() as $sourceTranslationString) {
			$totalCount += 1;

			/* @var $translationstring TranslationString|NULL */
			$translationString = $this->pack->getByKey($sourceTranslationString->getKey());
			if ($translationString === null) {
				//We don't have this one at all.
				continue;
			}

			$translatedCount += ($translationString->isTranslatedVersionOf($sourceTranslationString));
		}

		return new TranslationStats($translatedCount, $totalCount);
	}

}