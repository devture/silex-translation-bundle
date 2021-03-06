<?php
namespace Devture\Bundle\TranslationBundle\Twig;

class TranslationExtension extends \Twig_Extension {

	private $container;

	public function __construct(\Pimple\Container $container) {
		$this->container = $container;
	}

	public function getName() {
		return 'devture_translation_extension';
	}

	public function getFilters() {
		return array(
			new \Twig_SimpleFilter('trans', array($this, 'trans')),
			new \Twig_SimpleFilter('transchoice', array($this, 'transchoice')),
		);
	}

	public function getFunctions() {
		return array(
			new \Twig_SimpleFunction('devture_translation_get_locale_name', array($this, 'getLocaleName')),
		);
	}

	public function trans($message, array $arguments = array(), $domain = null, $locale = null) {
		if ($this->isDebug()) {
			return $message;
		}
		return $this->getTranslator()->trans($message, $arguments, $domain, $locale);
	}

	public function transchoice($message, $count, array $arguments = array(), $domain = null, $locale = null) {
		if ($this->isDebug()) {
			return $message;
		}
		return $this->getTranslator()->transChoice($message, $count, array_merge(array('%count%' => $count), $arguments), $domain, $locale);
	}

	public function getLocaleName($namespace, $localeKey) {
		$locales = $this->container[$namespace . '.locales'];
		return (isset($locales[$localeKey]) ? $locales[$localeKey]['name'] : null);
	}

	/**
	 * @return \Symfony\Component\Translation\TranslatorInterface
	 */
	private function getTranslator() {
		return $this->container['translator'];
	}

	private function isDebug() {
		try {
			return $this->container['request_stack']->getCurrentRequest()->query->has('__debug_translate__');
		} catch (\LogicException $e) {
			return false;
		}
	}

}
