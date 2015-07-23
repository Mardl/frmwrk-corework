<?php

namespace Corework\Application\Manager\Right;

use App\Models\Right\RightGroupModel;
use App\Models\Right\RightGroupUsersModel;
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
class RightGroupUsersManager extends Manager
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

		/** Gehe die gefundenen Einträge durch und lösche diese  */
		foreach ($allEntries as $rgrModel)
		{
			$success = $success && $this->deleteModel($rgrModel);
		}

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

		return $this->getBaseManager()->getModelsByQuery('\App\Models\Right\RightGroupUsersModel', $query);
	}
}