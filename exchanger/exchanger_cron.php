<?php
add_filter( 'cron_schedules', 'cron_add_five_min' );
function cron_add_five_min( $schedules ) {
    $schedules['five_min'] = array(
        'interval' => 60 * 5,
        'display' => 'Once every 5 minutes'
    );
    return $schedules;
}

add_action('wp', 'my_activation');
function my_activation() {
    if ( ! wp_next_scheduled( 'my_five_min_event' ) ) {
        wp_schedule_event( time(), 'five_min', 'my_five_min_event');
    }
}

add_action('my_five_min_event', 'do_every_five_min');
function do_every_five_min() {
    /*$resUSD = file_get_contents('https://api.coinmarketcap.com/v1/ticker/?convert=USD');
    $resEUR = file_get_contents('https://api.coinmarketcap.com/v1/ticker/?convert=EUR');
    $resUAH = file_get_contents('https://api.coinmarketcap.com/v1/ticker/?convert=UAH');
*/
}


add_filter( 'cron_schedules', 'cron_add_one_min' );
function cron_add_one_min( $schedules ) {
    $schedules['one_min'] = array(
        'interval' => 60,
        'display' => 'Once every 1 minute'
    );
    return $schedules;
}

add_action('wp', 'my_activation_one');
function my_activation_one() {
    if ( ! wp_next_scheduled( 'my_one_min_event' ) ) {
        wp_schedule_event( time(), 'one_min', 'my_one_min_event');
    }
}

