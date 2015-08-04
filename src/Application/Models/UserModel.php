<?php

namespace Corework\Application\Models;


/**
 * Class UserModel
 *
 * @category Corework
 * @package  Corework\Application\Models
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 *
 * @MappedSuperclass
 */
class UserModel extends \Corework\Model
{
	/**
	 * Id
	 *
	 * @var int
	 *
	 * @Id
	 * @Column(type="integer", name="usr_id")
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Name (Nickname, Screenname)
	 *
	 * @var string
	 *
	 * @Column(type="string", length=64, unique=true, name="usr_username", options={"default" = ""})
	 */
	protected $username = '';

	/**
	 * First name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=32, nullable=true, name="usr_firstname", options={"default" = ""})
	 */
	protected $firstname = '';

	/**
	 * Last name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=32, nullable=true, name="usr_lastname", options={"default" = ""})
	 */
	protected $lastname = '';

	/**
	 * Password
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255, name="usr_password", options={"default" = ""})
	 */
	protected $password = '';

	/**
	 * Email
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255, name="usr_email", options={"default" = ""})
	 */
	protected $email = '';

	/**
	 * Is email corrupted
	 *
	 * @var boolean
	 *
	 * @Column(type="boolean", name="usr_emailcorrupted", options={"default" = 0})
	 */
	protected $emailcorrupted = false;

	/**
	 * Avatar
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255, nullable=true, name="usr_avatar", options={"default" = ""})
	 */
	protected $avatar = '';

	/**
	 * Birthday
	 *
	 * @var \DateTime
	 *
	 * @Column(type="date", name="usr_birthday", nullable=true)
	 */
	protected $birthday = '';

	/**
	 * Gender
	 *
	 * @var integer
	 *
	 * @Column(type="integer", name="usr_gender", options={"default" = 1})
	 */
	protected $gender = 1;

	/**
	 * Status
	 *
	 * @var integer
	 *
	 * @Column(type="integer", name="usr_status", options={"default" = 0})
	 */
	protected $status = 0;

	/**
	 * Einmal-Passwort wurde gesetzt
	 *
	 * @var bool
	 *
	 * @Column(type="boolean", name="usr_otp", options={"default" = 0})
	 */
	protected $otp = false;

	/**
	 * Administrator
	 *
	 * @var bool
	 *
	 * @Column(type="boolean", name="usr_admin", options={"default" = 0})
	 */
	protected $admin = false;

	/**
	 * Language
	 *
	 * @ManyToOne(targetEntity="App\Models\LanguageModel")
	 * @JoinColumn(name="usr_language_id", referencedColumnName="lng_id", nullable=true)
	 */
	protected $language_id = null;

	/**
	 * Created (Registration date)
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime", name="usr_created", nullable=true)
	 */
	protected $created;

	/**
	 * Modified User Id
	 *
	 * @var \int
	 *
	 * @ManyToOne(targetEntity="App\Models\UserModel")
	 * @JoinColumn(name="usr_createduser_id", referencedColumnName="usr_id", nullable=true)
	 */
	protected $createduser_id = null;


	/**
	 * Modified Datum
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime", name="usr_modified", nullable=true)
	 */
	protected $modified = null;

	/**
	 * Modified User Id
	 *
	 * @var \int
	 *
	 * @ManyToOne(targetEntity="App\Models\UserModel")
	 * @JoinColumn(name="usr_modifieduser_id", referencedColumnName="usr_id", nullable=true)
	 */
	protected $modifieduser_id = null;

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 * @return void
	 */
	public function setUsername($username)
	{
		$this->set('username', $username);
	}

	/**
	 * @return string
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}

	/**
	 * @param string $firstname
	 * @return void
	 */
	public function setFirstname($firstname)
	{
		$this->set('firstname', $firstname);
	}

	/**
	 * @return string
	 */
	public function getLastname()
	{
		return $this->lastname;
	}

	/**
	 * @param string $lastname
	 * @return void
	 */
	public function setLastname($lastname)
	{
		$this->set('lastname', $lastname);
	}

