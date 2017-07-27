<?php

/**
 * Tao ra URL, luu giu cac tham so da co tren query string, cap nhat lai gia tri cho cac tham so theo mang truyen vao.
 * Vi du ban dau URL la /my.php?x=12&y=h&z=45
 * @param array $replace Mang assoc luu thong tin cac tham so can replace tren URL. Vi du array('x' => 13, 't' => 49)
 * @return string Vi du /my.php?x=13&y=h&z=45&t=49
 */
function getURL($replace) {
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

function makeImageUrl($image = '', $scan_dir = '', $size = 150){
  $allDir = explode('/', $scan_dir);
  array_pop($allDir);
  $allDir[] = 'size'.$size;
  $scan_dir = implode('/', $allDir);
  return WEB_ROOT.$scan_dir.'/'.$image;
}

function read_folder_directory($dir = "", &$info = array()){
  $listDir = array();
  if($handler = opendir($dir)) {
    while (($sub = readdir($handler)) !== FALSE) {
      if ($sub != "." && $sub != ".." && $sub != "Thumb.db" && $sub != "Thumbs.db") {
        $path = $dir."/".$sub;
        $info[$sub] = array(
          'type'  =>  filetype($path),
          'size'  =>  filesize($path),
          'modify'=>  filemtime($path),
          'read'  =>  is_readable($path),
          'write' =>  is_writable($path)
        );
        if(is_file($path)) {
          $listDir['files'][] = $sub;
        }elseif(is_dir($path)){
          $listDir['directories'][] = $sub;//[$sub] = read_folder_directory($dir."/".$sub);
        }
      }
    }
    closedir($handler);
  }
  return $listDir;
}

function getDimensionFile($size = 0){
  $kb = 1024;
  $mb = 1024*$kb;
  if($size >= $mb){
    return number_format(round($size/$mb, 10), 2)." MB";
  }elseif($size >= $kb){
    return number_format(round($size/$kb, 10), 2)." KB";
  }
  return $size.' Bytes';
}

define('ROOT_PATH', str_replace(array('core/'),array(''),strtr(dirname( __FILE__ ) ."/",array('\\'=>'/'))));
$webroot=str_replace('\\','/','http://'.$_SERVER['HTTP_HOST'].(dirname($_SERVER['SCRIPT_NAME'])?dirname($_SERVER['SCRIPT_NAME']):''));
$webroot.=$webroot[strlen($webroot)-1]!='/'?'/':'';
define('WEB_ROOT',$webroot);

$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
# Kiem tra dinh dang tham so dir truyen tren URL
$cleanedDir = trim(preg_replace('@[^a-z0-9/\\.\\-_]@i', '', $dir), '/');
if ($dir != $cleanedDir) {
  echo 'param is invalid!';
  die;
}

//tinh toan thu muc
$default_root = '_img_server';
$scan_dir = ($dir == '') ? $default_root : ($default_root.'/'.$dir);

//quest danh sach danh muc
$fileInfo = array();
$arr = read_folder_directory($scan_dir, $fileInfo);

$arrDirs = isset($arr['directories']) ? $arr['directories'] : array();
$arrFiles = isset($arr['files']) ? $arr['files'] : array();
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>File Browser</title>
    <link rel="stylesheet" href="style/style_browse.css" type="text/css" />
  </head>
  <body>
    <?php
# bread crumb
    $arrPath = array();
    $arrPath[] = '';
    $pos = strpos($dir, '/');
    while ($pos !== false) {
      $arrPath[] = substr($dir, 0, $pos);
      $pos = strpos($dir, '/', $pos + 1);
    }

    $nbPath = count($arrPath);
    $i = 0;
    echo '<div class="curPath"><b>Current Folder Path:</b> ';
    foreach ($arrPath as $ePath) {
      $i++;
      if ($ePath == ''){
        $ePathName = 'Root';
      }
      else{
        $ePathName = basename($ePath);
      }
      echo '<a href="' . getURL(array('dir' => $ePath)) . '">' . $ePathName . '</a>';
      if ($i < $nbPath){
        echo '<span> &gt; </span>';
      }
    }
    if ($dir != '') echo '<span> &gt; </span>' . basename($dir);

    echo '</div>';
# end bread crumb
    echo '<hr />';

# information
    echo '<div id="first_page" align="center"></div>
    <table cellspacing="0" cellpadding="0" border="0" class="file-tab">
    <tr>
      <th>&nbsp;</th>
      <th width="200">Name</th>
      <th>Size</th>
      <th>Modified</th>
      <th>Preview</th>
    </tr>';

# directory
    $check_dir = '';
    if($dir != ''){
      $exp_dir = explode('/', $dir);
      if(count($exp_dir) > 1){
        array_pop($exp_dir);
        $check_dir = implode('/', $exp_dir);
      }
    }
    echo '
    <tr>
      <td align="center"><img src="style/images/browse/folder.png" width="16" /></td>
      <td style="background:#fff">
        <a href="' . getURL(array('dir' => $check_dir)) . '">..</a></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>';
    foreach ($arrDirs as $eDir) {
      $path = '';
      if ($dir != '') {
        if (strpos($dir, 'gallery') !== 0) continue;
        $path = $dir . '/' . $eDir;
      } else {
        if (strpos($eDir, 'gallery') !== 0) continue;
        $path = $eDir;
      }

      echo '
      <tr>
        <td align="center"><img src="style/images/browse/folder.png" width="16" /></td>
        <td style="background:#fff">
          <a href="' . getURL(array('dir' => $path)) . '">' . $eDir . '</a></td>
        <td>&nbsp;</td>
        <td>'.date('d/m/Y H:i:s', $fileInfo[$eDir]['modify']).'</td>
        <td>&nbsp;</td>
      </tr>';
    }
# end directory
    ?>

    <?php
    $index = 0;
    $instanceCKEditorName = isset($_GET['CKEditor']) ? $_GET['CKEditor'] : '';
    $instanceCKEditorName = str_replace("'", '', $instanceCKEditorName);

    
    $nbFiles = count($arrFiles);
    $pagesize = 21;
    $totalPage = ceil($nbFiles/$pagesize);
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
    if ($currentPage < 1 || $currentPage > $totalPage) $currentPage = 1;
    $nextPage = $currentPage + 1;
    $previousPage = $currentPage - 1;
    
    $offset = ($currentPage - 1) * $pagesize;

# file creator
    for ($i = 0; $i < $pagesize; $i++) {
      $k = $offset + $i;
      if ($k >= $nbFiles) break; //out of range
      $eFile = $arrFiles[$k];
      $key = 'im' . $index++;
      $thumbnailUrl = makeImageUrl($eFile, $scan_dir);

      echo '
      <tr>
        <td align="center" valign="top"><img src="style/images/browse/picture.png" width="16" /></td>
        <td style="background:#fff" valign="top">
          <a href="javascript:void(0)" onclick="appendToCKEditor(' . "'$key', '$instanceCKEditorName'" . ');">'.$eFile.'</a>
        </td>
        <td valign="top">'.getDimensionFile($fileInfo[$eFile]['size']).'</td>
        <td valign="top">'.date('d/m/Y H:i:s', $fileInfo[$eFile]['modify']).'</td>
        <td valign="top"><img src="'.$thumbnailUrl.'" height="50" id="' . $key . '" /></td>
      </tr>';
    }
    echo '</table>';
# end file creator
    
# paging
    $html_page = '';
    if ($totalPage > 1) {
      $html_page = '<div style="clear:both; text-align:center">';
      if ($currentPage != 1) {
        $html_page .= '<a href="' . getURL(array('page' => 1)) . '">First</a> | ';
      } else {
        $html_page .= 'First | ';
      }
      
      if ($currentPage != 1) {
        $html_page .= '<a href="' . getURL(array('page' => $previousPage)) . '">Previous</a> | ';
      } else {
        $html_page .= 'Previous | ';
      }
      
      $html_page .= $currentPage . ' | ';
      
      if ($currentPage != $totalPage) {
        $html_page .= '<a href="' . getURL(array('page' => $nextPage)) . '">Next</a> | ';
      } else {
        $html_page .= 'Next | ';
      }
      
      if ($currentPage != $totalPage) {
        $html_page .= '<a href="' . getURL(array('page' => $totalPage)) . '">Last</a>';
      } else {
        $html_page .= 'Last';
      }
      $html_page .= '</div>';
    }
    echo '<div style="margin:15px 0 0">'.$html_page.'</div>';
# end  paging
    ?>
    <script type="text/javascript">
      document.getElementById('first_page').innerHTML = '<?=$html_page?>';
      function appendToCKEditor(id, instanceIndex) {
        if (!window.opener) return;
          var img150Src = document.getElementById(id).src;
        var img640Src = img150Src.replace('size150', 'size640'),
        img550Src = img150Src.replace('size150', 'size550'),
        html = "<p style='text-align: center;'><img src='" + img640Src + "' alt=''/></p>";
        window.opener.CKEDITOR.tools.callFunction(<?php echo isset($_GET['CKEditorFuncNum']) ? intval($_GET['CKEditorFuncNum']) : 0; ?>, img640Src);
        window.close();
      }
    </script>
  </body>
</html>