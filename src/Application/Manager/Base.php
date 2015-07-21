<?php

namespace Corework\Application\Manager;

use Corework\Exceptions\DatabaseException;
use jamwork\common\Registry;
use jamwork\database\Query;
use Corework\SystemMessages;
use Corework\Application\Interfaces\ModelsInterface;

/**
 * Class Base
 * @package Corework\Application\Manager
 */
class Base
{
	/**
	 * Event bevor das Model gespeichert wird
	 */
	const EVENT_BEFORE_SAVE = 'onBeforeSaveModel';

	/**
	 * Event nachdem das Model gespeichert wurde
	 */
	const EVENT_AFTER_SAVE = 'onAfterSaveModel';

	/**
	 * Event bevor das Model gelöscht wird
	 */
	const EVENT_BEFORE_DELETE = 'onBeforeDeleteModel';

	/**
	 * Event nachdem das Model gelöscht wurde
	 */
	const EVENT_AFTER_DELETE = 'onAfterDeleteModel';

	/**
	 * Event-Types
	 */
	const EVENT_TYPE_DELETE = 'delete';
	const EVENT_TYPE_INSERT = 'insert';
	const EVENT_TYPE_UPDATE = 'update';

	/**
	 * @var \jamwork\database\Database
	 */
	protected $con = null;

	/**
	 * @var \jamwork\common\EventDispatcher
	 */
	protected $eventDispatcher = null;

	/**
	 * @var \Corework\Application\Abstracts\Manager
	 */
	protected $manager;

	/**
	 * @param \jamwork\database\Database|null $dataBase
	 */
	public function __construct(\jamwork\database\Database $dataBase = null)
	{
		$this->con = is_null($dataBase) ? Registry::getInstance()->getDatabase() : $dataBase;
		$this->eventDispatcher = Registry::getInstance()->getEventDispatcher();
	}

	public function getConnection()
	{
		return $this->con;
	}

	/**
	 * @param string          $name
	 * @param ModelsInterface $model
	 * @param string          $type
	 * @param null|bool       $status
	 * @return \jamwork\common\Event
	 */
	private function triggerEvent($name, $model, $type, $status)
	{
		return $this->eventDispatcher->triggerEvent(
			$name,
			array(
				'model' => $model,
				'manager' => $this->getManager(),
			),
			array(
				'type' => $type,
				'status' => $status,
			)
		);
	}

	/**
	 * Insert method
	 *
	 * @param ModelsInterface $model
	 *
	 * @throws \Corework\Exceptions\DatabaseException
	 * @return int|boolean
	 */
	public function insert(ModelsInterface $model)
	{
		if ($model->getId() > 0)
		{
			return false;
		}

		$model->setCreated();
		$model->setCreateduser_Id();
		$event = $this->triggerEvent(self::EVENT_BEFORE_SAVE, $model, self::EVENT_TYPE_INSERT, null);
		if ($event && $event->isCanceled())
		{
			throw new DatabaseException(sprintf('"%s" für die Aktion "%s" wurde abgebrochen!', self::EVENT_BEFORE_SAVE, self::EVENT_TYPE_INSERT));
		}
		$inserted = $this->con->insert($model->getTableName(), $model->getDataRow());

		if (!$inserted)
		{
			SystemMessages::addError('Beim Erstellen ist ein Fehler aufgetreten');
			$this->triggerEvent(self::EVENT_AFTER_SAVE, $model, self::EVENT_TYPE_INSERT, false);
			return false;
		}

		$model->setId($inserted);
		$event = $this->triggerEvent(self::EVENT_AFTER_SAVE, $model, self::EVENT_TYPE_INSERT, true);
		if ($event && $event->isCanceled())
		{
			throw new DatabaseException(sprintf('"%s" für die Aktion "%s" wurde abgebrochen!', self::EVENT_AFTER_SAVE, self::EVENT_TYPE_INSERT));
		}
		return $inserted;
	}

