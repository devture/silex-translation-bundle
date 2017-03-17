<?php
namespace Devture\Bundle\TranslationBundle\Controller;

class ControllersProvider implements \Silex\Api\ControllerProviderInterface {

	private $namespace;

	public function __construct($namespace) {
		$this->namespace = $namespace;
	}

	public function connect(\Silex\Application $app) {
		$namespace = $this->namespace;
		$controllers = $app['controllers_factory'];

		$controllers->get('/manage', $namespace . '.controller.management:indexAction')
			->value('locale', $app['default_locale'])->bind($namespace . '.manage');

		$controllers->get('/search', $namespace . '.controller.management:searchAction')
			->value('locale', $app['default_locale'])->bind($namespace . '.search');

		$controllers->match('/edit/{resourceId}/{language}', $namespace . '.controller.management:editAction')
			->method('GET|POST')
			->value('locale', $app['default_locale'])->bind($namespace . '.edit');

		return $controllers;
	}

}
