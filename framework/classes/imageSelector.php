<?php
class imageSelector{

	private $accommodation;
	
	private static $imageFolder;
	
	private $name;
	
	private $plugID;
	
	public $upload_path;
	
	public function __construct( $accoMeta ){
		add_action( 'init', array( $this, 'init' ) );
		$this->name = 'Image Selector';
		$this->plugID = 'image_selector';
		$this->upload_path=wp_upload_dir();
		self::$imageFolder = ( $meta = get_option( IMG_PREFIX.'folder' ) ) ? $meta : 'galleries';
	}
	
	public function init(){
		add_action('add_meta_boxes', array($this,'create_meta_box'));
		add_action( 'save_post', array($this, 'save_image_meta') );
		add_action( 'admin_enqueue_scripts', array($this, 'add_admin_js') );
		
		add_action('admin_menu', array( $this, 'display_admin_page'));
	}
	
	public function add_admin_js( $hook ){
		if ( 'post.php' != $hook && 'post-new.php' != $hook && ( isset( $_GET['page'] ) && $_GET['page'] != $this->plugID )  ) return;
		
		wp_enqueue_style('bootstrap-img-plugin','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css');
		wp_enqueue_style('bootstrap-theme-img-plugin','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css');		
		wp_enqueue_script('bootstrap-js-img-plugin', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js', array('jquery'), '1.0', true);
		
		wp_register_script( $this->plugID.'.js',PLUG_URI_IMG_S.'framework/js/image_selector.js');
		wp_localize_script( $this->plugID.'.js','URL', array( 'siteurl' => get_option('siteurl') ));
		wp_enqueue_script( $this->plugID.'.js',  PLUG_URI_IMG_S.'framework/js/image_selector.js',array('jquery'), '1.0', true );
		wp_enqueue_style( $this->plugID.'.css', PLUG_URI_IMG_S.'framework/css/image_selector.css');
		
	}
	
	public function display_admin_page(){
		self::$imageFolder = ( $meta = get_option( IMG_PREFIX.'folder' ) ) ? $meta : 'galleries';
		$path=$this->upload_path['basedir'].'/'.self::$imageFolder;
		
		if( !is_dir( $path ) ) mkdir( $path, 0777,true );
		$this->check_form();
		add_menu_page($this->name, $this->name, 'administrator', $this->plugID, array($this, 'render_admin_page'), '', 27 );
	}
	
	public function render_admin_page(){
		$current_user = wp_get_current_user();
	
		$out = "<h1>Image Selector Plugin</h1><hr />";
		$out .= "<div class='col-md-12'><div class='alert alert-warning alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Warning!</strong> Contents of the old directory will be copied to the new directory. This will also delete the old directory.</div></div>";
		$out .= "<div class='col-md-6 pull-right'>";
		$out .= "<div class='panel panel-primary'><div class='panel-heading'><h3 class='panel-title'><strong>Welcome!</strong></h3></div><div class='panel-body'><p>Welcome, {$current_user->user_login} to Image Selector plugin admin page!  This plugin is used to set the feature image for posts without clicking the set feature image. </p><p>This page is where you will edit the path that will store the folders of your images. All folders created will be located at the <strong>Uploads</strong> folder of wordpress.</p></div></div>";
		$out .= "</div>";
		$out .= "<div class='col-md-6'>";
		$out .= "<form id='change-folder-path' action='' method='post' enctype='multipart/form-data'>";
		$out .= "<div class='form-group'>";
		$out .= "<label for='image-folder-form'>Folder Path: </label>";
		$out .= "<input type='text' id='image-folder-form' placeholder='Your Path To Folder of Images' class='form-control' name='path' value='".self::$imageFolder."' required>";
		$out .= "</div>";
		$out .= "<input type='submit' class='pull-right btn btn-primary' value='Submit'/>";
		$out .= "</form>";
		$out .= "</div>";
		echo $out;
	}
	
	public function check_form(){
		 if( isset( $_POST['path'] ) ){
			$oldPath=$this->upload_path['basedir'].'/'.self::$imageFolder;
			$newPath = $this->upload_path['basedir'].'/'.$_POST['path'] ;
			 
			update_option( IMG_PREFIX.'folder', stripslashes($_POST['path']) );
			//update option and create the new folder if it does not exist
			if( !is_dir( $newPath ) ) mkdir( $newPath, 0777,true );
			//copy files from the directory to new directory
			if( is_dir( $newPath ) && is_dir( $oldPath ) ){
				$i = new DirectoryIterator($oldPath);
				
				foreach($i as $f){
					if( !$f->isDot() )
						rename( $f->getPathname(), "{$newPath}/".$f->getFilename() );
				}
			}
			
			self::$imageFolder = stripslashes( $_POST['path'] );
			if( $newPath != $oldPath )
				rmdir($oldPath);
			
			echo "<div class='col-md-12'><div class='alert alert-success alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Success!</strong> You updated the path!</div></div>";
		
		}
	}

	public function set_post_type( $array ){
		if( !post_type_exists( 'accommodation' ) ){
			register_post_type($array['post_type']['id'], $array['post_type']);
		
			foreach( $array['taxonomies'] as $taxonomy )
				register_taxonomy( $taxonomy['taxonomy'], $taxonomy['object_type'], $taxonomy['settings']);
		
			add_action('add_meta_boxes', array($this,'create_meta_box'));
			add_action( 'save_post', array($this, 'save_acco_meta') );
		}
	}
	
	public function create_meta_box(){
		//add_meta_box(self::$accommodation['post_meta']['id'], self::$accommodation['post_meta']['title'], array($this,'renderMetaBox'), self::$accommodation['post_meta']['page'], self::$accommodation['post_meta']['context'], self::$accommodation['post_meta']['priority']);
		add_meta_box('image_folder_box', $this->name, array($this,'render_image_meta_box'), 'post', 'normal', 'high');
	}
	
	public function render_image_meta_box( $post ){
		wp_nonce_field( basename( __FILE__ ), 'image_nonce' );
		$stored_meta_folder = $this->getPostMeta($post->ID,'image_folder');
		$stored_meta_file = $this->getPostMeta($post->ID,'image_file');
		$out = $this->get_directories( $stored_meta_folder, $post->ID );
		$out .= "<input type='hidden' id='image-folder' value='".self::$imageFolder."'/>";
		$out .= "<input type='hidden' id='image-file' name='image-file' value=''/>";
		$out .= "<div id='file-wrap'>";
		$out .= ( !empty($stored_meta_file) ) ? self::get_files( $stored_meta_folder, $stored_meta_file ) : "";
		$out .= "</div>";
		
		echo $out;
	}
	
	public function save_image_meta ( $post_id ){
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'image_nonce' ] ) && wp_verify_nonce( $_POST[ 'image_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
		// Exits script depending on save status
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
		}
 
		// Checks for input and sanitizes/saves if needed
		if( isset( $_POST[ 'image_folder' ] ) && $_POST[ 'image_folder' ] != "None") {
			$upload = wp_upload_dir();
			$path = $upload['basedir'].'/'.self::$imageFolder.'/'.$_POST['image_folder'];
			$dir = new DirectoryIterator($path);
			$image_filename = $_POST['files'];
			$check_if_uploaded=false;
			$attach_id;
			//check if there is a filename submitted.
			if(isset($_POST['files'])){
				//Check if file is already attached to a post
				$args= array(
					'post_type' => 'attachment',
					'status' => 'inherit',
					'post_parent' => $post_id
				);
		
				$images = get_posts($args);
				
				foreach( $images as $image){
					if($image->post_title == preg_replace( '/\.[^.]+$/', '', $image_filename )){
						$check_if_uploaded = true;
						$attach_id=$image->ID;
						break;
					}
				}
		
				if(!$check_if_uploaded){
					$path .= "/".$image_filename;
					$file=wp_check_filetype($image_filename,null);
			
					$attachment = array(
						'guid' => $upload['url'].'/'.$image_filename,
						'post_mime_type' => $file['type'],
						'post_title' => preg_replace( '/\.[^.]+$/', '', $image_filename ),
						'post_content' => '',
						'post_status' => 'inherit'
					);
	
					$attach_id = wp_insert_attachment($attachment,$path,$post_id);
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					$attach_data = wp_generate_attachment_metadata($attach_id, $path);
					wp_update_attachment_metadata( $attach_id, $attach_data );
				}
		
				set_post_thumbnail($post_id, $attach_id);
			}
			update_post_meta($post_id,'_'.IMG_PREFIX.'image_folder',stripslashes($_POST['image_folder']));
			update_post_meta($post_id,'_'.IMG_PREFIX.'image_file',stripslashes($image_filename));
		}
	}
	