	/**
	 * Update method
	 *
	 * @param ModelsInterface $model
	 *
	 * @throws \Corework\Exceptions\DatabaseException
	 * @return int|boolean
	 */
	public function update(ModelsInterface $model)
	{
		if (!$model->getId())
		{
			return false;
		}

		$model->setModified();
		$model->setModifieduser_Id();
		$event = $this->triggerEvent(self::EVENT_BEFORE_SAVE, $model, self::EVENT_TYPE_UPDATE, null);
		if ($event && $event->isCanceled())
		{
			throw new DatabaseException(sprintf('"%s" für die Aktion "%s" wurde abgebrochen!', self::EVENT_BEFORE_SAVE, self::EVENT_TYPE_UPDATE));
		}
		$updated = $this->con->update($model->getTableName(), $model->getDataRow());

		if (!$updated)
		{
			SystemMessages::addError(sprintf('Beim Aktualisieren ist ein Fehler aufgetreten'));
			$this->triggerEvent(self::EVENT_AFTER_SAVE, $model, self::EVENT_TYPE_UPDATE, false);
			return false;
		}

		$event = $this->triggerEvent(self::EVENT_AFTER_SAVE, $model, self::EVENT_TYPE_UPDATE, true);
		if ($event && $event->isCanceled())
		{
			throw new DatabaseException(sprintf('"%s" für die Aktion "%s" wurde abgebrochen!', self::EVENT_AFTER_SAVE, self::EVENT_TYPE_UPDATE));
		}

		return $updated;
	}

	/**
	 * Delete method
	 *
	 * @param ModelsInterface $model
	 *
	 * @throws \Corework\Exceptions\DatabaseException
	 * @return boolean
	 */
	public function delete(ModelsInterface $model)
	{
		if (!$model->getId())
		{
			return false;
		}

		$event = $this->triggerEvent(self::EVENT_BEFORE_DELETE, $model, self::EVENT_TYPE_DELETE, null);
		if ($event && $event->isCanceled())
		{
			throw new DatabaseException(sprintf('"%s" für die Aktion "%s" wurde abgebrochen!', self::EVENT_BEFORE_DELETE, self::EVENT_TYPE_DELETE));
		}

		if (!$this->con->delete($model->getTableName(), $model->getDataRow()))
		{
			SystemMessages::addError('Beim Entfernen ist ein Fehler aufgetreten');
			$this->triggerEvent(self::EVENT_AFTER_DELETE, $model, self::EVENT_TYPE_DELETE, false);
			return false;
		}

		$model->setId(0);
		$event = $this->triggerEvent(self::EVENT_AFTER_DELETE, $model, self::EVENT_TYPE_DELETE, true);
		if ($event && $event->isCanceled())
		{
			throw new DatabaseException(sprintf('"%s" für die Aktion "%s" wurde abgebrochen!', self::EVENT_AFTER_DELETE, self::EVENT_TYPE_DELETE));
		}

		return true;
	}

	/**
	 * @param \Corework\Application\Abstracts\Manager $manager
	 * @return void
	 */
	public function setManager($manager)
	{
		$this->manager = $manager;
	}

	/**
	 * @return \Corework\Application\Abstracts\Manager
	 */
	public function getManager()
	{
		return $this->manager;
	}

	/**
	 * @param string|ModelsInterface  $prefixOrModel
	 * @param \jamwork\database\Query $query
	 * @return \jamwork\database\Query
	 */
	public function addDeleteWhere($prefixOrModel, \jamwork\database\Query $query)
	{

		if ($prefixOrModel instanceof ModelsInterface)
		{
			if (property_exists($prefixOrModel, 'deleted'))
			{
				$query->addWhere($prefixOrModel->getTablePrefix() . 'deleted', 0);
			}
		}
		elseif (!is_object($prefixOrModel))
		{
			/**
			 * Wenn er hier her kommt, dann wu5rde die Funktion Public von außen aufgerufen
			 * Parameter als String übergeben. Somit weiß der Herr Programmierer, was er macht!
			 */
			$query->addWhere($prefixOrModel . 'deleted', 0);
		}

		return $query;
	}

