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

		//store for 5 mins
		apc_store('ltcusdprice', $ltcusdprice, 300);
	}

	return $ltcusdprice;
}

function tipsltcsatprice() {
	if (apc_exists('tipsltcsatprice')) {
		$tipsltcsatprice = apc_fetch('tipsltcsatprice');
	} else {
		$cryptsyrawdata = file_get_contents("http://pubapi.cryptsy.com/api.php?method=singlemarketdata&marketid=147");

		$cryptsyjson = json_decode($cryptsyrawdata);

		$cryptsytipsltcsatprice = $cryptsyjson->return->markets->TIPS->lasttradeprice / 0.00000001;

		/* $coinedupapikey = "";
		$coinedupapisecret = "";

		$coinedupparams = array();  
		$coinedupparams['requestKey'] = (string)microtime();
		$coinedupparams['market'] = "TIPS_LTC";
		$coinedupparams['fromTime'] = time() - (1 * 24 * 60 * 60);
									  
		$coineduppostdata = http_build_query($coinedupparams, '', '&');
		$coinedupsign = hash_hmac("sha512", $coineduppostdata, $coinedupapisecret);
		$coinedupheaders = array('Sign: ' . $coinedupsign, 'Key: ' . $coinedupapikey);

		static $ch = null;

		if (is_null($ch)) {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 ('. php_uname('s') . '; PHP/' . phpversion() . ')');
		}

		curl_setopt($ch, CURLOPT_URL, "https://api.coinedup.com/trades");

		curl_setopt($ch, CURLOPT_POSTFIELDS, $coineduppostdata);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $coinedupheaders);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$coinedupresult = curl_exec($ch);

		$coinedupjson = json_decode($coinedupresult, true);

		$coineduptrade = end($coinedupjson['value']);

		$coineduptipsltcsatprice = $coineduptrade['rate'] / 0.00000001;

		if ($cryptsytipsltcsatprice == 0) { $tipsltcsatprice = $coineduptipsltcsatprice; }
		elseif ($coineduptipsltcsatprice == 0) { $tipsltcsatprice = $cryptsytipsltcsatprice;  }
		else { $tipsltcsatprice = round(($cryptsytipsltcsatprice + $coineduptipsltcsatprice) / 2); } */

		$tipsltcsatprice = $cryptsytipsltcsatprice;

		//store for 20 seconds
		apc_store('tipsltcsatprice', $tipsltcsatprice, 20);
	}

	return $tipsltcsatprice;
}

function tipsusdprice() {
	if (apc_exists('tipsusdprice')) {
		$tipsusdprice = apc_fetch('tipsusdprice');
	} else {
		$tipsltcprice = tipsltcsatprice() * 0.00000001;
		$tipsusdprice = ltcusdprice() * $tipsltcprice;

		// store for 30 seconds
		apc_store('tipsusdprice', $tipsusdprice, 20);
	}

	return $tipsusdprice;
}
?>
