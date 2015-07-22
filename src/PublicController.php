<?php

namespace Corework;

use jamwork\common\Registry;
use Corework\Application\Manager\UserManager;

/**
 * Class PublicController
 * inkl. Rechteabfrage
 *
 * @category Corework
 * @package  Corework
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class PublicController extends Controller
{

	/**
	 * @var bool
	 */
	protected $checkPermissions = true;
	protected $needLogin = true;

	protected $noPermissionActions = array();

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();

		if ($this->view->login)
		{
			$this->view->html->addJsAsset('loggedin');
		}
	}

	public function checkPermission()
	{
		if (!$this->needLogin)
		{
			return;
		}
		if ($this->checkPermissions)
		{
			$module = $this->request->getRouteParam('module');
			$controller = $this->request->getRouteParam('controller');
			$action = $this->request->getRouteParam('action');
			$prefix = $this->request->getRouteParam('prefix');

			$urlArray = array(
				'rig_module' => $module,
				'rig_controller' => $controller,
				'rig_action' => $action,
				'rig_prefix' => $prefix
			);

			$rm = new \App\Manager\Right\RightManager();
			$right = $rm->getModelFromArray($urlArray);

			try
			{
				$login = Registry::getInstance()->login;
			} catch (\Exception $e)
			{
				$registry = Registry::getInstance();
				$session = $registry->getSession();
				$session->set('callUrlAction', $urlArray);

				$this->response->redirect($this->view->url(array(), 'login', true));
			}

			if (!in_array($action, $this->noPermissionActions) && !$rm->isAllowed($right, $login))
			{
				throw new \Corework\Exceptions\AccessException('Zugriff auf nicht erlaubte Aktion');
			}
		}

	}

	/**
	 * Übergebene Array wird json encodiert und ausgegeben Header wird sauber angepasst
	 *
	 * @param array $json
	 * @return void
	 * @deprecated der DIE() hebelt alles aus !
	 */
	protected function flushJSON(array $json)
	{
		$registry = Registry::getInstance();
		$response = $registry->getResponse();
		$response->addHeader('Content-Type', 'application/json; charset=utf-8');

		$response->setBody(json_encode($json));
		$response->flush();
		die();
	}

	/**
	 * Function muss in der Anleitung individuell angepasst werden
	 * @throws \Exception
	 * @return void
	 */
	public function flushJSONResponse()
	{
		throw new \Exception('flushJSONResponse muss in der Ableitung implementiert werden!');
	}

	/**
	 * @return bool
	 */
	public function getCheckPermissions()
	{
		return $this->checkPermissions;
	}
}
