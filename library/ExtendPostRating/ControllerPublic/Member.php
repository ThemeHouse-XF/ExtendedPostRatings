<?php
class ExtendPostRating_ControllerPublic_Member extends XFCP_ExtendPostRating_ControllerPublic_Member
{
	protected function _getRatingsOutput($type = 'received')
	{
		$ratingId = $this->_input->filterSingle('rating', XenForo_Input::UINT);
		$likeModel = $this->getModelFromCache('XenForo_Model_Like');
		$ratingModel = $this->getModelFromCache('Dark_PostRating_Model');
		$ratings = $ratingModel->getRatings();

		$userId = $this->_input->filterSingle('user_id', XenForo_Input::UINT);

		$userFetchOptions = array(
			'join' => XenForo_Model_User::FETCH_LAST_ACTIVITY | XenForo_Model_User::FETCH_USER_PERMISSIONS
		);
		$user = $this->getHelper('UserProfile')->assertUserProfileValidAndViewable($userId, $userFetchOptions);

		$page = $this->_input->filterSingle('page', XenForo_Input::UINT);
		$perPage = 20;

		if ($type == 'given')
		{
			if ($ratingId)
			{
				$totalRatings = $ratingModel->countRatingsByContentUserAndRatingId($userId, $ratingId);
				$ratingsUser = $ratingModel->getRatingsByContentUserAndRatingId($userId, array(
					'page' => $page,
					'perPage' => $perPage
				), $ratingId);
			}
			else
			{
				$totalRatings = $ratingModel->countRatingsByContentUser($userId);
				$ratingsUser = $ratingModel->getRatingsByContentUser($userId, array(
					'page' => $page,
					'perPage' => $perPage
				));
			}
		}
		else
		{

			if ($ratingId)
			{
				$totalRatings = $ratingModel->countRatingsForContentUserAndRatingId($userId, $ratingId);
				$ratingsUser = $ratingModel->getRatingsForContentUserAndRatingId($userId, array(
					'page' => $page,
					'perPage' => $perPage
				), $ratingId);
			}
			else
			{
				$totalRatings = $ratingModel->countRatingsForContentUser($userId);
				$ratingsUser = $ratingModel->getRatingsForContentUser($userId, array(
					'page' => $page,
					'perPage' => $perPage
				));
			}
		}
		$ratingsUser = $likeModel->addContentDataToLikes($ratingsUser);

		foreach($ratingsUser as $ratingUserId => &$ratingUser)
		{
			if ($ratingId)
			{
				if ($ratingUser['rating'] != $ratingId)
				{
					unset($ratingsUser[$ratingUserId]);
					continue;
				}
			}
			$oldRating = $ratingUser['rating'];
			if ($type == 'given')
			{
				$ratingUser = $ratingUser['content'] + $ratingUser;
			}
			else
			{
				$ratingUser = $ratingUser + $ratingUser['content'];
			}
			if (empty($ratingUser['rating']))
			{
				$ratingUser['rating'] = $oldRating;
			}
			if (array_key_exists($ratingUser['rating'], $ratings))
			{
				$ratingUser['rating'] = $ratings[$ratingUser['rating']];
			}
		}

		if ($ratingId)
		{
			$linkParams = array(
				'rating'	=> $ratingId,
			);
		}
		else
		{
			$linkParams = array();
		}

		$viewParams = array(
			'linkParams'		=> $linkParams,
			'ratingId'			=> $ratingId,
			'type'				=> $type,
			'member'			=> $user,
			'ratings'			=> $ratingsUser,
			'totalRatings'		=> $totalRatings,
			'page'				=> $page,
			'ratingsPerPage'	=> $perPage
		);

		if ($type == 'given')
		{
			return $this->responseView('ExtendPostRating_ViewPublic_Member_Rating_Given', 'epr_ratings_given', $viewParams);
		}
		else
		{
			return $this->responseView('ExtendPostRating_ViewPublic_Member_Rating_Received', 'epr_ratings_received', $viewParams);
		}
	}

	public function actionRatingsReceived()
	{
		return $this->_getRatingsOutput('received');
	}

	public function actionRatingsGiven()
	{
		return $this->_getRatingsOutput('given');
	}
}