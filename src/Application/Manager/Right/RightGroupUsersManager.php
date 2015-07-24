<?php

namespace Corework\Application\Manager\Right;

use App\Models\Right\RightGroupModel;
use App\Models\Right\RightGroupUsersModel;
use App\Models\UserModel;
use Corework\Application\Abstracts\Manager;
use Corework\Application\Helper\Models\RightGroupsData;
use Corework\Application\Interfaces\ModelsInterface;

/**
 * Class RightGroupUsersManager
 *
 * @category Corework
 * @package  Corework\Application\Manager\Right
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
abstract class RightGroupUsersManager extends Manager
{
	/**
	 * @return ModelsInterface
	 */
	protected function getNewModel()
	{
		return $this->getAppModel('RightGroupUsersModel', 'Right');
	}

	/**
	 * Aktualisiert die Mitglieder einer Gruppe
	 *
	 * @param RightGroupsData $group zu aktualisierende Gruppe
	 *
	 * @return bool
	 */
	public function updateGroupUsers(RightGroupsData $group)
	{
		/** @var RightGroupModel $rightGroupModel */
		$rightGroupModel = $group->getRightGroupModel();
		$users = $group->getUsers();
		$success = true;

		/** Hole alle vorhandenen Einträge dieser Gruppe raus  */
		$allEntries = $this->getAllByGroupId($rightGroupModel->getId());

		/** Starte Transaktion */
		$this->getBaseManager()->getConnection()->startTransaction();

		$success = $this->clearExistingEntries($allEntries);

		/** Wenn das Löschen ohne Fehler ablief, dann speichere die neue Rechtevergabe  */
		if ($success)
		{
			/** @var RightGroupUsersModel $user */
			foreach ($users as $user)
			{
				$saveData['rgu_user_id'] = $user->getId();
				$saveData['rgu_rightgroup_id'] = $group->getRightGroupModel()->getId();

				$success = $success && $this->save($saveData);
			}
		}

		if (!$success)
		{
			$this->getBaseManager()->getConnection()->rollback();
			return false;
		}
		else
		{
			$this->getBaseManager()->getConnection()->commit();
			return true;
		}
	}

	/**
	 * @param array $allEntries
	 * @return bool
	 */
	public function clearExistingEntries(array $allEntries)
	{
		$success = true;

		/** Gehe die gefundenen Einträge durch und lösche diese  */
		foreach ($allEntries as $rgrModel)
		{
			$success = $success && $this->deleteModel($rgrModel);
		}

		return $success;
	}

	/**
	 * @param int $groupId
	 * @return array
	 */
	private function getAllByGroupId($groupId)
	{
		$mod = $this->getAppModel('RightGroupUsersModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('rgu_rightgroup_id', $groupId);

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('RightGroupUsersModel', 'Right'), $query);
	}

	/**
	 * @param int $userId
	 * @return array
	 */
	public function getAllByUserId($userId)
	{
		$mod = $this->getAppModel('RightGroupUsersModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('rgu_user_id', $userId);

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('RightGroupUsersModel', 'Right'), $query);
	}

	/**
	 * @param int $userId
	 * @return array
	 */
	public function getAllRightGroupsFromUser($userId)
	{
		$allFromUser = $this->getAllByUserId($userId);

		$userGroups = array();

		/** @var \App\Models\Right\RightGroupUsersModel $rightGroupsUserModel */
		foreach ($allFromUser as $rightGroupsUserModel)
		{
			$userGroups[$rightGroupsUserModel->getRightgroup_id()] = $rightGroupsUserModel;
		}

		return $userGroups;
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
}