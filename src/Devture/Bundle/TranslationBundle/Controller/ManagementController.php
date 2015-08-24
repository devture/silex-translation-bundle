<?php
namespace Devture\Bundle\TranslationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Devture\Bundle\TranslationBundle\Model\TranslationPack;

class ManagementController extends BaseController {

	public function indexAction(Request $request) {
		$resources = $this->getResourceFinder()->findAll();

		return $this->renderView('DevtureTranslationBundle/translation/index.html.twig', array(
			'sourceResources' => $this->getResourceFinder()->findAll(),
		));
	}

	public function searchAction(Request $request) {
		$searchRequest = $this->getSearchRequestBuilder()->buildFromHttpRequest($request);

		if ($searchRequest->isEmpty()) {
			$searchResults = null;
		} else {
			$searchResults = $this->getSearcher()->search($searchRequest);
		}

		return $this->renderView('DevtureTranslationBundle/translation/search.html.twig', array(
			'searchRequest' => $searchRequest,
			'searchResults' => $searchResults,
			'sourceResources' => $this->getResourceFinder()->findAll(),
		));
	}

	public function editAction(Request $request, $resourceId, $language) {
		$sourceResource = $this->getResourceFinder()->findOneById($resourceId);

		if ($sourceResource === null) {
			return $this->abort(404);
		}

		$localizedResource = $sourceResource->getLocalizedResourceByLocaleKey($language);
		if ($localizedResource === null) {
			return $this->abort(404);
		}

		$localizedResource->getTranslationPack()->syncWithSource($sourceResource->getTranslationPack());

		if ($request->isMethod('POST')) {
			$errors = $this->bindRequestToTranslationPack($request, $sourceResource->getTranslationPack(), $localizedResource->getTranslationPack());

			if (count($errors) > 0) {
				return $this->json(array('ok' => false, 'errors' => $errors));
			}

			$result = $this->getResourcePersister()->persist($sourceResource, $localizedResource);
			if (!$result) {
				return $this->json(array('ok' => false, 'errors' => array('Could not save the translation data.')));
			}

			$packStatus = array();
			/* @var $sourceTranslationString \Devture\Bundle\TranslationBundle\Model\TranslationString */
			foreach ($sourceResource->getTranslationPack() as $sourceTranslationString) {
				/* @var $translationString \Devture\Bundle\TranslationBundle\Model\TranslationString|NULL */
				$translationString = $localizedResource->getTranslationPack()->getByKey($sourceTranslationString->getKey());
				$packStatus[$sourceTranslationString->getKey()] = ($translationString === null ? false : $translationString->isTranslatedVersionOf($sourceTranslationString));
			}

			return $this->json(array('ok' => true, 'packStatus' => $packStatus));
		}

		$tabToActivate = $request->query->get('tab', 'untranslated');
		if (!in_array($tabToActivate, array('untranslated', 'translated', 'all'))) {
			$tabToActivate = 'untranslated';
		}

		return $this->renderView('DevtureTranslationBundle/translation/edit.html.twig', array(
			'sourceResource' => $sourceResource,
			'localizedResource' => $localizedResource,
			'tabToActivate' => $tabToActivate,
		));
	}

	private function bindRequestToTranslationPack(Request $request, TranslationPack $sourcePack, TranslationPack $targetPack) {
		$translations = (array) $request->request->get('translations', array());

		foreach ($translations as $key => $translationData) {
			if (!$targetPack->hasByKey($key) || !$sourcePack->hasByKey($key)) {
				continue;
			}

			$value = (string) (isset($translationData['translation']) ? $translationData['translation'] : null);
			$sourceValueHash = (string) (isset($translationData['sourceValueHash']) ? $translationData['sourceValueHash'] : null);

			if ($sourcePack->getByKey($key)->getSourceValueHash() !== $sourceValueHash) {
				//The string being translated actually changed. Require a reload.
				return array('The translation files have changed. Please reload the page and try again.');
			}

			$targetPack->getByKey($key)->setValue($value);
		}
	}

}
