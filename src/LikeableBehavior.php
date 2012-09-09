<?php

/**
 * @license    MIT License
 */

/**
 * @author Evgeny Smirnov <es@4xxi.com>
 */
class LikeableBehavior extends Behavior
{
	
	protected $likeTable;
	protected $builder;
	
	public function modifyTable()
  {
		$this->createLikesTable();
  }
  
	protected function createLikesTable()
	{
		$table        = $this->getTable();
		$database     = $table->getDatabase();
		$likeTableName = 'likes';

		if ($database->hasTable($likeTableName)) {
			$this->likeTable = $database->getTable($likeTableName);
		} else {
			$this->likeTable = $database->addTable(array(
				'name'      => $likeTableName,
				'phpName'   => 'Like',
				'package'   => $table->getPackage(),
				'schema'    => $table->getSchema(),
				'namespace' => '\\'.$table->getNamespace(),
			));
		}
		
		$likeTableIdColumn = 'id';
		if (!$this->likeTable->hasColumn($likeTableIdColumn)) {
			$this->likeTable->addColumn(array(
			'name'          => $likeTableIdColumn,
			'phpName'       => 'Id',
			'type'          => \PropelTypes::INTEGER,
			'primaryKey'    => 'true',
			'autoIncrement' => 'true',
			));
		}

		if (!$this->likeTable->hasColumn('user_id')) {
			$this->likeTable->addColumn(array(
			'name'          => 'user_id',
			'phpName'       => 'UserId',
			'type'          => \PropelTypes::INTEGER,
			));
		}

		if (!$this->likeTable->hasColumn('type')) {
			$this->likeTable->addColumn(array(
			'name'          => 'type',
			'phpName'       => 'Type',
			'type'          => \PropelTypes::VARCHAR,
			'size'          => '50',
			));
		}

		if (!$this->likeTable->hasColumn('item_id')) {
			$this->likeTable->addColumn(array(
			'name'          => 'item_id',
			'phpName'       => 'ItemId',
			'type'          => \PropelTypes::INTEGER,
			));
		}

		if (!$this->likeTable->hasColumn('mark')) {
			$this->likeTable->addColumn(array(
			'name'          => 'mark',
			'phpName'       => 'Mark',
			'type'          => \PropelTypes::INTEGER,
			));
		}
		
	}
	
  public function objectMethods($builder)
  {
		$this->builder = $builder;
		$content = '';
		
		$this->addLikeMethod($content);
		$this->removeLikeMethod($content);
		$this->hasLikeMethod($content);
		$this->countLikesMethod($content);

		return $content;
  }

	private function addLikeMethod(&$content)
	{
    $table = $this->getTable();
    $content .= "

/**
 * Add like object 
 * @param integer   \$user_id
 * @param integer   \$mark (optional)
 * @param PropelPDO \$con optional connection object
 * @return {$this->likeTable->getPhpName()}|false
*/
public function addLike(\$user_id, \$mark = 1, PropelPDO \$con = null)
{
	// Check is like already in DB or not
	\$like = {$this->likeTable->getPhpName()}Query::create()->filterByUserId(\$user_id)->filterByType(get_class(\$this))->filterByItemId(\$this->getId())->findOne();
	if (is_object(\$like))
	{
		return false;
	}
	\$like = new {$this->likeTable->getPhpName()}();
	\$like->setUserId(\$user_id);
	\$like->setMark(\$mark);
	\$like->setType(get_class(\$this));
	\$like->setItemId(\$this->getId());
	\$like->save();

	return \$like;
}"
		;
	}

	private function removeLikeMethod(&$content)
	{
    $table = $this->getTable();
    $content .= "

/**
 * @param integer   \$user_id
 * @param PropelPDO \$con optional connection object
 * @return boolean
*/
public function removeLike(\$user_id, PropelPDO \$con = null)
{
	// Check is like already in DB or not
	\$like = {$this->likeTable->getPhpName()}Query::create()->filterByUserId(\$user_id)->filterByType(get_class(\$this))->filterByItemId(\$this->getId())->delete();
	return \$like;
}"
		;
	}

	private function hasLikeMethod(&$content)
	{
    $table = $this->getTable();
    $content .= "

/**
 * @param integer   \$user_id
 * @param PropelPDO \$con optional connection object
 * @return boolean
*/
public function hasLike(\$user_id, PropelPDO \$con = null)
{
	// Check is like already in DB or not
	\$like = {$this->likeTable->getPhpName()}Query::create()->filterByUserId(\$user_id)->filterByType(get_class(\$this))->filterByItemId(\$this->getId())->findOne();
	if (is_object(\$like))
	{
		return true;
	}
	return false;
}"
		;
	}
	
	private function countLikesMethod(&$content)
	{
    $table = $this->getTable();
    $content .= "

/**
 * @param PropelPDO \$con optional connection object
 * @return integer
*/
public function countLikes(PropelPDO \$con = null)
{
	// Check is like already in DB or not
	return {$this->likeTable->getPhpName()}Query::create()->filterByType(get_class(\$this))->filterByItemId(\$this->getId())->count();
}"
		;
	}
	
	public function objectFilter(&$script)
	{
		/**
		 * @todo fix hook
		 * Add namespace use for Like* classes
		 */
		$namespace = str_replace('\\om', '', $this->builder->getNamespace());
		$script = "
use ".$namespace."\\Like;
use ".$namespace."\\LikeQuery;
		".$script;
	}
	
}