<?php
namespace Devture\Bundle\TranslationBundle\Model;

class SearchRequest {

	private $keywords;
	private $localeKey;
	private $sourceResourceId;

	public function setKeywords($value) {
		$this->keywords = trim($value);
	}

	public function getKeywords() {
		return $this->keywords;
	}

	public function setLocaleKey($value) {
		$this->localeKey = $value;
	}

	public function getLocaleKey() {
		return $this->localeKey;
	}

	public function setSourceResourceId($value) {
		$this->sourceResourceId = $value;
	}

	public function getSourceResourceId() {
		return $this->sourceResourceId;
	}

	public function isEmpty() {
		return !$this->getKeywords();
	}

}