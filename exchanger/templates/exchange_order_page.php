<?php
global $wpdb, $config_valutes, $reservs;
$code = !empty($_GET['ordn'])?$_GET['ordn']:'0';
if (isset($_POST['submit'])) {

    $table_name = $wpdb->prefix . "exchanger_orders";
    $data = array('status' => 1);

    $order = $wpdb->get_var("SELECT id FROM $table_name WHERE order_num = '$code'");
    if (!empty($order)) {
        $wpdb->update($table_name, $data, array("ID" => $order));
    }
}

$reservs = array();
$table_name = $wpdb->prefix . "exchanger_orders";
$ordertable = $wpdb->get_results( "SELECT * FROM $table_name WHERE order_num = '$code'" );
$order_statuses = array('pending', 'payed', 'complete', 'cancel');
$order_statuses_ = array('Ожидание оплаты', 'Оплачено', 'Завершено', 'Отменено');
$status = 0;
$valutes = array();
$table_valutes_name = $wpdb->prefix . "exchanger_valutes";
$newtable = $wpdb->get_results("SELECT * FROM $table_valutes_name");
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
foreach ($newtable as $item) {
    $valutes[$item->code] = $item->name;
}
$valutes = array_merge($valutes,$config_valutes);

?>
<div class="order-info">
    <?php
    foreach ($ordertable as $item) {
        $status = $item->status;
        $id = $item->id;
        $n = strlen($id);
        for($i=0;$i < (9-$n);$i++){
            $id = '0' . $id;
        }
        $order_data = unserialize($item->order_data);
        ?>
        <div class="h1">Заявка на обмен №<?=$id?></div>
        <div class="h2"><span class="order-status-icon<?=' '.$order_statuses[$item->status]?>"></span>Статус заявки: <?=_($order_statuses_[$item->status])?></div>
        <div class="mk-col-4-12">
            <div class="h3">Отдаете</div>
            <div class="h4"><?=ucfirst($valutes[$order_data['forvalute']])?></div>
            <div class="row"><strong>Сумма:</strong><?=$order_data['sumfor']?></div>
            <?php
            if(in_array($order_data['forvalute'],$crypto)){
            ?>
            <div class="row"><strong>Кошелек:</strong><?=$order_data['pursefor']?></div>
            <?php } ?>
            <?php
            if(in_array($order_data['forvalute'],$nal)){
            ?>
            <div class="row"><strong>Город:</strong><?=$order_data['cityfor']?></div>
            <?php } ?>
            <?php
            if(in_array($order_data['forvalute'],$v_nal)){
            ?>
            <div class="row"><strong>Город:</strong><?=$order_data['cardfor']?></div>
            <?php } ?>
        </div>
        <div class="mk-col-4-12">
            <div class="h3">Получаете</div>
            <div class="h4"><?=ucfirst($valutes[$order_data['tovalute']])?></div>
            <div class="row"><strong>Сумма:</strong><?=$order_data['sumto']?></div>
            <?php
            if(in_array($order_data['tovalute'],$crypto)){
            ?>
            <div class="row"><strong>Кошелек:</strong><?=$order_data['purseto']?></div>
            <?php } ?>
            <?php
            if(in_array($order_data['tovalute'],$nal)){
            ?>
            <div class="row"><strong>Город:</strong><?=$order_data['cityto']?></div>
            <?php } ?>
            <?php
            if(in_array($order_data['tovalute'],$v_nal)){
            ?>
            <div class="row"><strong>Город:</strong><?=$order_data['cardto']?></div>
            <?php } ?>
        </div>
        <div class="mk-col-4-12">
            <div class="h3">Личные данные:</div>
            <div class="row"><strong>Имя:</strong><?=$order_data['firstname']?></div>
        <div class="h2">Статус заявки: <strong><?=_($order_statuses[$item->status])?></strong></div>
		<div class="order-data">
			<div class="mk-col-4-12 data-col">
				<div class="col-head">Отдаете</div>
				<div class="col-cont">
					<div class="row val"><strong>Валюта:</strong> <?=ucfirst($valutes[$order_data['forvalute']])?></div>
					<div class="row"><strong>Сумма:</strong> <?=$order_data['sumfor']?></div>
					<?php
					if(in_array($order_data['forvalute'],$crypto)){
					?>
					<div class="row"><strong>Кошелек:</strong> <?=$order_data['pursefor']?></div>
					<?php } ?>
					<?php
					if(in_array($order_data['forvalute'],$nal)){
					?>
					<div class="row"><strong>Город:</strong> <?=$order_data['cityfor']?></div>
					<?php } ?>
					<?php
					if(in_array($order_data['forvalute'],$v_nal)){
					?>
					<div class="row"><strong>Город:</strong> <?=$order_data['cardfor']?></div>
					<?php } ?>
				</div>
			</div>
			<div class="mk-col-4-12 data-col">
				<div class="col-head">Получаете</div>
				<div class="col-cont">
					<div class="row val"><strong>Валюта:</strong> <?=ucfirst($valutes[$order_data['tovalute']])?></div>
					<div class="row"><strong>Сумма:</strong> <?=$order_data['sumto']?></div>
					<?php
					if(in_array($order_data['tovalute'],$crypto)){
					?>
					<div class="row"><strong>Кошелек:</strong> <?=$order_data['purseto']?></div>
					<?php } ?>
					<?php
					if(in_array($order_data['tovalute'],$nal)){
					?>
					<div class="row"><strong>Город:</strong> <?=$order_data['cityto']?></div>
					<?php } ?>
					<?php
					if(in_array($order_data['tovalute'],$v_nal)){
					?>
					<div class="row"><strong>Город:</strong> <?=$order_data['cardto']?></div>
					<?php } ?>
					<div class="row course"><strong>Курс на момент создания заявки:</strong> <?=$order_data['course']?></div>
				</div>
			</div>
			<div class="mk-col-4-12 data-col">
				<div class="col-head">Личные данные:</div>
				<div class="col-cont">
					<div class="row"><strong>Имя:</strong> <?=$order_data['firstname']?></div>
					<?php if(!empty($order_data['lastname'])){ ?>
					<div class="row"><strong>Фамилия:</strong> <?=$order_data['purseto']?></div>
					<?php } ?>
					<?php if(!empty($order_data['phone'])){ ?>
					<div class="row"><strong>Номер телефона:</strong> <?=$order_data['phone']?></div>
					<?php } ?>

					<div class="row"><strong>Email:</strong> <?=$order_data['email']?></div>
				</div>
			</div>
		</div>
        <?php
    }
    ?>

