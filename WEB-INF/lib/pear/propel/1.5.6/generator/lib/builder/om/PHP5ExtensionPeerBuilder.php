<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once 'builder/om/PeerBuilder.php';

/**
 * Generates the empty PHP5 stub peer class for user object model (OM).
 *
 * This class produces the empty stub class that can be customized with application
 * business logic, custom behavior, etc.
 *
 * This class replaces the ExtensionPeer.tpl, with the intent of being easier for users
 * to customize (through extending & overriding).
 *
 * @author     Hans Lellelid <hans@xmpl.org>
 * @package    propel.generator.builder.om
 */
class PHP5ExtensionPeerBuilder extends PeerBuilder
{

	/**
	 * Returns the name of the current class being built.
	 * @return     string
	 */
	public function getUnprefixedClassname()
	{
		return $this->getStubObjectBuilder()->getUnprefixedClassname() . 'Peer';
	}

	/**
	 * Adds the include() statements for files that this class depends on or utilizes.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addIncludes(&$script)
	{
		switch($this->getTable()->treeMode()) {
			case 'NestedSet':
				$requiredClassFilePath = $this->getNestedSetPeerBuilder()->getClassFilePath();
			break;

			case 'MaterializedPath':
			case 'AdjacencyList':
			default:
				$requiredClassFilePath = $this->getPeerBuilder()->getClassFilePath();
			break;
		}

		$script .="
require '".$requiredClassFilePath."';
";
	} // addIncludes()

	/**
	 * Adds class phpdoc comment and openning of class.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addClassOpen(&$script)
	{
		$table = $this->getTable();
		$this->declareClassFromBuilder($this->getPeerBuilder());
		$tableName = $table->getName();
		$tableDesc = $table->getDescription();

		switch($table->treeMode()) {
			case 'NestedSet':
				$baseClassname = $this->getNestedSetPeerBuilder()->getClassname();
			break;

			case 'MaterializedPath':
			case 'AdjacencyList':
			default:
				$baseClassname = $this->getPeerBuilder()->getClassname();
			break;
		}

		$script .= "

/**
 * Skeleton subclass for performing query and update operations on the '$tableName' table.
 *
 * $tableDesc
 *";
		if ($this->getBuildProperty('addTimeStamp')) {
			$now = strftime('%c');
			$script .= "
 * This class was autogenerated by Propel " . $this->getBuildProperty('version') . " on:
 *
 * $now
 *";
		}
		$script .= "
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.".$this->getPackage()."
 */
class ".$this->getClassname()." extends $baseClassname {
";
	}

		/**
	 * Specifies the methods that are added as part of the stub peer class.
	 *
	 * By default there are no methods for the empty stub classes; override this method
	 * if you want to change that behavior.
	 *
	 * @see        ObjectBuilder::addClassBody()
	 */

	protected function addClassBody(&$script)
	{
		// there is no class body
	}

	/**
	 * Closes class.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addClassClose(&$script)
	{
		$script .= "
} // " . $this->getClassname() . "
";
		$this->applyBehaviorModifier('extensionPeerFilter', $script, "");
	}


} // PHP5ExtensionPeerBuilder
