<?php
/**
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package DublinCoreExtended
 */

define('DC_EXTENDED_PLUGIN_DIR', dirname(dirname(__FILE__)));

/**
 * Test suite for DublinCoreExtended.
 *
 * @package DublinCoreExtended
 * @copyright Center for History and New Media, 2010
 */
class DublinCoreExtended_AllTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new DublinCoreExtended_AllTests('DublinCoreExtended Tests');
        $testCollector = new PHPUnit_Runner_IncludePathTestCollector(
          array(dirname(__FILE__) . '/cases')
        );
        $suite->addTestFiles($testCollector->collectTests());
        return $suite;
    }
}
