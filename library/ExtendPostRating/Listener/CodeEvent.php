<?php
class ExtendPostRating_Listener_CodeEvent
{
	public static function loadClass($class, array &$extend)
	{
		switch ($class)
		{
			case 'Dark_PostRating_Model':
				$extend[] = 'ExtendPostRating_Model';
				break;
			case 'XenForo_ControllerPublic_Member':
				$extend[] = 'ExtendPostRating_ControllerPublic_Member';
				break;
			case 'XenForo_Model_Like':
				$extend[] = 'ExtendPostRating_Model_Like';
				break;
			case 'XenForo_DataWriter_User':
				$extend[] = 'ExtendPostRating_DataWriter_User';
				break;
		}
	}

	public static function navigationTabs(array &$extraTabs, $selectedTabId)
	{
		$xenOptions = XenForo_Application::getOptions();
		if ($xenOptions->epr_enableNavigationLink)
		{
			$extraTabs['postRatings'] = array(
				'title'			=> new XenForo_Phrase('dark_post_ratings'),
				'href'			=> XenForo_Link::buildPublicLink('post-ratings'),
				'position'		=> 'middle',
				'linksTemplate'	=> 'epr_navigation_tab',
			);
		}
	}

	public static function widgetFrameworkReady(&$renderers)
	{
		$renderers[]= "ExtendPostRating_WidgetRenderer_TopUsers";
	}
}