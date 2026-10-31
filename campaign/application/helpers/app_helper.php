<?php

if(!function_exists('get_mysql_version')) {
	function get_mysql_version() {
		$mysql_info = explode(' ', mysqli_get_client_info());
		$mysql_version = isset($mysql_info[1]) ? $mysql_info[1] : false;
		$mysql_version_number = explode('-', $mysql_version)[0];

		if ($mysql_version_number) {
			return $mysql_version_number;
		} else if (isset($mysql_info[0])) {
			return (int)substr($mysql_info[0], 0, 3);
		}

		return 5;
	}
}

if(!function_exists('get_database_config')) {
	function get_database_config($param = '') {
		if(file_exists($file_path = APPPATH.'/config/database.php'))
		{
			include($file_path);
		}

		if(isset($db[$active_group][$param])) {
			return $db[$active_group][$param];
		}
	}
}

if(!function_exists('redirect_back')) {
	function redirect_back($url = '')
	{
	    if(isset($_SERVER['HTTP_REFERER']))
	    {
	        header('Location: '.$_SERVER['HTTP_REFERER']);
	    }
	    else
	    {
	        redirect($url);
	    }
	    exit;
	}
}

if(!function_exists('db_get_all_data')) {
	function db_get_all_data($table_name = null, $where = false) {
		$ci =& get_instance();
		if ($where) {
			$ci->db->where($where);
		}
	  	$query = $ci->db->get($table_name);

	    return $query->result();
	}
}

if(!function_exists('is_image')) {
	function is_image($filename = '') {
		$array = explode('.', $filename);
		$extension = strtolower(end($array));
		$list_image_ext = ['', 'png', 'jpg', 'jpeg', 'gif'];

		if (array_search($extension, $list_image_ext)) {
			return TRUE;
		}

		return FALSE;
	}
}

if(!function_exists('clean_snake_case')) {
	function clean_snake_case($text = '') {
		$text = preg_replace('/_/', ' ', $text);

		return $text;
	}
}

if(!function_exists('get_group_user')) {
	function get_group_user($id_user = '') {
		return get_user_groups($id_user);
	}
}

if(!function_exists('get_user_data')) {
	function get_user_data($field_name = '') {
		$ci =& get_instance();
		$user_id = $ci->session->userdata('id');
		if ($user_id) {
			if (empty($field_name)) {
				return $ci->aauth->get_user($user_id);
			} else {
				return $ci->aauth->get_user($user_id)->$field_name;
			}
		}

		return false;
	}
}

if(!function_exists('is_allowed')) {
	function is_allowed($permission, Closure $func) {
		$ci =& get_instance();
		$reflection = new ReflectionFunction($func);
		$arguments  = $reflection->getParameters();


		if ($ci->aauth->is_allowed($permission)) {
			call_user_func($func, $arguments);
		} else {
			ob_start();
			call_user_func($func, $arguments);
			$buffer = ob_get_contents();
			ob_end_clean();

		}
	}
}

if(!function_exists('message_flash')) {
	function message_flash($message, $type) {
		$ci =& get_instance();
		$ci->session->set_flashdata('f_message', $message);
		$ci->session->set_flashdata('f_type', $type);
	}
}

if(!function_exists('display_menu_module')) {
	function display_menu_module($parent, $level, $menu_type, $ignore_active = false) {
		$ci =& get_instance();
		$ci->load->database();
		$ci->load->model('menu/model_menu');
		$menu_type_id = $ci->model_menu->get_id_menu_type_by_flag($menu_type);
	    $result = $ci->db->query("SELECT a.id, a.label, a.type, a.active, a.link, Deriv1.Count FROM `menu` a  LEFT OUTER JOIN (SELECT parent, COUNT(*) AS Count FROM `menu` GROUP BY parent) Deriv1 ON a.id = Deriv1.parent WHERE a.menu_type_id = ".$menu_type_id." AND a.parent=" . $parent." ".($ignore_active ? '' : 'and active = 1')." order by `sort` ASC")->result();

		$ret = '';
		$display_menu_dropdown = function($row = null){
			return '<div class="dropdown  pull-right">
					    <a class="dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-cog "></i>
					    <span class="caret"></span></a>
					    <ul class="dropdown-menu">
					      <li><a class="remove-data" href="javascript:void()" data-href="'.site_url('administrator/menu/delete/'.$row->id).'"><i class="fa fa-trash btn-action"></i> <span class="text-danger">Delete</span></a></li>
					      <li><a href="'.site_url('administrator/menu/edit/'.$row->id).'"><i class="fa fa-pencil btn-action"></i> Edit</a></li> 
					      '.($row->type != 'label' ?'
					      <li><a href="'.site_url('administrator/menu/add/'.app()->uri->segment(4).'?parent='.$row->id).'"><i class="fa fa-level-down btn-action"></i> Add child</a></li>
					      ' : '').'
					    </ul>
					  </div>';
		};
	    if ($result) {
		    $ret .= '<ol class="dd-list">';
		   	foreach ($result as $row) {
		        if ($row->Count > 0) {
		        	 $ret .= '<li class="dd-item dd3-item '.($row->active ? '' : 'menu-toggle-activate_inactive').' menu-toggle-activate" data-id="'.$row->id.'" data-status="'.$row->active.'">';

		        	if ($row->type != 'label') {
		        		$ret .= '<div class="dd-handle dd3-handle dd-handles"></div>';
		            	$ret .= '<div class="dd3-content">'._ent($row->label);
		        	} else{
		        		$ret .= '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
		            	$ret .= '<div class="dd3-content "><b>'._ent($row->label).'</b>';
		        	}

		        	
		           
		            $ret .= $display_menu_dropdown($row);

		            $ret .= '</div>';
					$ret .= display_menu_module($row->id, $level + 1, $menu_type, $ignore_active);
					$ret .= "</li>";
		        } elseif ($row->Count==0) {
		            $ret .= '<li class="dd-item dd3-item '.($row->active ? '' : 'menu-toggle-activate_inactive').' menu-toggle-activate" data-id="'.$row->id.'" data-status="'.$row->active.'">';

		        	if ($row->type != 'label') {
		        		$ret .= '<div class="dd-handle dd3-handle dd-handles"></div>';
		            	$ret .= '<div class="dd3-content">'._ent($row->label);
		        	} else{
		        		$ret .= '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
		        		
		            	$ret .= '<div class="dd3-content  "><b>'._ent($row->label).'</b>';
		        	}

		          
		            $ret .= $display_menu_dropdown($row);



					$ret .= '</div></li>';
		        }
		    }
		    $ret .= "</ol>";
	    }

	    return $ret;
	}
}

