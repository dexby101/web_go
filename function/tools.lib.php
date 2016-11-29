<?php
/.
* Pack-tools - "Network"
* @version: 1.0-alpha,
* @author: Bogdan Karpov,
* @email: php_master@mail.ua,
* @date: 29.11.2016 14:47
*/

function ping($url, $len = 4){
	$open = new url($url);
	echo "Пінг сайту : {$url} \n";
	$r = array(
		'user_id' => 210700286,
		'fields' => 'bdate',
		'v' => '5.52'
	);
	$ser_tm = 0;
	for($i = 0 ; $i < $len ; $i++){
		if ( !empty($res = $open->post($r)) ){
			//if ($i == 1) print_r($open->info);
			$tm = $open->info['total_time'];
			$bite =  $open->info['upload_content_length'];
			$bite2 =  $open->info['download_content_length'];
			$ser_tm += $tm;
			echo "Відправлено ({$bite} байт) та отримано пакет ({$bite2} байт) , час = {$tm} сек. \n";
			$open->info = 0;
		}else{
			echo "Запит не вдався! \n";
		}
	}
	$ser_tm /= $len;
	echo "Середній час {$ser_tm} сек.\n";
}
