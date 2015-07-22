<?php

namespace Corework\Application\Models;

/**
 * Class LanguageModel
 *
 * @category Corework
 * @package  Corework\Application\Models
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 *
 * @MappedSuperclass
 */
class LanguageModel extends \Corework\Model
{
	/**
	 * Id
	 *
	 * @var integer
	 *
	 * @Id
	 * @Column(type="integer", name="lng_id")
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Kurzversion D, I, NL ... however
	 *
	 *
	 * @var string
	 *
	 * @Column(type="string", length=8, name="lng_short")
	 */
	protected $short;


	/**
	 * Landesspezifischer Name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=50, nullable=true, name="lng_national")
	 */
	protected $national;

	/**
	 * Internationaler Name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=50, name="lng_international")
	 */
	protected $international;

	/**
	 * Iso-Code from ISO-639-1
	 *
	 * @var string
	 *
	 * @Column(type="string", length=10, name="lng_isocode")
	 */
	protected $isocode;

	/**
	 * Iso-Code from ISO-3166-1
	 *
	 * @var string
	 *
	 * @Column(type="string", length=10, name="lng_countryCode")
	 */
	protected $countryCode;

	/**
	 * Created (Registration date)
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime", name="lng_created")
	 */
	protected $created;

	/**
	 * Modified User Id
	 *
	 * @var \int
	 *
	 * @ManyToOne(targetEntity="App\Models\UserModel")
	 * @JoinColumn(name="lng_createduser_id", referencedColumnName="usr_id", nullable=true)
	 */
	protected $createduser_id = null;


	/**
	 * Modified Datum
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime", name="lng_modified")
	 */
	protected $modified = null;

	/**
	 * Modified User Id
	 *
	 * @var \int
	 *
	 * @ManyToOne(targetEntity="App\Models\UserModel")
	 * @JoinColumn(name="lng_modifieduser_id", referencedColumnName="usr_id", nullable=true)
	 */
	protected $modifieduser_id = null;

	/**
	 * @return array
	 */
	public function getDataRow()
	{
		$data = array(
			'lng_id' => $this->getId(),
			'lng_isocode' => $this->getIsocode(),
			'lng_countryCode' => $this->getCountryCode(),
			'lng_international' => $this->getInternational(),
			'lng_national' => $this->getNational()
		);

		return $data;
	}

	/**
	 * Liefert L�nder-Sprachkennzeichen
	 * Beispiel de-de oder de-AT
	 *
	 * @return string
	 */
	public function getHtmlLanguage()
	{
		return $this->getIsocode() . '-' . $this->getCountryCode();
	}

	/**
	 * @param string $countryCode
	 * @return void
	 */
	public function setCountryCode($countryCode)
	{
		$this->set('countryCode', $countryCode);
	}

	/**
	 * @return string
	 */
	public function getCountryCode()
	{
		return $this->countryCode;
	}

	/**
	 * @param string $international
	 * @return void
	 */
	public function setInternational($international)
	{
		$this->set('international', $international);
	}

	/**
	 * @return string
	 */
	public function getInternational()
	{
		return $this->international;
	}

	/**
	 * @param string $isocode
	 * @return void
	 */
	public function setIsocode($isocode)
	{
		$this->set('isocode', $isocode);
	}

	/**
	 * @return string
	 */
	public function getIsocode()
	{
		return $this->isocode;
	}

	/**
	 * @param string $national
	 * @return void
	 */
	public function setNational($national)
	{
		$this->set('national', $national);
	}

	/**
	 * @return string
	 */
	public function getNational()
	{
		return $this->national;
	}

	/**
	 * @param string $short
	 * @return void
	 */
	public function setShort($short)
	{
		$this->set('short', $short);
	}

	/**
	 * @return string
	 */
	public function getShort()
	{
		return $this->short;
	}
}