<?php
/*
Credits go to Kirth at MMOwned for this!
*/

//Configuration Class
class Configuration
{
	private static $settings = array();

	public static function Set($name, $value)
	{
		self::$settings[$name] = $value;
	}

	public static function Get($name)
	{
		// Note:
		// Not checking wether $name is valid
		// may cause variable corruption and/or
		// incorrect returning of values.
		return self::$settings[$name];
	}
}

?>
