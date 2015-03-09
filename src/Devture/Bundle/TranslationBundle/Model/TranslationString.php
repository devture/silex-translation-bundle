<?php
namespace Devture\Bundle\TranslationBundle\Model;

class TranslationString {

	private $key;
	private $value;
	private $sourceValueHash;

	public function __construct($key, $value, $sourceValueHash) {
		$this->key = $key;
		$this->value = $value;
		$this->sourceValueHash = $sourceValueHash;
	}

	public function getKey() {
		return $this->key;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = (is_string($value) ? trim($value) : null);
	}

	public function getSourceValueHash() {
		return $this->sourceValueHash;
	}

	public function setSourceValueHash($hash) {
		$this->sourceValueHash = $hash;
	}

	public function isTranslatedVersionOf(TranslationString $other) {
		if (!$this->getValue()) {
			//Not translated at all.
			return false;
		}
		return ($this->getSourceValueHash() === $other->getSourceValueHash());
	}

}