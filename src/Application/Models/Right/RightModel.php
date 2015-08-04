<?php

namespace Corework\Application\Models\Right;

/**
 * Class RightModel
 *
 * @category Corework
 * @package  Corework\Application\Models\Right
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 *
 * @MappedSuperclass
 * @Prefix(name="rig_")
 */
class RightModel extends \Corework\Model
{
	/**
	 * Id
	 *
	 * @var integer
	 *
	 * @Id
	 * @Column(type="integer", name="rig_id")
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Module
	 *
	 * @var string
	 *
	 * @Column(type="string", name="rig_title", options={"default" = ""})
	 */
	protected $title;

	/**
	 * Module
	 *
	 * @var string
	 *
	 * @Column(type="string", name="rig_moduletitle", options={"default" = ""})
	 */
	protected $moduletitle;

	/**
	 * Controller
	 *
	 * @var string
	 *
	 * @Column(type="string", name="rig_controllertitle", options={"default" = ""})
	 */
	protected $controllertitle;

	/**
	 * Module
	 *
	 * @var string
	 *
	 * @Column(type="string", name="rig_module", options={"default" = ""})
	 */
	protected $module;

	/**
	 * Controller
	 *
	 * @var string
	 *
	 * @Column(type="string", name="rig_controller", options={"default" = ""})
	 */
	protected $controller;

	/**
	 * Action
	 *
	 * @var string
	 *
	 * @Column(type="string", name="rig_action", options={"default" = ""})
	 */
	protected $action;

	/**
	 * Prefix
	 *
	 * @var string
	 *
	 * @Column(type="string", name="rig_prefix", options={"default" = ""})
	 */
	protected $prefix = '';

	/**
	 * Inaktiv
	 *
	 * @var bool
	 *
	 * @Column(type="boolean", name="rig_inactive", options={"default" = 0})
	 */
	protected $inactive = 0;

	/**
	 * Created (Registration date)
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime", name="rig_created", nullable=true)
	 */
	protected $created;

	/**
	 * Modified User Id
	 *
	 * @var \int
	 *
	 * @ManyToOne(targetEntity="App\Models\UserModel")
	 * @JoinColumn(name="rig_createduser_id", referencedColumnName="usr_id", nullable=true)
	 */
	protected $createduser_id = null;

	/**
	 * Modified Datum
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime", name="rig_modified", nullable=true)
	 */
	protected $modified = null;

	/**
	 * Modified User Id
	 *
	 * @var \int
	 *
	 * @ManyToOne(targetEntity="App\Models\UserModel")
	 * @JoinColumn(name="rig_modifieduser_id", referencedColumnName="usr_id", nullable=true)
	 */
	protected $modifieduser_id = null;

	/**
	 * @return string
	 */
	public function getModuletitle()
	{
		return $this->moduletitle;
	}

	/**
	 * @param string $moduletitle
	 * @return void
	 */
	public function setModuletitle($moduletitle)
	{
		$this->set('moduletitle', $moduletitle);
	}

	/**
	 * @return string
	 */
	public function getControllertitle()
	{
		return $this->controllertitle;
	}

	/**
	 * @param string $controllertitle
	 * @return void
	 */
	public function setControllertitle($controllertitle)
	{
		$this->set('controllertitle', $controllertitle);
	}

	/**
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * @param string $module
	 * @return void
	 */
	public function setModule($module)
	{
		$this->set('module', $module);
	}

	/**
	 * @return string
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * @param string $controller
	 * @return void
	 */
	public function setController($controller)
	{
		$this->set('controller', $controller);
	}

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param string $action
	 * @return void
	 */
	public function setAction($action)
	{
		$this->set('action', $action);
	}

	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 * @return void
	 */
	public function setPrefix($prefix)
	{
		$this->set('prefix', $prefix);
	}

	/**
	 * @return boolean
	 */
	public function isInactive()
	{
		return $this->inactive == 1;
	}

	/**
	 * @return boolean
	 */
	public function getInactive()
	{
		return $this->inactive;
	}

	/**
	 * @param boolean $inactive
	 * @return void
	 */
	public function setInactive($inactive)
	{
		$this->set('inactive', $inactive);
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title)
	{
		$this->set('title', $title);
	}

	/**
	 * @return array
	 */
	public function getDataRow()
	{
		$data = array(
			'rig_id' => $this->getId(),
			'rig_title' => $this->getTitle(),
			'rig_moduletitle' => $this->getModuletitle(),
			'rig_controllertitle' => $this->getControllertitle(),
			'rig_module' => $this->getModule(),
			'rig_controller' => $this->getController(),
			'rig_action' => $this->getAction(),
			'rig_prefix' => $this->getPrefix(),
			'rig_inactive' => $this->getInactive(),
			'rig_created' => $this->getCreatedAsString(),
			'rig_createduser_id' => $this->getCreateduser_Id(),
			'rig_modified' => $this->getModifiedAsString(),
			'rig_modifieduser_id' => $this->getModifieduser_Id()
		);

		return $data;
	}

}