	public static function get_files( $folder, $fileName = '' ){
		$upload = wp_upload_dir();
		$path = $upload['basedir'].'/'.self::$imageFolder.'/'.$folder;
		$filesArray = array();
		$out = "";
		
		if( file_exists( $path ) ){
			$dir = new DirectoryIterator( $path );
			
			if ( ($files = @scandir($path)) && (count($files) > 2) ){
				$out .= "<label style='margin: 10px auto;'>Set Featured Image:</label><div id='images'>";//<select id='files' name='files' style='width: 100%'>";
				
				foreach($dir as $file)
					if (!$file->isDot()) 
						if(!preg_match('/(\d+)x(\d+)/',$fileInfo = $file->getFilename()))
							$filesArray[ $fileInfo ] = $file -> getSize();
				
				arsort( $filesArray );
				
				$out .= "<div class='row'>";
				
				foreach( $filesArray as $key => $val ){
					$out .= "<div class='col-md-3 img-wrapper'>";
					$out .= "<div class='img-thumbnail'>";
					$out .= "<img src=\"{$upload['baseurl']}/".self::$imageFolder."/{$folder}/{$key}\" data-filename='{$key}'/>" ;
					$out .= "</div>";
					$out .= "</div>";
				}
				
				$out .= "</div>";
				/*foreach( $filesArray as $key => $val ){
					$out .= "<option value='{$key}'";
					$out .= ( $fileName == $key ) ? "selected='selected'" : "";
					$out .= ">{$key}</option>";
				}
				//Get the first element of the associative array
				reset( $filesArray );
				$key = key( $filesArray );
				
				$out .= "</select><div id='show-preview'><label style='margin: 10px auto;'>Featured Image Preview:</label>";
				
				$out .= ( !empty($fileName) ) ? "<img src=\"{$upload['baseurl']}/".self::$imageFolder."/{$folder}/{$fileName}\"/>" : "<img src=\"{$upload['baseurl']}/".self::$imageFolder."/{$folder}/{$key}\"/>" ;
				
				$out .= "</div>";*/
				$out .= "</div>";
			}
		}
			
		return $out;
	}
	
