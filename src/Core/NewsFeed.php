<?php
namespace ACHD\Core
abstract class NewsFeed{
	protected static $type;
	protected static $contentHeaders;
	public $items = array();
	protected $data;
	protected $schema = array();
	protected $channelInfo = array();
	
	abstract public function setHeading($info);
	abstract public function setSchema($info);
	abstract public function saveAsFile($name);
	abstract public function outputToBrowser();
	abstract public function setItems($items);
	abstract protected function processItem($itm);
	abstract public function process();
	
}