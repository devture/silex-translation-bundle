<?php
namespace Devture\Bundle\TranslationBundle\Model;

class SearchResult {

	/**
	 * @var SourceResource
	 */
	private $sourceResource;

	/**
	 * @var LocalizedResource|NULL
	 */
	private $localizedResource;

	/**
	 * @var TranslationString
	 */
	private $translationString;

	public function __construct(SourceResource $sourceResource, LocalizedResource $localizedResource = null,
								TranslationString $translationString) {
		$this->sourceResource = $sourceResource;
		$this->localizedResource = $localizedResource;
		$this->translationString = $translationString;
	}

	public function getSourceResource() {
		return $this->sourceResource;
	}

	public function getLocalizedResource() {
		return $this->localizedResource;
	}

	public function getTranslationString() {
		return $this->translationString;
	}

}