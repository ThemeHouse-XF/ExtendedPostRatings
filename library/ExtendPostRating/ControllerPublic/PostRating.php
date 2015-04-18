<?php
class ExtendPostRating_ControllerPublic_PostRating extends XenForo_ControllerPublic_Abstract
{
	public function actionIndex()
	{
		$xenOption = XenForo_Application::getOptions();
		$type = $this->_input->filterSingle('type', XenForo_Input::STRING);
		$ratingId = $this->_input->filterSingle('rating', XenForo_Input::UINT);

		$model = $this->_getPostRatingModel();
		$postRatings = $model->getActiveRatings();
		if (!$ratingId)
		{
			$rating = reset($postRatings);
			$ratingId = $rating['id'];
		}
		$users = $this->_getTopUsers($ratingId, $type);

		if (!$type)
		{
			$bigKey = 'count_received';
		}
		else
		{
			$bigKey = 'count_given';
		}
		if ($xenOption->dark_postrating_like_id > 0 && $xenOption->dark_postrating_like_show && $ratingId == $xenOption->dark_postrating_like_id)
		{
			if (!$type)
			{
				$bigKey = 'like_count';
			}
			else
			{
				$bigKey = 'epr_like_given';
			}
		}

		$viewParams = array(
			'ratingId'		=> $ratingId,
			'type'			=> $type,
			'users'			=> $users,
			'postRatings'	=> $postRatings,
			'bigKey'		=> $bigKey,
		);

		return $this->responseView('ExtendPostRating_ViewPublic_PostRating_Received', 'epr_top_members', $viewParams);
	}

	protected function _getTopUsers($ratingId, $type, $limit=10)
	{
		$xenOption = XenForo_Application::getOptions();
		if (empty($type))
		{
			$type = 'received';
		}
		$model = $this->_getPostRatingModel();
		return $model->getTopRatingsForRatingId($ratingId, $type, $limit);
	}

	protected function _getPostRatingModel()
	{
		return $this->getModelFromCache('Dark_PostRating_Model');
	}
}