if(!function_exists('display_menu_admin')) {
	function display_menu_admin($parent, $level) {
		$ci =& get_instance();
		$ci->load->database();
		$ci->load->model('menu/model_menu');
	    $appId = $ci->config->item('app_id') ? (int) $ci->config->item('app_id') : 1;
	    $result = $ci->db->query("SELECT a.id, a.label,a.icon_color, a.type, a.link,a.icon, Deriv1.Count FROM `menu` a  LEFT OUTER JOIN (SELECT parent, COUNT(*) AS Count FROM `menu` GROUP BY parent) Deriv1 ON a.id = Deriv1.parent WHERE a.menu_type_id = 1 AND (a.app_id = $appId OR a.app_id = 0) AND a.parent=" . $parent." and active = 1  order by `sort` ASC")->result();

		$ret = '';
	    if ($result) {
	    	if (($level > 1) AND ($parent > 0) ) {
		    	$ret .= '<ul class="treeview-menu">';
	    	} else {
	    		$ret = '';
	    	}
		   	foreach ($result as $row) {
				
				$row->link = parse_nav_url($row->link);		   

				// Hide Internal Evaluation feature if disabled or for Staff Operational / unauthorized users
				if (strpos($row->link, 'internal_evaluation') !== false || stripos($row->label, 'evaluasi internal') !== false) {
					if (get_option('internal_evaluation_toggle', 'yes') === 'no' && !$ci->aauth->is_admin()) {
						continue;
					}
					if (!$ci->aauth->is_admin() && ($ci->aauth->is_member('Operational') || !$ci->aauth->is_allowed('internal_evaluation_view'))) {
						continue;
					}
				}

		   		$perms = 'menu_'.strtolower(str_replace(' ', '_', $row->label));

		   		// Dynamic multi-language label translation
		   		$perm_suffix = strtolower(str_replace([' ', '&', '(', ')', '-'], ['_', '_', '', '', '_'], $row->label));
		   		$translated_label = lang('menu_' . $perm_suffix);
		   		if (!$translated_label) {
		   			$translated_label = lang($perm_suffix);
		   		}
		   		$display_label = !empty($translated_label) ? $translated_label : $row->label;

		   		$link_path = explode('?', $row->link)[0];
		   		$link_query = parse_url($row->link, PHP_URL_QUERY);
		   		$links = explode('/', $link_path);
				$segments = array_slice($ci->uri->segment_array(), 0, count($links));
				
		   		if (implode('/', $segments) == implode('/', $links)) {
		   			if (!empty($link_query)) {
		   			    parse_str($link_query, $link_params);
		   			    $match = true;
		   			    foreach ($link_params as $k => $v) {
		   			        if ($ci->input->get($k) != $v) {
		   			            $match = false;
		   			            break;
		   			        }
		   			    }
		   			    $active = $match ? 'active' : '';
		   			} else {
		   			    // If menu link has no query param, check if the current URL has a 'view' param. 
		   			    // If it has a 'view' param, we don't highlight the parent/base link to avoid double highlights
		   			    if ($ci->input->get('view') && strpos($row->link, 'tasks') !== false) {
		   			        $active = '';
		   			    } else {
		   			        $active = 'active';
		   			    }
		   			}
		   		} else {
		   			$active = '';
		   		}

		   		$link = filter_var($row->link, FILTER_VALIDATE_URL) ? $row->link : base_url($row->link);
		   		if ($row->type == 'label') {
		   			if ($ci->aauth->is_allowed($perms)) {
		        		$ret .= '<li class="header treeview">'._ent($display_label).'</li>';
		        	}
		   		} else {
			        if ($row->Count > 0) {
			        	if ($ci->aauth->is_allowed($perms)) {
				        	$ret .= '<li class="'.$active.' "> 
										        	<a href="'.$link.'">';

							if ($parent) {
								$ret .= '<i class="fa fa-circle-o '._ent($row->icon_color).'"></i> <span>'._ent($display_label).'</span>
									            <span class="pull-right-container">
									              <i class="fa fa-angle-left pull-right"></i>
									            </span>
									          </a>';
							} else {
								$ret .= '<i class="fa '._ent($row->icon).' '._ent($row->icon_color).'"></i> <span>'._ent($display_label).'</span>
									            <span class="pull-right-container">
									              <i class="fa fa-angle-left pull-right"></i>
									            </span>
									          </a>';
							}

							$ret .= display_menu_admin($row->id, $level + 1);
							$ret .= "</li>";
						}
			        } elseif ($row->Count==0) {
			           if ($ci->aauth->is_allowed($perms)) {
							$ret .= '<li class="'.$active.' "> 
										        	<a href="'.$link.'">';

							if ($parent) {
								$ret .= '<i class="fa fa-circle-o '._ent($row->icon_color).'"></i> <span>'._ent($display_label).'</span>
									            <span class="pull-right-container"></i>
									            </span>
									          </a>';
							} else {
								$ret .= '<i class="fa '._ent($row->icon).' '._ent($row->icon_color).'"></i> <span>'._ent($display_label).'</span>
									            <span class="pull-right-container"></i>
									            </span>
									          </a>';
							}

							$ret .= "</li>";
						}
			        }
		   		}
		    	if ($row->link == ADMIN_NAMESPACE_URL . '/extension') {
		    		$ret .= cicool()->getSidebar();
		    	}
		    }
		    if ($level != 1) {
		    	$ret .= '</ul>';
	    	}

	    }



	    return $ret;
	}
}

if(!function_exists('set_message')) {
	function set_message($message = null, $type = 'success') {
		$ci =& get_instance();

		$ci->session->set_flashdata('f_message', $message);
        $ci->session->set_flashdata('f_type', $type);
	}
}

if(!function_exists('form_builder')) {
	function form_builder($id = 0) {
		$ci =& get_instance();
		
		$model_form = $ci->load->model('form/model_form');
		$form = $ci->model_form->find($id);

		if ($form) {
			$form_name = strtolower($form->table_name);
			$ci->template->title($form->title);

			return $ci->load->view('form/public/'.$form_name.'/' .$form_name, [], true);
		} else {
			return false;
		}
	}
}

if(!function_exists('get_icon_file')) {
	function get_icon_file($file_name = '') {
		$extension_list = [
			'avi' => ['avi'], 
			'css' => ['css'], 
			'csv' => ['csv'], 
			'eps' => ['eps'], 
			'html' => ['html', 'htm'], 
			'jpg' => ['jpg', 'jpeg'], 
			'mov' => ['mov', 'mp4', '3gp'], 
			'mp3' => ['mp3'], 
			'pdf' => ['pdf'], 
			'png' => ['png'], 
			'ppt' => ['ppt', 'pptx'], 
			'rar' => ['rar'], 
			'raw' => ['raw'], 
			'ttf' => ['ttf'],
			'txt' => ['txt'], 
			'wav' => ['wav'], 
			'xls' => ['xls', 'xlsx'], 
			'zip' => ['zip'], 
			'doc' => ['docx', 'doc']
		];

		$file_name_arr = explode('.', $file_name);
		if (is_array($file_name_arr)) {
			foreach ($extension_list as $ext => $list_ext) {
				if (in_array(end($file_name_arr), $list_ext)) {
					return BASE_ASSET . 'img/icon/' . $ext . '.png'; 
				}
			}
		}

		return BASE_ASSET . 'img/icon/any.png';
	}
}

