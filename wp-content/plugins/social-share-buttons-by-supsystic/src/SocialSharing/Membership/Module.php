<?php

class SocialSharing_Membership_Module extends SocialSharing_Core_Module {
	protected $membershipClass = 'SupsysticMembership';

	public function onInit() {
		parent::onInit();
		add_filter('sss_membership_html', array($this, 'getProjectHtml'), 10, 1);
	}

	public function getProjectHtml($params) {
		if(empty($params) || empty($params['projectId'])) {
			return null;
		}
		$projectId = (int) $params['projectId'];
		// get Social Share project Data
		$ssProject = $this->getProjectsModel()->get($projectId);
		if(!$ssProject) {
			return null;
		}
		// draw results
		echo $this->getProjectsModule()->doShortcode(array(
			'id' => $ssProject->id,
			'membershipParams' => $params,
		));
		return true;
	}

	protected function getProjectsModule() {
		$ssResolver = $this->getEnvironment()->getResolver();
		return $ssResolver->getModulesList()->get('projects');
	}

	protected function getProjectsModel() {
		return $this->getProjectsModule()->getModelsFactory()->get('projects');
	}

	public function isMemberShipActivated() {
		if(class_exists($this->membershipClass)) {
			return true;
		}
		return false;
	}

	public function getPluginInstallUrl() {
		return add_query_arg(
			array(
				's' => 'Membership by Supsystic',
				'tab' => 'search',
				'type' => 'term',
			),
			admin_url( 'plugin-install.php' )
		);
	}

	public function getPluginInstallWpUrl() {
		return 'https://wordpress.org/plugins/membership-by-supsystic/';
	}

	public function disableSocialSharing($socialSharingProjectId) {
		do_action('mbs_disable_social_sharing',$socialSharingProjectId);
	}
}