<?php

/**
 * File Doc Comment_
 * PHP version 5
 *
 * @category  Component
 * @package   Joomla.Administrator
 * @author    Joomla! <admin@joomla.org>
 * @copyright (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 * @link      admin@joomla.org
 */

namespace Joomla\Component\Guidedtours\Administrator\Table;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Factory;

/**
 * Guidedtour_steps table
 *
 * @since 1.5
 */
class StepTable extends Table
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver $db Database connector object
	 *
	 * @since 1.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__guidedtour_steps', 'id', $db);
	}

	/**
	 * Overloaded store function
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  mixed  False on failure, positive integer on success.
	 *
	 * @see     Table::store()
	 * @since   4.0.0
	 */
	public function store($updateNulls = true)
	{
		$date = Factory::getDate();
		$user = Factory::getUser();

		$table = new TourTable($this->getDbo());

		if ($this->id)
		{
			// Existing item
			$this->modified_by = $user->id;
			$this->modified = $date->toSql();
		}
		else
		{
			$this->modified_by = 0;
		}

		if (!(int) $this->created)
		{
			$this->created = $date->toSql();
		}

		if (empty($this->created_by))
		{
			$this->created_by = $user->id;
		}

		if (!(int) $this->modified)
		{
			$this->modified = $this->created;
		}

		if (empty($this->modified_by))
		{
			$this->modified_by = $this->created_by;
		}

		if ($this->default == '1')
		{
			// Verify that the default is unique for this workflow
			if ($table->load(array('default' => '1')))
			{
				$table->default = 0;
				$table->store();
			}
		}

		return parent::store($updateNulls);
	}
}