<?php
namespace Devture\Bundle\TranslationBundle\Model;

class TranslationStats {

	private $translatedCount;
	private $totalCount;

	public function __construct($translatedCount, $totalCount) {
		$this->translatedCount = (int) $translatedCount;
		$this->totalCount = (int) $totalCount;
	}

	public function getTranslatedCount() {
		return $this->translatedCount;
	}

	public function getTotalCount() {
		return $this->totalCount;
	}

	public function getTranslatedPercentage() {
		if ($this->totalCount === 0) {
			return 0;
		}
		return round(($this->translatedCount / $this->totalCount) * 100);
	}

}