<?php

class ExtendPostRating_WidgetRenderer_TopUsers extends WidgetFramework_WidgetRenderer
{
	public function extraPrepareTitle(array $widget)
	{
		if (empty($widget['title']))
		{
			switch ($widget['options']['ratingType'])
			{
				case '1':
					return new XenForo_Phrase('epr_top_givers');
					break;
				case '0':
				default:
					return new XenForo_Phrase('epr_top_receivers');
					break;
			}
		}

		return parent::extraPrepareTitle($widget);
	}

	protected function _getConfiguration()
	{
		return array(
			'name' => '[Extend Post Rating] Top Users',
			'options' => array(
				'ratingType' => XenForo_Input::BINARY
			),
			'useCache' => true,
			'useUserCache' => true,
			'cacheSeconds' => 3600, // cache for 1 hour
			'useWrapper' => true
		);
	}

	protected function _getOptionsTemplate()
	{
		return 'epr_widget_options_top_user';
	}

	protected function _render(array $widget, $positionCode, array $params, XenForo_Template_Abstract $renderTemplateObject)
	{
		$model = XenForo_Model::create('Dark_PostRating_Model');

		switch ($widget['options']['ratingType'])
		{
			case '1':
				$type = 'given';
				break;
			case '0':
			default:
				$type = 'receiver';
				break;
		}

		$ratings = $model->getTopUserForAllRatings($type);

		$renderTemplateObject->setParam('ratings', $ratings);

		return $renderTemplateObject->render();
	}

	protected function _getRenderTemplate(array $widget, $positionCode, array $params)
	{
		return 'epr_widget_top_user';
	}
}