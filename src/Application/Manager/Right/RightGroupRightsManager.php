<?php

namespace Corework\Application\Manager\Right;

use App\Models\Right\RightGroupModel;
use App\Models\Right\RightGroupRightsModel;
use Corework\Application\Abstracts\Manager;
use Corework\Application\Helper\Models\RightGroupsData;
use Corework\Application\Interfaces\ModelsInterface;

/**
 * Class RightGroupRightsManager
 *
 * @category Corework
 * @package  Corework\Application\Manager\Right
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
abstract class RightGroupRightsManager extends Manager
{

	/**
	 * @return ModelsInterface
	 */
	protected function getNewModel()
	{
		return $this->getAppModel('RightGroupRightsModel', 'Right');
	}

	/**
	 * Aktualisiert die Rechte einer Gruppe
	 *
	 * @param RightGroupsData $group zu aktualisierende Gruppenrechte
	 * @return bool
	 */
	public function updateGroupRights(RightGroupsData $group)
	{
		/** @var RightGroupModel $rightGroupModel */
		$rightGroupModel = $group->getRightGroupModel();
		$rights = $group->getRights();
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
			/** @var RightGroupRightsModel $right */
			foreach ($rights as $right)
			{
				$saveData['rgr_right_id'] = $right->getId();
				$saveData['rgr_rightgroup_id'] = $group->getRightGroupModel()->getId();

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
		$mod = $this->getAppModel('RightGroupRightsModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('rgr_rightgroup_id', $groupId);

		return $this->getBaseManager()->getModelsByQuery('\App\Models\Right\RightGroupRightsModel', $query);
	}
}