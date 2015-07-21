<?php

namespace Corework\Application\Models;


use Corework\Model;

/**
 * Class UserModel
 *
 * @category Basis
 * @package  Corework\Application\Models
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
class UserModel extends Model
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
	 * @Column(type="string", length=64, unique=true, name="usr_username)
	 */
	protected $username = '';

	/**
	 * First name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=32, nullable=true, name="usr_firstname")
	 */
	protected $firstname = '';

	/**
	 * Last name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=32, nullable=true, name="usr_lastname")
	 */
	protected $lastname = '';

	/**
	 * Password
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255, name="usr_password")
	 */
	protected $password = '';

	/**
	 * Email
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255, name="usr_email")
	 */
	protected $email = '';

	/**
	 * Is email corrupted
	 *
	 * @var boolean
	 *
	 * @Column(type="boolean", name="usr_emailcorrupted")
	 */
	protected $emailcorrupted = false;

	/**
	 * Avatar
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255, nullable=true, name="usr_avatar')
	 */
	protected $avatar = '';

	/**
	 * Birthday
	 *
	 * @var \DateTime
	 *
	 * @Column(type="date", name="usr_birthday")
	 */
	protected $birthday = '';

	/**
	 * Gender
	 *
	 * @var integer
	 *
	 * @Column(type="integer", name="usr_gender")
	 */
	protected $gender = 0;

	/**
	 * Status
	 *
	 * @var integer
	 *
	 * @Column(type="integer", name="usr_status")
	 */
	protected $status = 1;

	/**
	 * Einmal-Passwort wurde gesetzt
	 *
	 * @var bool
	 *
	 * @Column(type="boolean", name="usr_otp")
	 */
	protected $otp = false;

	/**
	 * Administrator
	 *
	 * @var bool
	 *
	 * @Column(type="boolean", name="usr_admin")
	 */
	protected $admin = false;


	/**
	 * Language
	 *
	 * @ManyToOne(targetEntity="App\Models\Language")
	 * @JoinColumn(name="usr_language_id", referencedColumnName="lng_id", nullable=false)
	 */
	protected $language_id = null;

	/**
	 * Created (Registration date)
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime", name="usr_created")
	 */
	protected $created;

	/**
	 * Modified User Id
	 *
	 * @var \int
	 *
	 * @ManyToOne(targetEntity="App\Models\User")
	 * @JoinColumn(name="usr_modifieduser_id", referencedColumnName="usr_id", nullable=true)
	 */
	protected $createduser_id = null;


	/**
	 * Modified Datum
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime", name="usr_modified")
	 */
	protected $modified = null;

	/**
	 * Modified User Id
	 *
	 * @var \int
	 *
	 * @ManyToOne(targetEntity="App\Models\User")
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
	 */
	public function setUsername($username)
	{
		$this->username = $username;
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
	 */
	public function setFirstname($firstname)
	{
		$this->firstname = $firstname;
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
	 */
	public function setLastname($lastname)
	{
		$this->lastname = $lastname;
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
	 */
	public function setEmail($email)
	{
		$this->email = $email;
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
	 */
	public function setEmailcorrupted($emailcorrupted)
	{
		$this->emailcorrupted = $emailcorrupted;
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
	 */
	public function setAvatar($avatar)
	{
		$this->avatar = $avatar;
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
		if (!($datetime instanceof \DateTime))
		{
			try
			{
				$datetime = new \DateTime($this->clearDateTimeSting($datetime));
			} catch (\Exception $e)
			{
				throw new \InvalidArgumentException('Ungültige Datumsangabe');
			}
		}

		$this->created = $datetime;
	}

	/**
	 * Liefert das Geburtsdatum Datetime im Format Y-m-d H:i:s zurück
	 *
	 * @return string
	 */
	public function getBirthdayAsString()
	{
		return $this->getBirthday()->format('Y-m-d H:i:s');
	}

	/**
	 * @return mixed
	 */
	public function getBirthday()
	{
		if (!($this->birthday instanceof \DateTime))
		{
			$this->birthday = new \DateTime(!empty($this->birthday) ? $this->birthday : '0000-00-00 00:00:00');
		}
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
	 */
	public function setGender($gender)
	{
		$this->gender = $gender;
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
	 */
	public function setStatus($status)
	{
		$this->status = $status;
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
	 */
	public function setOtp($otp)
	{
		$this->otp = $otp;
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
	 */
	public function setAdmin($admin)
	{
		$this->admin = $admin;
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
	 */
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return '';
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

}