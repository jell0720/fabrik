<?php
/**
 * Raw Visualization controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Fabrik. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @since       1.6
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Raw Visualization controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	Fabrik
 * @since		3.0
 */
class FabrikControllerVisualization extends JControllerForm
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var	string
	 */
	protected $text_prefix = 'COM_FABRIK_VISUALIZATION';

	/**
	 * Called via ajax to perform viz ajax task (defined by plugintask method)
	 *
	 * @return  JController  A JController object to support chaining.
	 */

	public function display()
	{
		$document = JFactory::getDocument();
		$id = JRequest::getInt('visualizationid');
		$viz = FabTable::getInstance('Visualization', 'FabrikTable');
		$viz->load($id);
		$modelpaths = JModel::addIncludePath(JPATH_SITE . '/plugins/fabrik_visualization/' . $viz->plugin . '/models');
		$model = $this->getModel($viz->plugin);
		$model->setId($id);
		$pluginTask = JRequest::getVar('plugintask', '', 'request');
		if ($pluginTask !== '')
		{
			echo $model->$pluginTask();
		}
		else
		{
			$task = JRequest::getVar('task');

			$path = JPATH_SITE . '/plugins/fabrik_visualization/' . $viz->plugin . '/controllers/' . $viz->plugin . '.php';
			if (file_exists($path))
			{
				require_once $path;
			}
			else
			{
				JError::raiseNotice(400, 'could not load viz:' . $viz->plugin);
				return;
			}

			$controllerName = 'FabrikControllerVisualization' . $viz->plugin;
			$controller = new $controllerName();
			$controller->addViewPath(JPATH_SITE . '/plugins/fabrik_visualization/' . $viz->plugin . '/views');
			$controller->addViewPath(COM_FABRIK_FRONTEND . '/views');

			//add the model path
			$modelpaths = JModel::addIncludePath(JPATH_SITE . '/plugins/fabrik_visualization/' . $viz->plugin . '/models');
			$modelpaths = JModel::addIncludePath(COM_FABRIK_FRONTEND . '/models');

			$origId = JRequest::getInt('visualizationid');
			JRequest::setVar('visualizationid', $id);
			$controller->$task();

		}
		return $this;
	}

	/**
	 * Get html for viz plugin
	 *
	 * @return  null
	 */

	public function getPluginHTML()
	{
		$plugin = JRequest::getCmd('plugin');
		$model = $this->getModel();
		$model->getForm();
		echo $model->getPluginHTML($plugin);
	}

}