</div>
<?php if(!$status) { ?>
    <div class="info-steps">
        <div class="steps-header"><h2>ПОРЯДОК ДЕЙСТВИЙ ДЛЯ СОВЕРШЕНИЯ ОБМЕНА:</h2></div>
        <div class="steps-cont">
			<div class="mk-col-4-12 steps-col col1">
				<div class="col-cont">
					<div class="info-img"></div>
					<p>Совершите платеж на сумму 0.05 BTC на счёт 12N7QXnVhuucSTcu4kEstvpJCxetY5wh6V</p>
				</div>
			</div>
			<div class="mk-col-4-12 steps-col col2">
				<div class="col-cont">
					<div class="info-img"></div>
					<p>После оплаты кликните по кнопке «Я ОПЛАТИЛ», чтобы мы получили уведомление и проверили поступление средств.</p>
				</div>
			</div>
			<div class="mk-col-4-12 steps-col col2">
				<div class="col-cont">
					<div class="info-img"></div>
					<p>После получения 1 подтверждения о переводе средств от Bitcoin перевод занимает от 2 до 30 минут.</p>
				</div>
			</div>
			<div class="clearfix"></div>
        </div>
    </div>
	
	<div class="notice-block">
		<div class="notice-wrap">
			<p>ВАЖНО!!! В целях максимально быстрой совершённой сделки настоятельно просим Вас указывать рекомендуемую комиссию в системе биткоин! В противном случае сделка по обмену может затянуться на очень длительный срок до 7 дней или же будет вообще отменена, а Ваш IP может автоматически оказаться в блэк-листе нашего сервиса! Давайте ценить Ваше и наше время и не тратить из-за этого нервы)</p>
		</div>
	</div>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="submit" name="submit" value="<?= _('Я оплатил') ?>"/>
    </form>

    <?php
}
if(isset($_POST['submit']) && $status) {

    $admin_email = get_option('admin_email');

    add_filter('wp_mail_content_type', 'set_html_content_type');

    wp_mail($admin_email, 'Новая заявка', "<p>Проведена оплата заявки на обмен № $id. <a href='" . admin_url('options-general.php?page=exchanger_orders' . '&post=' . $id . '&action=edit') . "'>Просмотреть детально</a></p>");

    remove_filter('wp_mail_content_type', 'set_html_content_type');

    function set_html_content_type()
    {
        return 'text/html';
    }
}
?>
