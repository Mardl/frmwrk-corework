<?php

namespace Corework\Form;

use Corework\Form, Corework\SystemMessages;

/**
 * Class BaseGenerator
 *
 * @category Corework
 * @package  Corework\Form
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class BaseGenerator
{

	protected $form;

	/**
	 * Konstruktor
	 *
	 * @param string $action Ziel des Formulars
	 * @param array  $data   Formulardaten
	 */
	public function __construct($action, $data = array())
	{
		$this->form = new Form($data);
		$this->form->setAction($action);
	}

	/**
	 * Liefert das Formularobjekt
	 *
	 * @return \Corework\Form
	 */
	public function getForm()
	{
		return $this->form;
	}

	/**
	 * Liefert das Formularobjekt zur Ausgabe
	 *
	 * @return \Corework\Form
	 */
	public function asString()
	{
		return $this->form;
	}

	/**
	 * @return mixed
	 */
	public function __toString()
	{
		return $this->form->__toString();
	}

	/**
	 * Standardvalidierung nach Pflichtfeldern
	 *
	 * @param \Corework\Form|\Corework\Form\Element $elementContainer
	 *
	 * @return bool
	 */
	protected function checkRequired($elementContainer)
	{
		$checkup = true;

		foreach ($elementContainer->getElements() as $el)
		{
			$check = true;
			if ($el instanceof \Corework\Html\Input)
			{
				$check = $el->validate();
			}

			if ($check !== true)
			{
				SystemMessages::addError($check);
				$el->addCssClass('error');
				$checkup = false;
			}

			if ($el->hasElements())
			{
				$check = $this->checkRequired($el);
				$checkup = $checkup && $check;
			}
		}

		return $checkup;
	}
}
