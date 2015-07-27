<?php

namespace Corework;

use Corework\Application\Interfaces\ModelsInterface;

/**
 * Class Model
 *
 * @category Corework
 * @package  Corework
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
abstract class Model implements ModelsInterface
{

	/**
	 * Integer value of gender male
	 * @var int
	 */
	const GENDER_MALE = 1;

	/**
	 * Integer value of gender female
	 * @var int
	 */
	const GENDER_FEMALE = 2;

	/**
	 * Integer value of gender unknown
	 * @var int
	 */
	const GENDER_BOTH = 3;

	/**
	 * @var array
	 */
	protected $changedFields = array();

	/**
	 * Handelt es sich um ein der Datenbank unbekanntes Objekt
	 *
	 * @var \ReflectionClass
	 */
	protected $reflectionClass = false;

	/**
	 * Abfangen von unbekannten Funktionen
	 * Derzeit werden folgende Methode auf Attribute angehandelt
	 * "set..." Wert für das Attribut setzten
	 * "get..." Liefere den Wert
	 * "is..."  Vergleiche Wert (Beispiel: $user->isName('John'))
	 * "has..." Prüft ob ein Attribut einen Wert hat (also nicht: null, 0 oder false)
	 */

	/**
	 * @var integer
	 */
	protected $id = null;

	/**
	 * @var \DateTime
	 */
	protected $modified = '';
	protected $modifieduser_id = null;

	/**
	 * @var \DateTime
	 */
	protected $created = '';
	protected $createduser_id = null;


	/**
	 * @return array
	 */
	abstract public function getDataRow();

	/**
	 * @param string $name
	 * @param array  $params
	 * @return mixed|void
	 * @throws \Exception
	 */
	public function __call($name, $params)
	{
		throw new \Exception("It is not allowed to use the call interceptor for '" . $name . "' from '" . get_class($this) . "' within the project models.");
	}

	/**
	 * Reflection kann nicht serialisiert werden und muss nach unserialize wieder auf default initialisiert werden.
	 * @return void
	 */
	public function __wakeup()
	{
		$this->reflectionClass = false;
	}

	/**
	 * Konstruktor
	 *
	 * @param array $data Attribut daten
	 */
	public function __construct($data = array())
	{
		$this->setDataRow($data);
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id)
	{
		$this->set('id', $id);
	}

	/**
	 * @return bool
	 */
	public function hasId()
	{
		return !empty($this->id);
	}

	/**
	 * Überprüft $data nach vorhandene Settern und liefert bereinigtes array zurück
	 *
	 *
	 * @param array $data
	 * @return array
	 */
	public function clearDataRow($data = array())
	{
		$ret = array();
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$keyToCheck = $key;
				$method = 'set' . ucfirst($keyToCheck);
				$prefix = $this->getTablePrefix();
				if (!empty($prefix))
				{
					$keyToCheck = str_replace($prefix, '', $keyToCheck);
					$method = 'set' . ucfirst($keyToCheck);
				}

				if (property_exists($this, $keyToCheck) || method_exists($this, $method))
				{
					$ret[$key] = $value;
				}
			}
		}

		return $ret;
	}

	protected function clearDateTimeSting($datetime)
	{
		return str_replace(':000', '', $datetime);
	}

	/**
	 * Validierung für Setter von Datumsfeldern
	 *
	 * @param \DateTime|string $dt
	 * @throws \InvalidArgumentException
	 * @return \DateTime
	 */
	public function setDateTimeFrom($dt)
	{
		if (!($dt instanceof \DateTime))
		{
			try
			{
				$dt = new \DateTime($this->clearDateTimeSting($dt));
			} catch (\Exception $e)
			{
				throw new \InvalidArgumentException('Ungültige Datumsangabe');
			}
		}

		return $dt;
	}

	/**
	 * Validierung für Getter von Datumsfeldern
	 *
	 * @param \Datetime|string $dt
	 * @param string           $default
	 * @return \DateTime
	 */
	public function getDateTimeFrom($dt, $default = '0000-00-00 00:00:00')
	{
		if (empty($dt))
		{
			$dt = $default;
		}
		if (!($dt instanceof \DateTime))
		{
			$dt = new \DateTime($dt);
		}

		return $dt;
	}

	/**
	 * Sorgt dafür, dass das Erstellungsdatum immer ein DateTime-Objekt ist.
	 *
	 * @param \DateTime|string $datetime Datetime-Objekt oder String
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function setCreated($datetime = 'now')
	{
		$this->set('created', $this->setDateTimeFrom($datetime));
	}

	/**
	 * Liefert Create Datetime als mysql Format zurück
	 *
	 * @return string
	 */
	public function getCreatedAsString()
	{
		return $this->getCreated()->format('Y-m-d H:i:s');
	}

	/**
	 * @return mixed
	 */
	public function getCreated()
	{
		$this->created = $this->getDateTimeFrom($this->created);

		return $this->created;
	}

	/**
	 * @param int $userId
	 * @return void
	 */
	public function setCreateduser_Id($userId = 0)
	{
		$register = \jamwork\common\Registry::getInstance();

		if (empty($userId) && isset($register->login) && $register->login instanceof \Corework\Application\Models\UserModel)
		{
			$this->set('createduser_id', $register->login->getId());
		}
		else
		{
			$this->set('createduser_id', !empty($userId) ? $userId : null);

		}
	}

	/**
	 * @return int|null
	 */
	public function getCreateduser_Id()
	{
		return $this->createduser_id;
	}

	/**
	 * Sorgt dafür, dass das Erstellungsdatum immer ein DateTime-Objekt ist.
	 *
	 * @param \DateTime|string $datetime Datetime-Objekt oder String
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function setModified($datetime = 'now')
	{
		$datetime = $this->setDateTimeFrom($datetime);
		$this->set('modified', $datetime);
	}

	/**
	 * Liefert Modified Datetime als mysql Format zurück
	 *
	 * @return string
	 */
	public function getModifiedAsString()
	{
		return $this->getModified()->format('Y-m-d H:i:s');
	}

	/**
	 * @return mixed
	 */
	public function getModified()
	{
		$this->modified = $this->getDateTimeFrom($this->modified);

		return $this->modified;
	}

	/**
	 * @param null $userId
	 * @return void
	 */
	public function setModifieduser_Id($userId = null)
	{
		$register = \jamwork\common\Registry::getInstance();
		if (empty($userId) && isset($register->login) && $register->login instanceof \Corework\Application\Models\UserModel)
		{
			$this->set('modifieduser_id', $register->login->getId());

		}
		else
		{
			$this->set('modifieduser_id', !empty($userId) ? $userId : null);
		}
	}

	/**
	 * @return int|null
	 */
	public function getModifieduser_Id()
	{
		return $this->modifieduser_id;
	}

	/**
	 * Liefert den Tabellennamen des Objekts anhand des Klassenkommentars @Table
	 *
	 * @return null|string
	 */
	public function getTableName()
	{
		$tableName = ModelInformation::get(get_class($this), "tablename");

		if (!is_null($tableName))
		{
			if (!($tableName == '-1'))
			{
				return $tableName;
			}

			return '';
		}

		if (!$this->reflectionClass)
		{
			$this->reflectionClass = new \ReflectionClass(get_class($this));
		}
		$doc = $this->reflectionClass->getDocComment();
		$cache = '-1';
		if (preg_match('/\@Table\((.*)\)/s', $doc, $matches))
		{
			$tmp = substr($matches[1], strpos($matches[1], 'name="'));
			$tmp = substr($tmp, strpos($tmp, '"') + 1);
			$tableName = substr($tmp, 0, strpos($tmp, '"'));
			$cache = $tableName;
		}
		ModelInformation::set(get_class($this), "tablename", $cache);

		return $tableName;
	}

	/**
	 * Liefert den Prefix des Tabellennamens anhand @Prefix zurück
	 *
	 * @return null|string
	 */
	public function getTablePrefix()
	{
		$prefix = ModelInformation::get(get_class($this), "prefix");

		if (!is_null($prefix))
		{
			if (!($prefix == '-1'))
			{
				return $prefix;
			}

			return '';
		}

		if (!$this->reflectionClass)
		{
			$this->reflectionClass = new \ReflectionClass($this);
		}

		$doc = $this->reflectionClass->getDocComment();

		$cache = '-1';
		if (preg_match('/\@Prefix\((.*)\)/s', $doc, $matches))
		{
			$tmp = substr($matches[1], strpos($matches[1], 'name="'));
			$tmp = substr($tmp, strpos($tmp, '"') + 1);
			$prefix = substr($tmp, 0, strpos($tmp, '"'));
			$cache = $prefix;
		}
		ModelInformation::set(get_class($this), "prefix", $cache);

		return $prefix;
	}

	/**
	 * @return null|string
	 * @throws \ErrorException
	 */
	public function getIdField()
	{
		$id = ModelInformation::get(get_class($this), "idfield");

		if (!is_null($id))
		{
			return $id;
		}

		if (!$this->reflectionClass)
		{
			$this->reflectionClass = new \ReflectionClass($this);
		}
		$properties = $this->reflectionClass->getProperties();

		foreach ($properties as $prop)
		{
			$doc = $prop->getDocComment();

			if (preg_match('/\@Id/s', $doc, $matches))
			{
				$id = $this->getTablePrefix() . $prop->getName();
				ModelInformation::set(get_class($this), "idfield", $id);

				return $id;
			}
		}

		throw new \ErrorException("Kein ID-Feld über @Id definiert");
	}

	/**
	 * @param array $data
	 * @return void
	 */
	public function setDataRow($data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$key = $this->clearPropertyKey($key);
				$setter = 'set' . ucfirst($key);
				if (method_exists($this, $setter))
				{
					$this->$setter($value);
				}
			}
		}
	}

	/**
	 * @param string $key
	 * @return string
	 */
	public function clearPropertyKey($key)
	{
		$prefix = $this->getTablePrefix();
		if (!empty($prefix))
		{
			return preg_replace('/^' . $prefix . '/', '', $key);
		}

		return $key;
	}

	/**
	 * @param string $propertyName
	 * @param mixed  $value
	 * @return void
	 */
	public function set($propertyName, $value)
	{
		$this->throwExceptionIfNotModelProperty($propertyName);

		$propertyValue = $this->{$propertyName};
		if ($this->isDifferent($propertyValue, $value))
		{
			// Change property
			$this->{$propertyName} = $value;
			$this->registerChange($propertyName);
		}
	}

	/**
	 * @return void
	 */
	public function resetRegisterChange()
	{
		$this->changedFields = array();
	}

	/**
	 * @param string $propertyName
	 * @return void
	 */
	protected function registerChange($propertyName)
	{
		$this->changedFields[] = $propertyName;
	}

	/**
	 * @param string $value1
	 * @param string $value2
	 * @return bool
	 */
	protected function isDifferent($value1, $value2)
	{
		return (bool)$value1 !== $value2;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasChanges($name)
	{
		$this->throwExceptionIfNotModelProperty($name);

		return in_array($name, $this->changedFields);
	}

	/**
	 * @param string $name
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function throwExceptionIfNotModelProperty($name)
	{
		if (!property_exists($this, $name))
		{
			throw new \InvalidArgumentException(sprintf("Object '%s' hasnt the property '%s'", get_class($this), $name));
		}
	}
}
