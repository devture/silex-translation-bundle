<?php
namespace Devture\Bundle\TranslationBundle\Twig;

class TranslationExtension extends \Twig_Extension {

	private $container;

	public function __construct(\Pimple $container) {
		$this->container = $container;
	}

	public function getName() {
		return 'devture_translation_extension';
	}

	public function getFunctions() {
		return array(
			'devture_translation_get_locale_name' => new \Twig_Function_Method($this, 'getLocaleName'),
		);
	}

	public function getLocaleName($namespace, $localeKey) {
		$locales = $this->container[$namespace . '.locales'];
		return (isset($locales[$localeKey]) ? $locales[$localeKey]['name'] : null);
	}

}
