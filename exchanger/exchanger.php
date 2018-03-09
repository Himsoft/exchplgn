<?php
/*
    Plugin Name: exchanger
    Description: Exganger custom plugin
    Version: 1.2
    Author: Himsoft
    License: GPL2

    Copyright YEAR  Himsoft  (email : himfoft@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
global $config_valutes;

$config_valutes = array('USD' => 'USD', 'UAH' => 'UAH');

$path = plugin_dir_path( __FILE__ );
require_once($path . 'exchanger_install.php');
register_activation_hook(__FILE__,'exchanger_install');
require_once($path . 'exchanger_cron.php');
require_once($path . 'exchanger_options.php');

function generate_order_num($id = false){
    global $wpdb;
    //if(!$id)
    $id = uniqid();
    $table_name = $wpdb->prefix . "exchanger_orders";
    $ids = $wpdb->get_var("SELECT id FROM $table_name WHERE order_num = '$id'");
    if(!empty($ids)) $id = generate_order_num();
    return $id;
}

add_action('admin_menu', 'exchanger_menu');
function exchanger_menu() {
    add_options_page('Exchanger Options', 'Exchanger options', 'manage_options', 'exchanger_options', 'exchanger_options_page');
    add_options_page('Exchanger Koefficient Options', 'Exchanger koefficients', 'manage_options', 'exchanger_koef_options', 'exchanger_koefficients_options_page');
    add_options_page('Exchanger Reserv config', 'Exchanger reservs', 'manage_options', 'exchanger_reserv_congig', 'exchanger_reserv_page');
    add_options_page('Exchanger orders', 'Exchanger orders', 'manage_options', 'exchanger_orders', 'exchanger_orders_list');
}

function exchanger_form() {

    global $wpdb, $config_valutes;
    $valutes = $icons = array();
    $table_name = $wpdb->prefix . "exchanger_valutes";
    $newtable = $wpdb->get_results("SELECT * FROM $table_name");

    foreach ($newtable as $item) {
        $icons[$item->code] = $item->valute_icon;
        if(strpos($item->code, 'USD_') !== false || strpos($item->code, 'EUR_') !== false || strpos($item->code, 'UAH_') !== false){
            $valutes[$item->code] = $item->name;
        }else {
            $valutes[$item->code] = $item->code;
        }
    }
    $valutes = array_merge($valutes,$config_valutes);
    ?>
<style>
    .cource {
        text-align: center;
        font-weight: bold;
    }

    .card, .purse, .city, .crypto-hide, .nal-hide {
        display: none;
    }
</style>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="exchange-form">
        <div class="step-one formcol vc_col-sm-3">
            <div class="h2"><span class="step-num">1</span><?=_('Отдаете')?></div>
			<div class="col-data">
				<div>
					<label for="forvalute"><?php echo _('Валюта'); ?>:</label>
					<select id="forvalute" name="forvalute">
						<option disabled="disabled" value="" <?php echo ( isset( $_POST['forvalute'] ) ? null : ' selected="selected"'); ?>><?php echo _('Выберите валюту'); ?></option>
						<?php
						foreach ($valutes as $value => $code){
							?>
							<option data-icon="<?=$icons[$code]?>" <?php echo(isset($_POST['forvalute']) && $_POST['forvalute'] == $value ? ' selected="selected"' : null); ?> value="<?php echo $value; ?>"><?php echo $code; ?></option>
						<?php } ?>
					</select>
				</div>
				<div>
					<label for="sumfor"><?php echo _('Сумма'); ?>:</label>
					<input type="text" autocomplete="off" id="sumfor" name="sumfor" min="0" value="<?php echo ( isset( $_POST['sumfor'] ) ? $_POST['sumfor'] : null ); ?>">
				</div>

				<div class="card">
					<label for="cardfor"><?php echo _('№ карты');?>:</label>
					<input type="text" autocomplete="off" name="cardfor" value="<?php echo(isset($_POST['cardfor']) ? $_POST['cardfor'] : null); ?>">
				</div>

				<div class="purse">
					<label for="pursefor"><?php echo _('Счет');?>:</label>
					<input type="text" autocomplete="off" name="pursefor" value="<?php echo(isset($_POST['pursefor']) ? $_POST['pursefor'] : null); ?>">
				</div>

				<div class="city">
					<label for="cityfor"><?php echo _('Город');?>:</label>
					<input type="text" autocomplete="off" name="cityfor" value="<?php echo(isset($_POST['cityfor']) ? $_POST['cityfor'] : null); ?>">
				</div>
				<div class="transfer-ico"><i class="fa fa-arrows-h" aria-hidden="true"></i></div>
			</div>
        </div>

        <div class="step-two formcol vc_col-sm-3">
            <div class="h2"><span class="step-num">2</span><?=_('Получаете')?></div>
			<div class="col-data">
				<div class="form-item st2-val">
					<div class="cource"></div>
					<label for="tovalute"><?php echo _('Валюта'); ?></label>
					<select id="tovalute" name="tovalute">
						<option disabled="disabled" value="" <?php echo ( isset( $_POST['tovalute'] ) ? null : ' selected="selected"'); ?>><?php echo _('Выберите валюту'); ?></option>
						<?php
						foreach ($valutes as $value => $code){
							?>
							<option data-icon="<?=$icons[$code]?>" <?php echo(isset($_POST['tovalute']) && $_POST['tovalute'] == $value ? ' selected="selected"' : null); ?> value="<?php echo $value; ?>"><?php echo $code; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-item st2-summ">
					<div class="reserv"></div>
					<label for="sumto"><?php echo _('Сумма'); ?>:</label>
					<input type="text" autocomplete="off" readonly="readonly" id="sumto" max="0" name="sumto" value="<?php echo ( isset( $_POST['sumto'] ) ? $_POST['sumto'] : null ); ?>">
				</div>

				<div class="form-item card">
					<label for="cardto"><?php echo _('№ карты');?>:</label>
					<input type="text" autocomplete="off" name="cardto" value="<?php echo(isset($_POST['cardto']) ? $_POST['cardto'] : null); ?>">
				</div>

				<div class="form-item purse">
					<label for="purseto"><?php echo _('Счет');?>:</label>
					<input type="text" autocomplete="off" name="purseto" value="<?php echo(isset($_POST['purseto']) ? $_POST['purseto'] : null); ?>">
				</div>

				<div class="form-item city">
					<label for="cityto"><?php echo _('Город');?>:</label>
					<input type="text" autocomplete="off" name="cityto" value="<?php echo(isset($_POST['cityto']) ? $_POST['cityto'] : null); ?>">
				</div>
			</div>
        </div>

        <div class="step-three user-info formcol vc_col-sm-3">
            <div class="h2"><span class="step-num">3</span><?=_('Личные данные')?>:</div>
			<div class="col-data">
				<div class="form-item form-item">
					<label for="firstname"><?php echo _('Имя');?>:</label>
					<input type="text" autocomplete="off" name="firstname" value="<?php echo(isset($_POST['firstname']) ? $_POST['firstname'] : null); ?>">
				</div>

				<div class="form-item crypto-hide nal-hide">
					<label for="lastname"><?php echo _('Фамилия');?>:</label>
					<input type="text" autocomplete="off" name="lastname" value="<?php echo(isset($_POST['lastname']) ? $_POST['lastname'] : null); ?>">
				</div>

				<div class="form-item">
					<label for="email">E-mail:</label>
					<input type="text" autocomplete="off" name="email" value="<?php echo(isset($_POST['email']) ? $_POST['email'] : null); ?>">
				</div>

				<div class="form-item crypto-hide">
					<label for="phone"><?php echo _('Телефон'); ?>:</label>
					<input type="text" autocomplete="off" name="phone" value="<?php echo(isset($_POST['phone']) ? $_POST['phone'] : null); ?>">
				</div>
			</div>
        </div>
        <div class="step-four actions formcol vc_col-sm-3">
            <button type="submit" name="submit">
			    <div class="submit-before-wrap">
					<span class="step-num">4</span>
					<span class="icon-submit"></span>
				</div>
                <span class="capture-submit"><?=_('Совершить обмен')?></span>
            </button>
            <div class="description-submit">
                <p>"Нажимая кнопку "Совершить обмен" </br>вы подтверждаете свое согласие </br>с <a href="#">Правилами предоставления услуг</a> </br>сервиса CryptoMarket"</p>
            </div>
        </div>
    </form>
<?php
}
function exchanger_validation($postdata)
{
    global $wpdb, $reg_errors, $config_valutes, $orderdata;
    $orderdata = $postdata;
    $table_name = $wpdb->prefix . "exchanger_valutes";
    $newtable = $wpdb->get_results("SELECT * FROM $table_name");

    $reg_errors = new WP_Error;
    $nal = array_keys($config_valutes);
    $crypto = $v_nal = array();
    foreach ($newtable as $item){
        foreach ($nal as $val){
            if(strpos($item->code, $val . '_') !== false){
                $v_nal[] = $item->code;
            }
        }
        $crypto[] = $item->code;
    }
    $crypto = array_diff($crypto,$v_nal);

    if (empty($postdata['forvalute'])) {
        $reg_errors->add('field', 'Forvalute form field is required');
    }
    if (empty($postdata['tovalute'])) {
        $reg_errors->add('field', 'Tovalute form field is required');
    }
    if (empty($postdata['sumfor'])) {
        $reg_errors->add('field', 'Sumfor form field is required');
    }
    if (empty($postdata['firstname'])) {
        $reg_errors->add('field', 'Name form field is required');
    }

    if(in_array($postdata['forvalute'], $v_nal) || in_array($postdata['tovalute'], $v_nal)){
        if (empty($postdata['lastname'])) {
            $reg_errors->add('field', 'Lastname form field is required');
        }
        if (empty($postdata['phone'])) {
            $reg_errors->add('field', 'Phone form field is required');
        }

        if(in_array($postdata['forvalute'], $v_nal)){
            if (empty($postdata['cardfor'])) {
                $reg_errors->add('field', 'Cardfor form field is required');
            }
        }
        if(in_array($postdata['tovalute'], $v_nal)){
            if (empty($postdata['cardto'])) {
                $reg_errors->add('field', 'Cardto form field is required');
            }
        }

    }

    if (in_array($postdata['forvalute'], $nal) || in_array($postdata['tovalute'], $nal)){
        if (empty($postdata['phone'])) {
            $reg_errors->add('field', 'Phone form field is required');
        }
        if(in_array($postdata['forvalute'], $nal)){
            if (empty($postdata['cityfor'])) {
                $reg_errors->add('field', 'Cityfor form field is required');
            }
        }
        if(in_array($postdata['tovalute'], $nal)){
            if (empty($postdata['cityto'])) {
                $reg_errors->add('field', 'Cityto form field is required');
            }
        }

    }

    if (in_array($postdata['forvalute'], $crypto) || in_array($postdata['tovalute'], $crypto)){
        if(in_array($postdata['forvalute'], $crypto)){
            if (empty($postdata['pursefor'])) {
                $reg_errors->add('field', 'Pursefor form field is required');
            }
        }
        if(in_array($postdata['tovalute'], $crypto)){
            if (empty($postdata['purseto'])) {
                $reg_errors->add('field', 'Purseto form field is required');
            }
        }

    }

    if (empty($postdata['email'])) {
        $reg_errors->add('field', 'Email form field is required');
    }

    if (is_wp_error($reg_errors)) {

        foreach ($reg_errors->get_error_messages() as $error) {

            echo '<div>';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';
            echo '</div>';

        }

    }
}

function exchanger_complete() {
    global $reg_errors, $orderdata, $wpdb;
    if (1 > count($reg_errors->get_error_messages())) {
        unset($orderdata['submit']);
        $err = false;

        $table_name = $wpdb->prefix . "exchanger_cources";
        $symbol = $wpdb->get_row("SELECT * FROM $table_name WHERE code = '".$orderdata['forvalute']."' AND exchange_code = '".$orderdata['tovalute']."'");
        $course = round($symbol->cource*(1-($symbol->koefficient/100)));

        $reserv_table_name = $wpdb->prefix . "exchanger_valutes_reserv";
        $reserv = $wpdb->get_row("SELECT * FROM $reserv_table_name WHERE code = '".$orderdata['tovalute']."'");

        $minsum = !is_null($symbol->minsum)?$symbol->minsum:0;
        $reserv = !is_null($reserv->reserv)?$reserv->reserv:0;
        $sumto = round($course*$orderdata['sumfor'],2);

        if($orderdata['sumfor'] < $minsum){
            $err = true;
            echo '<div>';
            echo '<strong>ERROR</strong>:';
            echo _('The minimum amount must be '). $minsum . ' '. $orderdata['forvalute'] . '<br/>';
            echo '</div>';
            $_POST['sumfor'] = $minsum;
        }
        if( $sumto > $reserv){
            $err = true;
            echo '<div>';
            echo '<strong>ERROR</strong>:';
            echo _('The maximum amount not should be '). $reserv . ' '. $orderdata['tovalute'] . '<br/>';
            echo '</div>';
            $_POST['sumto'] = $reserv;
        }
        if(!$err) {
            $orderdata['sumto'] = $sumto;
            $orderdata['course'] = $course;
            $table_order_name = $wpdb->prefix . "exchanger_orders";
            $data = array(
                'time' => time(),
                'time_update' => time(),
                'order_num' => generate_order_num(),
                'order_data' => serialize($orderdata),
                'status' => 0,
            );
            $orderid = $wpdb->insert($table_order_name, $data);
            if($orderid){
                ?>
                <script>
                    window.location.href = "<?=get_site_url().'/my-order?ordn='.$data['order_num']?>";
                </script>

                <?php
            }
        }
    }
}

function exchanger_function() {
    if ( isset($_POST['submit'] ) ) {
        exchanger_validation($_POST);
        global $reg_errors, $orderdata;
        exchanger_complete();
    }

    exchanger_form();
}

// Register a new shortcode: [exchanger_forms]
add_shortcode( 'exchanger_forms', 'exchanger_form_shortcode' );

// The callback function that will replace [book]
function exchanger_form_shortcode() {
    ob_start();
    exchanger_function();
    return ob_get_clean();
}

// Register a new shortcode: [exchanger_order_info]
add_shortcode( 'exchanger_order_info', 'exchanger_order_info_shortcode' );

// The callback function that will replace [book]
function exchanger_order_info_shortcode() {
    ob_start();
    exchanger_order_info_function();
    return ob_get_clean();
}

function exchanger_order_info_function(){
    $dir = plugin_dir_path( __FILE__ );
    include($dir."templates" . DIRECTORY_SEPARATOR . "exchange_order_page.php");
}

add_action('wp_ajax_get_course'       , 'get_course_callback');
add_action('wp_ajax_nopriv_get_course', 'get_course_callback');

function get_course_callback(){
    /*
    $res = file_get_contents('https://api.coinmarketcap.com/v1/ticker/'.$_POST['for'].'/?convert='.$_POST['to']);
    $res = json_decode($res);
    */
    global  $wpdb;
    $table_name = $wpdb->prefix . "exchanger_cources";
    $symbol = $wpdb->get_row("SELECT * FROM $table_name WHERE code = '".$_POST['for']."' AND exchange_code = '".$_POST['to']."'");

    $course = $symbol->cource*(1-($symbol->koefficient/100));

    echo(json_encode(
            array(
                'status'=>'ok',
                'request_vars'=>round($course*$_POST['sumfor'],15),
                'cource' => $course
            )
        )
    );
    wp_die();
}

