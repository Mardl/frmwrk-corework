<?php

namespace Corework\Application\Abstracts;

use Corework\Application\Interfaces\ModelsInterface;
use Corework\SystemMessages;
use jamwork\common\Registry;

/**
 * Class Manager
 *
 * @category Corework
 * @package  Corework\Application\Abstracts
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
abstract class Manager
{

	/**
	 * @var \Corework\Application\Manager\Base
	 */
	protected $baseManager = null;

	/**
	 * @return ModelsInterface
	 */
	abstract protected function getNewModel();


	/**
	 * @param \Corework\Application\Manager\Base $base
	 */
	public function __construct(\Corework\Application\Manager\Base $base = null)
	{
		$this->baseManager = (is_null($base)) ? new \Corework\Application\Manager\Base() : $base;
		$this->baseManager->setManager($this);
	}

	/**
	 * @return \Corework\Application\Manager\Base
	 */
	public function getBaseManager()
	{
		return $this->baseManager;
	}


	/**
	 * @param ModelsInterface $model
	 * @param int             $fieldId
	 * @return ModelsInterface
	 */
	public function getModelById(ModelsInterface $model, $fieldId)
	{
		return $this->baseManager->getModelById($model, $fieldId);
	}

	/**
	 * @param int $id
	 *
	 * @return ModelsInterface
	 */
	public function getById($id)
	{
		return $this->baseManager->getModelById($this->getNewModel(), $id);
	}

	/**
	 * Funktion wird vor dem Speichern aufgerufen. In der Regel ist data der $_POST
	 *
	 * @param array $data
	 * @param bool  $forSave
	 * @return array
	 */
	protected function verifySaveData(array $data, $forSave = true)
	{
		return $data;
	}

	/**
	 * @param \Corework\Application\Interfaces\ModelsInterface $model
	 * @return array
	 */
	protected function verifyBevorSave(ModelsInterface $model)
	{
		return true;
	}

	/**
	 * @param \Corework\Application\Interfaces\ModelsInterface $model
	 * @param array                                        $data
	 * @return bool
	 */
	protected function afterSaveAddOn(ModelsInterface $model, array $data)
	{
		return true;
	}

	/**
	 * @param \Corework\Application\Interfaces\ModelsInterface $model
	 * @return bool
	 */
	protected function verifyBevorDelete(ModelsInterface $model)
	{
		return true;
	}

	/**
	 * @param \Corework\Application\Interfaces\ModelsInterface $model
	 * @return bool
	 */
	protected function afterDeleteAddOn(ModelsInterface $model)
	{
		return true;
	}

	/**
	 * @param array           $data
	 * @param ModelsInterface $model
	 * @return ModelsInterface|\Corework\Model
	 */
	public function getModelFromArray(array $data, ModelsInterface $model = null)
	{
		if (is_null($model))
		{
			$model = $this->getNewModel();
		}
		$data = $this->verifySaveData($data, false);
		$toSave = $model->clearDataRow($data);
		$model->setDataRow($toSave);

		return $model;
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function delete($id)
	{
		try
		{
			$model = $this->getById($id);
		} catch (\Exception $e)
		{
			SystemMessages::addError($e->getMessage(), array('exception' => $e));
			return false;
		}

		$this->baseManager->getConnection()->startTransaction();
		try
		{
			$deleteOk = $this->verifyBevorDelete($model);
			$deleteOk = $deleteOk && $this->baseManager->delete($model);
			$deleteOk = $deleteOk && $this->afterDeleteAddOn($model);
		} catch (\Exception $e)
		{
			SystemMessages::addError($e->getMessage(), array('exception' => $e));
			$log = Registry::getInstance()->getLogger($this);
			$log->fatal($e->getMessage());
			$deleteOk = false;
		}

		if ($deleteOk === false)
		{
			$this->baseManager->getConnection()->rollback();
		}
		else
		{
			$this->baseManager->getConnection()->commit();
		}

		return $deleteOk;
	}

	/**
	 * @param array $data
	 * @return ModelsInterface
	 */
	public function save(array $data)
	{
		$data = $this->verifySaveData($data);

		$model = $this->getNewModel();

		$id = isset($data[$model->getIdField()]) ? $data[$model->getIdField()] : 0;
		$neuModel = empty($id);
		if (!$neuModel)
		{
			try
			{
				$model = $this->getById($id);
			} catch (\Exception $e)
			{
				SystemMessages::addError($e->getMessage(), array('exception' => $e));

				return false;
			}
		}


		$toSave = $model->clearDataRow($data);
		$model->setDataRow($toSave);

		$this->baseManager->getConnection()->startTransaction();
		try
		{
			$saveOk = $this->verifyBevorSave($model);

			$saveOk = $saveOk && ($neuModel ? $this->insertModel($model) : $this->updateModel($model));

			if ($saveOk !== false)
			{
				$saveOk = $this->afterSaveAddOn($model, $data);
			}
		} catch (\Exception $e)
		{
			SystemMessages::addError($e->getMessage(), array('exception' => $e));
			$log = Registry::getInstance()->getLogger($this);
			$log->fatal($e->getMessage());
			$saveOk = false;
		}

		if ($saveOk === false)
		{
			$model = $this->getNewModel();
			$this->baseManager->getConnection()->rollback();
		}
		else
		{
			$this->baseManager->getConnection()->commit();
		}

		return $model;
	}

	/**
	 * @param ModelsInterface $model
	 * @return bool|int
	 */
	public function insertModel(ModelsInterface $model)
	{
		$saveId = $this->getBaseManager()->insert($model);
		$model->setId($saveId);

		return $saveId;
	}

	/**
	 * @param ModelsInterface $model
	 * @return bool|int
	 */
	public function deleteModel(ModelsInterface $model)
	{
		return $this->getBaseManager()->delete($model);
	}

	/**
	 * @param ModelsInterface $model
	 * @return bool|int
	 */
	public function updateModel(ModelsInterface $model)
	{
		return $this->getBaseManager()->update($model);
	}

	/**
	 * @param string $appModel
	 * @param string $directory
	 * @return ModelsInterface
	 */
	public function getAppModel($appModel, $directory = '')
	{
		$model = $this->getAppModelName($appModel, $directory);
		return new $model();
	}

	/**
	 * @param string $appModel
	 * @param string $directory
	 * @return ModelsInterface
	 */
	protected function getAppModelName($appModel, $directory = '')
	{
		return "\\App\\Models\\" . (($directory) ? $directory."\\" : "") . ucfirst($appModel);
	}

}