if(!function_exists('check_is_image_ext')) {
	function check_is_image_ext($file_name = '') {
		$extension_list = [
			'jpg' => ['jpg', 'jpeg'], 
			'png' => ['png']
		];

		$file_name_arr = explode('.', $file_name);
		if (is_array($file_name_arr)) {
			foreach ($extension_list as $ext => $list_ext) {
				if (in_array(end($file_name_arr), $list_ext)) {
					return $file_name;
				}
			}
		}

		return get_icon_file($file_name);
	}
}

if(!function_exists('build_rules')) {
	function build_rules($delimiter = '|', $rules = []) {
		if (count($rules)) {
			return $delimiter.implode($delimiter, $rules);
		}
	}
}

if(!function_exists('_ent')) {
	function _ent($string = null) {
		return htmlentities($string);
	}
}

if(!function_exists('dd')) {
	function dd($array) {
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}
}


if(!function_exists('get_captcha')) {
	function get_captcha($string = null) {
		$ci =& get_instance();
		$ci->load->helper('captcha');

		$vals = array(
		        'img_path'      => './captcha/',
		        'img_url'       => base_url('/captcha/'),
		        'font_path'     => FCPATH . '/asset/font/captcha.ttf',
		        'img_width'     => '150',
		        'img_height'    => 30,
		        'expiration'    => 7200,
		        'word_length'   => 4,
		        'font_size'     => 15,
		        'img_id'        => 'image-captcha',
		        'pool'          => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',

		        // White background and border, black text and red grid
		        'colors'        => array(
		                'background' => array(255, 255, 255),
		                'border' => array(255, 255, 255),
		                'text' => array(0, 0, 0),
		                'grid' => array(190, 190, 190)
		        )
		);

		$cap = create_captcha($vals);
		$expiration = time() - 7200; // Two hour limit
		$ci->db->where('captcha_time < ', $expiration)
		        ->delete('captcha');


		$data = array(
		        'captcha_time'  => $cap['time'],
		        'ip_address'    => $ci->input->ip_address(),
		        'word'          => $cap['word']
		);

		$query = $ci->db->insert_string('captcha', $data);
		$ci->db->query($query);


		return $cap;
	}
}

if(!function_exists('display_block_element')) {
	function display_block_element() {
		$ci =& get_instance();
		$ci->load->database();
	    $result = $ci->db->query("SELECT * FROM `page_block_element` GROUP BY `group_name` ")->result();
	    $childs = $ci->db->query("SELECT * FROM `page_block_element`")->result();
	    $child_list = [];

	    foreach ($childs as $row) {
	    	$child_list[$row->group_name][] = $row;
	    }

	    $ret = null;

	    foreach ($result as $row) {
	    	$ret .= '<li><a href="#">'.ucwords($row->group_name).'</a>';
	    	if (isset($child_list[$row->group_name])) {
	    		$ret .= '<ul>';
	    		foreach ($child_list[$row->group_name] as $child) {
	    			$ret .= '<li class="block-item" data-src="'.$child->content.'" data-block-name="'.$child->block_name.'">
				                <div class="nav-content-wrapper noselect">
				                  <i class="fa fa-gear"></i>
				                  <div class="tool-nav delete">
				                    <i class="fa fa-trash"></i> <span class="info-nav">Delete</span>
				                  </div>
				                  <div class="tool-nav source">
				                    <i class="fa fa-code"></i> <span class="info-nav">Source</span>
				                  </div>
				                  <div class="tool-nav copy">
				                    <i class="fa fa-copy"></i> <span class="info-nav">Copy</span>
				                  </div>
				                  <div class="tool-nav handle">
				                    <i class="fa fa-arrows"></i> <span class="info-nav">Move</span>
				                  </div>
				                </div>
				              <img src="'.BASE_ASSET.'img/header10.png" data-src="aadas/asdasd.html" class="preview-only">
				              <div id="element'.$child->id.'" class="block-content"><div class="edit"></div></div>
				            </li>';
	    		}

	    		$ret .= '</ul>';
	    	}
	    	$ret .= '</li>';
	    }
	    return $ret;
	}
}

if(!function_exists('get_extensions')) {
	function get_extensions($type = false) {
		$ci =& get_instance();
		$ci->load->helper('directory');

		$ext_paths[] = FCPATH . 'cc-content/extensions/';

		foreach (Modules::$locations as $path => $dir) {
			$path_items = directory_map($path, 3); 
			foreach ($path_items as $path_item => $sub_path_item) {
				$check = $path . $path_item . '/extensions/';
				if (is_dir($check)) {
					$ext_paths[] = $check;
				}
			}
		}

		$list_extension = [];

		foreach ($ext_paths as $ext_path) {
			$dir = directory_map($ext_path, 2);

			foreach ($dir as $dirname => $childs) {
				if (is_file($ext_path . $dirname . '/ext.json')) {
					$ext_info = file_get_contents($ext_path . $dirname . '/ext.json');
					$ext_info_array = json_decode($ext_info);
					$ext_info_array->path = $ext_path . $dirname;
					$ext_info_array->dirname = $dirname;
					$list_extension[$ext_info_array->type][] = $ext_info_array;
				}
			}
		}

		if ($type !== false) {
			if (isset($list_extension[$type])) {
				return $list_extension[$type];
			}
		} else {
			return $list_extension;
		}

		return false;
	}
}

if(!function_exists('get_installed_extension')) {
	function get_installed_extension() {
		$ci =& get_instance();
		$ci->load->library('cc_extension');
		$extensions = $ci->cc_extension->getExtensions();
		$actived = [];
		foreach ($extensions as $extension) {
			if (is_dir($extension->item->path)) {
				$actived[] = $extension->item->regid;
			}
		}

		return $actived;
	}
}


if(!function_exists('get_page_element')) {
	function get_page_element($group = false) {
		$ci =& get_instance();

		$ci->cc_page_element->get_page_element();
	}
}

if(!function_exists('load_extensions')) {
	function load_extensions() {

		$ci =& get_instance();
		$ci->load->helper('directory');
		$ci->load->library('cc_extension');

		$list_extensions = get_extensions();
		if (!is_array($list_extensions)) {
			return false;
		}

		$ext_load = null;
		$cc_core = get_instance();
		$current_uri = $ci->uri->uri_string;

		foreach ($list_extensions as $type => $extensions) {
			foreach ($extensions as $ext) {
				if (isset($ext->loader)) {
					if (isset($ext->routes)) {
						foreach ($ext->routes as $route) {


							// Convert wildcards to RegEx
							$route = str_replace(array(':any', ':num'), array('[^/]+', '[0-9]+'), $route);

							if (preg_match('#^'.$route.'$#', $current_uri, $matches)) {
								foreach ($ext->loader as $filename) {
									if (is_file($ext->path.$filename)) {
										if ($ci->input->method()) {
											$ccExtension = new Cc_extension_item($ext);
											include  $ext->path.$filename;
										}
									}
								}
							}

						}
					} else {
						foreach ($ext->loader as $filename) {
							if (is_file($ext->path.$filename)) {
								if (file_exists($ext->path . 'actived')) {
									debug('loaded'.$ext->path.$filename); 

									if ($ci->input->method() == 'get') {
										$ccExtension = new Cc_extension_item($ext);
										include  $ext->path.$filename;
									} else {
										$ccExtension = new Cc_extension_item($ext);
										ob_start();
										include  $ext->path.$filename;
										$buffer = ob_get_contents();
										ob_end_clean();
									}
								}
							} else {
							debug('not loaded'.$ext->path.$filename); 

							}
						}
					}
				}
			}
		}
		return false;
	}
}

