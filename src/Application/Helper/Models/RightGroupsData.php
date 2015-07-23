<?php

namespace Corework\Application\Helper\Models;

use Corework\Application\Models\Right\RightGroupModel;

/**
 * Class RightGroupsData
 *
 * @category Dreiwerken
 * @package  App\Helper\Models
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
class RightGroupsData
{
	/** @var array of UserModel */
	protected $users = array();

	/** @var array of RightModel */
	protected $rights = array();

	/** @var RightGroupModel */
	protected $rightGroupModel = null;

	/**
	 * @return array
	 */
	public function getUsers()
	{
		return $this->users;
	}

	/**
	 * @param array $users
	 */
	public function setUsers($users)
	{
		$this->users = $users;
	}

	/**
	 * @return array
	 */
	public function getRights()
	{
		return $this->rights;
	}

	/**
	 * @param array $rights
	 */
	public function setRights($rights)
	{
		$this->rights = $rights;
	}

	/**
	 * @return RightGroupModel
	 */
	public function getRightGroupModel()
	{
		return $this->rightGroupModel;
	}

	/**
	 * @param RightGroupModel $rightGroupModel
	 */
	public function setRightGroupModel($rightGroupModel)
	{
		$this->rightGroupModel = $rightGroupModel;
	}
}