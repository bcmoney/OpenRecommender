<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Currency Converter with Google Currency API</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	
	$('#convert').click(function(){
	 
	 //Get all the values
	 var amount = $('#amount').val();
	 var from = $('#fromCurrency').val();
	 var to = $('#toCurrency').val();
	 
	 //Make data string
	 var dataString = "amount=" + amount + "&from=" + from + "&to=" + to;
	 
		 $.ajax({
		   type: "POST",
		   url: "GoogleCurrency.class.php",
		   data: dataString,
		   success: function(data){
			 //Show results div
			 $('#results').show();
			
			//Put received response into result div
			 $('#results').html(data);
		   }
		 });
	});
});
</script>
<style type="text/css">
body {font-family:Arial, Helvetica, sans-serif;font-size:12px;}

#heading, #currencyBox, #results {width:775px;margin:0 auto;}

#heading {font-weight:bold;font-size:28px;padding-bottom:10px;color:#333;}

#currencyBox {-moz-border-radius:5px;border-radius:5px;background:#999;overflow:hidden;padding:10px;}

label {display:block;padding-bottom:2px;font-weight:bold;}

.data {float:left;width:auto;padding:0 5px;}

input[type=button] {margin-top:15px;cursor:pointer;background:#333333;color:#ffffff;border:none;padding:2px 5px;-moz-border-radius:5px;border-radius:5px;}

#results {display:none;font-weight:bold;font-size:24px;}
#results {-moz-border-radius:5px;border-radius:5px;background:#999999;overflow:hidden;padding:10px;text-align:center;margin-top:10px;}
</style>
</head>

<body>
<div id="heading">Currency Converter</div>
<div id="currencyBox">
    <div class="data">
        <label for="amount">Convert this amount:</label>
        <input type="text" name="amount" id="amount" value="1" />
    </div>
    
    <div class="data">
        <label for="fromCurrency">From this currency:</label>
        <select name="fromCurrency" id="fromCurrency">
          <option selected="" value="EUR">Euro - EUR</option>
          <option value="USD">United States Dollars - USD</option>
          <option value="GBP">United Kingdom Pounds - GBP</option>
          <option value="CAD">Canada Dollars - CAD</option>
          <option value="AUD">Australia Dollars - AUD</option>
          <option value="JPY">Japan Yen - JPY</option>
          <option value="INR">India Rupees - INR</option>
          <option value="NZD">New Zealand Dollars - NZD</option>
          <option value="CHF">Switzerland Francs - CHF</option>
          <option value="ZAR">South Africa Rand - ZAR</option>
          <option value="DZD">Algeria Dinars - DZD</option>
          <option value="USD">America (United States) Dollars - USD</option>
          <option value="ARS">Argentina Pesos - ARS</option>
          <option value="AUD">Australia Dollars - AUD</option>
          <option value="BHD">Bahrain Dinars - BHD</option>
          <option value="BRL">Brazil Reais - BRL</option>
          <option value="BGN">Bulgaria Leva - BGN</option>
          <option value="CAD">Canada Dollars - CAD</option>
          <option value="CLP">Chile Pesos - CLP</option>
          <option value="CNY">China Yuan Renminbi - CNY</option>
          <option value="CNY">RMB (China Yuan Renminbi) - CNY</option>
          <option value="COP">Colombia Pesos - COP</option>
          <option value="CRC">Costa Rica Colones - CRC</option>
          <option value="HRK">Croatia Kuna - HRK</option>
          <option value="CZK">Czech Republic Koruny - CZK</option>
          <option value="DKK">Denmark Kroner - DKK</option>
          <option value="DOP">Dominican Republic Pesos - DOP</option>
          <option value="EGP">Egypt Pounds - EGP</option>
          <option value="EEK">Estonia Krooni - EEK</option>
          <option value="EUR">Euro - EUR</option>
          <option value="FJD">Fiji Dollars - FJD</option>
          <option value="HKD">Hong Kong Dollars - HKD</option>
          <option value="HUF">Hungary Forint - HUF</option>
          <option value="ISK">Iceland Kronur - ISK</option>
          <option value="INR">India Rupees - INR</option>
          <option value="IDR">Indonesia Rupiahs - IDR</option>
          <option value="ILS">Israel New Shekels - ILS</option>
          <option value="JMD">Jamaica Dollars - JMD</option>
          <option value="JPY">Japan Yen - JPY</option>
          <option value="JOD">Jordan Dinars - JOD</option>
          <option value="KES">Kenya Shillings - KES</option>
          <option value="KRW">Korea (South) Won - KRW</option>
          <option value="KWD">Kuwait Dinars - KWD</option>
          <option value="LBP">Lebanon Pounds - LBP</option>
          <option value="MYR">Malaysia Ringgits - MYR</option>
          <option value="MUR">Mauritius Rupees - MUR</option>
          <option value="MXN">Mexico Pesos - MXN</option>
          <option value="MAD">Morocco Dirhams - MAD</option>
          <option value="NZD">New Zealand Dollars - NZD</option>
          <option value="NOK">Norway Kroner - NOK</option>
          <option value="OMR">Oman Rials - OMR</option>
          <option value="PKR">Pakistan Rupees - PKR</option>
          <option value="PEN">Peru Nuevos Soles - PEN</option>
          <option value="PHP">Philippines Pesos - PHP</option>
          <option value="PLN">Poland Zlotych - PLN</option>
          <option value="QAR">Qatar Riyals - QAR</option>
          <option value="RON">Romania New Lei - RON</option>
          <option value="RUB">Russia Rubles - RUB</option>
          <option value="SAR">Saudi Arabia Riyals - SAR</option>
          <option value="SGD">Singapore Dollars - SGD</option>
          <option value="SKK">Slovakia Koruny - SKK</option>
          <option value="ZAR">South Africa Rand - ZAR</option>
          <option value="KRW">South Korea Won - KRW</option>
          <option value="LKR">Sri Lanka Rupees - LKR</option>
          <option value="SEK">Sweden Kronor - SEK</option>
          <option value="CHF">Switzerland Francs - CHF</option>
          <option value="TWD">Taiwan New Dollars - TWD</option>
          <option value="THB">Thailand Baht - THB</option>
          <option value="TTD">Trinidad and Tobago Dollars - TTD</option>
          <option value="TND">Tunisia Dinars - TND</option>
          <option value="TRY">Turkey Lira - TRY</option>
          <option value="AED">United Arab Emirates Dirhams - AED</option>
          <option value="GBP">United Kingdom Pounds - GBP</option>
          <option value="USD">United States Dollars - USD</option>
          <option value="VEB">Venezuela Bolivares - VEB</option>
          <option value="VND">Vietnam Dong - VND</option>
          <option value="ZMK">Zambia Kwacha - ZMK</option>
        </select>
    </div>
    
    <div class="data">
        <label for="toCurrency">To this currency:</label>
        <select name="toCurrency" id="toCurrency">
          <option value="USD">United States Dollars - USD</option>
          <option value="GBP">United Kingdom Pounds - GBP</option>
          <option value="CAD">Canada Dollars - CAD</option>
          <option value="AUD">Australia Dollars - AUD</option>
          <option value="JPY">Japan Yen - JPY</option>
          <option value="INR">India Rupees - INR</option>
          <option value="NZD">New Zealand Dollars - NZD</option>
          <option value="CHF">Switzerland Francs - CHF</option>
          <option value="ZAR">South Africa Rand - ZAR</option>
          <option value="DZD">Algeria Dinars - DZD</option>
          <option value="USD">America (United States) Dollars - USD</option>
          <option value="ARS">Argentina Pesos - ARS</option>
          <option value="AUD">Australia Dollars - AUD</option>
          <option value="BHD">Bahrain Dinars - BHD</option>
          <option value="BRL">Brazil Reais - BRL</option>
          <option value="BGN">Bulgaria Leva - BGN</option>
          <option value="CAD">Canada Dollars - CAD</option>
          <option value="CLP">Chile Pesos - CLP</option>
          <option value="CNY">China Yuan Renminbi - CNY</option>
          <option value="CNY">RMB (China Yuan Renminbi) - CNY</option>
          <option value="COP">Colombia Pesos - COP</option>
          <option value="CRC">Costa Rica Colones - CRC</option>
          <option value="HRK">Croatia Kuna - HRK</option>
          <option value="CZK">Czech Republic Koruny - CZK</option>
          <option value="DKK">Denmark Kroner - DKK</option>
          <option value="DOP">Dominican Republic Pesos - DOP</option>
          <option value="EGP">Egypt Pounds - EGP</option>
          <option value="EEK">Estonia Krooni - EEK</option>
          <option value="EUR">Euro - EUR</option>
          <option value="FJD">Fiji Dollars - FJD</option>
          <option value="HKD">Hong Kong Dollars - HKD</option>
          <option value="HUF">Hungary Forint - HUF</option>
          <option value="ISK">Iceland Kronur - ISK</option>
          <option value="INR">India Rupees - INR</option>
          <option value="IDR">Indonesia Rupiahs - IDR</option>
          <option value="ILS">Israel New Shekels - ILS</option>
          <option value="JMD">Jamaica Dollars - JMD</option>
          <option value="JPY">Japan Yen - JPY</option>
          <option value="JOD">Jordan Dinars - JOD</option>
          <option value="KES">Kenya Shillings - KES</option>
          <option value="KRW">Korea (South) Won - KRW</option>
          <option value="KWD">Kuwait Dinars - KWD</option>
          <option value="LBP">Lebanon Pounds - LBP</option>
          <option value="MYR">Malaysia Ringgits - MYR</option>
          <option value="MUR">Mauritius Rupees - MUR</option>
          <option value="MXN">Mexico Pesos - MXN</option>
          <option value="MAD">Morocco Dirhams - MAD</option>
          <option value="NZD">New Zealand Dollars - NZD</option>
          <option value="NOK">Norway Kroner - NOK</option>
          <option value="OMR">Oman Rials - OMR</option>
          <option value="PKR">Pakistan Rupees - PKR</option>
          <option value="PEN">Peru Nuevos Soles - PEN</option>
          <option value="PHP">Philippines Pesos - PHP</option>
          <option value="PLN">Poland Zlotych - PLN</option>
          <option value="QAR">Qatar Riyals - QAR</option>
          <option value="RON">Romania New Lei - RON</option>
          <option value="RUB">Russia Rubles - RUB</option>
          <option value="SAR">Saudi Arabia Riyals - SAR</option>
          <option value="SGD">Singapore Dollars - SGD</option>
          <option value="SKK">Slovakia Koruny - SKK</option>
          <option value="ZAR">South Africa Rand - ZAR</option>
          <option value="KRW">South Korea Won - KRW</option>
          <option value="LKR">Sri Lanka Rupees - LKR</option>
          <option value="SEK">Sweden Kronor - SEK</option>
          <option value="CHF">Switzerland Francs - CHF</option>
          <option value="TWD">Taiwan New Dollars - TWD</option>
          <option value="THB">Thailand Baht - THB</option>
          <option value="TTD">Trinidad and Tobago Dollars - TTD</option>
          <option value="TND">Tunisia Dinars - TND</option>
          <option value="TRY">Turkey Lira - TRY</option>
          <option value="AED">United Arab Emirates Dirhams - AED</option>
          <option value="GBP">United Kingdom Pounds - GBP</option>
          <option value="USD">United States Dollars - USD</option>
          <option value="VEB">Venezuela Bolivares - VEB</option>
          <option value="VND">Vietnam Dong - VND</option>
          <option value="ZMK">Zambia Kwacha - ZMK</option>
        </select>
    </div>
    
    <div class="data">
    	<input type="button" name="convert" id="convert" value="Convert" />
    </div>
</div>

<!-- Below conversion rsults will be displayed -->
<div id="results"></div>
</body>
</html>