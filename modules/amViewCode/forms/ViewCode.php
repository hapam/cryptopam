<?php

class ViewCodeForm extends Form {

	public function __construct() {
		parent::__construct();
	}

	public function on_draw($region = '') {
		# Bang TRUE thi co the duyet duoc den cac duong dan thu muc khac
		$GET_ROOT = false;
		$arrRestrictedFile = array('config.php');
		$arrTextExtension = array('txt', 'php', 'tpl', 'html', 'htm', 'ini', 'log', 'conf', 'xml');

		/* @var $display TplLoad */
		global $display;

		$arrFolder = array();
		$arrFile = array();
		$fileData = array();
		$breadcrumb = '';
		$err = '';

		$webRoot = realpath(dirname(dirname(dirname(dirname(__FILE__)))));

		$file = isset($_GET['f']) ? (string) $_GET['f'] : '';
		if (!$file) {
			$sourceDir = isset($_GET['cd']) ? (string) $_GET['cd'] : '';
			if (!$sourceDir) {
				$sourceDir = realpath(dirname(dirname(dirname(dirname(__FILE__))))); // no slash at end
			}

			// Chuan hoa duong dan
			$sourceDir = str_replace(array('/', '\\'), '/', $sourceDir);
			$webRoot = str_replace(array('/', '\\'), '/', $webRoot);
			$pos = strpos($sourceDir, $webRoot);
			if (!$GET_ROOT) {
				if ($pos !== 0) {
					ob_start();
					echo "Want access: $sourceDir <br />But ROOT is: $webRoot <br />";
					echo '<div style="font-size:24px;">&#8594; <span style="color:red"> Access is denied</span></div>';
					echo '<div style="margin-top:10px"><a href="javascript:history.go(-1)" style="color:green;font-size:14px;text-decoration:underline"> Go back </a></div>';
					$err = ob_get_clean();
				}
			}
			if($err == ''){
				$iterator = new DirectoryIterator($sourceDir);
				
				// iterate over it
				foreach ($iterator as $file) {
					/* @var $file SplFileInfo */
					if ($file->isFile()) {
						$filename = $file->getFilename();
						if (strpos($filename, '.') === 0){
							continue;
						}
						$arrFile[] = array(
							'file' => $this->gPath($file->getPathname()),
							'name' => $filename,
							'ext'  => $file->getExtension(),
							'dir'  => $file->getPath(),
							'last_modified' => date('H:i:s d-m-Y', $file->getMTime())
						);
					} elseif ($file->isDir()) {
						$filename = $file->getFilename();
						if (strpos($filename, '.') === 0){
							continue;
						}
						$arrFolder[] = array(
							'folder' => $this->gPath($file->getPathname()),
							'name' => $filename,
							'parent' => $file->getPath()
						);
					}
				}
				# bread crumb
				$arrPath = $this->getBreadCrumb($sourceDir);
				ob_start();
				echo '<div style="font-size:14px" class="mBottom10">';
				echo '<a style="color:red" href="' . $this->getURL(array('cd' => $webRoot)) . '">WEB ROOT</a>';
				echo '</div>';
				echo '<div style="font-size:14px" class="mBottom10">';
				$nbPath = count($arrPath);
				foreach ($arrPath as $iPath => $onePath) {
					if ($iPath == $nbPath - 1) {
						echo '<span>' . basename($onePath) . '</span>';
					} else {
						echo '<a href="' . $this->getURL(array('cd' => $onePath)) . '">' . basename($onePath) . '</a> &nbsp;/ &nbsp;';
					}
				}
				echo '</div>';
				$breadcrumb = ob_get_clean();
				# end bread crumb
			}
		} else {
			$file = str_replace(array('/', '\\'), '/', $file);
			$webRoot = str_replace(array('/', '\\'), '/', $webRoot);
			$pos = strpos($file, $webRoot);
			if (!$GET_ROOT) {
				if ($pos !== 0) {
					ob_start();
					echo "Want access: $file <br />But ROOT is: $webRoot <br />";
					echo '<div style="font-size:24px;">&#8594; <span style="color:red"> Access is denied</span></div>';
					echo '<div style="margin-top:10px"><a href="javascript:history.go(-1)" style="color:green;font-size:14px;text-decoration:underline"> Go back </a></div>';
					$err = ob_get_clean();
				}
			}
			if($err == ''){
				if (is_file($file)) {
					$fileName = basename($file);
					if (in_array($fileName, $arrRestrictedFile)) {
						$err = '<div style="font-size:24px;">&#8594; <span style="color:red">File Access is denied</span></div>';
					}
					if($err == ''){
						if (filesize($file) > 5 * 1024 * 1024) {#5M
							$err = '<div style="font-size:24px;">&#8594; <span style="color:red">File is too large</span></div>';
						}
						if($err == ''){
							// Download
							if (isset($_GET['dl']) || !in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $arrTextExtension)) {
								ob_clean();
								$mm_type = "application/octet-stream";
								header("Cache-Control: public, must-revalidate");
								header("Pragma: hack");
								header("Content-Type: " . $mm_type);
								header("Content-Length: " . (string) (filesize($file)));
								header('Content-Disposition: attachment; filename="' . $fileName . '"');
								header("Content-Transfer-Encoding: binary\n");
								readfile($file);
								exit;
							}
							# bread crumb
							$arrPath = $this->getBreadCrumb($file);
							ob_start();
							echo '<div style="font-size:14px" class="mBottom10">';
							echo '<a style="color:red" href="' . Url::build('admin', array('cmd' => 'view-code', 'cd' => $webRoot)) . '">WEB ROOT</a>';
							echo '</div>';
							echo '<div style="font-size:14px" class="mBottom10">';
							$nbPath = count($arrPath);
							foreach ($arrPath as $iPath => $onePath) {
								if ($iPath == $nbPath - 1) {
									echo '<span>' . basename($onePath) . '</span>';
								} else {
									echo '<a href="' . Url::build('admin', array('cmd' => 'view-code')) . '?cd='.$onePath.'">' . basename($onePath) . '</a> &nbsp;/ &nbsp;';
								}
							}
							echo '</div>';
							$breadcrumb = ob_get_clean();
							# end bread crumb
			
							$fileData = array(
								'file' => $file,
								'name' => $fileName,
								'last_modified' => date('H:i:s d-m-Y', filemtime($file)),
								'content' => file_get_contents($file)
							);
						}
					}
				} else {
					$err = '<div style="font-size:24px;">&#8594; <span style="color:red">File doesn\'t exist</span></div>';
				}
			}
		}
		$display->add('arrFile', $arrFile);
		$display->add('arrFolder', $arrFolder);
		$display->add('urlViewCode', Url::build('admin', array('cmd' => 'view-code')));
		$display->add('fileData', $fileData);
		$display->unregister_outputfilter('trimwhitespace');

