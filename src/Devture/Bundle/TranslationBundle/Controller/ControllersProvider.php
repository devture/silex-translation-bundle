<?php
namespace Devture\Bundle\TranslationBundle\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Devture\Bundle\ReservationBundle\Controller\ManagementController;

class ControllersProvider implements ControllerProviderInterface {

	private $namespace;

	public function __construct($namespace) {
		$this->namespace = $namespace;
	}

	public function connect(Application $app) {
		$namespace = $this->namespace;
		$controllers = $app['controllers_factory'];

		$controllers->get('/manage', $namespace . '.controller.management:indexAction')
			->value('locale', $app['default_locale'])->bind($namespace . '.manage');

		$controllers->match('/edit/{resourceId}/{language}', $namespace . '.controller.management:editAction')
			->method('GET|POST')
			->value('locale', $app['default_locale'])->bind($namespace . '.edit');

		return $controllers;
	}

}
