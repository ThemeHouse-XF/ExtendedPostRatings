<?php
class ExtendPostRating_Model_Like extends XFCP_ExtendPostRating_Model_Like
{
	public function likeContent($contentType, $contentId, $contentUserId, $likeUserId = null, $likeDate = null)
	{
		$result = parent::likeContent($contentType, $contentId, $contentUserId, $likeUserId, $likeDate);
		if (!$result) return false;

		$visitor = XenForo_Visitor::getInstance();
		if ($likeUserId === null)
		{
			$likeUserId = $visitor['user_id'];
		}

		$db = XenForo_Application::getDb();
		$db->query('
			UPDATE xf_user
			SET epr_like_given = epr_like_given + 1
			WHERE user_id = ?
			', $likeUserId);

		return $result;
	}

	public function unlikeContent(array $like)
	{
		$result = parent::unlikeContent($like);
		if ($result === false) return false;

		if ($like['like_user_id'])
		{
			$db = XenForo_Application::getDb();
			$db->query('
				UPDATE xf_user
				SET epr_like_given = IF(epr_like_given > 1, epr_like_given - 1, 0)
				WHERE user_id = ?
			', $like['like_user_id']);

			$this->_getAlertModel()->deleteAlerts(
				$like['content_type'], $like['content_id'], $like['like_user_id'], 'like'
			);
		}
	}
}