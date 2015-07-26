<?php
namespace Devture\Bundle\TranslationBundle\Helper;

use Devture\Bundle\TranslationBundle\Model\SourceResource;
use Devture\Bundle\TranslationBundle\Model\LocalizedResource;
use Devture\Bundle\TranslationBundle\Model\TranslationString;

class ResourcePersister {

	public function persist(SourceResource $sourceResource, LocalizedResource $localizedResource) {
		$translations = array();

		/* @var $translationString TranslationString */
		foreach ($localizedResource->getTranslationPack() as $translationString) {
			if (!$translationString->getValue()) {
				continue;
			}
			$translations[$translationString->getKey()] = $translationString->getValue();
			$hashes[$translationString->getKey()] = $translationString->getSourceValueHash();
		}

		ksort($translations);
		ksort($hashes);

		$translations = $this->unflatten($translations);

		$result = @file_put_contents($localizedResource->getPath(), $this->jsonEncode($translations));
		$result = @file_put_contents($localizedResource->getPath() . '.hash', $this->jsonEncode($hashes));
		return (bool) $result;
	}

	private function unflatten(array $array) {
		$result = array();

		foreach ($array as $key => $value) {
			$keyParts = explode('.', $key);
			$target = &$result;
			foreach ($keyParts as $nestingKey) {
				if (!isset($target[$nestingKey])) {
					$target[$nestingKey] = array();
				}
				$target = &$target[$nestingKey];
			}
			$target = $value;
		}

		return $result;
	}

	private function jsonEncode(array $array) {
		$text = json_encode($array, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

		//JSON_PRETTY_PRINT makes json_encode() indent.
		//However, it uses spaces, instead of tabs. We don't like that.
		$text = str_replace('    ', "\t", $text);

		return $text;
	}

}