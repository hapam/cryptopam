<?php

class Category {
    /*
     * Tạo danh select option cho danh mục sản phẩm
     */

    static function optCategory($id = 0, $type = -1, &$defReturn = array()) {
        $sql = "SELECT id, title, parent_id, type FROM " . T_CATEGORY . " WHERE status = 1";
        if ($type != -1) {
            $sql .= " AND type = $type";
        }
        $sql .= " ORDER BY weight, safe_title";
        $res = DB::query($sql);
        $data = array();
        $subData = array();
        while ($row = mysql_fetch_assoc($res)) {
            $row['active'] = $id == $row['id'];
            if ($row['active']) {
                $defReturn = $row;
            }
            if ($row['parent_id'] == 0) {
                $data[$row['id']] = $row;
            } else {
                $subData[$row['parent_id']][$row['id']] = $row;
            }
        }

        foreach ($data as $k => $d) {
            if (isset($subData[$k])) {
                $data[$k]['items'] = $subData[$k];
                unset($subData[$k]);
            }
        }
        if (!empty($subData)) {
            foreach ($data as $k => $d) {
                if (isset($d['items'])) {
                    foreach ($d['items'] as $kk => $i) {
                        if (isset($subData[$kk])) {
                            $data[$k]['items'][$kk]['items'] = $subData[$kk];
                            unset($subData[$kk]);
                        }
                    }
                }
            }
        }
        $html = '<option value="0">Danh mục gốc</option>';

        if (!empty($data)) {
            //bat dau tao giao dien select box
            $c = 1;
            $t = count($data);
            foreach ($data as $k => $v) {
                $char = ($c == $t) ? '└ ' : '├ ';
                $html .= '<option value="' . $k . '"' . ($v['active'] ? 'selected' : '') . ' class="bold">' . $char . $v['title'] . '</option>';
                if (isset($v['items']) && !empty($v['items'])) {
                    $total = count($v['items']);
                    $count = 1;
                    foreach ($v['items'] as $item) {
                        $char = $c < $t ? '│' : '&nbsp;&nbsp;';
                        $char.= ' &nbsp;&nbsp;&nbsp;';
                        $char.= ($count == $total) ? '└' : '├';
                        $char.= '─ ';
                        $html .= '<option value="' . $item['id'] . '"' . ($item['active'] ? 'selected' : '') . '>' . $char . $item['title'] . '</option>';
                        $count++;

                        if (isset($item['items']) && !empty($item['items'])) {
                            $tt = count($item['items']);
                            $cc = 1;
                            foreach ($item['items'] as $i) {
                                $char = $count < $total ? '│' : ($c < $t ? '│' : '&nbsp;&nbsp;');
                                $char.= ' &nbsp;&nbsp;&nbsp;';
                                $char .= ($count <= $total) ? '│' : '&nbsp;&nbsp;';
                                $char.= ' &nbsp;&nbsp;';
                                $char.= ($cc == $tt) ? '└' : '├';
                                $char.= '─ ';
                                $html .= '<option value="' . $i['id'] . '"' . ($i['active'] ? 'selected' : '') . '>' . $char . $i['title'] . '</option>';
                                $cc++;
                            }
                        }
                    }
                }
                $c++;
            }
        }
        return $html;
    }

    /*
     * Tao mang danh muc tin tuc & san pham
     */

    static function create_link($row) {
        return $row;
    }

    static function getCategoryArr($type = -1) {
        $items = array();
        $type = ($type != -1) ? "AND type = $type" : "";
        $re = DB::query('SELECT * FROM ' . T_CATEGORY . ' WHERE status = 1 ' . $type . ' ORDER BY weight, safe_title');
        if ($re) {
            while ($row = mysql_fetch_assoc($re)) {
                $row = self::create_link($row);

                if ($row['parent_id'] == 0) {
                    $items[$row['id']]['data'] = $row;
                } else {
                    $items[$row['parent_id']]['items'][$row['id']] = $row;
                }
            }
            if (!empty($items)) {
                foreach ($items as $p => $cat) {
                    if (!isset($cat['data'])) {
                        foreach ($items as $p1 => $cat1) {
                            if (isset($cat1['data']) && !empty($cat1['items']) && isset($cat1['items'][$p])) {
                                $items[$p1]['items'][$p]['extra'] = $items[$p]['items'];
                            }
                        }
                        unset($items[$p]);
                    }
                }
            }
        }
        return $items;
    }

