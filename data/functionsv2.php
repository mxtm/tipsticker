<?php
function difficulty() {
	if (apc_exists('difficulty')) {
    	$difficulty = apc_fetch('difficulty');
	} else {
    	$json = file_get_contents('http://chickenstrips.net/index.php?page=api&action=getdifficulty&api_key=');
		$json = json_decode($json);

		$difficulty = $json->getdifficulty->data;

    	//store for 2 mins
    	apc_store('difficulty', $difficulty, 120);
	}

	return $difficulty;
}

function ltcusdprice() {
	if (apc_exists('ltcusdprice')) {
		$ltcusdprice = apc_fetch('ltcusdprice');
	} else {
		$json = file_get_contents('https://btc-e.com/api/2/ltc_usd/ticker');  
		$json = json_decode($json);
	  
		$ltcusdprice = $json->ticker->avg;

		//store for 3 mins
		apc_store('ltcusdprice', $ltcusdprice, 180);
	}

	return $ltcusdprice;
}

function cryptsyltcsatprice() {
	if (apc_exists('cryptsyltcsatprice')) {
		$cryptsyltcsatprice = apc_fetch('cryptsyltcsatprice');
	} else {
		$rawdata = file_get_contents("http://pubapi.cryptsy.com/api.php?method=singlemarketdata&marketid=147");

		$json = json_decode($rawdata);

		$cryptsyltcsatprice = $json->return->markets->TIPS->lasttradeprice / 0.00000001;

		apc_store('cryptsyltcsatprice', $cryptsyltcsatprice, 20);
	}

	return $cryptsyltcsatprice;
}

function coinedupltcsatprice() {
	if (apc_exists('coinedupltcsatprice')) {
		$coinedupltcsatprice = apc_fetch('coinedupltcsatprice');
	} else {
		$apikey = "";
		$apisecret = "";

		$params = array();  
		$params['requestKey'] = (string)microtime();
		$params['market'] = "TIPS_LTC";
		$params['fromTime'] = time() - (1 * 24 * 60 * 60);
									  
		$postdata = http_build_query($params, '', '&');
		$sign = hash_hmac("sha512", $postdata, $apisecret);
		$headers = array('Sign: ' . $sign, 'Key: ' . $apikey);

		static $ch = null;

		if (is_null($ch)) {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 ('. php_uname('s') . '; PHP/' . phpversion() . ')');
		}

		curl_setopt($ch, CURLOPT_URL, "https://api.coinedup.com/trades");

		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$json = curl_exec($ch);

		$json = json_decode($json, true);

		$trade = end($json['value']);

		$coinedupltcsatprice = $trade['rate'] / 0.00000001;

		apc_store('coinedupltcsatprice', $coinedupltcsatprice, 20);
	}

	return $coinedupltcsatprice;
}

function avgltcsatprice() {
	if (apc_exists('avgltcsatprice')) {
		$avgltcsatprice = apc_fetch('avgltcsatprice');
	} else {
		if (cryptsyltcsatprice() == 0) { $avgltcsatprice = coinedupltcsatprice(); }
		elseif (coinedupltcsatprice() == 0) { $avgltcsatprice = cryptsyltcsatprice(); }
		else { $avgltcsatprice = round((cryptsyltcsatprice() + coinedupltcsatprice()) / 2); }

		//store for 20 seconds
		apc_store('avgltcsatprice', $avgltcsatprice, 20);
	}

	return $avgltcsatprice;
}

function tipsusdprice() {
	if (apc_exists('tipsusdprice')) {
		$tipsusdprice = apc_fetch('tipsusdprice');
	} else {
		$tipsltcprice = avgltcsatprice() * 0.00000001;
		$tipsusdprice = ltcusdprice() * $tipsltcprice;

		// store for 20 seconds
		apc_store('tipsusdprice', $tipsusdprice, 20);
	}

	return $tipsusdprice;
}
?>
