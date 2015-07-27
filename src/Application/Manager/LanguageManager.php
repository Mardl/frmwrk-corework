<?php

namespace Corework\Application\Manager;

use Corework\Application\Abstracts\Manager;
use Corework\Application\Interfaces\ModelsInterface;

/**
 * LanguageManager
 *
 * @category Corework
 * @package  Corework\Application\Manager
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
class LanguageManager extends Manager
{

	/**
	 * @return ModelsInterface
	 */
	protected function getNewModel()
	{
		return $this->getAppModel('LanguageModel');
	}

	/**
	 * Liest alle Sprachen ein
	 *
	 * @return array
	 */
	public function getAllLanguages()
	{
		$mod = $this->getAppModel('LanguageModel');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('LanguageModel'), $query);
	}
}
