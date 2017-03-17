<?php
namespace Devture\Bundle\TranslationBundle;

class ServicesProvider implements \Pimple\ServiceProviderInterface, \Silex\Api\BootableProviderInterface {

	private $namespace;
	private $config;

	public function __construct($namespace, array $config) {
		$requiredConfigKeys = array(
			'base_path',
			'source_language_locale_key',
			'locales',
		);
		foreach ($requiredConfigKeys as $k) {
			if (!array_key_exists($k, $config)) {
				throw new \InvalidArgumentException(sprintf('The %s parameter passed to %s is missing.', $k, __CLASS__));
			}
		}

		foreach ($config['locales'] as $localeKey => $localeData) {
			if (!isset($localeData['name'])) {
				throw new \InvalidArgumentException(sprintf('Data for locale %s is invalid. Missing name attribute.', $localeKey));
			}
		}

		$this->namespace = $namespace;
		$this->config = $config;
	}

	public function register(\Pimple\Container $container) {
		$namespace = $this->namespace;
		$config = $this->config;

		$container[$namespace . '.base_path'] = $config['base_path'];

		$container[$namespace . '.locales'] = $config['locales'];

		$container[$namespace . '.search_request_builder'] = function ($container) use ($namespace, $config) {
			return new Helper\SearchRequestBuilder(
				array_keys($container[$namespace . '.locales'])
			);
		};

		$container[$namespace . '.searcher'] = function ($container) use ($namespace, $config) {
			return new Helper\Searcher(
				$container[$namespace . '.resource_finder']
			);
		};

		$container[$namespace . '.resource_persister'] = function ($container) use ($config) {
			return new Helper\ResourcePersister();
		};

		$container[$namespace . '.resource_translation_pack_loader'] = function ($container) use ($config) {
			return new Helper\ResourceTranslationPackLoader();
		};

		$container[$namespace . '.resource_finder'] = function ($container) use ($namespace, $config) {
			return new Helper\ResourceFinder(
				$container[$namespace . '.base_path'],
				$config['source_language_locale_key'],
				$container[$namespace . '.locales'],
				$container[$namespace . '.resource_translation_pack_loader']
			);
		};

		$this->registerControllerServices($container);
	}

	private function registerControllerServices(\Pimple\Container $container) {
		$namespace = $this->namespace;

		$container[$namespace . '.controllers_provider.management'] = function ($container) use ($namespace) {
			return new Controller\ControllersProvider($namespace);
		};

		$container[$namespace . '.controller.management'] = function ($container) use ($namespace) {
			return new Controller\ManagementController($container, $namespace);
		};
	}

	public function boot(\Silex\Application $app) {
		$app['devture_localization.translator.resource_loader']->addResources(__DIR__ . '/Resources/translations/');

		//Also register the templates path at a custom namespace, to allow templates overriding+extending.
		$app['twig.loader.filesystem']->addPath(__DIR__ . '/Resources/views/');
		$app['twig.loader.filesystem']->addPath(__DIR__ . '/Resources/views/', 'DevtureTranslationBundle');

		$app['twig']->addExtension(new Twig\TranslationExtension($app));
	}

}
