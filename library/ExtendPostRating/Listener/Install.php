<?php
class ExtendPostRating_Listener_Install
{
	public static function install($existingAddOn, $addOnData)
	{
		$db = XenForo_Application::getDb();
		if ($existingAddOn['version_id'] < 1000270)
		{
			$db->query('ALTER TABLE xf_user ADD epr_like_given INT(10) UNSIGNED NOT NULL DEFAULT \'0\' AFTER like_count;');
		}
	}

	public static function uninstall($addOnData)
	{
		$db = XenForo_Application::getDb();
		$db->query('ALTER TABLE xf_user DROP epr_like_given;');
	}
}