<?php
/*
 * @version $Id$
 LICENSE

 This file is part of the datainjection plugin.

 Datainjection plugin is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Datainjection plugin is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with datainjection. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 @package   datainjection
 @author    the datainjection plugin team
 @copyright Copyright (c) 2010-2011 Order plugin team
 @license   GPLv2+
            http://www.gnu.org/licenses/gpl.txt
 @link      https://forge.indepnet.net/projects/datainjection
 @link      http://www.glpi-project.org/
 @since     2009
 ---------------------------------------------------------------------- */
 
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginDatainjectionKnowbaseItemCategoryInjection extends KnowbaseItemCategory
                                                       implements PluginDatainjectionInjectionInterface {

   function __construct() {
      $this->table = getTableForItemType(get_parent_class($this));
   }


   function isPrimaryType() {
      return true;
   }


   function connectedTo() {
      return array();
   }


   function getOptions($primary_type = '') {
      global $LANG;

      $tab = Search::getOptions(get_parent_class($this));

      $tab[100]['name']        = $LANG['setup'][75];
      $tab[100]['table']       = $this->table;
      $tab[100]['field']       = 'completename';
      $tab[100]['linkfield']   = 'completename';
      $tab[100]['injectable']  = 1;
      $tab[100]['checktype']   = 'text';
      $tab[100]['displaytype'] = 'dropdown_kb_category';

      //Remove some options because some fields cannot be imported
      $options['ignore_fields'] = array(1, 80, 2, 19);
      $options['displaytype']   = array("multiline_text" => array(16));
      $tab = PluginDatainjectionCommonInjectionLib::addToSearchOptions($tab, $options, $this);

      return $tab;
   }


   /**
    * Standard method to add an object into glpi
 
    *
    * @param values fields to add into glpi
    * @param options options used during creation
    *
    * @return an array of IDs of newly created objects : for example array(Computer=>1, Networkport=>10)
   **/
   function addOrUpdateObject($values=array(), $options=array()) {

      $lib = new PluginDatainjectionCommonInjectionLib($this, $values, $options);
      $lib->processAddOrUpdate();
      return $lib->getInjectionResults();
   }


   function getSpecificFieldValue($itemtype, $searchOption, $field, &$values) {
      global $DB;

      $parent = $values[$itemtype]['name'];
      $value  = $values[$itemtype][$field];

      switch ($searchOption['displaytype']) {
         case "dropdown_kb_category" :
            $query = "SELECT `level`, `completename`
                      FROM `glpi_knowbaseitemcategories`
                      WHERE `completename` = '$value'";
            $result = $DB->query($query);
            if ($DB->numrows($result) > 0) {
               $parent = $value . ' > '.$values[$itemtype]['name'];
               $values[$itemtype]['level'] = ($DB->result($result,0,'level') + 1);
            } else {
               $values[$itemtype]['level'] = 1;
            }
            break;
      }
      return $parent;
   }

}

?>