		ViewCode::viewCodeAuto($this, array(
			'html_search' => '&nbsp;',
			'html_view_table' => $display->output('ViewCode', true),
			'bread' => $breadcrumb
		), $err);
	}

	public function on_submit() {
		
	}

	private function getURL($replace) {
		$ret = '';
		$updatedKeys = array();
		$rKeys = array_keys($replace);
		foreach ($_GET as $key => $value) {
			if (in_array($key, $rKeys)) {
				$updatedKeys[] = $key;
				$ret .= urlencode($key) . '=' . urlencode($replace[$key]);
			} else {
				$ret .= urlencode($key) . '=' . urlencode($value);
			}

			$ret .= '&';
		}

		// Khong co cac tham so trong URL thi them vao cuoi
		$appendKeys = array_diff($rKeys, $updatedKeys);
		foreach ($appendKeys as $eKey) {
			$ret .= urlencode($eKey) . '=' . urlencode($replace[$eKey]) . '&';
		}

		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, '?')) {
			$uri = preg_replace('@\\?.*$@', '?' . trim($ret, '&'), $uri);
		} else {
			$uri .= '?' . trim($ret, '&');
		}

		return $uri;
	}

	private function gPath($s) {
		return str_replace(array('/', '\\'), '/', $s);
	}

	private function getBreadCrumb($fullPath) {
		$arrPath = array();
		$fullPath = str_replace(array('\\', '/'), '/', $fullPath);
		$pos = strpos($fullPath, '/');
		while ($pos !== false) {
			$path = substr($fullPath, 0, $pos);
			if ($path != '') { // For Linux
				$arrPath[] = $path;
			}
			$pos = strpos($fullPath, '/', $pos + 1);
		}

		$arrPath[] = $fullPath;
		return $arrPath;
	}

}