	public function getPostMeta($ID, $key, $default='') {
		$meta=get_post_meta($ID, '_'.IMG_PREFIX.$key, true);
		
		if($key==="image_folder" && empty($meta)){
			$title=get_the_title($ID);
			return ($title === "Auto Draft") ? "New-Hotel" : str_replace(" ","-",$title); 
		}
		
		return ($meta=='' && (!empty($default) || is_array($default))) ? $default : $meta;
	}
	
	public function get_directories( $stored_meta, $post_id ){
		$upload_path=wp_upload_dir();
		$hotel_path=$upload_path['basedir'].'/'.self::$imageFolder;
		$dir = new DirectoryIterator($hotel_path);		
		$array = array();
		
		foreach ($dir as $fileinfo) {
			if ($fileinfo->isDir() && !$fileinfo->isDot()) {
				$array[]=$fileinfo->getFilename();
			}
		}
		
		asort($array);
		
		$c="<label>Image Folder:</label><select name='image_folder' id='image_folder' style='width:100%'><option>None</option>";

		foreach($array as $item){
			$c .= "<option value='".$item."'";
			$c .= ( $item == $stored_meta ) ? "selected='selected'" : "";
			$c .= ">".$item."</option>";
		}
		$c .= "</select>";
		
		return $c;
		
	}
	
	public static function renderMetaBox(){
		global $post;
		
		$out='<input type="hidden" name="themex_nonce" value="'.wp_create_nonce($post->ID).'" />'; 
		$out.='<table class="themex-metabox" style="width: 100%;">';
		
		//render metabox
		foreach(self::$accommodation['post_meta']['options'] as $option) {					
						
		//render option
			$option['id']='_acco_'.$option['id'];	

		//get option value
			$option['value']=get_post_meta($post->ID, $option['id'], true);
		
			$out.='<tr><th><h4 class="themex-meta-title">'.$option['name'].'</h4></th><td>'.self::render_option($option).'</td></tr>';
		}
	
		$out.='</table>';
	
		echo $out;
	}
	
	public static function render_option( $option ){
		$out = "";
	
		if(!isset($option['value'])) {
			$option['value']='';
		}
	
		switch($option['type']) {
			//text field
			case 'text':
				$out.='<input type="text" id="'.$option['id'].'" name="'.$option['id'].'" value="'.$option['value'].'" style="width: 100%; height: 35px;" />';
			break;
			
			//number field
			case 'number':
				$out.='<input type="number" id="'.$option['id'].'" name="'.$option['id'].'" value="'.abs(intval($option['value'])).'" style="width: 100%;height: 35px;" />';
			break;

			//message field
			case 'textarea':
				$out.='<textarea id="'.$option['id'].'" name="'.$option['id'].'" style="width: 100%;height: 100px;">'.$option['value'].'</textarea>';
			break;
			
			//custom dropdown
			case 'select':
				$out.='<select id="'.$option['id'].'" name="'.$option['id'].'" style="width: 100%;height: 35px;">';
				
				if(isset($option['options'])) {
					foreach($option['options'] as $name=>$title) {
						$selected='';
						if($option['value']!='' && ($name==$option['value'] || (is_array($option['value']) && in_array($name, $option['value'])))) {
							$selected='selected="selected"';
						}
						
						$out.='<option value="'.$name.'" '.$selected.'>'.$title.'</option>';
					}
				}
				
				$out.='</select>';
			break;
		}
		
		return $out;
	}
	
	public function save_acco_meta( $post_id ){

		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'themex_nonce' ] ) && wp_verify_nonce( $_POST[ 'themex_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
		// Exits script depending on save status
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
		}
	
		foreach( self::$accommodation['post_meta']['options'] as $option ){
			if( isset( $_POST[ '_acco_'.$option['id'] ] ) ) {
				update_post_meta($post_id,'_acco_'.$option['id'] ,stripslashes($_POST['_acco_'.$option['id']]));
			}
		}
	}
}