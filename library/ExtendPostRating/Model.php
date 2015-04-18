<?php
class ExtendPostRating_Model extends XFCP_ExtendPostRating_Model
{
	public function countRatingsByContentUserAndRatingId($userId, $ratingId)
	{
		$xenOptions = XenForo_Application::get('options')->getOptions();

		if ($ratingId == $xenOptions['dark_postrating_like_id'])
		{
			return $this->_getDb()->fetchOne('
				SELECT COUNT(*)
				FROM xf_liked_content
				WHERE like_user_id = ?
				', $userId);
		}
		else
		{
			return $this->_getDb()->fetchOne('
				select COUNT(*)
				FROM dark_postrating
				WHERE user_id = ? and rating = ?
				', array($userId, $ratingId));
		}
	}

	public function countRatingsForContentUserAndRatingId($userId, $ratingId)
	{
		$xenOptions = XenForo_Application::get('options')->getOptions();

		if ($ratingId == $xenOptions['dark_postrating_like_id'])
		{
			return $this->_getDb()->fetchOne('
				SELECT COUNT(*)
				FROM xf_liked_content
				WHERE content_user_id = ?
				', $userId);
		}
		else
		{
			return $this->_getDb()->fetchOne('
				select COUNT(*)
				FROM dark_postrating
				WHERE rated_user_id = ? and rating = ?
				', array($userId, $ratingId));
		}
	}

	public function getRatingsByContentUserAndRatingId($userId, array $fetchOptions = array(), $ratingId)
	{
		$xenOptions = XenForo_Application::get('options')->getOptions();
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

		if ($ratingId == $xenOptions['dark_postrating_like_id'])
		{
			return $this->fetchAllKeyed($this->limitQueryResults('
				SELECT liked_content.like_id as id, liked_content.content_id as post_id, liked_content.like_user_id as user_id, liked_content.content_user_id as rated_user_id, ? as rating, liked_content.like_date as date,
					user.*, liked_content.like_user_id as user_id, liked_content.content_type, liked_content.content_id, liked_content.like_user_id as rating_user_id
				FROM xf_liked_content AS liked_content
				INNER JOIN xf_user AS user ON (user.user_id = liked_content.content_user_id)
				WHERE 1 = ? and liked_content.like_user_id = ? and liked_content.content_type = \'post\'
				ORDER BY liked_content.like_date DESC
			', $limitOptions['limit'], $limitOptions['offset']
			), 'id', array($xenOptions['dark_postrating_like_id'], $xenOptions['dark_postrating_like_id'] > 0 ? 1 : 0, $userId));
		}
		else
		{
			return $this->fetchAllKeyed($this->limitQueryResults('
				SELECT pr.*, user.*, pr.user_id as user_id, "post" as content_type, pr.post_id as content_id, pr.user_id as rating_user_id
				FROM dark_postrating pr
				INNER JOIN xf_user AS user ON (user.user_id = pr.rated_user_id)
				WHERE pr.user_id=? and pr.rating = ?
				ORDER BY pr.date DESC
			', $limitOptions['limit'], $limitOptions['offset']
			), 'id', array($userId, $ratingId));
		}
	}

	public function getRatingsForContentUserAndRatingId($userId, array $fetchOptions = array(), $ratingId)
	{
		$xenOptions = XenForo_Application::get('options')->getOptions();
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

		if ($ratingId == $xenOptions['dark_postrating_like_id'])
		{
			return $this->fetchAllKeyed($this->limitQueryResults('
				SELECT liked_content.like_id as id, liked_content.content_id as post_id, liked_content.like_user_id as user_id, liked_content.content_user_id as rated_user_id, ? as rating, liked_content.like_date as date,
					user.*, liked_content.like_user_id as user_id, liked_content.content_type, liked_content.content_id, liked_content.like_user_id as rating_user_id
				FROM xf_liked_content AS liked_content
				INNER JOIN xf_user AS user ON (user.user_id = liked_content.like_user_id)
				WHERE 1 = ? and liked_content.content_user_id = ? and liked_content.content_type = \'post\'
				ORDER BY liked_content.like_date DESC
			', $limitOptions['limit'], $limitOptions['offset']
			), 'id', array($xenOptions['dark_postrating_like_id'], $xenOptions['dark_postrating_like_id'] > 0 ? 1 : 0, $userId));
		}
		else
		{
			return $this->fetchAllKeyed($this->limitQueryResults('
				SELECT pr.*, user.*, pr.user_id as user_id, "post" as content_type, pr.post_id as content_id, pr.user_id as rating_user_id
				FROM dark_postrating pr
				INNER JOIN xf_user AS user ON (user.user_id = pr.user_id)
				WHERE pr.rated_user_id=? and pr.rating = ?
				ORDER BY pr.date DESC
			', $limitOptions['limit'], $limitOptions['offset']
			), 'id', array($userId, $ratingId));
		}
	}

	public function getTopRatingsForRatingId($ratingId, $type='received', $limit=10)
	{
		$xenOption = XenForo_Application::getOptions();
		if ($xenOption->dark_postrating_like_id > 0 && $xenOption->dark_postrating_like_show && $ratingId == $xenOption->dark_postrating_like_id)
		{
			if ($type == 'received')
			{
				$orderField = 'like_count';
			}
			else
			{
				$orderField = 'epr_like_given';
			}

			return $this->fetchAllKeyed('
				SELECT *
				FROM xf_user
				ORDER BY '.$orderField.' DESC
				LIMIT '.$limit
				, 'user_id');
		}
		if ($type == 'received')
		{
			$orderField = 'count_received';
		}
		else
		{
			$orderField = 'count_given';
		}
		return $this->fetchAllKeyed('
			SELECT user.*, rating.'.$orderField.', rating.rating
			FROM xf_user AS user
			LEFT JOIN dark_postrating_count AS rating ON(rating.user_id = user.user_id AND rating.rating=?)
			ORDER BY '.$orderField.' DESC
			LIMIT '.$limit
			, 'user_id', $ratingId);
	}

	public function getTopUserForRatingId($ratingId, $type='received')
	{
		$xenOption = XenForo_Application::getOptions();
		$db = XenForo_Application::getDb();
		if ($xenOption->dark_postrating_like_id > 0 && $xenOption->dark_postrating_like_show && $ratingId == $xenOption->dark_postrating_like_id)
		{
			if ($type == 'received')
			{
				$orderField = 'like_count';
			}
			else
			{
				$orderField = 'epr_like_given';
			}

			return $this->_getDb()->fetchRow('
				SELECT *, '.$orderField.' AS rating
				FROM xf_user
				ORDER BY '.$orderField.' DESC
				LIMIT 1'
				);
		}
		if ($type == 'received')
		{
			$orderField = 'count_received';
		}
		else
		{
			$orderField = 'count_given';
		}
		return $this->_getDb()->fetchRow('
			SELECT user.*, rating.'.$orderField.', rating.rating
			FROM xf_user AS user
			LEFT JOIN dark_postrating_count AS rating ON(rating.user_id = user.user_id AND rating.rating=?)
			ORDER BY '.$orderField.' DESC
			LIMIT 1'
			, $ratingId);
	}

	public function getActiveRatings($regen = false){
		/** @var XenForo_Model_DataRegistry */

		$ratings = $this->fetchAllKeyed('
				SELECT *
				FROM dark_postrating_ratings
				WHERE disabled=0
				ORDER BY display_order asc
			', 'id');
		foreach($ratings as &$rating){
			if(!empty($rating['whitelist']))
				$rating['whitelist'] = unserialize($rating['whitelist']);
			else
				$rating['whitelist'] = array();

			if(!empty($rating['group_whitelist']))
				$rating['group_whitelist'] = unserialize($rating['group_whitelist']);
			else
				$rating['group_whitelist'] = array();

			if($rating['sprite_mode'] && !empty($rating['sprite_params']))
				$rating['sprite_params'] = unserialize($rating['sprite_params']);
			else
				$rating['sprite_params'] = array();
		}

		foreach($ratings as &$rating){
			$rating['title'] = new XenForo_Phrase($this->getRatingTitlePhraseName($rating['id']));
		}

		return $ratings;
	}
}