add_action('my_one_min_event', 'do_every_one_min');
function do_every_one_min() {
    global $wpdb, $config_valutes;
    $valutes_codes = array_keys($config_valutes);
    $table_name = $wpdb->prefix . "exchanger_valutes";
    $newtable = $wpdb->get_results("SELECT * FROM $table_name");
    $valutes = array();
    $valutes_codes_ps = array();
    foreach ($newtable as $item) {
        $valutes[] = $item->name;
        if(strpos($item->code, 'USD_') !== false || strpos($item->code, 'EUR_') !== false || strpos($item->code, 'UAH_') !== false){
            $valutes_codes_ps[$item->name] = $item->code;
        }
    }
    $table_name = $wpdb->prefix . "exchanger_cources";
    foreach ($valutes_codes as $valutes_code) {
        $res = file_get_contents("https://api.coinmarketcap.com/v1/ticker/?convert=$valutes_code");
        $res = json_decode($res);

        foreach ($res as $item) {
            if (!in_array($item->id, $valutes)) continue;
            $key = 'price_' . strtolower($valutes_code);
            $data = array(
                'time' => time(),
                'name' => $item->id,
                'code' => $item->symbol,
                'exchange_name' => $valutes_code,
                'exchange_code' => $valutes_code,
                'cource' => 1 * $item->$key,
            );

            $data_i = array(
                'time' => time(),
                'name' => $valutes_code,
                'code' => $valutes_code,
                'exchange_name' => $item->id,
                'exchange_code' => $item->symbol,
                'cource' => round(1 / $item->$key, 15),
            );

            $symbol = $wpdb->get_var("SELECT id FROM $table_name WHERE code = '$item->symbol' AND exchange_code = '$valutes_code'");
            if (!empty($symbol)) {
                $wpdb->update($table_name, $data, array("ID" => $symbol));
            } else {
                $wpdb->insert($table_name, $data);
            }
            $symbol = $wpdb->get_var("SELECT id FROM $table_name WHERE exchange_code = '$item->symbol' AND code = '$valutes_code'");
            if (!empty($symbol)) {
                $wpdb->update($table_name, $data_i, array("ID" => $symbol));
            } else {
                $wpdb->insert($table_name, $data_i);
            }

            if($valutes_code != 'USD') {

                $key_val = 'price_' . strtolower($valutes_code);

                $data_val = array(
                    'time' => time(),
                    'name' => 'USD',
                    'code' => 'USD',
                    'exchange_name' => $valutes_code,
                    'exchange_code' => $valutes_code,
                    'cource' => round($item->$key_val / $item->price_usd, 15),
                );

                $data_val_i = array(
                    'time' => time(),
                    'name' => $valutes_code,
                    'code' => $valutes_code,
                    'exchange_name' => 'USD',
                    'exchange_code' => 'USD',
                    'cource' => round($item->price_usd / $item->$key_val, 15),
                );

                $symbol = $wpdb->get_var("SELECT id FROM $table_name WHERE code = 'USD' AND exchange_code = '$valutes_code'");
                if (!empty($symbol)) {
                    $wpdb->update($table_name, $data_val, array("ID" => $symbol));
                } else {
                    $wpdb->insert($table_name, $data_val);
                }
                $symbol = $wpdb->get_var("SELECT id FROM $table_name WHERE exchange_code = 'USD' AND code = '$valutes_code'");
                if (!empty($symbol)) {
                    $wpdb->update($table_name, $data_val_i, array("ID" => $symbol));
                } else {
                    $wpdb->insert($table_name, $data_val_i);
                }
            }

            foreach ($valutes_codes_ps as $id => $valutes_code_ps){

                if(strpos($valutes_code_ps, $valutes_code.'_') !== false) {
                    $key = 'price_' . strtolower($valutes_code);
                    $data = array(
                        'time' => time(),
                        'name' => $item->id,
                        'code' => $item->symbol,
                        'exchange_name' => $id,
                        'exchange_code' => $valutes_code_ps,
                        'cource' => 1 * $item->$key,
                    );

                    $data_i = array(
                        'time' => time(),
                        'name' => $id,
                        'code' => $valutes_code_ps,
                        'exchange_name' => $item->id,
                        'exchange_code' => $item->symbol,
                        'cource' => round(1 / $item->$key, 15),
                    );
                    $symbol = $wpdb->get_var("SELECT id FROM $table_name WHERE code = '$item->symbol' AND exchange_code = '$valutes_code_ps'");
                    if (!empty($symbol)) {
                        $wpdb->update($table_name, $data, array("ID" => $symbol));
                    } else {
                        $wpdb->insert($table_name, $data);
                    }
                    $symbol = $wpdb->get_var("SELECT id FROM $table_name WHERE exchange_code = '$item->symbol' AND code = '$valutes_code_ps'");
                    if (!empty($symbol)) {
                        $wpdb->update($table_name, $data_i, array("ID" => $symbol));
                    } else {
                        $wpdb->insert($table_name, $data_i);
                    }
                }
                foreach ($valutes_codes as $valutes_code_tmp) {
                    $key_val = 'price_' . strtolower($valutes_code_tmp);
                    $val_code = explode('_', $valutes_code_ps);
                    $val_code = $val_code[0];
                    $key_val_ps = 'price_' . strtolower($val_code);

                    $data_val = array(
                        'time' => time(),
                        'name' => $id,
                        'code' => $valutes_code_ps,
                        'exchange_name' => $valutes_code_tmp,
                        'exchange_code' => $valutes_code_tmp,
                        'cource' => round($item->$key_val / $item->$key_val_ps, 15),
                    );

                    $data_val_i = array(
                        'time' => time(),
                        'name' => $valutes_code_tmp,
                        'code' => $valutes_code_tmp,
                        'exchange_name' => $id,
                        'exchange_code' => $valutes_code_ps,
                        'cource' => round($item->$key_val_ps / $item->$key_val, 15),
                    );

                    $symbol = $wpdb->get_var("SELECT id FROM $table_name WHERE code = '$valutes_code_ps' AND exchange_code = '$valutes_code_tmp'");
                    if (!empty($symbol)) {
                        $wpdb->update($table_name, $data_val, array("ID" => $symbol));
                    } else {
                        $wpdb->insert($table_name, $data_val);
                    }
                    $symbol = $wpdb->get_var("SELECT id FROM $table_name WHERE exchange_code = '$valutes_code_ps' AND code = '$valutes_code_tmp'");
                    if (!empty($symbol)) {
                        $wpdb->update($table_name, $data_val_i, array("ID" => $symbol));
                    } else {
                        $wpdb->insert($table_name, $data_val_i);
                    }
                }
            }
        }
    }

    $table_name = $wpdb->prefix . "exchanger_cources";
    foreach ($newtable as $valute) {
        foreach ($res as $item) {
            if (!in_array($item->id, $valutes)) continue;
            $exchanger_valute = find_exchanger_valute($res,$valute->code);
            if(!$exchanger_valute) continue;
            $data = array(
                'time' => time(),
                'name' => $item->id,
                'code' => $item->symbol,
                'exchange_name' => $exchanger_valute->id,
                'exchange_code' => $exchanger_valute->symbol,
                'cource' => $item->price_usd/$exchanger_valute->price_usd,
            );

            $data_i = array(
                'time' => time(),
                'name' => $exchanger_valute->id,
                'code' => $exchanger_valute->symbol,
                'exchange_name' => $item->id,
                'exchange_code' => $item->symbol,
                'cource' => $exchanger_valute->price_usd/$item->price_usd,
            );

            $symbol = $wpdb->get_var("SELECT id FROM $table_name WHERE code = '$item->symbol' AND exchange_code = '$exchanger_valute->symbol'");
            if (!empty($symbol)) {
                $wpdb->update($table_name, $data, array("ID" => $symbol));
            } else {
                $wpdb->insert($table_name, $data);
            }
            $symbol = $wpdb->get_var("SELECT id FROM $table_name WHERE exchange_code = '$item->symbol' AND code = '$exchanger_valute->symbol'");
            if (!empty($symbol)) {
                $wpdb->update($table_name, $data_i, array("ID" => $symbol));
            } else {
                $wpdb->insert($table_name, $data_i);
            }
        }
    }

}

function find_exchanger_valute($res, $symbol){
    foreach ($res as $valute){
        if($valute->symbol == $symbol){
            return $valute;
        }
    }
    return false;
}
?>