if(!function_exists('url_extension')) {
	function url_extension($ext = null) {
		return BASE_URL . 'cc-content/extensions/' . $ext;
	}
}

if(!function_exists('get_option')) {
	function get_option($option_name = null, $default = null) {
		$ci =& get_instance();
		$ci->load->library('cc_app');
		return $ci->cc_app->getOption($option_name, $default);
	}
}

if(!function_exists('add_option')) {
	function add_option($option_name = null, $option_value = null) {
		$ci =& get_instance();
		$ci->load->library('cc_app');
		return $ci->cc_app->addOption($option_name, $option_value);
	}
}

if(!function_exists('set_option')) {
	function set_option($option_name = null, $option_value = null) {
		$ci =& get_instance();
		return $ci->cc_app->setOption($option_name, $option_value);
	}
}

if(!function_exists('delete_option')) {
	function delete_option($option_name = null) {
		$ci =& get_instance();
		return $ci->cc_app->deleteOption($option_name);
	}
}

if(!function_exists('option_exists')) {
	function option_exists($option_name = null) {
		$ci =& get_instance();
		return $ci->cc_app->optionExists($option_name);
	}
}

if(!function_exists('theme_url')) {
	function theme_url($url_additional = null) {
		$ci =& get_instance();
        $active_theme = get_option('active_theme', 'cicool');

        return BASE_URL . 'cc-content/themes/' . $active_theme . '/' . $url_additional;
	}
}

if(!function_exists('theme_asset')) {
	function theme_asset() {
        return theme_url('asset/');
	}
}


if(!function_exists('site_name')) {
	function site_name() {
        return get_option('site_name');
	}
}

if(!function_exists('installation_complete')) {
	function installation_complete() {
		return is_file(FCPATH . '/application/config/site.php');
	}
}

if(!function_exists('get_menu')) {
	function get_menu($menu_type = null) {
		$ci =& get_instance();
		$ci->load->database();
		$ci->load->model('menu/model_menu');

		if(is_numeric($menu_type)) {
			$menu_type_id = $menu_type;
		} 
		else {
			$menu_type_id = $ci->model_menu->get_id_menu_type_by_flag($menu_type);
		}


		$appId = $ci->config->item('app_id') ? $ci->config->item('app_id') : 0;

		$menus = $ci->db
			->where(['menu_type_id' =>  $menu_type_id])
			->where("(app_id = $appId OR app_id = 0)")
			->order_by('sort', 'ASC')
			->get('menu')
			->result();

		$menu_parents = $ci->db
			->where( ['menu_type_id' => $menu_type_id, 'parent' => 0])
			->where("(app_id = $appId OR app_id = 0)")
			->order_by('sort', 'ASC')
			->get('menu')
			->result();
		

		$new = array();
		foreach ($menus as $a){
		    $new[$a->parent][] = $a;
		}

		$news = array();
		$menus_tree = array();
		foreach ($menus as $a){
		    $news[$a->parent][] = $a;
		}

		foreach ($menu_parents as $new) {
			$menus_tree = array_merge($menus_tree, create_tree($news, array($new)));
		}
		return $menus_tree;
	}
}


if(!function_exists('create_tree')) {
	function create_tree(&$list, $parent) {

	    $tree = array();
	    foreach ($parent as $k=>$l){
	        if(isset($list[$l->id])){

	            $l->children = create_tree($list, $list[$l->id]);
	        }
	        $tree[] = $l;
	    } 
	    return $tree;
	}
}

if(!function_exists('get_header')) {
	function get_header() {
		$ci =& get_instance();
		return $ci->cc_app->getHeader();
	}
}

if(!function_exists('get_footer')) {
	function get_footer() {
		$ci =& get_instance();
		return $ci->cc_app->getFooter();
	}
}

if(!function_exists('get_navigation')) {
	function get_navigation() {
		$ci =& get_instance();
		return $ci->cc_app->getNavigation();
	}
}

if(!function_exists('get_view_component')) {
	function get_view_component($view = null, $data = []) {
		$ci =& get_instance();
        return $ci->template->build($view, $data, true);
	}
}

if(!function_exists('generate_key')) {
	function generate_key($length = 40) {
		$ci =& get_instance();
        $salt = base_convert(bin2hex($ci->security->get_random_bytes(64)), 16, 36);
        if ($salt === FALSE)
        {
            $salt = hash('sha256', time() . mt_rand());
        }
        $ci->load->config('config');

        $new_key = substr($salt, 0, $length);
        return $new_key;
	}
}

if(!function_exists('get_table_not_allowed_for_builder')) {
	function get_table_not_allowed_for_builder() {
		return [
			'aauth_group_to_group',
			'aauth_groups',
			'aauth_login_attempts',
			'aauth_perm_to_group',
			'aauth_perm_to_user',
			'aauth_perms',
			'aauth_pms',
			'aauth_user',
			'aauth_user_to_group',
			'aauth_user_variables',
			'aauth_users',
			'captcha',
			'cc_options',
			'cc_session',
			'crud',
			'crud_custom_option',
			'crud_field',
			'crud_field_validation',
			'crud_input_type',
			'crud_input_validation',
			'form',
			'form_custom_attribute',
			'form_custom_option',
			'form_field',
			'form_field_validation',
			'keys',
			'menu',
			'menu_icon',
			'menu_type',
			'migrations',
			'page',
			'page_block_element',
			'rest',
			'rest_field',
			'rest_field_validation',
			'rest_input_type',
			'cc_log',
			'cc_block_client',
			'cc_block',
			'cc_visitor'
		];
	}
}

if(!function_exists('app')) {
	function app() {
		return get_instance();
	}
}

if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
           $headers = '';
       foreach ($_SERVER as $name => $value)
       {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
} 

if (!function_exists('cclang'))
{
    function cclang($langkey = null, $params = [])
    {
    	if (!is_array($params)) {
    		$params = [$params];
    	}

        $lang = lang($langkey);

        $idx = 1;
        foreach ($params as $value) {
        	$lang = str_replace('$'.$idx++, $value, $lang);
        }

        $lang = preg_replace('/\$([0-9])/', '', $lang);

        if (!$lang) {
        	return ucwords($langkey);
        }

        return $lang;
    }
} 

