<?

class FileVersion extends Object {
	
	private $numThumbnailLevels = 3;
	
	public function getFileID() {return $this->fID;}
	public function getFileVersionID() {return $this->fvID;}
	public function getPrefix() {return $this->fvPrefix;}
	public function getFileName() {return $this->fvFilename;}
	
	/** 
	 * Returns a full filesystem path to the file on disk.
	 */
	public function getPath() {
		$f = Loader::helper('concrete/file');
		$path = $f->getSystemPath($this->fvPrefix, $this->fvFilename);
		return $path;
	}
	
	public function getThumbnailPath($level) {
		$f = Loader::helper('concrete/file');
		$path = $f->getThumbnailSystemPath($this->fvPrefix, $this->fvFilename, $level);
		return $path;
	}
	
	// 
	public function setThumbnail($level, $hasThumbnail) {
		$db = Loader::db();
		$db->Execute("update FileVersions set fvHasThumbnail" . $level . "= ? where fID = ? and fvID = ?", array($hasThumbnail, $this->fID, $this->fvID));
	}
	
	// update types
	const UT_NEW = 0;
	
	
	/** 
	 * Responsible for taking a particular version of a file and rescanning all its attributes
	 * This will run any type-based import routines, and store those attributes, generate thumbnails,
	 * etc...
	 */
	public function refreshAttributes() {
		$fh = Loader::helper('file');
		$ext = $fh->getExtension($this->fvFilename);
		
		$ftl = FileTypeList::getType($ext);
		if (is_object($ftl)) {
			$db = Loader::db();
			$size = filesize($this->getPath());
			$db->Execute('update FileVersions set fvTitle = ?, fvGenericType = ?, fvSize = ? where fID = ? and fvID = ?',
				array($this->getFilename(), $ftl->getGenericType(), $size, $this->getFileID(), $this->getFileVersionID())
			);
			
			if ($ftl->getCustomImporter() != false) {
				Loader::library('file/inspector');
				
				// we have a custom library script that handles this stuff
				$script = 'file/types/' . $ftl->getCustomImporter();
				Loader::library($script);
				
				$class = Object::camelcase($ftl->getCustomImporter()) . 'FileTypeInspector';
				$cl = new $class;
				$cl->inspect($this);
				
			}
		}
	}

	public function createThumbnailDirectories() {
		$f = Loader::helper('concrete/file');
		for ($i = 1; $i <= $this->numThumbnailLevels; $i++) {
			$path = $f->getThumbnailSystemPath($this->fvPrefix, $this->fvFilename, $i, true);	
		}
	}
	
	public function setAttribute($fakHandle, $value) {
		$db = Loader::db();
		$fakID = $db->GetOne("select fakID from FileAttributeKeys where fakHandle = ?", array($fakHandle));
		if ($fakID > 0) {
			$db->Replace('FileAttributeValues', array(
				'fID' => $this->fID,
				'fvID' => $this->getFileVersionID(),
				'fakID' => $fakID,
				'value' => $value
			),
			array('fID', 'fvID', 'fakID'), true);
		}
		
	}
	
	
}