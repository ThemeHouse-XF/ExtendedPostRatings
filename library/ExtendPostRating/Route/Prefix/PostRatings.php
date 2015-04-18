<?php
class ExtendPostRating_Route_Prefix_PostRatings implements XenForo_Route_Interface
{
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		return $router->getRouteMatch('ExtendPostRating_ControllerPublic_PostRating', $routePath, 'postRatings');
	}
}