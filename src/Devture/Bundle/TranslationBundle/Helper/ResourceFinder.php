<?php
namespace Devture\Bundle\TranslationBundle\Helper;

use Devture\Bundle\TranslationBundle\Model\SourceResource;
use Devture\Bundle\TranslationBundle\Model\LocalizedResource;

class ResourceFinder {

	private $basePath;
	private $sourceLocaleKey;
	private $locales;
	private $translationPackLoader;

	public function __construct($basePath, $sourceLocaleKey, array $locales, ResourceTranslationPackLoader $translationPackLoader) {
		$this->basePath = $basePath;
		$this->sourceLocaleKey = $sourceLocaleKey;
		$this->locales = $locales;
		$this->translationPackLoader = $translationPackLoader;
	}

	/**
	 * @return multitype:\Devture\Bundle\TranslationBundle\Model\SourceResource
	 */
	public function findAll() {
		$cmd = 'find ' . escapeshellarg($this->basePath) . ' -name ' . $this->sourceLocaleKey . '.json';
		$output = trim(shell_exec($cmd));

		$resources = array();

		foreach (explode("\n", $output) as $filePath) {
			$filePath = trim($filePath);
			if ($filePath === '') {
				continue;
			}

			$humanFriendlyName = $this->getHumanFriendlyNameByPath($filePath);

			$sourceResource = new SourceResource($humanFriendlyName, $filePath, $this->sourceLocaleKey);
			$sourceResource->setTranslationPack($this->translationPackLoader->load($sourceResource));

			foreach ($this->locales as $localeKey => $localeData) {
				if ($localeKey === $this->sourceLocaleKey) {
					continue;
				}

				$localizedResourcePath = str_replace($this->sourceLocaleKey . '.json', $localeKey . '.json', $filePath);
				$localizedResource = new LocalizedResource($humanFriendlyName, $localizedResourcePath, $localeKey);
				$localizedResource->setTranslationPack($this->translationPackLoader->load($localizedResource));

				$sourceResource->addLocalizedResource($localizedResource);
			}

			$resources[] = $sourceResource;
		}

		return $resources;
	}

	public function findOneById($id) {
		foreach ($this->findAll() as $sourceResource) {
			if ($sourceResource->getId() === $id) {
				return $sourceResource;
			}
		}
		return null;
	}

	private function getHumanFriendlyNameByPath($filePath) {
		if (preg_match('/Bundle\/(.+?)\/Resources\/translations\/([^\/]+)\.json$/', $filePath, $matches)) {
			return $matches[1];
		}

		$filePath = str_replace($this->basePath, '', $filePath);
		$parts = explode('/', $filePath);

		//Last part is the filename itself. We don't want it.
		array_pop($parts);

		$newParts = array();
		foreach ($parts as $part) {
			if ($part === '') {
				continue;
			}

			if (in_array($part, array('src', 'translations', 'Resources'))) {
				//Meaningless part.
				continue;
			}

			$newParts[] = $part;
		}
		return implode('-', $newParts);
	}

}