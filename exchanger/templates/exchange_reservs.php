<?php
function exchanger_reservs_shortcode(){
    global $wpdb;
    $table_reserv_name = $wpdb->prefix . "exchanger_valutes_reserv";
    $table_valutes = $wpdb->prefix . "exchanger_valutes";
    $reservs = array();
    $reservs = $wpdb->get_results("SELECT * FROM $table_reserv_name");
    //$reservs = $wpdb->get_results("SELECT * FROM $table_reserv_name JOIN $table_valutes WHERE $table_reserv_name.code = $table_valutes.code");

    ?>
    <div class="reservs-data">
        <?php foreach ($reservs as $reserv){
            if(strpos($reserv->code, 'USD_') === false && strpos($reserv->code, 'EUR_') === false && strpos($reserv->code, 'UAH_') === false){
            ?>
        <div class="data-col">
            <div class="icon"><img src="<?=$reserv->valute_icon?>"/></div>
            <div class="name"><?=$reserv->name?></div>
            <div class="reserv"><?=$reserv->reserv?></div>
        </div>
        <?php
            }
        } ?>
        <div class="clearfix"></div>
    </div>
    <?php

}
?>