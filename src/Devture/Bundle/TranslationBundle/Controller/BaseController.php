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

}
