<?php
$true_page = 'adatray';
function adatray_settings() {
    add_options_page( 'Ada tray for Accessibility', 'ADA Tray®', 'manage_options', 'Ada-tray', 'adatray_settings_page');
}
add_action('admin_menu', 'adatray_settings');

function adatray_settings_page(){
    global $true_page;
    $all_options = get_option('adatray_settings');
    ?><div class="wrap">
    <div id="logo_adatray">
        <img src="<?php echo plugin_dir_url(dirname( __FILE__ )) . 'assets/images/ada-tray.svg'; ?>" alt="ADA TRAY" width="75">
    </div>
 
<style>
#logo_adatray {text-align:center;}
.adatraymain {    margin: 0 auto; box-shadow: 5px 10px #ccc;  max-width: 550px;    background: #fff;    padding: 15px;    border-radius: 9px; border:1px solid #ccc;}
.regular-text {height:40px;}
input.regular-text:focus {border-color:#000;}
.form-table th {font-size:16px;}
.adatraybutton {text-align:center !important;}
#logo_adatray img{width:280px;}
.adatraybutton input{font-size: 16px !important; padding:8px !important;  width:180px;}
.adatray_code_error{color:red;}
</style>
    <?php if(!empty($all_options['code'])): ?>
        <div class="adatray_code">
            <p>Your Plugin is successfully installed/registered.</p>
        </div>
    <?php endif; ?>

    <?php if(empty($all_options['code'])): ?>
        <div class="adatray_code">
		<p>Please fill below details to activate your Plugin.</p>
	</div>
    <?php endif; ?>

    <?php if($all_options['status'] === 'error'): ?>
        <div class="adatray_code_error">
            <?php foreach($all_options['errors'] as $err) { ?>
            <p><strong>Error:</strong> <?= $err ?></p>
            <?php } ?>
        </div>
    <?php endif; ?>

    <p id="errordisplay" style="display:none;"></p>
    <form method="post" enctype="multipart/form-data" action="options.php">
	<div class="adatraymain">
        <?php
        settings_fields('adatray_settings');
        do_settings_sections($true_page);
        ?>
        <p class="submit adatraybutton">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
		</div>
    </form>

   
    </div><?php } ?>
<?php 
function ada_tray_form_settings() {
    global $true_page;
    register_setting( 'adatray_settings', 'adatray_settings', 'ada_tray_validate' );
    add_settings_section( 'true_section_1', '', '', $true_page );

    $true_field_params = array(
        'type'      => 'text',
        'id'        => 'email',
        'label_for' => 'email'
    );
    add_settings_field( 'ada_email_field', 'Email', 'ada_tray_settings', $true_page, 'true_section_1', $true_field_params );

    $true_field_params = array(
        'type'      => 'text',
        'id'        => 'first_name',
        'label_for' => 'first_name'
    );
    add_settings_field( 'ada_first_name_field', 'First name', 'ada_tray_settings', $true_page, 'true_section_1', $true_field_params );

    $true_field_params = array(
        'type'      => 'text',
        'id'        => 'last_name',
        'label_for' => 'last_name'
    );
    add_settings_field( 'ada_last_name_field', 'Last name', 'ada_tray_settings', $true_page, 'true_section_1', $true_field_params );

    $true_field_params = array(
        'type'      => 'text',
        'id'        => 'url',
        'label_for' => 'url'
    );
    add_settings_field( 'ada_url_field', 'URL', 'ada_tray_settings', $true_page, 'true_section_1', $true_field_params );

}
add_action( 'admin_init', 'ada_tray_form_settings' );

function ada_tray_settings($args) {

    extract( $args );

    $option_name = 'adatray_settings';

    $o = get_option( $option_name );
    $all_options = get_option('adatray_settings');

    switch ( $type ) {

        case 'text':
 
            $o[$id] = esc_attr( stripslashes($o[$id]) );
            echo "<input class='regular-text' type='text' id='".esc_attr($id)."' 
name='".esc_attr($option_name)."[".esc_html($id)."]' value='".esc_attr($o[$id])."' />";
            break;
    }
}

function ada_tray_validate($input) {

    $errors = [];

    if (empty($input['email'])) {
        $errors[] = 'Please enter your email Id';
    }
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    	$errors[] = 'Please enter valid email format';
    }
    if (empty($input['first_name'])) {
        $errors[] = 'Please enter your first name';
    }
    if (empty($input['last_name'])) {
        $errors[] = 'Please enter your last name';
    }
    if (!filter_var($input['url'], FILTER_VALIDATE_URL)) {
    	$errors[] = 'Please enter valid URL format along with http/https';
    }

    if (count($errors) > 0) {
        $input['errors'] = $errors;
        $input['status'] = 'error';
    }
    $input['widget'] = '1';
    if (empty($input['errors'])) {
        $data_string = $input;
        $args = array(
	    'body'        => $data_string,
	    'timeout'     => '5',
	    'redirection' => '5',
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'headers'     => array(),
	    'cookies'     => array(),
	);
  
	$response = wp_remote_post('https://www.adatray.com/index.php/widget-registration', $args);
	$result = $response['body'];   	
	
	if ($result != "") {
            $input['code'] = $result;
            $input['status'] = 'success';
        } else {
            $input['status'] = 'error';
            $input['errors'][] = 'All fields are required';
            foreach($input as $k => $v) {
                $valid_input[$k] = trim($v);
                if(($input[$k]) == '') {
                    $input['errors'][$k] = '_error';
                }
            }
        }
    }
    return $input;
}