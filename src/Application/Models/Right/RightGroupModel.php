<?php

namespace Corework\Application\Models\Right;

/**
 * Class RightGroupModel
 *
 * @category Corework
 * @package  Corework\Application\Models\Right
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 *
 * @MappedSuperclass
 */
class RightGroupModel extends \Corework\Model
{
	/**
	 * Id
	 *
	 * @var integer
	 *
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO", name="gro_id")
	 */
	protected $id;

	/**
	 * Module
	 *
	 * @var string
	 *
	 * @Column(type="string", unique=true, name="gro_name")
	 */
	protected $name;

	/**
	 * Created (Registration date)
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime", name="gro_created")
	 */
	protected $created;

	/**
	 * Modified User Id
	 *
	 * @var \int
	 *
	 * @ManyToOne(targetEntity="Corework\Application\Models\UserModel")
	 * @JoinColumn(name="gro_createduser_id", referencedColumnName="usr_id", nullable=true)
	 */
	protected $createduser_id = null;

	/**
	 * Modified Datum
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime", name="gro_modified")
	 */
	protected $modified = null;

	/**
	 * Modified User Id
	 *
	 * @var \int
	 *
	 * @ManyToOne(targetEntity="Corework\Application\Models\UserModel")
	 * @JoinColumn(name="grogro_modifieduser_id", referencedColumnName="usr_id", nullable=true)
	 */
	protected $modifieduser_id = null;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name)
	{
		$this->set('name', $name);
	}

	/**
	 * @return array
	 */
	public function getDataRow()
	{
		$data = array(
			'gro_id' => $this->getId(),
			'gro_name' => $this->getName(),
			'gro_created' => $this->getCreatedAsString(),
			'gro_createduser_id' => $this->getCreateduser_Id(),
			'gro_modified' => $this->getModifiedAsString(),
			'gro_modifieduser_id' => $this->getModifieduser_Id()
		);

		return $data;
	}


}