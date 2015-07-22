<?php

namespace Corework\Application\Models\Right;

use App\Helper\Models\RightGroupsData;
use App\Manager\Right\RightManager;
use App\Manager\UserManager;
use App\Models\Right\RightGroupModel;
use App\Models\UserModel;
use Corework\Application\Abstracts\Manager;

/**
 * Class RightGroupManager
 *
 * @category Corework
 * @package  Corework\Application\Models\Right
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
class RightGroupManager extends Manager
{

	/** @var RightManager */
	private $rightManager = null;

	/** @var UserManager */
	private $userManager = null;

	/**
	 * @return UserManager
	 */
	private function getUserManager()
	{
		if (is_null($this->userManager))
		{
			$this->userManager = new UserManager();
		}

		return $this->userManager;
	}

	/**
	 * @return \App\Manager\RightManager
	 */
	private function getRightManager()
	{
		if (is_null($this->rightManager))
		{
			$this->rightManager = new RightManager();
		}

		return $this->rightManager;
	}

	/**
	 * @return RightGroupModel
	 */
	protected function getNewModel()
	{
		return new RightGroupModel();
	}

	/**
	 * Liefert alle Rechtegruppen
	 *
	 * @return array of RightGroupModel
	 */
	public function getAllGroups()
	{
		$mod = $this->getAppModel('RigthtGroupModel', 'Right');

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
		$mod = $this->getAppModel('RigthtGroupModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('gro_id', $id);

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
		$mod = $this->getAppModel('RigthtGroupModel', 'Right');

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
		$mod = $this->getAppModel('RigthtGroupModel', 'Right');

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
		$mod = $this->getAppModel('RigthtGroupModel', 'Right');
		$modRgu = $this->getAppModel('RigthtGroupUsersModel', 'Right');

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

		$rightGroupData = new RightGroupsData();
		$rightGroupData->setRightGroupModel($rightGroupModel);
		$rightGroupData->setRights(($rights ? $rights : array()));
		$rightGroupData->setUsers(($users ? $users : array()));

		return $rightGroupData;
	}
}