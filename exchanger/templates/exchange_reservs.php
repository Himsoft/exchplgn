<?php
function exchanger_reservs_shortcode(){
    global $wpdb;
    $table_reserv_name = $wpdb->prefix . "exchanger_valutes_reserv";
    $table_valutes = $wpdb->prefix . "exchanger_valutes";
    $reservs = array();
    $reservs = $wpdb->get_results("SELECT * FROM $table_reserv_name");
    //$reservs = $wpdb->get_results("SELECT * FROM $table_reserv_name JOIN $table_valutes WHERE $table_reserv_name.code = $table_valutes.code");

    ?>
    <div class="reservs-block">
		<div class="reservs-head vc_col-sm-2">Резервы </br>валют</div>
		<div class="reservs-data">
        <?php foreach ($reservs as $reserv) {
            if(strpos($reserv->code, 'USD_') === false && strpos($reserv->code, 'EUR_') === false && strpos($reserv->code, 'UAH_') === false){
            ?>
			<div class="data-col <?=strtolower($reserv->code)?> vc_col-sm-2">
				<div class="res-icon"><img src="<?=$reserv->valute_icon?>"/></div>
				<div class="res-val-name"><?=$reserv->name?></div>
				<div class="res-val-data"><?=$reserv->reserv?></div>
			</div>
        <?php
            }
        } ?>
		</div>
        <div class="clearfix"></div>
    </div>
    <?php
}
?>