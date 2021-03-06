<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchItem extends KalturaESearchBaseItem {

	/**
	 * @var string
	 */
	public $searchTerm;

	/**
	 * @var KalturaESearchItemType
	 */
	public $itemType;

	/**
	 * @var KalturaESearchRange
	 */
	public $range;

	private static $map_between_objects = array(
		'searchTerm',
		'itemType',
		'range',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	abstract protected function getItemFieldName();

	abstract protected function getDynamicEnumMap();

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$dynamicEnumMap = $this->getDynamicEnumMap();
		if(isset($dynamicEnumMap[$this->getItemFieldName()]))
		{
			try
			{
				$enumType = call_user_func(array($dynamicEnumMap[$this->getItemFieldName()], 'getEnumClass'));
				$SearchTermValue = kPluginableEnumsManager::apiToCore($enumType, $this->searchTerm);
				$object_to_fill->setSearchTerm($SearchTermValue);
				$props_to_skip[] = 'searchTerm';
			}
			catch (kCoreException $e)
			{
				if($e->getCode() == kCoreException::ENUM_NOT_FOUND)
					throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $this->searchTerm, 'searchTerm', $dynamicEnumMap[$this->getItemFieldName()]);
			}

		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
