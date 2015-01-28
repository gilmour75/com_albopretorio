<?php
/**
* @version		$Id:$
* @package		plgSystemAlbopretorio
* @copyright	Copyright (C) 2015 ItOpen. All rights reserved.
* @licence      GNU/AGPL
*
* @description
*
* This plugin implements an ACL to prevent users enter Albo Pretorio
* category management, the ACL is 'category.create', 'com_albopretorio'
* defined in access.xml
*
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! Albopretorio  plugin
 *
 * @package		COM_ALBOPRETORIO
 * @subpackage	System
 */
class plgSystemAlbopretorio extends JPlugin
{
    protected $_component;

    function __construct( $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage('com_albopretorio', JPATH_ADMINISTRATOR . '/components/com_albopretorio/');
        $this->_component = JComponentHelper::getComponent('com_albopretorio');
    }

    function onAfterRoute(){

        $app = JFactory::getApplication();
        if (!$app->isAdmin()){
            return true;
        }

        JURI::current();// It's very strange, but without this line at least Joomla 3 fails to fulfill the task
        $router = JSite::getRouter();// get router
        $d = JURI::getInstance();
        $query = $router->parse($d); // Get the real joomla query as an array - parse current joomla link
        $url = 'index.php?'.JURI::getInstance()->buildQuery($query);

        // Check component's ACL
        if ($query['option'] == 'com_categories' && $query['extension'] == 'com_albopretorio' ) {
            if ( $this->_component ){
                $user    = JFactory::getUser();
                if (! $user->authorise('category.create', 'com_albopretorio') ) {
                    JError::raiseWarning( 100, JText::_( 'COM_ALBOPRETORIO_CATEGORY_EDIT_DISALLOWED' ));
                    $link = JRoute::_('index.php?option=com_albopretorio');
                    JFactory::getApplication()->redirect($link);
                }
            }
        }
        return true;
    }

}
