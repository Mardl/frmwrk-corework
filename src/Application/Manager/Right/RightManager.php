<?php

namespace Corework\Application\Manager\Right;

use Corework\Application\Abstracts\Manager;
use Corework\Application\Models\Right\RightModel;
use Corework\Application\Models\UserModel;
use Corework\SystemMessages;
use Exception;
use jamwork\common\Registry;

/**
 * Class RightManager
 *
 * @category Corework
 * @package  Corework\Application\Manager\Right
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
class RightManager extends Manager
{
	/**
	 * @return RightModel
	 */
	protected function getNewModel()
	{
		return $this->getAppModel('RightModel', 'Right');
	}

	/**
	 * @param RightModel $right
	 * @return bool|\ReflectionClass
	 */
	public function controllerExists(RightModel $right)
	{
		if ($right->getPrefix() == '')
		{
			$controller = '\\App\Modules\\' . ucfirst($right->getModule()) . '\\Controller\\' . ucfirst($right->getController());
		}
		else
		{
			$controller = '\\App\Modules\\' . ucfirst($right->getPrefix()) . '\\' . ucfirst($right->getModule()) . '\\Controller\\' . ucfirst($right->getController());
		}

		try
		{
			$reflection = new \ReflectionClass($controller);
		}
		catch (Exception $e)
		{
			SystemMessages::addError($e->getMessage());

			return false;
		}

		return $reflection;
	}

	/**
	 * Zunächst wird das Recht aktualisiert und danach geprüft ob der Benutzer das Recht besitzt.
	 *
	 * @param RightModel $rightModel Das zu prüfende Recht
	 * @param UserModel  $userModel  Der Benutzer
	 *
	 * @return bool
	 */
	public function isAllowed(RightModel $rightModel, UserModel $userModel)
	{
		if (!defined("UNITTEST"))
		{
			$reflection = $this->controllerExists($rightModel);

			if ($reflection === false)
			{
				return false;
			}

			$properties = $reflection->getDefaultProperties();

			if ($properties['checkPermissions'] == false || in_array($rightModel->getAction(), $properties['noPermissionActions']))
			{
				return true;
			}
		}

		$this->createRightEx($rightModel);

		$registry = Registry::getInstance();
		$config = $registry->conf;

		if ($userModel->isAdmin() || !defined('CHECK_PERMISSIONS') || ($config->APPLICATION_ENV == ENV_DEV && $config->CHECK_PERMISSIONS == false))
		{
			return true;
		}

		return $this->isAllowedCheckUser($rightModel, $userModel);
	}

	/**
	 * Erstellt ein neues Recht. Falls es schon existiert wird die "Modified"-Eigenschaft aktualisiert
	 *
	 * @param RightModel $rightModel
	 * @param bool       $force
	 * @return bool
	 */
	public function createRight(RightModel $rightModel, $force=false)
	{
		try
		{
			return $this->createRightEx($rightModel, $force);
		}
		catch (\Exception $e)
		{
			SystemMessages::addError($e->getMessage());
		}
	}

	/**
	 * @param RightModel $rightModel
	 * @param bool|false $force
	 * @return bool
	 * @throws Exception
	 */
	private function createRightEx(RightModel $rightModel, $force = false)
	{
		$toCheck = strtolower('setright' . $rightModel->getModule() . ':' . $rightModel->getController() . ':' . $rightModel->getAction() . ':' . $rightModel->getPrefix());

		$registry = Registry::getInstance();
		$sess = $registry->getSession();
		$config = $registry->conf;

		if (!$force && $sess->has($toCheck))
		{
			return true;
		}
		$sess->set($toCheck, 1);

		try
		{
			$actionInfo = $this->getActionName($rightModel->getModule(), $rightModel->getController(), $rightModel->getAction(), $rightModel->getPrefix(), $force);
			$actionName = isset($actionInfo['actionName']) ? $actionInfo['actionName'] : '';
			$moduleTitle = isset($actionInfo['modulTitle']) ? $actionInfo['modulTitle'] : '';
			$controllerTitle = isset($actionInfo['title']) ? $actionInfo['title'] : '';
		} catch (\Exception $e)
		{
			$actionName = '';
		}
		if (empty($actionName) && $config->APPLICATION_ENV == ENV_DEV && !defined("UNITTEST"))
		{
			$prefixSlash = '';
			$pre = $rightModel->getPrefix();

			if (!empty($pre))
			{
				$prefixSlash .= $pre . "\\";
			}

			$class = "\\App\\Modules\\" . ucfirst($prefixSlash) . ucfirst($rightModel->getModule()) . "\\Controller\\" . ucfirst($rightModel->getController());

			throw new Exception('@actionName in der Action des Controllers "' . $class . '" -> "' . $rightModel->getAction() . '" nicht gesetzt!');
		}

		$rightModel->setModuletitle($moduleTitle);
		$rightModel->setControllertitle($controllerTitle);

		$data = $rightModel->getDataRow();

		$saveModel = $this->save($data);

		return $saveModel ? true : false;
	}

	/**
	 * @param string  $module
	 * @param  string $controller
	 * @param string  $action
	 * @param string  $prefix
	 * @param bool    $force
	 * @return string
	 */
	protected function getActionName($module, $controller, $action, $prefix = '', $force = false)
	{
		$registry = Registry::getInstance();
		$toCheck = strtolower('getActionName:' . "$module:$controller:$action:$prefix");

		/** @var $sess    \jamwork\common\Session */
		$sess = $registry->getSession();
		if (!$force && $sess->has($toCheck))
		{
			return $sess->get($toCheck);
		}
		$retArray = array();

		$prefixSlash = '';
		if (!empty($prefix))
		{
			$prefixSlash .= $prefix . "\\";
		}

		$class = "\\App\\Modules\\" . ucfirst($prefixSlash) . ucfirst($module) . "\\Controller\\" . ucfirst($controller);
		$reflect = new \ReflectionClass($class);

		$classDoc = $reflect->getDocComment();
		if ($classDoc !== false)
		{
			preg_match('/.*\@title([A-Za-z0-9äöüÄÖÜ\: \-\s\t]+).*/s', $classDoc, $matchClassDoc);
			if (!empty($matchClassDoc))
			{
				$retArray['title'] = trim($matchClassDoc[1]);
			}
			preg_match('/.*\@modulTitle([A-Za-z0-9äöüÄÖÜ\: \-\s\t]+).*/s', $classDoc, $matchClassDoc);
			if (!empty($matchClassDoc))
			{
				$retArray['modulTitle'] = trim($matchClassDoc[1]);
			}
			else{
				preg_match('/.*\@moduleTitle([A-Za-z0-9äöüÄÖÜ\: \-\s\t]+).*/s', $classDoc, $matchClassDoc);
				if (!empty($matchClassDoc))
				{
					$retArray['modulTitle'] = trim($matchClassDoc[1]);
				}
			}
		}

		/** Methoden auslesen */
		$methods = $reflect->getMethods();
		foreach ($methods as $method)
		{
			/** Prüfe ob eine Methode eine HTML-Action ist */
			preg_match("/(.+)(HTML|Html|JSON|Json)Action/", $method->getName(), $matches);
			if (!empty($matches))
			{
				/** Lade den Kommentar */
				$docComment = $method->getDocComment();
				$retArray['actionName'] = '';
				if ($docComment !== false)
				{
					/** Hold den ActionName um in der Rechteverwaltung einen schönen titel zu haben */
					preg_match('/.*\@actionName([A-Za-z0-9äöüÄÖÜ\: \/\-\s\t]+).*/s', $docComment, $matchDoc);

					if (!empty($matchDoc))
					{
						/** Name des Aktion ermitteln */
						$toCheck = strtolower('getActionName:' . "$module:$controller:" . $matches[1] . ":$prefix");
						$retArray['actionName'] = trim($matchDoc[1]);
						$sess->set($toCheck, $retArray);
					}
				}
			}
		}

		$toCheck = strtolower('getActionName:' . "$module:$controller:$action:$prefix");
		if ($sess->has($toCheck))
		{
			return $sess->get($toCheck);
		}

		return $retArray;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected function verifySaveData(array $data, $forSave = true)
	{
		if (!$forSave)
		{
			return $data;
		}
		/** @var RightModel $checkModel */
		$checkModel = $this->getModelFromArray($data);

		$rightModel = $this->checkRightExists($checkModel);

		if ($rightModel)
		{
			$data['rig_id'] = $rightModel->getId();
		}

		return $data;
	}

	/**
	 * @param RightModel $rightModel
	 * @return bool|\Corework\Application\Interfaces\ModelsInterface
	 */
	private function checkRightExists(RightModel $rightModel)
	{
		$mod = $this->getAppModel('RightModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('rig_module', $rightModel->getModule());
		$query->addWhere('rig_controller', $rightModel->getController());
		$query->addWhere('rig_action', $rightModel->getAction());
		$query->addWhere('rig_prefix', $rightModel->getPrefix());

		return $this->getBaseManager()->getModelByQuery($this->getAppModelName('RightModel', 'Right'), $query);
	}

	/**
	 * @param RightModel $rightModel
	 * @param UserModel  $userModel
	 * @return bool
	 */
	private function isAllowedCheckUser(RightModel $rightModel, UserModel $userModel)
	{
		$mod = $this->getAppModel('RightModel', 'Right');
		$modU = $this->getAppModel('UserModel');
		$modRgr = $this->getAppModel('RightGroupRights', 'Right');
		$modRgu = $this->getAppModel('RightGroupUsers', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('usr_id');
		$query->from($modU->getTableName());
		$query->innerJoin($modRgu->getTablename());
		$query->on('usr_id = rgu_user_id');
		$query->innerJoin($modRgr->getTableName());
		$query->on('rgr_group_id = rgu_group_id');
		$query->innerJoin($mod->getTableName());
		$query->on('rig_id = rgr_right_id');
		$query->addWhere('usr_id', $userModel->getId());
		$query->addWhere('rig_module', $rightModel->getModule());
		$query->addWhere('rig_controller', $rightModel->getController());
		$query->addWhere('rig_action', $rightModel->getAction());
		$query->addWhere('rig_prefix', $rightModel->getPrefix());

		$result = $this->getBaseManager()->getArrayByQuery($query);

		return ($result && $result[0]['usr_id']) ? true : false;
	}

	/**
	 * Liefert alle Rechte
	 *
	 * @return array of RightModel
	 */
	public function getAllRights()
	{
		$mod = $this->getAppModel('RightModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTablename());
		$query->orderBy('rig_prefix, rig_module, rig_controller, rig_action ASC');

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('RightModel', 'Right'), $query);
	}

	/**
	 * Aktualisiert ein Recht
	 *
	 * @param RightModel $rightModel Das zu aktualisierende Recht
	 *
	 * @return boolean
	 */
	public function update(RightModel $rightModel)
	{
		$data = $rightModel->getDataRow();

		return $this->save($data);
	}

	/**
	 * Liefert alle Rechte einer Rolle
	 *
	 * @param integer $groupId ID der zugehörigen Gruppe
	 * @return array of RightModel
	 */
	public function getRightsByGroupId($groupId)
	{
		$mod = $this->getAppModel('RightModel', 'Right');
		$modRgr = $this->getAppModel('RightGroupRightsModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->innerJoin($modRgr->getTableName());
		$query->on('rig_id = rgr_right_id');
		$query->addWhere('rgr_group_id', $groupId);

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('RightModel', 'Right'), $query);
	}

	/**
	 * Liefert alle Rechte
	 *
	 * @param array $ids Array mit den IDs der zu liefernden Rechte
	 *
	 * @return array of RightModel
	 */
	public function getRightsByMultipleIds(array $ids)
	{
		$mod = $this->getAppModel('RightModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('rig_id', $ids);
		$query->orderBy('rig_module');

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('RightModel', 'Right'), $query);
	}
}