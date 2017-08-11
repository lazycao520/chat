<?php
/**
* app
*/
class App
{
	
	public static function getInstane($app='')
	{
		if (empty($app)) {
			return;
		}else{
			return call_user_method('getInstane', $app)
		}
		
	}
}