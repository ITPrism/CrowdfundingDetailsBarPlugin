<?php
/**
 * @package      Crowdfunding
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Crowdfunding Details Bar Plugin
 *
 * @package      Crowdfunding
 * @subpackage   Plugins
 */
class plgContentCrowdfundingDetailsBar extends JPlugin
{
    /**
     * @param string  $context
     * @param stdClass $item
     * @param Joomla\Registry\Registry $params
     *
     * @return null|string
     * @throws Exception
     */
    public function onContentBeforeDisplay($context, $item, $params)
    {
        // Check the position.
        if (strcmp('before_content', $this->params->get('position', 'before_content')) !== 0) {
            return null;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp('html', $docType) !== 0) {
            return null;
        }

        if (strcmp('com_crowdfunding.details', $context) !== 0) {
            return null;
        }

        // Prepare output
        return $this->prepareContent($item);
    }


    /**
     * @param string  $context
     * @param stdClass $item
     * @param Joomla\Registry\Registry $params
     *
     * @return null|string
     * @throws Exception
     */
    public function onContentAfterDisplayMedia($context, $item, $params)
    {
        // Check the position.
        if (strcmp('after_media', $this->params->get('position', 'before_content')) !== 0) {
            return null;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp('html', $docType) !== 0) {
            return null;
        }

        if (strcmp('com_crowdfunding.details', $context) !== 0) {
            return null;
        }

        // Prepare output
        return $this->prepareContent($item);
    }

    /**
     * @param string  $context
     * @param stdClass $item
     * @param Joomla\Registry\Registry $params
     *
     * @return null|string
     * @throws Exception
     */
    public function onContentAfterDisplay($context, $item, $params)
    {
        // Check the position.
        if (strcmp('after_content', $this->params->get('position', 'before_content')) !== 0) {
            return null;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp('html', $docType) !== 0) {
            return null;
        }

        if (strcmp('com_crowdfunding.details', $context) !== 0) {
            return null;
        }

        // Prepare output
        return $this->prepareContent($item);
    }

    protected function prepareContent($item)
    {
        // Load language
        $this->loadLanguage();

        $itemData = $this->getData($item->id);

        // Get the path for the layout file
        $path = JPluginHelper::getLayoutPath('content', 'crowdfundingdetailsbar', 'default');

        // Render the login form.
        ob_start();
        include $path;

        return ob_get_clean();
    }

    /**
     * @throws \RuntimeException
     *
     * @param int $projectId
     *
     * @return array
     */
    protected function getData($projectId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(
                'a.id, a.title, ' .
                $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug'
            )
            ->from($db->quoteName('#__crowdf_projects', 'a'));
        
        // Get category.
        if ((bool)$this->params->get('show_category', 1)) {
            $query
                ->select(
                    'c.title AS category, '.
                    $query->concatenate(array('c.id', 'c.alias'), ':') . ' AS catslug'
                )
                ->leftJoin($db->quoteName('#__categories', 'c') . ' ON a.catid = c.id');
        }

        // Get type.
        if ((bool)$this->params->get('show_type', 0)) {
            $query
                ->select('t.id AS type_id, t.title AS type')
                ->leftJoin($db->quoteName('#__crowdf_types', 't') . ' ON a.type_id = t.id');
        }

        $query->where('a.id = '. (int)$projectId);

        $db->setQuery($query);
        $rowOne = (array)$db->loadAssoc();

        // The second row.

        $rowTwo = array();

        // Get location ID.
        $showCountry  = (bool)$this->params->get('show_country', 0);
        $showLocation = (bool)$this->params->get('show_location', 0);
        $showRegion   = (bool)$this->params->get('show_region', 0);
        if ($showLocation or $showCountry or $showRegion) {
            $query = $db->getQuery(true);
            $query
                ->select(
                    'a.id, a.title, ' .
                    'l.id AS locid, l.name AS location, ' .
                    $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug '
                )
                ->from($db->quoteName('#__crowdf_projects', 'a'))
                ->leftJoin($db->quoteName('#__crowdf_locations', 'l') . ' ON a.location_id = l.id');

            if ($showCountry) { // Load country data.
                $query
                    ->select('l.country_code, co.name AS country ')
                    ->leftJoin($db->quoteName('#__crowdf_countries', 'co') . ' ON l.country_code = co.code');
            }

            if ($showRegion) {
                $query
                    ->select('l.admin1code_id AS region_code, r.name AS region ')
                    ->leftJoin($db->quoteName('#__crowdf_regions', 'r') . ' ON l.admin1code_id = r.admincode_id');
            }

            $query->where('a.id = '. (int)$projectId);

            $db->setQuery($query);
            $rowTwo = (array)$db->loadAssoc();
        }

        return array_merge($rowOne, $rowTwo);
    }
}
