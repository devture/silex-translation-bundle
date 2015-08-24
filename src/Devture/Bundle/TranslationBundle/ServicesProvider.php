<?php
namespace Devture\Bundle\TranslationBundle;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ServicesProvider implements ServiceProviderInterface {

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

	public function register(Application $app) {
		$namespace = $this->namespace;
		$config = $this->config;

		$app[$namespace . '.base_path'] = $config['base_path'];

		$app[$namespace . '.locales'] = $config['locales'];

		$app[$namespace . '.search_request_builder'] = $app->share(function ($app) use ($namespace, $config) {
			return new Helper\SearchRequestBuilder(
				array_keys($app[$namespace . '.locales'])
			);
		});

		$app[$namespace . '.searcher'] = $app->share(function ($app) use ($namespace, $config) {
			return new Helper\Searcher(
				$app[$namespace . '.resource_finder']
			);
		});

		$app[$namespace . '.resource_persister'] = $app->share(function ($app) use ($config) {
			return new Helper\ResourcePersister();
		});

		$app[$namespace . '.resource_translation_pack_loader'] = $app->share(function ($app) use ($config) {
			return new Helper\ResourceTranslationPackLoader();
		});

		$app[$namespace . '.resource_finder'] = $app->share(function ($app) use ($namespace, $config) {
			return new Helper\ResourceFinder(
				$app[$namespace . '.base_path'],
				$config['source_language_locale_key'],
				$app[$namespace . '.locales'],
				$app[$namespace . '.resource_translation_pack_loader']
			);
		});

		$this->registerControllerServices($app);
	}

	private function registerControllerServices(Application $app) {
		$namespace = $this->namespace;

		$app[$namespace . '.controllers_provider.management'] = $app->share(function ($app) use ($namespace) {
			return new Controller\ControllersProvider($namespace);
		});
		$app[$namespace . '.controller.management'] = $app->share(function ($app) use ($namespace) {
			return new Controller\ManagementController($app, $namespace);
		});
	}

	public function boot(Application $app) {
		$app['devture_localization.translator.resource_loader']->addResources(__DIR__ . '/Resources/translations/');

		//Also register the templates path at a custom namespace, to allow templates overriding+extending.
		$app['twig.loader.filesystem']->addPath(__DIR__ . '/Resources/views/');
		$app['twig.loader.filesystem']->addPath(__DIR__ . '/Resources/views/', 'DevtureTranslationBundle');

		$app['twig']->addExtension(new Twig\TranslationExtension($app));
	}

}
