<?
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('user_attributes');
class ProfileController extends Controller {
	
	var $helpers = array('html', 'form'); 
	
	public function on_start(){
		$this->error = Loader::helper('validation/error');
	}
	
	public function view($userID = 0) {
		$html = Loader::helper('html');
		$canEdit = false;
		$u = new User();

		if ($userID > 0) {
			$profile = UserInfo::getByID($userID);
			if (!is_object($profile)) {
				throw new Exception('Invalid User ID.');
			}
		} else if ($u->isRegistered()) {
			$profile = UserInfo::getByID($u->getUserID());
			$canEdit = true;
		} else {
			$this->set('intro_msg', t('You must sign in order to access this page!'));
			$this->render('/login');
		}
		$this->set('profile', $profile);
		$this->set('av', Loader::helper('concrete/avatar'));
		$this->set('t', Loader::helper('text'));
		$this->set('canEdit',$canEdit);
	}
	

	public function on_before_render() {
		$this->set('error', $this->error);
	}	
}