	/**
	 * @return string
	 */
	public function getFullname()
	{
		return $this->getFirstname() .' '.$this->getLastname();
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return void
	 */
	public function setEmail($email)
	{
		$this->set('email', $email);
	}

	/**
	 * @return boolean
	 */
	public function isEmailcorrupted()
	{
		return $this->emailcorrupted == 1;
	}

	/**
	 * @return boolean
	 */
	public function getEmailcorrupted()
	{
		return $this->emailcorrupted == 1;
	}

	/**
	 * @param boolean $emailcorrupted
	 * @return void
	 */
	public function setEmailcorrupted($emailcorrupted)
	{
		$this->set('emailcorrupted', $emailcorrupted);
	}

	/**
	 * @return string
	 */
	public function getAvatar()
	{
		return $this->avatar;
	}

	/**
	 * @param string $avatar
	 * @return void
	 */
	public function setAvatar($avatar)
	{
		$this->set('avatar', $avatar);
	}

	/**
	 * Sorgt dafür, dass das Geburtsdatum immer ein DateTime-Objekt ist.
	 *
	 * @param \DateTime|string $datetime Datetime-Objekt oder String
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function setBirthday($datetime = '0000-00-00')
	{
		$this->set('birthday', $this->setDateTimeFrom($datetime));
	}

	/**
	 * Liefert das Geburtsdatum Datetime im Format Y-m-d H:i:s zurück
	 *
	 * @return string
	 */
	public function getBirthdayAsString()
	{
		$dt = $this->getBirthday();
		if ($dt->format('Y') < 1900)
		{
			return null;
		}
		return $this->getBirthday()->format('Y-m-d H:i:s');
	}

	/**
	 * @return mixed
	 */
	public function getBirthday()
	{
		$this->birthday = $this->getDateTimeFrom($this->birthday);
		return $this->birthday;
	}

	/**
	 * @return int
	 */
	public function getGender()
	{
		return $this->gender;
	}

	/**
	 * @param int $gender
	 * @return void
	 */
	public function setGender($gender)
	{
		$this->set('gender', $gender);
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param int $status
	 * @return void
	 */
	public function setStatus($status)
	{
		$this->set('status', $status);
	}

	/**
	 * @return boolean
	 */
	public function isOtp()
	{
		return $this->otp == 1;
	}

	/**
	 * @return boolean
	 */
	public function getOtp()
	{
		return $this->otp;
	}

	/**
	 * @param boolean $otp
	 * @return void
	 */
	public function setOtp($otp)
	{
		$this->set('otp', $otp);
	}

	/**
	 * @return boolean
	 */
	public function isAdmin()
	{
		return $this->admin == 1;
	}

	/**
	 * @return boolean
	 */
	public function getAdmin()
	{
		return $this->admin;
	}

	/**
	 * @param boolean $admin
	 * @return void
	 */
	public function setAdmin($admin)
	{
		$this->set('admin', $admin);
	}

	/**
	 * @return mixed
	 */
	public function getLanguage_id()
	{
		return $this->language_id;
	}

	/**
	 * @param mixed $language_id
	 * @return void
	 */
	public function setLanguage_id($language_id)
	{
		$this->set('language_id', $language_id);
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 * @return void
	 */
	public function setPassword($password)
	{
		$this->set('password', $password);
	}

	/**
	 * @return array
	 */
	public function getDataRow()
	{
		$data = array(
			'usr_id' => $this->getId(),
			'usr_language_id' => $this->getLanguage_id(),
			'usr_username' => $this->getUsername(),
			'usr_firstname' => $this->getFirstname(),
			'usr_lastname' => $this->getLastname(),
			'usr_password' => $this->getPassword(),
			'usr_email' => $this->getEmail(),
			'usr_emailcorrupted' => $this->getEmailcorrupted(),
			'usr_avatar' => $this->getAvatar(),
			'usr_birthday' => $this->getBirthdayAsString(),
			'usr_gender' => $this->getGender(),
			'usr_status' => $this->getStatus(),
			'usr_otp' => $this->getOtp(),
			'usr_admin' => $this->getAdmin(),
			'usr_created' => $this->getCreatedAsString(),
			'usr_createduser_id' => $this->getCreateduser_Id(),
			'usr_modified' => $this->getModifiedAsString(),
			'usr_modifieduser_id' => $this->getModifieduser_Id()
		);

		return $data;
	}

	/**
	 * Liefert das Alter des Mitglieds
	 *
	 * @return string
	 */
	public function getAge()
	{
		$today = new \DateTime();
		$birthdate = $this->getBirthday();
		$interval = $today->diff($birthdate);

		return $interval->format('%y');
	}
}