if (!function_exists('get_langs'))
{
    function get_langs()
    {
    	return [
    		[
    			'folder_name' => 'english',
    			'name' => 'English',
    			'initial_name' => 'gb',
    			'icon_name' => 'flag-icon-gb',
    		],
    		[
    			'folder_name' => 'indonesian',
    			'name' => 'Indonesian',
    			'initial_name' => 'id',
    			'icon_name' => 'flag-icon-id',
    		],
    		[
    			'folder_name' => 'italian',
    			'name' => 'Italian',
    			'initial_name' => 'it',
    			'icon_name' => 'flag-icon-it',
    		],
    		[
    			'folder_name' => 'arabic',
    			'name' => 'Arabic',
    			'initial_name' => 'ar',
    			'icon_name' => 'flag-icon-sa',
    		],
    		[
    			'folder_name' => 'portuguese-brazilian',
    			'name' => 'Brazil',
    			'initial_name' => 'br',
    			'icon_name' => 'flag-icon-br',
    		],
    		[
    			'folder_name' => 'german',
    			'name' => 'Germany',
    			'initial_name' => 'de',
    			'icon_name' => 'flag-icon-de',
    		],
    		[
    			'folder_name' => 'french',
    			'name' => 'France',
    			'initial_name' => 'fr',
    			'icon_name' => 'flag-icon-fr',
    		],
    		[
    			'folder_name' => 'spanish',
    			'name' => 'Spain',
    			'initial_name' => 'es',
    			'icon_name' => 'flag-icon-es',
    		],
    		[
    			'folder_name' => 'russian',
    			'name' => 'Russia',
    			'initial_name' => 'ru',
    			'icon_name' => 'flag-icon-ru',
    		],
    		[
    			'folder_name' => 'japanese',
    			'name' => 'Japan',
    			'initial_name' => 'ja',
    			'icon_name' => 'flag-icon-jp',
    		],
    		[
    			'folder_name' => 'traditional-chinese',
    			'name' => 'Chinese',
    			'initial_name' => 'zh-TW',
    			'icon_name' => 'flag-icon-cn',
    		],
    		[
    			'folder_name' => 'turkish',
    			'name' => 'Turkey',
    			'initial_name' => 'tr',
    			'icon_name' => 'flag-icon-tr',
    		],
    		[
    			'folder_name' => 'dutch',
    			'name' => 'Netherland',
    			'initial_name' => 'nl',
    			'icon_name' => 'flag-icon-nl',
    		],
    	];
    }
} 


if (!function_exists('get_current_lang'))
{
    function get_current_lang()
    {
    	$ci =& get_instance();
    	return get_cookie('language') ? get_cookie('language') : $ci->config->item('language');
    }
} 

if (!function_exists('get_current_initial_lang'))
{
    function get_current_initial_lang()
    {
    	$current_lang = get_current_lang();

    	foreach (get_langs() as $lang) {
    		if ($current_lang == $lang['folder_name']) {
    			return $lang['icon_name'];
    		}
    	}
    }
} 



if (!function_exists('get_geolocation')) {
    
    function get_geolocation($ip) {
		/*$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));

		if (isset($details->country)) {
			return strtolower($details->country);
		}*/

		return 'gb';
    }
}


if (!function_exists('get_lang_by_ip')) {
    
    function get_lang_by_ip($ip) {
    	$location = get_geolocation($ip);

    	foreach (get_langs() as $key) {
    		if ($key['initial_name'] == $location) {
    			return $key['folder_name'];
    		}
    	}

    	return 'english';
    }
}


if (!function_exists('debug')) {
    
    function debug($vars = null) {
    	return get_instance()->console->debug($vars);
    }
}


if (!function_exists('cicool')) {
    function cicool() {
    	app()->load->library('cc_app');
    	return app()->cc_app->initialize();
    }
}


if (!function_exists('webPageUrl')) {
    function webPageUrl($page, $params = []) {
    	$params = array_merge(['page' => $page], $params);
    	return site_url('administrator/web-page?'.http_build_query($params));
    }
}

