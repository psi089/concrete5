<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardPageController extends Controller {

	public $helpers = array('html', 'form', 'validation/token');
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
	}

	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
}