	/**
	 * Liefert ein Model von ModelsInterface aus dem Query-Select
	 *
	 * @param \Corework\Application\Interfaces\ModelsInterface $model
	 * @param                                              $id
	 * @return \Corework\Application\Interfaces\ModelsInterface
	 * @throws \ErrorException
	 */
	public function getModelById(ModelsInterface $model, $id)
	{
		$query = $this->con->newQuery();
		$query->select('*');
		$query->from($model->getTableName());
		$query->addWhere($model->getIdField(), $id);
		$query = $this->addDeleteWhere($model, $query);

		/**
		 * @var $rs \jamwork\database\PDORecordset
		 */
		$rs = $this->con->newRecordSet();
		$rs->execute($query);

		if ($rs->isSuccessful() && ($rs->count() > 0))
		{
			$model->setDataRow($rs->get());
			$model->resetRegisterChange();


			return $model;
		}

		$reflection = new \ReflectionClass($model);
		$name = $reflection->getName();

		throw new \ErrorException('Datensatz nicht gefunden mit ID "' . $id . '" in Model "' . $name . '"');

	}

	/**
	 * Liefert ein Array von Models aus dem Query-Select
	 *
	 * @param string                  $modelClassName
	 * @param \jamwork\database\Query $query
	 * @return array
	 */
	public function getModelsByQuery($modelClassName, Query $query)
	{
		$query = $this->addDeleteWhere(new $modelClassName(), $query);
		/**
		 * @var $rs \jamwork\database\PDORecordset
		 */
		$rs = $this->con->newRecordSet();
		$rs->execute($query);

		$models = array();

		if ($rs->isSuccessful() && ($rs->count() > 0))
		{
			while (($rec = $rs->get()) == true)
			{
				/** @var $model \Corework\Model */
				$model = new $modelClassName();
				$clean = $model->clearDataRow($rec);
				$model->setDataRow($clean);
				$model->resetRegisterChange();

				$models[] = $model;
			}
		}

		return $models;
	}

	/**
	 * Liefert ein Model von $modelClassName aus dem Query-Select
	 *
	 * @param string                  $modelClassName
	 * @param \jamwork\database\Query $query
	 * @return \Corework\Application\Interfaces\ModelsInterface|bool
	 */
	public function getModelByQuery($modelClassName, Query $query)
	{
		$query = $this->addDeleteWhere(new $modelClassName(), $query);

		/**
		 * @var $rs \jamwork\database\PDORecordset
		 */
		$rs = $this->con->newRecordSet();
		$query->limit(0, 1);
		$rs->execute($query);

		if ($rs->isSuccessful() && ($rs->count() > 0))
		{
			/** @var $model \Corework\Model */
			$model = new $modelClassName();
			$clean = $model->clearDataRow($rs->get());
			$model->setDataRow($clean);
			$model->resetRegisterChange();

			return $model;
		}

		return false;
	}

	/**
	 * Liefert ein Array von Records aus dem Query-Select
	 *
	 * @param \jamwork\database\Query $query
	 * @return array
	 */
	public function getArrayByQuery(Query $query)
	{
		/**
		 * @var $rs \jamwork\database\PDORecordset
		 */
		$rs = $this->con->newRecordSet();
		$rs->execute($query);

		$models = array();

		if ($rs->isSuccessful() && ($rs->count() > 0))
		{
			while (($rec = $rs->get()) == true)
			{
				$models[] = $rec;
			}
		}

		return $models;
	}

	/**
	 * Führt ein Update anhand des übergebenen Update Objects aus.
	 * @param \jamwork\database\Query $query
	 * @return bool
	 * @deprecated Wo bitte wird das verwendet? Da müsste der Update-Query ja per hand gebaut werden! Mardl
	 */
	public function updateByQuery(Query $query)
	{
		/**
		 * @var $ret \jamwork\database\PDORecordset
		 */
		$rs = $this->con->newRecordSet();
		$ret = $rs->execute($query);

		return $ret->isSuccessful();
	}


}