    //	Lay thong tin cua 1 danh muc
    static function getCatInfo($id = 0, $pid = 0, $type = 0) {
        $type = $type < 0 ? 0 : $type; // san pham: 0 | tin tuc: 1
        $category = CGlobal::get('category', array());

        if (isset($category[$type])) {
            $catArr = $category[$type];
            if (isset($catArr[$id])) {
                //neu tim thay danh muc o goc thi tra ve luon
                return $catArr[$id];
            } else {
                if ($pid > 0) {
                    if (isset($catArr[$pid])) {
                        //Neu tim thay danh muc cha o goc thi tra ve luon neu tim thay danh muc con
                        if (isset($catArr[$pid]['items']) && isset($catArr[$pid]['items'][$id])) {
                            $catArr[$pid]['items'][$id]['pRoot'] = $catArr[$pid]['data'];
                            return $catArr[$pid]['items'][$id];
                        }
                    } else {
                        foreach ($catArr as $k => $cat) {
                            if (isset($cat['items'])) {
                                //Neu tim thay danh muc o list sub 1 thi tra ve luon
                                if (isset($cat['items'][$id])) {
                                    $cat['items'][$id]['pRoot'] = $cat['data'];
                                    return $cat['items'][$id];
                                }
                                //Tim trong danh muc cha
                                if (isset($cat['items'][$pid])) {
                                    foreach ($cat['items'] as $sub) {
                                        if (isset($sub['extra']) && isset($sub['extra'][$id])) {
                                            $sub['extra'][$id]['pRoot'] = $cat['data'];
                                            $sub['extra'][$id]['parent'] = $sub;
                                            return $sub['extra'][$id];
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    //duyet tu dau
                    foreach ($catArr as $k => $cat) {
                        if (isset($cat['items'])) {
                            //Neu tim thay danh muc o list sub 1 thi tra ve luon
                            if (isset($cat['items'][$id])) {
                                $cat['items'][$id]['pRoot'] = $cat['data'];
                                return $cat['items'][$id];
                            }
                            foreach ($cat['items'] as $sub) {
                                if (isset($sub['extra']) && isset($sub['extra'][$id])) {
                                    $sub['extra'][$id]['pRoot'] = $cat['data'];
                                    $sub['extra'][$id]['parent'] = $sub;
                                    return $sub['extra'][$id];
                                }
                            }
                        }
                    }
                }
            }
        }
        return array();
    }

    //	Lay thong tin cua 1 danh muc theo ten
    static function getCatInfoByName($name = '') {
        $category = DB::fetch("SELECT * FROM " . T_CATEGORY . " WHERE status = 1 AND safe_title='" . StringLib::safe_title($name) . "'");
        if (empty($category)) {
            $category = array();
        }
        return $category;
    }

    static function getAllSubCatID($id = 0, $type = 0, &$thisCat = array()) {
        $ids = array();
        $type = $type < 0 ? 0 : $type;

        $category = CGlobal::get('category', array());
        if (isset($category[$type])) {
            $catArr = $category[$type];
            if (isset($catArr[$id])) {
                $thisCat = $catArr[$id];
                if (isset($thisCat['items'])) {
                    $ids = array_keys($thisCat['items']);
                    foreach ($thisCat['items'] as $sub) {
                        if (isset($sub['extra'])) {
                            $ids = $ids + array_keys($sub['extra']);
                        }
                    }
                }
            } else {
                foreach ($catArr as $k => $cat) {
                    if (isset($cat['items'])) {
                        //Neu tim thay danh muc o list sub 1 thi tra ve luon
                        if (isset($cat['items'][$id])) {
                            $thisCat = $cat['items'][$id];
                            $ids[] = intval($id);
                            if (isset($thisCat['extra'])) {
                                $ids = $ids + array_keys($thisCat['extra']);
                            }
                            break;
                        }
                        foreach ($cat['items'] as $sub) {
                            if (isset($sub['extra']) && isset($sub['extra'][$id])) {
                                $thisCat = $sub['extra'][$id];
                                $ids[] = $id;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $ids;
    }

    static function getCategoryImage($img = '', $time = 0, $size = 0) {
        return ImageUrl::getImageURL($img, $time, $size, CATEGORY_KEY , CATEGORY_FOLDER);
    }
    
    static function delCache($type = '', $all = false){
        $cacheKey = 'allCategory-';
        if($all){
            $types = CGlobal::get('categoryType', '');
            if(!empty($types)){
                foreach($types as $k => $v){
                    CacheLib::delete($cacheKey.$k);
                }
            }
        }else{
            CacheLib::delete($cacheKey.$type);
        }
    }
}
