<?php

namespace Corework\Application\Models\Right;

/**
 * Class RightGroupUsersModel
 *
 * @category Corework
 * @package  Corework\Application\Models\Right
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 *
 * @MappedSuperclass
 */
class RightGroupUsersModel extends \Corework\Model
{
	/**
	 * Id
	 *
	 * @var integer
	 *
	 * @Id
	 * @Column(type="integer", name="rgu_id")
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * RightGroup Id
	 *
	 * @ManyToOne(targetEntity="App\Models\Right\RightGroupModel")
	 * @JoinColumn(name="rgu_rightgroup_id", referencedColumnName="gro_id", nullable=false)
	 */
	protected $rightgroup_id = null;

	/**
	 * User Id
	 *
	 * @ManyToOne(targetEntity="App\Models\UserModel")
	 * @JoinColumn(name="rgu_user_id", referencedColumnName="usr_id", nullable=false)
	 */
	protected $user_id = null;

	/**
	 * @return mixed
	 */
	public function getRightgroup_id()
	{
		return $this->rightgroup_id;
	}

	/**
	 * @param mixed $rightgroup_id
	 * @return void
	 */
	public function setRightgroup_id($rightgroup_id)
	{
		$this->set('rightgroup_id', $rightgroup_id);
	}

	/**
	 * @return mixed
	 */
	public function getUser_id()
	{
		return $this->user_id;
	}

	/**
	 * @param mixed $user_id
	 * @return void
	 */
	public function setUser_id($user_id)
	{
		$this->set('user_id', $user_id);
	}

	/**
	 * @return array
	 */
	public function getDataRow()
	{
		$data = array(
			'rgu_id' => $this->getId(),
			'rgu_rightgroup_id' => $this->getRightgroup_id(),
			'rgu_user_id' => $this->getUser_id()
		);

		return $data;
	}
}