if (!function_exists('recurse_copy')) {
	function recurse_copy($src,$dst) { 
	    $dir = opendir($src); 
	    @mkdir($dst); 
	    while(false !== ( $file = readdir($dir)) ) { 
	        if (( $file != '.' ) && ( $file != '..' )) { 
	            if ( is_dir($src . '/' . $file) ) { 
	                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	            else { 
	                copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	        } 
	    } 
	    closedir($dir); 
	} 
}

if (!function_exists('create_childern')) {

	function create_childern($childern, $parent, $tree) {
	   foreach($childern as $child): 
	   	?>
	    <option <?= $child->id == $parent? 'selected="selected"' : ''; ?> value="<?= $child->id; ?>"><?= str_repeat('----', $tree) ?>   <?= ucwords($child->label); ?></option>
	    <?php if (isset($child->children) and count($child->children)): 
	    $tree++;
	    ?>
	    <?php create_childern($child->children, $parent, $tree); ?>
	    <?php endif ?>
	    <?php endforeach;  
	} 
}

if (!function_exists('extendsObject')) {
	function extendsObject($obj = []) {
		return $obj;
	}
}


if(!function_exists('get_all_blog')) {
	function get_all_blog() {
		return db_get_all_data('blog', ['status' => 'publish']);
	}
}

if(!function_exists('parse_nav_url')) {
	function parse_nav_url($url) {
		return str_replace([
			'{admin_url}'
		], [
			ADMIN_NAMESPACE_URL
		], $url);
	}
}



if (!function_exists('admin_url')) {
    function admin_url($url = null) {
    	return base_url(ADMIN_NAMESPACE_URL .'/'. $url);
    }
}



if(!function_exists('currency_format')) {
	function currency_format($number = '') {
		$thausand_separator = get_option('ecommerce_thausand_separator', '.');
		$decimal_separator = get_option('ecommerce_decimal_separator', ',');
		$decimal_length = get_option('ecommerce_decimal_length', 0);
		$text = 'IDR '.number_format($number, $decimal_length, $decimal_separator,$thausand_separator);

		return $text;
	}
}

if(!function_exists('get_theme_list')) {
	function get_theme_list() {
		$result = directory_map('cc-content/themes', 1);
		$result = array_map(function($item){
			return str_replace(DIRECTORY_SEPARATOR, '', $item);
		}, $result);
		return $result;
	}
}


if(!function_exists('get_message')) {
	function get_message($id) {
		app()->load->model('model_message');
		return app()->model_message->find($id);
	}
}

if(!function_exists('now')) {
	function now() {
		return date('Y-m-d H:i:s');
	}
}

if(!function_exists('now_utc')) {
	function now_utc() {
        date_default_timezone_set('UTC');

		return date('Y-m-d H:i:s');
	}
}


if(!function_exists('join_multi_select')) {
	function join_multi_select($values = null, 
		$relation_table = null, 
		$relation_value = null, 
		$relation_label = null) {

		if ($values) {
			$result = app()->db
				->select($relation_label)
			    ->where_in($relation_value, explode(',', $values))
			    ->get($relation_table)->result();
		    $labels = [];
		    foreach ($result as $row) {
		    	$labels[] = $row->{$relation_label};
		    }
		    return implode(',', $labels);
		}
		return '-';	
	}
}


if(!function_exists('is_dir_empty')) {
	function is_dir_empty($dir) {
	  if (!is_readable($dir)) return true; 
	  return (count(scandir($dir)) == 2);
	}
}


if(!function_exists('get_user_first_group')) {
	function get_user_first_group($user_id = null) {
		app()->load->library('aauth');
		foreach (app()->aauth->get_user_groups($user_id) as $row) {
			if (strtolower($row->name) != 'default') {
				return $row;
			}
		} 

		return false;
	}
}

if(!function_exists('render_status_badge')) {
	function render_status_badge($status) {
		if ($status === 1 || $status === '1') {
			$status = 'ACTIVE';
		} elseif ($status === 0 || $status === '0') {
			$status = 'INACTIVE';
		}

		$st_upper = strtoupper(trim($status ?? ''));
		if (in_array($st_upper, ['APPROVED', 'COMPLETED', 'DONE', 'SUCCESS', 'POSTED'])) {
			$bg = '#e6f6ec'; $color = '#16803c';
		} elseif (in_array($st_upper, ['REJECTED', 'CANCELLED', 'INACTIVE', 'INAKTIF', 'FAILED', 'BANNED', 'REVISION', 'REVISION_DRAFT'])) {
			$bg = '#fdecec'; $color = '#b42318';
		} elseif (in_array($st_upper, ['PENDING', 'NEGOTIATION', 'ON PROGRESS', 'ON HOLD', 'WAITING'])) {
			$bg = '#fdf3e7'; $color = '#b54708';
		} elseif (in_array($st_upper, ['OPEN_REGISTRATION', 'OPEN_REG', 'OPEN'])) {
			$bg = '#eff6ff'; $color = '#2563eb';
		} elseif (in_array($st_upper, ['ACTIVE', 'AKTIF', 'ASSIGNED', 'PAID', 'ORGANIC', 'APPROVED_DRAFT'])) {
			$bg = '#e8f1fb'; $color = '#1f4b6e';
		} elseif (in_array($st_upper, ['PENDING_REVIEW', 'IN_REVIEW_DRAFT'])) {
			$bg = '#f4ebff'; $color = '#6938b8';
		} elseif (in_array($st_upper, ['WAITING_DRAFT'])) {
			$bg = '#e0f2fe'; $color = '#0369a1';
		} elseif (in_array($st_upper, ['DRAFT'])) {
			$bg = '#f1f5f9'; $color = '#475569';
		} else {
			$bg = '#eef1f5'; $color = '#6b778c';
		}
		
		$display = $status ? _ent($status) : '-';
		return '<span style="display:inline-block; padding: 3px 9px; border-radius: 4px; background: ' . $bg . '; color: ' . $color . '; font-weight: 600; font-size: 12px; white-space: nowrap;">' . $display . '</span>';
	}
}

if (!function_exists('get_kol_profile_url')) {
	/**
	 * Build or format a valid external profile URL for a KOL platform account.
	 * Resolves relative URLs without http/https protocol, constructs URLs from username
	 * when profile_url is empty/invalid, and prevents broken target links in admin views.
	 *
	 * @param string $profile_url
	 * @param string $username
	 * @param string $platform_name
	 * @return string
	 */
	function get_kol_profile_url($profile_url = '', $username = '', $platform_code = '', $platform_name = '') {
		$profile_url    = trim((string)$profile_url);
		$username       = trim((string)$username);
		$clean_user     = ltrim($username, '@');
		$code           = strtoupper(trim((string)$platform_code));
		$plat_name      = strtolower(trim((string)$platform_name ?: (string)$platform_code));

		// 1. If explicit profile_url is provided and valid
		if (!empty($profile_url) && $profile_url !== '#' && strpos($profile_url, '.') !== false) {
			if (!preg_match('~^https?://~i', $profile_url)) {
				return 'https://' . ltrim($profile_url, '/');
			}
			return $profile_url;
		}

		// 2. If profile_url is empty, '#', or missing domain dot, construct from platform + username
		if (!empty($clean_user)) {
			if ($code === 'INSTAGRAM' || strpos($plat_name, 'instagram') !== false || $plat_name === 'ig') {
				return 'https://www.instagram.com/' . rawurlencode($clean_user);
			} elseif ($code === 'TIKTOK' || strpos($plat_name, 'tiktok') !== false || $plat_name === 'tt') {
				return 'https://www.tiktok.com/@' . rawurlencode($clean_user);
			} elseif ($code === 'YOUTUBE' || strpos($plat_name, 'youtube') !== false || $plat_name === 'yt') {
				if (strpos($clean_user, 'UC') === 0 || strpos($clean_user, 'channel/') !== false) {
					$chan = (strpos($clean_user, 'channel/') === 0) ? $clean_user : 'channel/' . $clean_user;
					return 'https://www.youtube.com/' . $chan;
				}
				return 'https://www.youtube.com/@' . rawurlencode($clean_user);
			} elseif ($code === 'TWITTER' || strpos($plat_name, 'twitter') !== false || strpos($plat_name, 'x') !== false) {
				return 'https://x.com/' . rawurlencode($clean_user);
			} elseif ($code === 'FACEBOOK' || strpos($plat_name, 'facebook') !== false || $plat_name === 'fb') {
				return 'https://www.facebook.com/' . rawurlencode($clean_user);
			} elseif ($code === 'LINKEDIN' || strpos($plat_name, 'linkedin') !== false) {
				return 'https://www.linkedin.com/in/' . rawurlencode($clean_user);
			}
			return 'https://' . rawurlencode($clean_user);
		}

		// 3. Fallback: prepend https:// to profile_url if non-empty and not '#'
		if (!empty($profile_url) && $profile_url !== '#') {
			if (!preg_match('~^https?://~i', $profile_url)) {
				return 'https://' . ltrim($profile_url, '/');
			}
			return $profile_url;
		}

		return 'javascript:void(0);';
	}
}

if (!function_exists('format_currency_short')) {
	/**
	 * Formats large IDR currency numbers compactly (e.g. 78636663.15 -> 78.6Jt, 4084549.20 -> 4.1Jt)
	 *
	 * @param float|int|string $num
	 * @return string
	 */
	function format_currency_short($num) {
		if ($num === null || $num === '' || (float)$num <= 0) return '0';
		$n = (float)$num;
		if ($n >= 1000000) {
			$val = $n / 1000000;
			if ($val >= 1000) {
				return number_format($val, 0, ',', '.') . 'Jt';
			} else if ($val >= 100) {
				return round($val, 0) . 'Jt';
			} else {
				return round($val, 1) . 'Jt';
			}
		} else if ($n >= 1000) {
			return round($n / 1000, 1) . 'K';
		}
		return number_format($n, 0, ',', '.');
	}
}

if (!function_exists('format_idr_friendly')) {
	/**
	 * Format currency into human-friendly Indonesian Rupiah denominations.
	 * Examples:
	 *   139627010418.00 => 'Rp 139,6 Miliar'
	 *   480246251.50    => 'Rp 480,2 Juta'
	 *   14051838.94     => 'Rp 14,1 Juta'
	 *   121286.50       => 'Rp 121.287'
	 *   0               => 'Rp 0'
	 *
	 * @param float|int|string|null $num
	 * @param bool $with_prefix Include 'Rp ' prefix (default true)
	 * @return string
	 */
	function format_idr_friendly($num, $with_prefix = true) {
		if ($num === null || $num === '' || (float)$num <= 0) {
			return $with_prefix ? 'Rp 0' : '0';
		}
		$n = (float)$num;
		$prefix = $with_prefix ? 'Rp ' : '';

		if ($n >= 1000000000000) {
			$val = $n / 1000000000000;
			$formatted = number_format($val, 1, ',', '.');
			return $prefix . $formatted . ' Triliun';
		} else if ($n >= 1000000000) {
			$val = $n / 1000000000;
			$formatted = number_format($val, 1, ',', '.');
			return $prefix . $formatted . ' Miliar';
		} else if ($n >= 1000000) {
			$val = $n / 1000000;
			$formatted = number_format($val, 1, ',', '.');
			return $prefix . $formatted . ' Juta';
		} else if ($n >= 1000) {
			return $prefix . number_format($n, 0, ',', '.');
		}
		return $prefix . number_format($n, 0, ',', '.');
	}
}

if (!function_exists('is_kol_user')) {
	/**
	 * Check if the user is a member of the Kol role group
	 *
	 * @param int|null $user_id
	 * @return bool
	 */
	function is_kol_user($user_id = null) {
		$ci =& get_instance();
		$ci->load->library('aauth');
		if (!$user_id) {
			$user_id = $ci->session->userdata('id');
		}
		if (!$user_id) {
			return false;
		}
		return $ci->aauth->is_member('Kol', $user_id);
	}
}

if (!function_exists('get_current_user_kol_id')) {
	/**
	 * Get the KOL ID linked to the given or current user
	 *
	 * @param int|null $user_id
	 * @return int|null
	 */
	function get_current_user_kol_id($user_id = null) {
		$ci =& get_instance();
		$ci->load->library('aauth');
		$ci->load->database();

		if (!$user_id) {
			$user_id = $ci->session->userdata('id');
		}
		if (!$user_id) {
			return null;
		}

		// 1. Check aauth_user_variables for 'kol_id'
		$kol_id = $ci->aauth->get_user_var('kol_id', $user_id);
		if (!empty($kol_id) && is_numeric($kol_id)) {
			$exists = $ci->db->select('id')->from('kols')->where('id', (int)$kol_id)->get()->row();
			if ($exists) {
				return (int)$kol_id;
			}
		}

		// 2. Fallback: match by email (case-insensitive & trimmed)
		$user = $ci->aauth->get_user($user_id);
		if ($user && !empty($user->email)) {
			$clean_email = strtolower(trim($user->email));
			$kol = $ci->db->select('id')->from('kols')->where('LOWER(TRIM(email))', $clean_email)->get()->row();
			if ($kol) {
				$ci->aauth->set_user_var('kol_id', $kol->id, $user_id);
				return (int)$kol->id;
			}
		}

		return null;
	}
}

if (!function_exists('kol_rate_content_types')) {
	/**
	 * Canonical rate/task content types (format-deliverable axis).
	 * Merged from legacy types: VT/Reels/Shorts/Story => Short Form Video;
	 * Review Post/Unboxing => Long Form Video. 'Other' removed.
	 *
	 * @return array type => ['desc' => ..., 'legacy_price' => kols column or null]
	 */
	function kol_rate_content_types() {
		return [
			'Short Form Video' => [
				'desc'         => 'Video vertikal pendek: TikTok, Reels, Shorts, Story (≤60 detik).',
				'legacy_price' => 'price_vt',
			],
			'Feed / Carousel' => [
				'desc'         => 'Konten feed statis atau carousel/swipe.',
				'legacy_price' => null,
			],
			'Live Streaming' => [
				'desc'         => 'Sesi interaktif / live shopping per sesi atau per jam.',
				'legacy_price' => 'price_live',
			],
			'Long Form Video' => [
				'desc'         => 'Video horizontal mendalam, review & unboxing produk.',
				'legacy_price' => 'price_long_form',
			],
		];
	}
}

if (!function_exists('kol_default_content_type')) {
	function kol_default_content_type() {
		return 'Short Form Video';
	}
}

if (!function_exists('kol_rate_type_slugs')) {
	/**
	 * URL/POST-safe slugs for rate types.
	 * CI3 global sanitizer coerces array keys with spaces to key 0,
	 * so human-readable type names must never be used as POST keys.
	 *
	 * @return array slug => canonical type
	 */
	function kol_rate_type_slugs() {
		return [
			'short_form' => 'Short Form Video',
			'feed'        => 'Feed / Carousel',
			'live'        => 'Live Streaming',
			'long_form'   => 'Long Form Video',
		];
	}
}

if (!function_exists('kol_content_type_aliases')) {
	/**
	 * Lowercase legacy => canonical map for client-side normalization.
	 */
	function kol_content_type_aliases() {
		return [
			'short form video' => 'Short Form Video',
			'short form'       => 'Short Form Video',
			'video / vt'       => 'Short Form Video',
			'vt'               => 'Short Form Video',
			'shorts'           => 'Short Form Video',
			'reels'            => 'Short Form Video',
			'story / reels'    => 'Short Form Video',
			'story'            => 'Short Form Video',
			'feed / carousel'  => 'Feed / Carousel',
			'carousel / feed'  => 'Feed / Carousel',
			'carousel'         => 'Feed / Carousel',
			'feed'             => 'Feed / Carousel',
			'live streaming'   => 'Live Streaming',
			'live'             => 'Live Streaming',
			'long form video'  => 'Long Form Video',
			'long form'        => 'Long Form Video',
			'review post'      => 'Long Form Video',
			'review'           => 'Long Form Video',
			'unboxing'         => 'Long Form Video',
			'unboxing & review produk' => 'Long Form Video',
			'unboxing / review' => 'Long Form Video',
		];
	}
}

if (!function_exists('normalize_content_type')) {
	/**
	 * Map any legacy content_type string to the canonical 4 types.
	 * Returns default when empty or unrecognized.
	 */
	function normalize_content_type($value) {
		$v = strtolower(trim((string)$value));
		$map = kol_content_type_aliases();
		if (isset($map[$v])) {
			return $map[$v];
		}
		$types = kol_rate_content_types();
		foreach ($types as $canonical => $meta) {
			if (strtolower($canonical) === $v) {
				return $canonical;
			}
		}
		return kol_default_content_type();
	}
}




// =============================================================================
// MULTI-BRAND TENANCY HELPERS
// Added: 2026-10-31 | Architecture: Single-Database Scoped Tenancy
// =============================================================================

if (!function_exists('get_user_brand_id')) {
    /**
     * Returns the brand_id assigned to a user in aauth_users.
     * Returns NULL for Superadmin (brand_id IS NULL = global access).
     *
     * @param  int|null $user_id  Defaults to the currently logged-in user.
     * @return int|null
     */
    function get_user_brand_id($user_id = null)
    {
        $ci =& get_instance();
        $ci->load->database();

        if (!$user_id) {
            $user_id = $ci->session->userdata('id');
        }
        if (!$user_id) {
            return null;
        }

        // Use a static cache to avoid repeated DB hits per request
        static $cache = [];
        if (array_key_exists($user_id, $cache)) {
            return $cache[$user_id];
        }

        $row = $ci->db->select('brand_id')
            ->where('id', (int) $user_id)
            ->get('aauth_users')
            ->row();

        $brand_id = ($row && $row->brand_id) ? (int) $row->brand_id : null;
        $cache[$user_id] = $brand_id;

        return $brand_id;
    }
}

if (!function_exists('is_superadmin')) {
    /**
     * Returns TRUE if the current user is a Superadmin:
     *   - Member of group ID 1 (admin), AND
     *   - Has brand_id IS NULL in aauth_users (no brand assignment).
     *
     * @return bool
     */
    function is_superadmin()
    {
        $ci =& get_instance();
        $ci->load->library('aauth');

        $user_id = $ci->session->userdata('id');
        if (!$user_id) {
            return false;
        }

        // Must be in admin group
        if (!$ci->aauth->is_admin()) {
            return false;
        }

        // Must have no brand assignment (brand_id IS NULL = true global admin)
        return get_user_brand_id($user_id) === null;
    }
}

if (!function_exists('get_active_brand_id')) {
    /**
     * Returns the effective brand_id for the current request.
     *
     * Scoped users (Staff / Leader): always returns their own brand_id.
     * Superadmin: reads from session 'active_brand_id'.
     *   - If a specific brand is selected → returns that brand's validated ID.
     *   - If 'all' or not set → returns null (global / no scope).
     *
     * Security: The session value is ALWAYS re-validated against the `brands`
     * table to prevent session forgery or stale references.
     *
     * @return int|null  brand_id to scope queries on, or null for "all brands"
     */
    function get_active_brand_id()
    {
        $ci =& get_instance();
        $ci->load->database();

        $user_id = $ci->session->userdata('id');
        if (!$user_id) {
            return null;
        }

        $user_brand_id = get_user_brand_id($user_id);

        // Scoped user: their brand is always authoritative, ignore session
        if ($user_brand_id !== null) {
            return $user_brand_id;
        }

        // Superadmin: read and validate session selection
        $session_brand_id = $ci->session->userdata('active_brand_id');

        if (!$session_brand_id || $session_brand_id === 'all') {
            return null; // Global view
        }

        // SECURITY: Re-validate the session value against DB every request
        // to prevent forged/stale session values from bypassing isolation
        static $validated_brand_ids = [];
        $key = (int) $session_brand_id;

        if (!array_key_exists($key, $validated_brand_ids)) {
            $brand = $ci->db->select('id')
                ->where('id', $key)
                ->where('is_active', 1)
                ->get('brands')
                ->row();

            $validated_brand_ids[$key] = $brand ? (int) $brand->id : null;
        }

        if ($validated_brand_ids[$key] === null) {
            // Session value points to a non-existent or inactive brand — clear it
            $ci->session->unset_userdata('active_brand_id');
        }

        return $validated_brand_ids[$key];
    }
}

if (!function_exists('get_active_brand')) {
    /**
     * Returns the full brand row object for the currently active brand,
     * or FALSE if no brand is selected / user is Superadmin in "all" mode.
     *
     * @return object|false
     */
    function get_active_brand()
    {
        $brand_id = get_active_brand_id();
        if (!$brand_id) {
            return false;
        }

        $ci =& get_instance();
        $ci->load->database();

        static $brand_cache = [];
        if (isset($brand_cache[$brand_id])) {
            return $brand_cache[$brand_id];
        }

        $brand = $ci->db->where('id', $brand_id)
            ->where('is_active', 1)
            ->get('brands')
            ->row();

        $brand_cache[$brand_id] = $brand ?: false;
        return $brand_cache[$brand_id];
    }
}

if (!function_exists('switch_brand')) {
    /**
     * Superadmin-only: switch the active brand context stored in session.
     * Pass 'all' to switch to the global / all-brands view.
     *
     * Validates the target brand_id against DB before writing to session.
     *
     * @param  int|string $brand_id  Target brand ID or 'all'
     * @return bool  TRUE on success, FALSE if brand not found / not superadmin
     */
    function switch_brand($brand_id)
    {
        if (!is_superadmin()) {
            return false; // Only superadmin may switch brand context
        }

        $ci =& get_instance();

        if ($brand_id === 'all' || $brand_id === null || $brand_id === 0) {
            $ci->session->set_userdata('active_brand_id', 'all');
            return true;
        }

        // Validate brand exists and is active before accepting it
        $ci->load->database();
        $brand = $ci->db->select('id')
            ->where('id', (int) $brand_id)
            ->where('is_active', 1)
            ->get('brands')
            ->row();

        if (!$brand) {
            return false;
        }

        $ci->session->set_userdata('active_brand_id', (int) $brand->id);
        return true;
    }
}


if (!function_exists('log_brand_event')) {
	/**
	 * Record a brand-related action to the audit_logs table.
	 *
	 * Recommended $action values:
	 *   brand_created, brand_updated, brand_deactivated, brand_reactivated,
	 *   brand_user_assigned, brand_user_unassigned, superadmin_brand_switch
	 *
	 * @param  string     $action       Slug describing the action
	 * @param  string     $entity_type  Table/entity name (e.g. 'brands', 'aauth_users')
	 * @param  int|null   $entity_id    Primary key of the affected record
	 * @param  array|null $old_values   Snapshot before change (will be JSON-encoded)
	 * @param  array|null $new_values   Snapshot after change (will be JSON-encoded)
	 * @param  int|null   $brand_id     The brand context. Defaults to get_active_brand_id().
	 * @return bool
	 */
	function log_brand_event(
		$action,
		$entity_type,
		$entity_id    = null,
		$old_values   = null,
		$new_values   = null,
		$brand_id     = null
	) {
		$ci =& get_instance();
		$ci->load->database();

		$user_id = $ci->session->userdata('id');

		if ($brand_id === null) {
			$brand_id = get_active_brand_id();
		}

		$data = [
			'user_id'     => $user_id    ? (int) $user_id    : null,
			'brand_id'    => $brand_id   ? (int) $brand_id   : null,
			'entity_type' => $entity_type,
			'entity_id'   => $entity_id !== null ? (int) $entity_id : 0,
			'action'      => $action,
			'old_values'  => $old_values ? json_encode($old_values, JSON_UNESCAPED_UNICODE) : null,
			'new_values'  => $new_values ? json_encode($new_values, JSON_UNESCAPED_UNICODE) : null,
			'created_at'  => date('Y-m-d H:i:s'),
		];

		return (bool) $ci->db->insert('audit_logs', $data);
	}
}