add_action('wp_ajax_get_minsum_reserv'       , 'get_minsum_reserv_callback');
add_action('wp_ajax_nopriv_get_minsum_reserv', 'get_minsum_reserv_callback');

function get_minsum_reserv_callback(){
    /*
    $res = file_get_contents('https://api.coinmarketcap.com/v1/ticker/'.$_POST['for'].'/?convert='.$_POST['to']);
    $res = json_decode($res);
    */
    global  $wpdb;
    $table_name = $wpdb->prefix . "exchanger_cources";
    $symbol = $wpdb->get_row("SELECT * FROM $table_name WHERE code = '".$_POST['for']."' AND exchange_code = '".$_POST['to']."'");
    $course = $symbol->cource*(1-($symbol->koefficient/100));

    $reserv_table_name = $wpdb->prefix . "exchanger_valutes_reserv";
    $reserv = $wpdb->get_row("SELECT * FROM $reserv_table_name WHERE code = '".$_POST['to']."'");

    echo(json_encode(
            array(
                'status'=>'ok',
                'minsum' => !is_null($symbol->minsum)?$symbol->minsum:0,
                'reserv' => !is_null($reserv->reserv)?$reserv->reserv:0,
                'cource' => $course
            )
        ));
    wp_die();
}

add_action( 'wp_head', 'exchanger_add_js' );
function exchanger_add_js() {
    $path = plugin_dir_url( __FILE__ );
    wp_enqueue_script( 'exhanger_ajax', $path. 'js/exhanger_ajax.js', array( 'jquery' ), '1.0.0', true );

    $protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';

    $params = array(
        'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
    );
    wp_localize_script( 'exhanger_ajax', 'exhanger_params', $params );
}

function load_admin_libs() {
    $path = plugin_dir_url( __FILE__ );
    wp_enqueue_media();
    wp_enqueue_script( 'wp-media-uploader', $path. 'js/wp_media_uploader.js', array( 'jquery' ), 1.0 );
}
add_action( 'admin_enqueue_scripts', 'load_admin_libs' );
?>