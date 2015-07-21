<?php

namespace Core\Calendar;

/**
 * Class Ics
 *
 * @category Core
 * @package  Core\Calendar
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Ics
{

	protected $prodid = "-//Wellergy Health Management Calendar Version 1.0//DE";
	protected $version = "2.0";

	/*
	BEGIN:VCALENDAR
	PRODID:-//xyz Corp//NONSGML PDA Calendar Version 1.0//EN
	VERSION:2.0
	BEGIN:VEVENT
	DTSTAMP:19960704T120000Z
	UID:uid1@example.com
	ORGANIZER:mailto:jsmith@example.com
	DTSTART:19960918T143000Z
	DTEND:19960920T220000Z
	STATUS:CONFIRMED
	CATEGORIES:CONFERENCE
	SUMMARY:Networld+Interop Conference
	DESCRIPTION:Networld+Interop Conference
	and Exhibit\nAtlanta World Congress Center\n
	Atlanta\, Georgia
	END:VEVENT
	END:VCALENDAR
	*/
}