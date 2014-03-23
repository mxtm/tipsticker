<?php
function difficulty() {
	if (apc_exists('difficulty')) {
    	$difficulty = apc_fetch('difficulty');
	} else {
    	$json = file_get_contents('http://chickenstrips.net/index.php?page=api&action=getdifficulty&api_key=38f2a4cb74f3e4cad68e40440c325b82ab01334b6de56c29749ad61534e5726e');
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

function bterltcsatprice() {
	if (apc_exists('bterltcsatprice')) {
		$bterltcsatprice = apc_fetch('bterltcsatprice');
	} else {
		$rawdata = file_get_contents("http://data.bter.com/api/1/ticker/tips_ltc");

		$json = json_decode($rawdata);

		$bterltcsatprice = $json->last / 0.00000001;

		apc_store('bterltcsatprice', $bterltcsatprice, 20);
	}

	return $bterltcsatprice;
}

function avgltcsatprice() {
	if (apc_exists('avgltcsatprice')) {
		$avgltcsatprice = apc_fetch('avgltcsatprice');
	} else {
		if (cryptsyltcsatprice() == 0) { $avgltcsatprice = bterltcsatprice(); }
		elseif (bterltcsatprice() == 0) { $avgltcsatprice = cryptsyltcsatprice(); }
		else { $avgltcsatprice = round((cryptsyltcsatprice() + bterltcsatprice()) / 2); }

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
