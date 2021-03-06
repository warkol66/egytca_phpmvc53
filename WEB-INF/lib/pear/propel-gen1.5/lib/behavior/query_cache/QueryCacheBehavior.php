<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * Speeds up queries on a model by caching the query
 *
 * @author     François Zaninotto
 * @version    $Revision: 1619 $
 * @package    propel.generator.behavior.cacheable
 */
class QueryCacheBehavior extends Behavior
{
	// default parameters value
	protected $parameters = array(
		'backend'     => 'apc',
		'lifetime'    => 3600,
	);
	
	public function queryAttributes($builder)
	{
		$script = "protected \$queryKey = '';
";
		switch ($this->getParameter('backend')) {
			case 'backend':
				$script .= "protected static \$cacheBackend = array();
			";
				break;
			case 'apc':
				break;
			case 'custom':
			default:
				$script .= "protected static \$cacheBackend;
			";
				break;
		}
		
		return $script;
	}
	
	public function queryMethods($builder)
	{
		$this->peerClassname = $builder->getStubPeerBuilder()->getClassname();
		$script = '';
		$this->addSetQueryKey($script);
		$this->addGetQueryKey($script);
		$this->addCacheContains($script);
		$this->addCacheFetch($script);
		$this->addCacheStore($script);
		$this->addGetSelectStatement($script);
		
		return $script;
	}

	protected function addSetQueryKey(&$script)
	{
		$script .= "
public function setQueryKey(\$key)
{
	\$this->queryKey = \$key;
	return \$this;
}
";
	}
	
	protected function addGetQueryKey(&$script)
	{
		$script .= "
public function getQueryKey()
{
	return \$this->queryKey;
}
";
	}
	
	protected function addCacheContains(&$script)
	{
		$script .= "
public function cacheContains(\$key)
{";
		switch ($this->getParameter('backend')) {
			case 'apc':
				$script .= "
	return apc_fetch(\$key);";
				break;
			case 'array':
				$script .= "
	return isset(self::\$cacheBackend[\$key]);";
				break;
			case 'custom':
			default:
				$script .= "
	throw new PropelException('You must override the cacheContains(), cacheStore(), and cacheFetch() methods to enable query cache');";
				break;

		}
		$script .= "
}
";
	}
	
	protected function addCacheStore(&$script)
	{
		$script .= "
public function cacheStore(\$key, \$value, \$lifetime = " .$this->getParameter('lifetime') . ")
{";
		switch ($this->getParameter('backend')) {
			case 'apc':
				$script .= "
	apc_store(\$key, \$value, \$lifetime);";
				break;
			case 'array':
				$script .= "
	self::\$cacheBackend[\$key] = \$value;";
				break;
			case 'custom':
			default:
				$script .= "
	throw new PropelException('You must override the cacheContains(), cacheStore(), and cacheFetch() methods to enable query cache');";
				break;
		}
		$script .= "
}
";
	}
	
	protected function addCacheFetch(&$script)
	{
		$script .= "
public function cacheFetch(\$key)
{";
		switch ($this->getParameter('backend')) {
			case 'apc':
				$script .= "
	return apc_fetch(\$key);";
				break;
			case 'array':
				$script .= "
	return isset(self::\$cacheBackend[\$key]) ? self::\$cacheBackend[\$key] : null;";
				break;
			case 'custom':
			default:
				$script .= "
	throw new PropelException('You must override the cacheContains(), cacheStore(), and cacheFetch() methods to enable query cache');";
				break;
		}
		$script .= "
}
";
	}
	
	protected function addGetSelectStatement(&$script)
	{
		$script .= "
protected function getSelectStatement(\$con = null)
{
	\$dbMap = Propel::getDatabaseMap(" . $this->peerClassname ."::DATABASE_NAME);
	\$db = Propel::getDB(" . $this->peerClassname ."::DATABASE_NAME);
  if (\$con === null) {
		\$con = Propel::getConnection(" . $this->peerClassname ."::DATABASE_NAME, Propel::CONNECTION_READ);
	}
	
	// we may modify criteria, so copy it first
	\$criteria = clone \$this;

	if (!\$criteria->hasSelectClause()) {
		\$criteria->addSelfSelectColumns();
	}
	
	\$con->beginTransaction();
	try {
		\$criteria->basePreSelect(\$con);
		\$key = \$criteria->getQueryKey();
		if (\$key && \$criteria->cacheContains(\$key)) {
			\$params = \$criteria->getParams();
			\$sql = \$criteria->cacheFetch(\$key);
		} else {
			\$params = array();
			\$sql = BasePeer::createSelectSql(\$criteria, \$params);
			if (\$key) {
				\$criteria->cacheStore(\$key, \$sql);
			}
		}
		\$stmt = \$con->prepare(\$sql);
		BasePeer::populateStmtValues(\$stmt, \$params, \$dbMap, \$db);
		\$stmt->execute();
		\$con->commit();
	} catch (PropelException \$e) {
		\$con->rollback();
		throw \$e;
	}
	
	return \$stmt;
}
";
	}

}