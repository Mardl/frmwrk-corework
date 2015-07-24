<?php

namespace Corework\Application\Manager\Right;

use App\Models\Right\RightGroupModel;
use App\Models\UserModel;
use Corework\Application\Abstracts\Manager;
use Corework\Application\Helper\Models\RightGroupsData;
use Corework\Application\Interfaces\ModelsInterface;
use Corework\SystemMessages;
use jamwork\common\Registry;

/**
 * Class RightGroupManager
 *
 * @category Corework
 * @package  Corework\Application\Models\Right
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
abstract class RightGroupManager extends Manager
{

	/** @var RightManager */
	private $rightManager = null;

	/** @var \App\Manager\UserManager */
	private $userManager = null;

	/** @var \App\Manager\Right\RightGroupRightsManager */
	private $rightGroupRightsManager = null;

	/** @var \App\Manager\Right\RightGroupUsersManager */
	private $rightGroupUsersManager = null;

	/**
	 * @return RightGroupModel
	 */
	protected function getNewModel()
	{
		return $this->getAppModel('RightGroupModel', 'Right');
	}

	/**
	 * @return RightGroupRightsManager
	 */
	protected function getRightGroupRightsManager()
	{
		if (is_null($this->rightGroupRightsManager))
		{
			$this->rightGroupRightsManager = new \App\Manager\Right\RightGroupRightsManager();
		}

		return $this->rightGroupRightsManager;
	}

	/**
	 * @return RightGroupUsersManager
	 */
	protected function getRightGroupUsersManager()
	{
		if (is_null($this->rightGroupUsersManager))
		{
			$this->rightGroupUsersManager = new \App\Manager\Right\RightGroupUsersManager();
		}

		return $this->rightGroupUsersManager;
	}

	/**
	 * @return \App\Manager\UserManager
	 */
	protected function getUserManager()
	{
		if (is_null($this->userManager))
		{
			$this->userManager = new \App\Manager\UserManager();
		}

		return $this->userManager;
	}

	/**
	 * @return \App\Manager\Right\RightManager
	 */
	protected function getRightManager()
	{
		if (is_null($this->rightManager))
		{
			$this->rightManager = new \App\Manager\Right\RightManager();
		}

		return $this->rightManager;
	}

	/**
	 * @param \Corework\Application\Interfaces\ModelsInterface $model
	 * @return array
	 */
	protected function verifyBevorSave(ModelsInterface $model)
	{
		try
		{
			$found = $this->getGroupByName($model->getName());
		} catch (\Exception $e)
		{
			$found = false;
		}
		if ($model->getId() == 0 && $found)
		{
			// Neuanlage, und Name bereits vorhanden !
			SystemMessages::addError('Rolle bereits vorhanden');
			return false;
		}
		if ($model->getId() > 0 && $found && $found->getRightGroupModel()->getId() != $model->getId())
		{
			// Update und Name bereits vorhanden !
			SystemMessages::addError('Rolle bereits vorhanden');
			return false;
		}
		return true;
	}

	/**
	 * Liefert alle Rechtegruppen
	 *
	 * @return array of RightGroupModel
	 */
	public function getAllGroups()
	{
		$mod = $this->getAppModel('RightGroupModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->orderBy('gro_name');

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('RightGroupModel', 'Right'), $query);
	}

	/**
	 * @param int  $id
	 * @param bool $rights
	 * @param bool $users
	 * @return RightGroupsData
	 */
	public function getGroupById($id, $rights = false, $users = false)
	{
		$rightGroupModel = $this->getById($id);

		/** @var RightGroupModel $rightGroupModel */
		if ($rightGroupModel)
		{
			if ($rights)
			{
				$rights = $this->getRightManager()->getRightsByGroupId($rightGroupModel->getId());
			}

			if ($users)
			{
				$users = $this->getUserManager()->getUsersByGroupId($rightGroupModel->getId());
			}
		}

		$rightGroupData = new RightGroupsData();
		$rightGroupData->setRightGroupModel($rightGroupModel);
		$rightGroupData->setRights(($rights ? $rights : array()));
		$rightGroupData->setUsers(($users ? $users : array()));

		return $rightGroupData;
	}

	/**
	 * @param array $ids
	 * @param bool  $rights
	 * @param bool  $users
	 * @return array of RightGroupsData
	 */
	public function getGroupsByIds(array $ids, $rights = false, $users = false)
	{
		$mod = $this->getAppModel('RightGroupModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('gro_id', $ids);

		$rightGroups = $this->getBaseManager()->getModelsByQuery($this->getAppModelName('RightGroupModel', 'Right'), $query);

		$groupData = array();

		foreach ($rightGroups as $rightGroupModel)
		{
			if ($rights)
			{
				$rights = $this->getRightManager()->getRightsByGroupId($rightGroupModel->getId());
			}

			if ($users)
			{
				$users = $this->getUserManager()->getUsersByGroupId($rightGroupModel->getId());
			}

			$rightGroupData = new RightGroupsData();
			$rightGroupData->setRightGroupModel($rightGroupModel);
			$rightGroupData->setRights(($rights ? $rights : array()));
			$rightGroupData->setUsers(($users ? $users : array()));

			$groupData[] = $rightGroupData;
		}

		return $groupData;
	}

	/**
	 * @param string $name
	 * @param bool   $rights
	 * @param bool   $users
	 * @return RightGroupsData
	 */
	public function getGroupByName($name, $rights = false, $users = false)
	{
		$mod = $this->getAppModel('RightGroupModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('gro_name', $name);


		$rightGroupModel = $this->getBaseManager()->getModelByQuery($this->getAppModelName('RightGroupModel', 'Right'), $query);

		/** @var RightGroupModel $rightGroupModel */
		if ($rightGroupModel)
		{
			if ($rights)
			{
				$rights = $this->getRightManager()->getRightsByGroupId($rightGroupModel->getId());
			}

			if ($users)
			{
				$users = $this->getUserManager()->getUsersByGroupId($rightGroupModel->getId());
			}
		}
		else{
			throw new \ErrorException("Rolle mit dem Namen: $name nicht gefunden!");
		}

		$rightGroupData = new RightGroupsData();
		$rightGroupData->setRightGroupModel($rightGroupModel);
		$rightGroupData->setRights(($rights ? $rights : array()));
		$rightGroupData->setUsers(($users ? $users : array()));

		return $rightGroupData;
	}

	/**
	 * @param UserModel $user
	 * @param bool      $rights
	 * @param bool      $users
	 * @return RightGroupsData
	 */
	public function getGroupsByUser(UserModel $user, $rights = false, $users = false)
	{
		$mod = $this->getAppModel('RightGroupModel', 'Right');
		$modRgu = $this->getAppModel('RightGroupUsersModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->innerJoin($modRgu->getTableName());
		$query->on('gro_id = rgu_rightgroup_id');
		$query->addWhere('rgu_user_id', $user->getId());

		$rightGroupModel = $this->getBaseManager()->getModelByQuery($this->getAppModelName('RightGroupModel', 'Right'), $query);

		/** @var RightGroupModel $rightGroupModel */
		if ($rightGroupModel)
		{
			if ($rights)
			{
				$rights = $this->getRightManager()->getRightsByGroupId($rightGroupModel->getId());
			}

			if ($users)
			{
				$users = $this->getUserManager()->getUsersByGroupId($rightGroupModel->getId());
			}
		}
		else
		{
			$rightGroupModel = new RightGroupModel();
		}

		$rightGroupData = new RightGroupsData();
		$rightGroupData->setRightGroupModel($rightGroupModel);
		$rightGroupData->setRights(($rights ? $rights : array()));
		$rightGroupData->setUsers(($users ? $users : array()));

		return $rightGroupData;
	}

	/**
	 * Aktualisiert eine Gruppe
	 *
	 * @param RightGroupsData $group       Zu aktualisierende Gruppe
	 * @param bool            $forceRights Rechte erzwingen
	 * @param bool            $forceUser   Benutzer erzwingen
	 *
	 * @return bool
	 */
	public function updateGroup(RightGroupsData $group, $forceRights = false, $forceUser = false)
	{
		$updated = $this->save($group->getRightGroupModel()->getDataRow());

		if (!$updated)
		{
			SystemMessages::addError('Beim Update der Rolle ist ein Fehler aufgetreten!');

			return false;
		}

		$rights = $group->getRights();

		if (!empty($rights) || $forceRights)
		{
			if (!$this->getRightGroupRightsManager()->updateGroupRights($group))
			{
				SystemMessages::addError('Beim Update der Rechte ist ein Fehler aufgetreten!');

				return false;
			}
		}

		$users = $group->getUsers();

		if (!empty($users) || $forceUser)
		{
			if (!$this->getRightGroupUsersManager()->updateGroupUsers($group))
			{
				SystemMessages::addError('Beim Update der Mitglieder ist ein Fehler aufgetreten!');

				return false;
			}
		}

		return true;
	}

	/**
	 * @return array
	 */
	public function getAll()
	{
		$mod = $this->getAppModel('RightGroupModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('RightGroupModel', 'Right'), $query);
	}

	/**
	 * @return array
	 */
	public function getAllAsArray()
	{
		$allGroups = $this->getAll();

		$groupArray = array();

		foreach ($allGroups as $group)
		{
			$groupArray[$group->getId()] = $group;
		}

		return $groupArray;
	}

	/**
	 * Fügt einen einzelnen Benutzer zur Gruppe hinzu
	 *
	 * @param RightGroupModel $group Betreffende Gruppe
	 * @param UserModel  $user  Betreffender Benutzer
	 * @return mixed
	 */
	public function addUserToGroup(RightGroupModel $group, UserModel $user)
	{
		$data = array(
			'rgu_rightgroup_id' => $group->getId(),
			'rgu_user_id' => $user->getId(),
		);

		$saveModel = $this->save($data);

		return $saveModel->getId() > 0 ? true : false;
	}

	/**
	 * @param array $user
	 * @return array
	 */
	public function getGroupsFromAllUser(array $user)
	{
		$rights = array();
		/** @var UserModel $userModel */
		foreach($user as $userModel)
		{
			$groups = $this->getRightGroupUsersManager()->getAllByUserId($userModel->getId());

			$rights[$userModel->getId()] = $groups;

		}

		return $rights;
	}

	/**
	 * @param int $userId
	 * @return array
	 */
	public function getByUserId($userId)
	{
		$mod = $this->getAppModel('RightGroupModel', 'Right');
		$modRgu = $this->getAppModel('RightGroupUsersModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->leftJoin($modRgu->getTableName());
		$query->on('gro_id = rgu_rightgroup_id');
		$query->addWhere('rgu_user_id', $userId);

		$ret = $this->getBaseManager()->getArrayByQuery($query);

		$resultArray = array();
		foreach ($ret as $row)
		{
			$group = new RightGroupModel();
			$clearStd = $group->clearDataRow($row);
			$group->setDataRow($clearStd);

			$resultArray[$row['gro_id']] = $group;
		}

		return $resultArray;
	}

	/**
	 * @param int $groupId
	 * @param int $userId
	 * @return bool
	 */
	public function addGroupsUser($groupId , $userId)
	{
		$group = $this->getGroupById($groupId, false, true);
		$user = $this->getUserManager()->getById($userId);

		$groupUsers = $group->getUsers();
		$check = true;

		foreach ($groupUsers as $gUser)
		{
			if ($gUser == $user)
			{
				$check = false;
			}
		}

		$saveOk = true;
		if ($check)
		{
			$groupUsers[] = $user;
			$group->setUsers($groupUsers);

			$this->getBaseManager()->getConnection()->startTransaction();
			try
			{
				if (!$this->updateGroup($group, false, true))
				{
					$saveOk = false;
				}
			} catch (\Exception $e)
			{
				\Corework\SystemMessages::addError($e->getMessage(), array('exception' => $e));
				$log = Registry::getInstance()->getLogger($this);
				$log->fatal($e->getMessage());
				$saveOk = false;
			}

			if ($saveOk === false)
			{
				$this->getBaseManager()->getConnection()->rollback();
			}
			else
			{
				$this->getBaseManager()->getConnection()->commit();
			}
		}
		return $saveOk;
	}

	/**
	 * @param int $groupId
	 * @param int $userId
	 * @return bool
	 */
	public function subGroupsUser($groupId , $userId)
	{
		$group = $this->GetGroupById($groupId, false, true);
		$user = $this->getUserManager()->getById($userId);

		$groupUsers = $group->getUsers();
		$newUsers = array();

		foreach ($groupUsers as $gUser)
		{
			if ($gUser != $user)
			{
				$newUsers[] = $gUser;
			}
		}

		$group->setUsers($newUsers);
		$saveOk = true;

		$this->getBaseManager()->getConnection()->startTransaction();
		try
		{

			if (!$this->updateGroup($group, false, true))
			{
				$saveOk = false;
			}
		} catch (\Exception $e)
		{
			\Corework\SystemMessages::addError($e->getMessage(), array('exception' => $e));
			$log = Registry::getInstance()->getLogger($this);
			$log->fatal($e->getMessage());
			$saveOk = false;
		}

		if ($saveOk === false)
		{
			$this->getBaseManager()->getConnection()->rollback();
		}
		else
		{
			$this->getBaseManager()->getConnection()->commit();
		}

		return $saveOk;
	}

	/**
	 * @param array $groups
	 * @return array
	 */
	public function getUsersByGroups(array $groups)
	{
		$users = array();
		/** @var RightGroupModel  $groupModel */
		foreach ($groups as $groupModel)
		{
			$userArray = $this->getUserManager()->getUsersByGroupId($groupModel->getId());
			$users[$groupModel->getId()] = $userArray;
		}

		return $users;
	}
}