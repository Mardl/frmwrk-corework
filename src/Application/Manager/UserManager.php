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
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class UserManager extends Manager
{

	/**
	 * @return ModelsInterface
	 */
	protected function getNewModel()
	{
		return new UserModel();
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

		return $this->getBaseManager()->getModelByQuery('\Corework\Application\Models\UserModel', $query);
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

		return $this->getBaseManager()->getModelByQuery('\Corework\Application\Models\UserModel', $query);
	}

	/**
	 * Liefert die Anzahl der Benutzer
	 *
	 * @param int $status Der maximale Benutzerstatus, Default: Deleted
	 * @return int
	 */
	public function getUserCount($status = STATUS_DELETED)
	{
		$mod = $this->getAppModel('UserModel');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('COUNT(*) AS count');
		$query->from($mod->getTableName());
		$query->addWhere('status', $status, '<=');

		$result = $this->getBaseManager()->getArrayByQuery($query);

		return $result ? $result[0]['count'] : 0;
	}

	/**
	 * Liefert ein Array mit den Benutzern.
	 * Im ersten Parameter $status wird übermittelt bis (exklusive) welchem Benutzerstatus die Benutzer aus der Datenbank gelesen werden sollen.
	 *
	 * @param int $status
	 * @return array of UserModel
	 */
	public function getUsersByStatus($status = STATUS_DELETED)
	{
		$mod = $this->getAppModel('UserModel');

		$query = $this->getBaseManager()->getConnection()->newQuery();
		$query->select('*');
		$query->from($mod->getTableName());
		$query->addWhere('status', $status, '<=');

		return $this->getBaseManager()->getModelsByQuery('\Corework\Application\Models\UserModel', $query);
	}

	public function login($username, $password)
	{

	}

	public function getUsersByGroupId($groupId)
	{

	}

	public function insertUser(UserModel $user, $password)
	{

	}

	public function updateUser(UserModel $user, $password = '')
	{

	}

	public static function searchUsers($keyword, $status = STATUS_DELETED)
	{

	}

	private function checkUniqueUsername(UserModel $model)
	{

	}

	/**
	 * Loggt den Benutzer aus, indem die Session zerstört wird.
	 * @return void
	 */
	public function logout()
	{
		Registry::getInstance()->getSession()->destroy();
	}
	/////////////////////////////////////////// alt /////////////////////////////////////////////////



	/**
	 * Users
	 *
	 * @var array
	 */
	private static $_users = array();


	/**
	 * Sucht einen Benutzer anhand von Benutzername und Passwort.
	 * Wenn dies klappt, wird die Benutzer-ID in der Session gespeichert und die ID zurückgeliefert wird.
	 *
	 * @param string $username Benutzername
	 * @param string $password Passwort
	 *
	 * @return mixed
	 *
	 * @throws \ErrorException Wenn mit den angegebenen Daten kein Benutzer gefunden wird
	 */
	public static function login_old($username, $password)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery();
		$query->select(
			'id,
			 password,
			 otp,
			 language_id as language'
		);
		$query->from('users')->addWhere('username', $username)->addWhere('status', STATUS_ACTIVE);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();

			if (strlen($rs['password']) <= 32)
			{
				$checkup = (md5($password) == $rs['password']);
			}
			else
			{
				$checkup = \Corework\String::bcryptCheckup($password, $rs['password']);
			}

			if ($checkup)
			{
				$session = Registry::getInstance()->getSession();
				$session->set('user', $rs['id']);
				$session->set('otp', $rs['otp']);
				$session->set('language', $rs['language']);

				return $rs['id'];
			}
		}

		throw new \ErrorException('Benutzer nicht gefunden!');
	}

	/**
	 * Liefert Benutzer einer bestimmten Rolle
	 *
	 * @param int $groupId ID der Rolle
	 * @return UserModel
	 */
	public static function getUsersByGroupId_old($groupId)
	{
		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery();
		$query->select(
			'u.id,
			u.username,
			u.firstname,
			u.lastname,
			u.email,
			u.email_corrupted AS emailCorrupted,
			u.avatar,
			u.birthday,
			u.gender,
			u.created,
			u.status,
			u.admin,
			u.language_id as language,
			u.otp'
		);
		$query->from('users as u')->innerJoin('right_group_users AS rgu')->on('rgu.user_id = u.id')->addWhere('rgu.group_id', $groupId);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		$users = array();

		if ($rsExecution->isSuccessfull())
		{
			while (($rs = $rsExecution->get()) == true)
			{
				$users[] = new UserModel($rs);
			}
		}

		return $users;
	}

	/**
	 * Speichert einen neuen Benutzer in der Datenbank
	 *
	 * @param UserModel $user     User-Objekt
	 * @param string    $password Passwort
	 * @return UserModel|bool
	 * @throws \ErrorException
	 */
	public static function insertUser_old(UserModel $user, $password)
	{
		$con = Registry::getInstance()->getDatabase();
		$datetime = new \DateTime();

		if (!self::checkUniqueUsername($user))
		{
			throw new \ErrorException('Der gewünschte Benutzername ist bereits vergeben!');
		}

		$user->setCreated($datetime);

		$id = $con->insert('users',
			array(
				'username' => $user->getUsername(),
				'firstname' => $user->getFirstname(),
				'lastname' => $user->getLastname(),
				'password' => $user->setPassword($password, false),
				'email' => $user->getEmail(),
				'email_corrupted' => '',
				'avatar' => null,
				'birthday' => $user->getBirthday()->format('Y-m-d'),
				'gender' => $user->getGender(),
				'created' => $datetime->format('Y-m-d H:i:s'),
				'status' => STATUS_ACTIVE,
				'language_id' => $user->getLanguageId(),
				'otp' => 0,
				'admin' => $user->getAdmin()
			)
		);

		if ($id)
		{
			$user->setId($id);

			return $user;
		}

		return false;
	}

	/**
	 * Speichert Änderungen eines Benutzers
	 *
	 * @param UserModel $user     User-Objekt
	 * @param string    $password Optionales Passwort
	 * @return UserModel|bool
	 * @throws \ErrorException
	 */
	public static function updateUser_old(UserModel $user, $password = '')
	{
		unset(self::$_users[$user->getId()]);
		$con = Registry::getInstance()->getDatabase();

		if (!$user->getId())
		{
			return false;
		}

		if (!self::checkUniqueUsername($user))
		{
			throw new \ErrorException('Der gewünschte Benutzername ist bereits vergeben!');
		}

		if (empty($password))
		{
			$data = array(
				'username' => $user->getUsername(),
				'firstname' => $user->getFirstname(),
				'lastname' => $user->getLastname(),
				'email' => $user->getEmail(),
				'birthday' => $user->getBirthday()->format('Y-m-d'),
				'gender' => $user->getGender(),
				'avatar' => $user->getAvatarId(),
				'status' => $user->getStatus(),
				'admin' => $user->getAdmin(),
				'otp' => $user->getOtp(),
				'language' => $user->getLanguage(),
				'id' => $user->getId()
			);
		}
		else
		{
			$data = array(
				'username' => $user->getUsername(),
				'firstname' => $user->getFirstname(),
				'lastname' => $user->getLastname(),
				'email' => $user->getEmail(),
				'birthday' => $user->getBirthday()->format('Y-m-d'),
				'gender' => $user->getGender(),
				'avatar' => $user->getAvatarId(),
				'status' => $user->getStatus(),
				'password' => $user->setPassword($password, false),
				'admin' => $user->getAdmin(),
				'otp' => $user->getOtp(),
				'language' => $user->getLanguage(),
				'id' => $user->getId()
			);
		}

		if ($con->update('users', $data))
		{
			return $user;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Liefert ein Array mit den Benutzern.
	 * Im ersten Parameter $status wird übermittelt bis (exklusive) welchem Benutzerstatus die Benutzer aus der Datenbank gelesen werden sollen.
	 *
	 * @param string $keyword Suchwort
	 * @param int    $status  Der maximale Benutzerstatus, Default: Deleted
	 * @return array von UserModel
	 */
	public static function searchUsers_old($keyword, $status = STATUS_DELETED)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery();
		$query->select(
			'u.id,
			u.username,
			u.firstname,
			u.lastname,
			u.email,
			u.email_corrupted AS emailCorrupted,
			u.avatar,
			u.birthday,
			u.gender,
			u.created,
			u.status,
			u.admin');
		$query->from('users as u')
			->addWhere('status', $status, '<=')
			->openClosure()
			->addWhereLike('username', $keyword)
			->addWhereLike('firstname', $keyword, '%%%s%%', 'OR')
			->addWhereLike('lastname', $keyword, '%%%s%%', 'OR')
			->closeClosure()
			->orderBy('username');

		$query->distinct();

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		$models = array();

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			while (($rec = $rs->get()) == true)
			{
				$models[] = new UserModel($rec);
			}
		}

		return $models;
	}

	/**
	 * @param UserModel $model
	 * @return bool
	 */
	public static function checkUniqueUsername_old(UserModel $model)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()->select('*')->from('users')->addWhere('username', $model->getUsername());


		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rsExecution = $rs->get();
			if ($rsExecution['id'] != $model->getId())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Generiert ein einmaliges Passwort
	 *
	 * @param int $userid ID des Benutzers
	 * @return string
	 */
	public static function generateOTP($userid)
	{
		$userModel = self::getUserById($userid);

		$crypttime = md5(crypt(time()));
		$randompass = substr($crypttime, 0, 8);
		$randompass = strtolower($randompass);

		$userModel->setOtp(1);
		self::updateUser($userModel, $randompass);

		return $randompass;
	}

}
