<?php
function exchanger_options_form($valname, $symbol) {

    global $wpdb;
    $linkapi = 'https://api.coinmarketcap.com/v1/ticker/';

    $table_name = $wpdb->prefix . "exchanger_valutes";
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['post']) && $_GET['post'] > 0) {
        $wpdb->delete($table_name, array('ID' => $_GET['post']));
        ?>
        <script>window.location = '<?=admin_url('options-general.php?page=exchanger_options')?>'</script>
        <?php
    }
    $newtable = $wpdb->get_results( "SELECT * FROM $table_name" );
    ?>
    <style>
        div {
            margin-bottom:2px;
        }
        input{
            margin-bottom:4px;
        }
        table{
            margin-bottom: 30px;
        }
        table{
            text-align: center;
        }
        table td{
            border: 1px solid #0e0e0e;
            padding: 5px 20px;
        }
    </style>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <table>
            <tbody>
            <tr>
                <th><?= 'ID' ?></th>
                <th><?= _('Symbol') ?></th>
                <th><?= _('Image') ?></th>
                <th></th>
            </tr>
            </tbody>
            <?php
            foreach ($newtable as $item) {
                ?>
                <tr>
                    <td>
                        <input type="text" name="valnames[<?=$item->code?>]" value="<?=$item->name?>">
                    </td>
                    <td><?= $item->code ?></td>
                    <td>
                        <a href="<?= admin_url('options-general.php?page=exchanger_options&post=' . $item->id . '&action=delete') ?>">Удалить</a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>

        <div>
            <label for="valname"><?php echo _('Id valute'); ?> <strong>*</strong></label>
            <input type="text" id="valname" name="valname" value="<?php echo ( isset( $_POST['valname'] ) ? $_POST['valname'] : null ); ?>">
        </div>
        <div>
            <label for="symbol"><?php echo _('Symbol'); ?> <strong>*</strong></label>
            <input type="text" id="symbol" name="symbol" value="<?php echo ( isset( $_POST['symbol'] ) ? $_POST['symbol'] : null ); ?>">
        </div>
        <input type="submit" name="submit" value="save"/>
    </form>
    <div><a target="_blank" href="<?php echo $linkapi; ?>">View valutes of the API</a></div>
    <?php
}
function exchanger_options_validation($valname, $symbol, $valnames = array())
{
    global $reg_errors;
    $reg_errors = new WP_Error;


    if ((empty($valname) && !empty($symbol)) || (!empty($valname) && empty($symbol))) {
        $reg_errors->add('field', _('Required form field is missing'));
    }
    foreach ($valnames as $valnam) {
        if (empty($valnam)) {
            $reg_errors->add('field', _('Required form field is missing'));
        }
    }
    if ( is_wp_error( $reg_errors ) ) {

        foreach ( $reg_errors->get_error_messages() as $error ) {

            echo '<div>';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';
            echo '</div>';

        }

    }
}

function exchanger_options_complete() {
    global $reg_errors, $valname, $valnames, $symbol, $wpdb;

    if ( 1 > count($reg_errors->get_error_messages()) ) {
        $linkapi = 'https://api.coinmarketcap.com/v1/ticker/';
        $table_name = $wpdb->prefix . "exchanger_valutes";
        $table_name_reserv = $wpdb->prefix . "exchanger_valutes_reserv";
        $ids = $wpdb->get_var("SELECT id FROM $table_name WHERE name = '$valname' AND code = '$symbol'");
        if(empty($ids) && !empty($valname) && !empty($symbol)){
            $data=array(
                'time'          => time(),
                'name'          => $valname,
                'code'          => $symbol,
            );
            $wpdb->insert($table_name, $data);
            $wpdb->insert($table_name_reserv, $data);
            echo 'Валюта успешно сохранена.<br />';
        }else{
            echo "Валюта $valname уже сущевствует.<br />";
        }
        foreach ($valnames as $id => $valnam) {

            $data = array(
                'time'          => time(),
                'name'          => $valnam,
            );
            if(!empty($valnam)){
                $wpdb->update($table_name, $data, array('code' => $id));
                $wpdb->update($table_name_reserv, $data, array('code' => $id));
            }
            //echo "Валюта $valnam успешно обновлена.<br />";
        }
    }
}

