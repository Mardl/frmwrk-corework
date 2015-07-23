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
	 * @return UserManager
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
	 * @param UserModel $user
	 * @param array     $groups
	 * @return bool
	 */
	public function updateUsersGroups(UserModel $user, array $groups)
	{
		$delete = "
			DELETE FROM
				right_group_users
			WHERE
				user_id = %d;
		";
		$insert = "
			INSERT INTO
				right_group_users
				(`group_id`, `user_id`)
			VALUES
				%s;
		";

		$values = array();
		$value = "(%d, %d)";

		/**
		 * mysql_real_escape_string wird hier nicht benötigt, Daten kommen bereits aus der Datenbank
		 *
		foreach ($groups as $group)
		{
		$values[] = sprintf($value, mysql_real_escape_string($group->getId()), mysql_real_escape_string($user->getId()));
		}

		$deleteQuery = sprintf($delete, mysql_real_escape_string($user->getId()));
		 */

		foreach ($groups as $group)
		{
			$values[] = sprintf($value, $group->getId(), $user->getId());
		}

		$deleteQuery = sprintf($delete, $user->getId());

		$insertQuery = '';
		if (!empty($values))
		{
			$insertQuery = sprintf($insert, implode(',', $values));
		}

		$con = Registry::getInstance()->getDatabase();
		$rs = $con->newRecordSet();

		// Starte Transaktion
		$con->startTransaction();

		// Lösche die bestehenden Mitglieder
		$execDelete = $rs->execute($con->newQuery()->setQueryOnce($deleteQuery));

		// Verbinde Mitglieder und Gruppe neu
		if ($execDelete->isSuccessfull())
		{
			if (!empty($values))
			{
				$execInsert = $rs->execute($con->newQuery()->setQueryOnce($insertQuery));
			}
			if (empty($values) || $execInsert->isSuccessfull())
			{
				// Schließe Transaktion erfolgreich ab
				$con->commit();

				return true;
			}
			else
			{
				//Führe Rollback durch
				$con->rollback();

				return false;
			}
		}
		else
		{
			// Führe Rollback durch
			$con->rollback();

			return false;
		}
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
		$con = Registry::getInstance()->getDatabase();

		return $con->insert('right_group_users',
		                    array(
			                    'group_id' => $group->getId(),
			                    'user_id' => $user->getId()
		                    )
		);
	}
}