<?php

namespace Corework\Application\Manager;

use Corework\Application\Abstracts\Manager;
use Corework\Application\Interfaces\ModelsInterface;
use Corework\Application\Models\UserModel;
use jamwork\common\Registry;

/**
 * Class User
 *
 * @category Corework
 * @package  Corework\Application\Manager
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
class UserManager extends Manager
{
    const STATUS_ACTIVE = 0;
    const STATUS_INACTIVE = 1;
    const STATUS_BLOCKED = 2;
    const STATUS_DELETED = 3;
	/**
	 * @return UserModel
	 */
	public function getNewModel()
	{
		return $this->getAppModel('UserModel');
	}

	/**
	 * @param string $username
	 * @return bool|ModelsInterface
	 */
	public function getUserByUsername($username)
	{
		if (empty($username))
		{
			return false;
		}

		$mod = $this->getAppModel('UserModel');
		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('usr_username', $username);

		return $this->getBaseManager()->getModelByQuery($this->getAppModelName('UserModel'), $query);
	}

	/**
	 * @param string $email
	 * @return bool|ModelsInterface
	 */
	public function getUserByEMail($email)
	{
		if (empty($email))
		{
			return false;
		}

		$mod = $this->getAppModel('UserModel');
		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('usr_email', $email);

		return $this->getBaseManager()->getModelByQuery($this->getAppModelName('UserModel'), $query);
	}

	/**
	 * Liefert die Anzahl der Benutzer
	 *
	 * @param int $status Der maximale Benutzerstatus, Default: Deleted
	 * @return int
	 * @deprecated Prüfen, obs noch verwendet wird
	 */
	public function getUserCount($status = self::STATUS_DELETED)
	{
		$mod = $this->getAppModel('UserModel');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('COUNT(*) AS count');
		$query->from($mod->getTableName());
		$query->addWhere('usr_status', $status, '<=');

		$result = $this->getBaseManager()->getArrayByQuery($query);

		return $result ? $result[0]['count'] : 0;
	}

	/**
	 * Liefert ein Array mit den Benutzern.
	 * Im ersten Parameter $status wird übermittelt bis (exklusive) welchem Benutzerstatus die Benutzer aus der Datenbank gelesen werden sollen.
	 *
	 * @param int $default
	 * @param int $status
	 * @return array of UserModel
	 */
	public function getAll($default = 0, $status = -1)
	{
		$mod = $this->getAppModel('UserModel');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		if ($status >= 0)
		{
			$query->addWhere('status', $status);
		}
		else{
			$query->addWhere('status', self::STATUS_DELETED, '<=');
		}
		if (!empty($default))
		{
			$query->addWhere('id', $default, '=', 'OR');
		}
		$query->orderBy('usr_lastname, usr_status');

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('UserModel'), $query);
	}

	/**
	 * Liefert Benutzer einer bestimmten Rolle
	 *
	 * @param int $groupId
	 * @return array
	 */
	public function getUsersByGroupId($groupId)
	{
		$mod = $this->getAppModel('UserModel');
		$mod2 = $this->getAppModel('RightGroupUsersModel', 'Right');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->leftJoin($mod2->getTableName());
		$query->on('usr_id = rgu_user_id');
		$query->addWhere('rgu_rightgroup_id', $groupId);

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('UserModel'), $query);
	}

	/**
	 * @param UserModel $model
	 * @return bool
	 */
	private function checkUniqueUsername(UserModel $model)
	{
		$userModelExists = $this->getUserByUsername($model->getUsername());

		if ($userModelExists && $model->getId() != $userModelExists->getId())
		{
			return false;
		}

		return true;
	}

	/**
	 * Sucht einen Benutzer anhand von Benutzername und Passwort.
	 * Wenn dies klappt, wird die Benutzer-ID in der Session gespeichert und das UserModel zurückgeliefert.
	 *
	 * @param string $username Benutzername
	 * @param string $password Passworts
	 *
	 * @return mixed
	 *
	 * @throws \ErrorException Wenn mit den angegebenen Daten kein Benutzer gefunden wird
	 */
	public function login($username, $password)
	{
		$mod = $this->getAppModel('UserModel');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('usr_username', $username);
		$query->addWhere('usr_status', self::STATUS_ACTIVE);

		$model = $this->getBaseManager()->getModelByQuery($this->getAppModelName('UserModel'), $query);

		/** @var \Corework\Application\Models\UserModel $model */

		if ($model && $this->checkPasswordInput($password, $model))
		{
			$session = Registry::getInstance()->getSession();
			$session->set('user', $model->getId());
			$session->set('otp', $model->isOtp());
			$session->set('language', $model->getLanguage_id());

			return $model;
		}

		throw new \ErrorException('Benutzer nicht gefunden!');
	}

	/**
	 * @param string    $password
	 * @param UserModel $model
	 * @return bool
	 */
	private function checkPasswordInput($password, UserModel $model)
	{
		if (strlen($model->getPassword()) <= 32)
		{
			$checkup = (md5($password) == $model->getPassword());
		}
		else
		{
			$checkup = \Corework\String::bcryptCheckup($password, $model->getPassword());
		}

		return $checkup;
	}

	/**
	 * Loggt den Benutzer aus, indem die Session zerstört wird.
	 *
	 * @return void
	 */
	public function logout()
	{
		Registry::getInstance()->getSession()->destroy();
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected function verifySaveData(array $data, $forSave = true)
	{
		if (!$forSave)
		{
			return $data;
		}

		if (array_key_exists('otpPasswd', $data) && !empty($data['otpPasswd']))
		{
			$data['usr_password'] = $data['otpPasswd'];
		}
		else
		{
			if (array_key_exists('pwd', $data) && !empty($data['pwd']))
			{
				/** @var UserModel $userModel */
				$userModel = $this->getModelFromArray($data);
				$generatedPassword = $this->generatePassword($userModel, $data['pwd'], ($userModel->getId() > 0 ? false : true));
				$data['usr_password'] = $generatedPassword;
			}
		}

		return $data;
	}

	/**
	 * @param UserModel $user
	 * @param string    $password
	 * @param bool|true $md5
	 * @return string
	 */
	private function generatePassword(UserModel $user, $password, $md5 = true)
	{
		if ($md5)
		{
			$generatedPwd = md5($password);
		}
		else
		{
			$birthday = $user->getBirthday() ? $user->getBirthday()->format('Ymd') : '00000000';

			$generatedPwd = \Corework\String::bcryptEncode(
				$password, md5($user->getId() . $birthday . $user->getGender() . $user->getCreated()->format("Ymd"))
			);
		}

		return $generatedPwd;
	}

	/**
	 * Speichert einen neuen Benutzer in der Datenbank
	 *
	 * @param array $data
	 * @return bool|UserModel
	 * @throws \ErrorException
	 */
	public function save(array $data)
	{
		/** @var UserModel $user */
		$user = $this->getModelFromArray($data);

		if (!$this->checkUniqueUsername($user))
		{
			throw new \ErrorException('Der gewünschte Benutzername ist bereits vergeben!');
		}

		$saveModel = parent::save($data);

		return $saveModel;
	}

	/**
	 * @param string $keyword
	 * @param int    $status
	 * @return array
	 */
	public function searchUsers($keyword, $status = self::STATUS_ACTIVE)
	{
		$mod = $this->getAppModel('UserModel');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTablename());
		$query->addWhere('usr_status', $status, '<=');
		$query->openClosure();
		$query->addWhereLike('usr_username', $keyword);
		$query->addWhereLike('usr_firstname', $keyword, '%%%s%%', 'OR');
		$query->addWhereLike('usr_lastname', $keyword, '%%%s%%', 'OR');
		$query->closeClosure();
		$query->orderBy('usr_username');

		return $this->getBaseManager()->getModelsByQuery($this->getAppModelName('UserModel'), $query);
	}

	/**
	 * Generiert ein einmaliges Passwort
	 *
	 * @param int $userid ID des Benutzers
	 * @return string
	 */
	public function generateOtp($userid)
	{
		/** @var UserModel $userModel */
		$userModel = $this->getModelById($this->getNewModel(), $userid);

		$crypttime = md5(crypt(time()));
		$randompass = substr($crypttime, 0, 8);
		$randompass = strtolower($randompass);

		$userModel->setOtp(1);

		$data = $userModel->getDataRow();
		$data['otpPasswd'] = $randompass;

		$this->save($data);

		return $randompass;
	}
}