function exchanger_options_function() {
    if ( isset($_POST['submit'] ) ) {
        exchanger_options_validation($_POST['valname'], $_POST['symbol'], $_POST['valnames']);
        global $valnames, $valname, $symbol;
        $valnames = $_POST['valnames'];
        $valname = $_POST['valname'];
        $symbol  = $_POST['symbol'];
        exchanger_options_complete();
    }
    exchanger_options_form($valname, $symbol);
}

function exchanger_options_page(){
    ob_start();
    exchanger_options_function();
    $out = ob_get_clean();
    echo $out;
}

function exchanger_koefficients_options_form() {

    global $wpdb;
    $table_name = $wpdb->prefix . "exchanger_cources";
    $newtable = $wpdb->get_results( "SELECT * FROM $table_name" );
    ?>
    <style>
        div {
            margin-bottom:2px;
        }

        input{
            margin-bottom:4px;
        }
        table{
            text-align: center;
        }
        table td{
            border: 1px solid #0e0e0e;
            padding: 5px 20px;
        }
        table td.cource{
            text-align: left;
        }
        table td input{
            width: 50px;
        }


    </style>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <table>
            <tbody>
            <tr>
                <!--<th><?/*='ID'*/?></th>-->
                <th><?=_('Valute')?></th>
                <th>&nbsp;</th>
                <th><?=_('Exchange to')?></th>
                <th><?=_('course')?></th>
                <th><?=_('Koefficient,%')?></th>
                <th><?=_('Minimal sum')?></th>
                <th><?=_('Message')?></th>
            </tr>
            </tbody>
            <?php
            foreach ($newtable as $item) {
                ?>
                <tr>
                    <!--<td><?/*=$item->name*/?></td>-->
                    <td><?=$item->code?></td>
                    <td>&nbsp;->&nbsp;</td>
                    <td><?=$item->exchange_code?></td>
                    <td class="cource"><?=$item->cource?></td>
                    <td>
                        <input type="text" id="valname_koef_<?=$item->id?>" name="valname[<?=$item->id?>][koef]" value="<?=$item->koefficient?>">
                    </td>
                    <td>
                        <input type="text" id="valname_minsum_<?=$item->id?>" name="valname[<?=$item->id?>][minsum]" value="<?=$item->minsum?>">
                    </td>
                    <td>
                        <textarea style="width: 300px;" id="valname_message_<?=$item->id?>" name="valname[<?=$item->id?>][message]"><?=$item->message?></textarea>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <input type="submit" name="submit" value="save"/>
    </form>
    <?php
}
function exchanger_koefficients_options_validation($valname)
{
    global $reg_errors;
    $reg_errors = new WP_Error;

    foreach ($valname as $item) {
        if (empty($item['koef']) && $item['koef'] != '0') {
            $reg_errors->add('field', _('Koefficient is required'));
            break;
        }
        if (empty($item['minsum']) && $item['minsum'] != '0') {
            $reg_errors->add('field', _('Minimal sum is required and must > 0'));
            break;
        }
    }

    if ( is_wp_error( $reg_errors ) ) {

        foreach ( $reg_errors->get_error_messages() as $error ) {

            echo '<div>';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';
            echo '</div>';

        }

    }
}

function exchanger_koefficients_options_complete() {
    global $reg_errors, $valname, $wpdb;

    if ( 1 > count($reg_errors->get_error_messages()) ) {
        $table_name = $wpdb->prefix . "exchanger_cources";
        foreach ($valname as $id => $value) {
            $data = array(
                'time' => time(),
                'koefficient' => $value['koef'],
                'minsum' => $value['minsum'],
                'message' => $value['message'],
            );
            $wpdb->update($table_name, $data, array('ID' => $id));
        }
        echo 'Valute koefficients&minsums save complete.';
    }
}

function exchanger_koefficients_options_function() {
    if ( isset($_POST['submit'] ) ) {
        exchanger_koefficients_options_validation($_POST['valname']);
        global $valname, $symbol;
        $valname = $_POST['valname'];
        exchanger_koefficients_options_complete();
    }
    exchanger_koefficients_options_form($valname, $symbol);
}

function exchanger_koefficients_options_page(){
    ob_start();
    exchanger_koefficients_options_function();
    $out = ob_get_clean();
    echo $out;
}

function exchanger_reserv_form() {

    global $wpdb, $config_valutes, $reservs;
    $reservs = array();
    $table_name = $wpdb->prefix . "exchanger_valutes";
    $newtable = $wpdb->get_results( "SELECT * FROM $table_name" );
    $table_reserv_name = $wpdb->prefix . "exchanger_valutes_reserv";
    $reservtable = $wpdb->get_results( "SELECT * FROM $table_reserv_name" );

    foreach ($config_valutes as $code => $reserv){
        $reservs[$code]['name'] = $reserv;
        $reservs[$code]['reserv'] = 0;
    }
    foreach ($newtable as $item){
        $reservs[$item->code]['reserv'] = 0;
        $reservs[$item->code]['name'] = $item->name;
    }
    foreach ($reservtable as $item){
        $reservs[$item->code]['reserv'] = $item->reserv;
        $reservs[$item->code]['name'] = $item->name;
        $reservs[$item->code]['icon'] = $item->valute_icon;
    }
    ?>
    <style>
        div {
            margin-bottom:2px;
        }

        input{
            margin-bottom:4px;
        }
        table{
            text-align: center;
        }
        table td{
            border: 1px solid #0e0e0e;
            padding: 5px 20px;
        }
        table td.cource{
            text-align: left;
        }



    </style>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <table>
            <tbody>
            <tr>
                <th><?=_('Valute')?></th>
                <th><?=_('Reserv')?></th>
            </tr>
            </tbody>
            <?php
            foreach ($reservs as $code => $reserv) {
                ?>
                <tr>
                    <td>
                        <?=$code?>
                        <input type="hidden" id="reserv_name_<?=$code?>" name="reserv[<?=$code?>][name]" value="<?=$reserv['name']?>">
                    </td>
                    <td>
                        <input type="text" id="reserv_<?=$code?>" name="reserv[<?=$code?>][reserv]" value="<?=$reserv['reserv']?>">
                    </td>
                    <td>
                        <div class="form-group smartcat-uploader">
                            <?php
                            if(!empty($reserv['icon'])){
                                ?>
                                <div>
                                    <br>
                                    <img src="<?=$reserv['icon']?>" style="width: 50px;"/>
                                </div>
                                <?php
                            }
                            ?>
                            <input type="text" name="reserv[<?=$code?>][icon]" value="<?=$reserv['icon']?>">
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <input type="submit" name="submit" value="save"/>
    </form>
    <script>
        $.wpMediaUploader({

            target : '.smartcat-uploader', // The class wrapping the textbox
            uploaderTitle : 'Выбрать или загрузить картинку', // The title of the media upload popup
            uploaderButton : 'Выбрать', // the text of the button in the media upload popup
            multiple : false, // Allow the user to select multiple images
            buttonText : 'Загрузить иконку', // The text of the upload button
            buttonClass : '.smartcat-upload', // the class of the upload button
            previewSize : '50px', // The preview image size
            modal : false, // is the upload button within a bootstrap modal ?
            buttonStyle : { // style the button
                color : '#fff',
                background : '#3bafda',
                fontSize : '10px',
                padding : '5px 8px',
            },

        });
    </script>
    <?php
}
function exchanger_reserv_validation($reserv)
{
    global $reg_errors;
    $reg_errors = new WP_Error;

    foreach ($reserv as $code => $item) {
        if (empty($item['reserv']) && $item['reserv'] != '0') {
            $reg_errors->add('field', _('Please enter reserv for ' . $code));
            break;
        }
    }

    if ( is_wp_error( $reg_errors ) ) {

        foreach ( $reg_errors->get_error_messages() as $error ) {

            echo '<div>';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';
            echo '</div>';

        }

    }
}

function exchanger_reserv_complete() {
    global $reg_errors, $reserv, $wpdb;

    if ( 1 > count($reg_errors->get_error_messages()) ) {
        $table_name = $wpdb->prefix . "exchanger_valutes_reserv";
        foreach ($reserv as $code => $value) {
            $data = array(
                'time'   => time(),
                'reserv' => $value['reserv'],
                'valute_icon'   => $value['icon'],
            );
            $ids = $wpdb->get_var("SELECT id FROM $table_name WHERE code = '$code'");
            if(empty($ids)){
                $data['name'] = $value['name'];
                $data['code'] = $code;
                $wpdb->insert($table_name, $data);
            }else{
                $wpdb->update($table_name, $data, array('CODE' => $code));
            }
        }
        echo 'Valutes reservs save complete.';
    }
}

function exchanger_reserv_function() {
    if ( isset($_POST['submit'] ) ) {
        exchanger_reserv_validation($_POST['reserv']);
        global $reserv;
        $reserv = $_POST['reserv'];
        exchanger_reserv_complete();
    }
    exchanger_reserv_form($reserv);
}

function exchanger_reserv_page(){
    ob_start();
    exchanger_reserv_function();
    $out = ob_get_clean();
    echo $out;
}

function exchanger_orders_list()
{

    global $wpdb, $config_valutes;

    $table_name = $wpdb->prefix . "exchanger_orders";
    $order_statuses = array('pending', 'paychecked', 'payed', 'complete', 'cancel');
    $order_statuses_ = array('Ожидание оплаты', 'Оплата проверяется', 'Оплата получена', 'Деньги отправлены', 'Оплата отменена');

    if (isset($_POST['submit'])) {

        $data = array('status' => $_POST['status']);

        $order = $wpdb->get_row("SELECT * FROM $table_name WHERE id = '" . $_POST['post'] . "'");
        if (!empty($order)) {
            if($_POST['status'] != $order->status){
                if($order_statuses[$_POST['status']] == 'cancel' || $order_statuses[$_POST['status']] == 'complete') {
                    $id = $order->id;
                    $n = strlen($id);
                    for ($i = 0; $i < (9 - $n); $i++) {
                        $id = '0' . $id;
                    }
                    $order_data = unserialize($order->order_data);
                    $mail = $order_data['email'];
                    $message = '';
                    if ($order_statuses[$_POST['status']] == 'cancel') {
                        $message = '<p>К сожалению Ваша заявка № ' . $id . ' была отменена. </p>';
                    } elseif ($order_statuses[$_POST['status']] == 'complete') {
                        $message = '<p>Проведена оплата по Вашей заявке № ' . $id . '.</p>';
                    }

                    add_filter('wp_mail_content_type', 'set_html_content_type');

                    wp_mail($mail, 'Есть изменения по заявке на обмен', "<p>Проведена оплата заявки на обмен № $code. <a href='" . admin_url('options-general.php?page=exchanger_orders' . '&post=' . $id . '&action=edit') . "'>Просмотреть детально</a></p>");

                    remove_filter('wp_mail_content_type', 'set_html_content_type');
                }

            }
            $wpdb->update($table_name, $data, array("ID" => $order->id));
        }
    }

    $valutes = array();
    $table_valutes_name = $wpdb->prefix . "exchanger_valutes";
    $newtable = $wpdb->get_results("SELECT * FROM $table_valutes_name");
    $nal = array_keys($config_valutes);
    $crypto = $v_nal = array();
    foreach ($newtable as $item) {
        foreach ($nal as $val) {
            if (strpos($item->code, $val . '_') !== false) {
                $v_nal[] = $item->code;
            }
        }
        $crypto[] = $item->code;
    }
    $crypto = array_diff($crypto, $v_nal);
    foreach ($newtable as $item) {
        $valutes[$item->code] = $item->name;
    }
    $valutes = array_merge($valutes, $config_valutes);

    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['post']) && $_GET['post'] > 0) {
        $ordertable = $wpdb->get_results("SELECT * FROM $table_name WHERE id=" . $_GET['post']);
    } else {

        $ordertable = $wpdb->get_results("SELECT * FROM $table_name");
    }
        ?>
        <style>
            div {
                margin-bottom: 2px;
            }

            input {
                margin-bottom: 4px;
            }

            table {
                text-align: center;
            }

            table td {
                border: 1px solid #0e0e0e;
                padding: 5px 20px;
            }

            table td div {
                text-align: left;
            }

            table td.cource {
                text-align: left;
            }

            table td input {
                width: 50px;
            }
        </style>

        <?php
            if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['post']) && $_GET['post'] > 0) {
                $url = explode('?',$_SERVER['REQUEST_URI']);
                $url = $url[0].'?page='.$_GET['page'];
            ?>
        <form action="<?php echo $url; ?>" method="post">
        <?php
        }
        ?>

        <table>
            <thead>
            <tr>
                <th>Заявка №</th>
                <th>Статус заявки</th>
                <th>Отдают</th>
                <th>Получают</th>
                <th>Информация о клиенте</th>
                <th>Курс обмена</th>
                <?php
                if (!isset($_GET['action']) || !$_GET['action'] == 'edit' || !isset($_GET['post']) || !$_GET['post'] > 0){
                ?>
                    <th></th>
                <?php
                }
                ?>
            </tr>
            </thead>
            <?php
            foreach ($ordertable as $item) {
                $id = $item->id;
                $n = strlen($id);
                for ($i = 0; $i < (9 - $n); $i++) {
                    $id = '0' . $id;
                }
                $order_data = unserialize($item->order_data);
                ?>
                <tr>
                    <td>
                        <?= $id ?>
                    </td>
                    <td>
                        <?php
                            if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['post']) && $_GET['post'] > 0) {
                                ?>
                                <select name="status">
                                    <?php
                                    foreach ($order_statuses_ as $key => $order_status) {
                                        ?>
                                        <option<?php if ($key == $item->status) echo ' selected="selected"'; ?>
                                                value="<?= $key ?>"><?= $order_status ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            }else{
                                echo _($order_statuses_[$item->status]);
                            }
                        ?>
                    </td>
                    <td>
                        <div class="h4"><?= ucfirst($valutes[$order_data['forvalute']]) ?></div>
                        <div class="row"><strong>Сумма:</strong><?= $order_data['sumfor'] ?></div>
                        <?php
                        if (in_array($order_data['forvalute'], $crypto)) {
                            ?>
                            <div class="row"><strong>Кошелек:</strong><?= $order_data['pursefor'] ?></div>
                        <?php } ?>
                        <?php
                        if (in_array($order_data['forvalute'], $nal)) {
                            ?>
                            <div class="row"><strong>Город:</strong><?= $order_data['cityfor'] ?></div>
                        <?php } ?>
                        <?php
                        if (in_array($order_data['forvalute'], $v_nal)) {
                            ?>
                            <div class="row"><strong>Город:</strong><?= $order_data['cardfor'] ?></div>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="h3">Получаете</div>
                        <div class="h4"><?= ucfirst($valutes[$order_data['tovalute']]) ?></div>
                        <div class="row"><strong>Сумма:</strong><?= $order_data['sumto'] ?></div>
                        <?php
                        if (in_array($order_data['tovalute'], $crypto)) {
                            ?>
                            <div class="row"><strong>Кошелек:</strong><?= $order_data['purseto'] ?></div>
                        <?php } ?>
                        <?php
                        if (in_array($order_data['tovalute'], $nal)) {
                            ?>
                            <div class="row"><strong>Город:</strong><?= $order_data['cityto'] ?></div>
                        <?php } ?>
                        <?php
                        if (in_array($order_data['tovalute'], $v_nal)) {
                            ?>
                            <div class="row"><strong>Город:</strong><?= $order_data['cardto'] ?></div>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="row"><strong>Имя:</strong><?= $order_data['firstname'] ?></div>

                        <?php if (!empty($order_data['lastname'])) { ?>
                            <div class="row"><strong>Фамилия:</strong><?= $order_data['purseto'] ?></div>
                        <?php } ?>
                        <?php if (!empty($order_data['phone'])) { ?>
                            <div class="row"><strong>Номер телефона:</strong><?= $order_data['phone'] ?></div>
                        <?php } ?>

                        <div class="row"><strong>Email:</strong><?= $order_data['email'] ?></div>

                    </td>
                    <td><?= $order_data['course'] ?></td>
                    <?php
                    if (!isset($_GET['action']) || !$_GET['action'] == 'edit' || !isset($_GET['post']) || !$_GET['post'] > 0){
                    ?>
                    <td>
                        <a href="<?= $_SERVER['REQUEST_URI'] . '&post=' . $item->id . '&action=edit' ?>">Редактировать</a>
                    </td>
                    <?php
                        }
                    ?>
                </tr>
                <?php
            }
            ?>
        </table>
    <?php
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['post']) && $_GET['post'] > 0) {
    ?>
            <input type="hidden" name="post" value="<?=$_GET['post']?>">
            <input type="submit" name="submit" value="Сохранить"/>
        </form>
        <?php
    }
}
