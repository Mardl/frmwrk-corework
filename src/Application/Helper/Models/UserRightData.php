<?php

namespace Corework\Application\Helper\Models;

use Corework\Application\Models\UserModel;


/**
 * Class UserRightData
 *
 * @category Corework
 * @package  Corework\Application\Helper\Models;
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
class UserRightData
{
	/** @var UserModel */
	private $userModel = null;

	/** @var array of RightGroupModel */
	private $groupModels = array();

	/**
	 * @param array $groupModels
	 * @return void
	 */
	public function setGroupModels($groupModels)
	{
		if ($groupModels === false)
		{
			$groupModels = array();
		}

		$this->groupModels = $groupModels;
	}

	/**
	 * @return array
	 */
	public function getGroupModels()
	{
		return $this->groupModels;
	}

	/**
	 * @param UserModel $userModel
	 * @return void
	 */
	public function setUserModel(UserModel $userModel)
	{
		$this->userModel = $userModel;
	}

	/**
	 * @return UserModel
	 */
	public function getUserModel()
	{
		return $this->userModel;
	}


}