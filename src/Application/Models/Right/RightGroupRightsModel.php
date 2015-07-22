<?php

namespace Corework\Application\Models\Right;

/**
 * Class RightGroupRightsModel
 *
 * @category Corework
 * @package  Corework\Application\Models\Right
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 *
 * @MappedSuperclass
 */
class RightGroupRightsModel extends \Corework\Model
{
	/**
	 * Id
	 *
	 * @var integer
	 *
	 * @Id
	 * @Column(type="integer", name="rgr_id")
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Right Id
	 *
	 * @ManyToOne(targetEntity="App\Models\Right\RightModel")
	 * @JoinColumn(name="rgr_right_id", referencedColumnName="rig_id", nullable=false)
	 */
	protected $right_id = null;

	/**
	 * RightGroup Id
	 *
	 * @ManyToOne(targetEntity="App\Models\Right\RightGroupModel")
	 * @JoinColumn(name="rgr_rightgroup_id", referencedColumnName="gro_id", nullable=false)
	 */
	protected $rightgroup_id = null;

	/**
	 * @return mixed
	 */
	public function getRight_id()
	{
		return $this->right_id;
	}

	/**
	 * @param mixed $right_id
	 * @return void
	 */
	public function setRight_id($right_id)
	{
		$this->set('right_id', $right_id);
	}

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
	 * @return array
	 */
	public function getDataRow()
	{
		$data = array(
			'rgr_id' => $this->getId(),
			'rgr_right_id' => $this->getRight_id(),
			'rgr_rightgroup_id' => $this->getRightgroup_id()
		);

		return $data;
	}

}