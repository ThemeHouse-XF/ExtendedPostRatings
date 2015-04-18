<?php
class ExtendPostRating_Listener_CodeEvent
{
	public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
	{
		$xenOption = XenForo_Application::getOptions();
		$model = XenForo_Model::create('Dark_PostRating_Model');
		switch ($hookName)
		{
			case 'forum_list_sidebar':
				if ($xenOption->epr_enableSidebarBlock)
				{
					$ratings = $model->getActiveRatings();
					$ratingIds = array();
					$topUsers = array();
					foreach ($ratings as &$rating)
					{
						$rating['top_user'] = $model->getTopUserForRatingId($rating['id']);
					}

					$viewParams = array(
						'ratings'	=> $ratings
					);
					$contents .= $template->create('epr_top_user_sidebar', $viewParams)->render();
				}
				break;
		}
	}
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
}