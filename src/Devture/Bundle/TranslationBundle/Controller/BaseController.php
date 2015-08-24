<?php
namespace Devture\Bundle\TranslationBundle\Controller;

abstract class BaseController extends \Devture\Bundle\FrameworkBundle\Controller\BaseController {

	/**
	 * @return \Devture\Bundle\TranslationBundle\Helper\ResourceFinder
	 */
	protected function getResourceFinder() {
		return $this->getNs('resource_finder');
	}

	/**
	 * @return \Devture\Bundle\TranslationBundle\Helper\ResourcePersister
	 */
	protected function getResourcePersister() {
		return $this->getNs('resource_persister');
	}

	/**
	 * @return \Devture\Bundle\TranslationBundle\Helper\SearchRequestBuilder
	 */
	protected function getSearchRequestBuilder() {
		return $this->getNs('search_request_builder');
	}

	/**
	 * @return \Devture\Bundle\TranslationBundle\Helper\Searcher
	 */
	protected function getSearcher() {
		return $this->getNs('searcher');
	}

}
