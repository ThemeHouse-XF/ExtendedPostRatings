<?php
class ExtendPostRating_DataWriter_User extends XFCP_ExtendPostRating_DataWriter_User
{
	protected function _getFields()
	{
		$fields = parent::_getFields();
		$fields['xf_user']['epr_like_given'] = array('type' => self::TYPE_UINT, 'default' => 0);

		return $fields;
	}
}