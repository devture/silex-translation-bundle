<?php
namespace Devture\Bundle\TranslationBundle\Helper;

use Symfony\Component\HttpFoundation\Request;
use Devture\Bundle\TranslationBundle\Model\SearchRequest;

class SearchRequestBuilder {

	private $localeKeys;

	public function __construct(array $localeKeys) {
		$this->localeKeys = $localeKeys;
	}

	public function buildFromHttpRequest(Request $httpRequest) {
		$searchRequest = new SearchRequest();

		$searchRequest->setKeywords($httpRequest->query->get('q', null));

		$localeKey = $httpRequest->query->get('localeKey', null);
		if (in_array($localeKey, $this->localeKeys)) {
			$searchRequest->setLocaleKey($localeKey);
		}

		$searchRequest->setSourceResourceId($httpRequest->query->get('sourceResourceId', null));

		return $searchRequest;
	}

}