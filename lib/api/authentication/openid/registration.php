<?php

session_start();
$username = $_SESSION['username'];

$form = $_POST;
if ($form) {
	$config = '$GLOBALS[\'sreg\'] = array(' . "\n";
	$config .= ($form['nickname']) ? "\t" . "'nickname' => '" . $form['nickname'] . "'," . "\n" : '';
	$config .= ($form['email']) ? "\t" . "'email' => '" . $form['email'] . "'," . "\n" : '';
	$config .= ($form['fullname']) ? "\t" . "'fullname' => '" . $form['fullname'] . "'," . "\n" : '';
	$config .= ($form['gender']) ? "\t" . "'gender' => '" . $form['gender'] . "'," . "\n" : '';
	$config .= ($form['postcode']) ? "\t" . "'postcode' => '" . $form['postcode'] . "'," . "\n" : '';
	$config .= ($form['country']) ? "\t" . "'country' => '" . $form['country'] . "'," . "\n" : '';
	$config .= ($form['language']) ? "\t" . "'language' => '" . $form['language'] . "'," . "\n" : '';
	$config .= ($form['timezone']) ? "\t" . "'timezone' => '" . $form['timezone'] . "'," . "\n" : '';
	$config .= ($form['dobday'] && $form['dobmonth'] && $form['dobyear']) ? "\t" . "'dob' => '" . date('Y-m-d',strtotime($form['dobyear'].'-'.$form['dobmonth'].'-'.$form['dobday'])) . "'," . "\n" : '';
	$config = (substr($config,-2,2) == (',' . "\n")) ? substr($config,0,-2) . "\n" : $config; // remove last comma
	$config .= ');' . "\n\n";
	$config .= '?>';
	
	$filename = 'config/' . $username . '.php';
	if (!$handle = fopen($filename, 'a')) {
		echo "Cannot open file ($filename)";
		exit;
	}
	if (fwrite($handle, $config) === false) {
		echo "Cannot write to file ($filename)";
		exit;
	}
	
	header('location: ./complete.php');
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>phpMyOpenID - Step 2</title>
</head>
<body>
	<h1>phpMyOpenID Installation</h1>
	<h2>Step Two (optional)</h2>
	<p>Please fill in the optional registration info below:</p>
	
	<form method="post" action="">
		
		<label for="fullname">Full Name:</label>
		<input type="text" class="text" name="fullname" id="fullname" value="<?php echo $fullname ?>" />
		<br />
		
		<label for="nickname">Nickname:</label>
		<input type="text" class="text" name="nickname" id="nickname" value="<?php echo $nickname ?>" />
		<br />
		
		<label for="email">Email:</label>
		<input type="text" class="text" name="email" id="email" value="<?php echo $email ?>" />
		<br />
		
		<label for="dob">Date of Birth:</label>
		<select name="dobday" id="dob">
			<option value="">-- None --</option>
			<?php for ($i=1;$i<32;$i++) { ?>
				<option value="<?php echo $i ?>"><?php echo $i ?></option>
			<?php } ?>
		</select>
		<select name="dobmonth">
			<option value="">-- None --</option>
			<?php for ($i=1;$i<13;$i++) { ?>
				<option value="<?php echo $i ?>"><?php echo date('F',strtotime('1970-'.$i.'-01')) ?></option>
			<?php } ?>
		</select>
		<select name="dobyear">
			<option value="">-- None --</option>
			<?php for ($i=date('Y');$i>(date('Y')-120);$i--) { ?>
				<option value="<?php echo $i ?>"><?php echo $i ?></option>
			<?php } ?>
		</select>
		<br />
		
		<label for="gender">Gender:</label>
		<select name="gender" id="gender">
			<option value="">-- None --</option>
			<option value="F">Female</option>
			<option value="M">Male</option>
		</select>
		<br />
		
		<label for="postcode">Postal Code:</label>
		<input type="text" class="text" name="postcode" id="postcode" value="<?php echo $postcode ?>" />
		<br />
		
		<label for="country">Country:</label>
		<select name="country" id="country">
			<option value="">-- None --</option>
			<option value="GB">United Kingdom</option>
			<option value="US">United States</option>
			<option value="AF">Afghanistan</option>
			<option value="AX">Aland Islands</option>
			<option value="AL">Albania</option>
			<option value="DZ">Algeria</option>
			<option value="AS">American Samoa</option>
			<option value="AD">Andorra</option>
			<option value="AO">Angola</option>
			<option value="AI">Anguilla</option>

			<option value="AQ">Antarctica</option>
			<option value="AG">Antigua and Barbuda</option>
			<option value="AR">Argentina</option>
			<option value="AM">Armenia</option>
			<option value="AW">Aruba</option>
			<option value="AU">Australia</option>
			<option value="AT">Austria</option>
			<option value="AZ">Azerbaijan</option>
			<option value="BS">Bahamas</option>

			<option value="BH">Bahrain</option>
			<option value="BD">Bangladesh</option>
			<option value="BB">Barbados</option>
			<option value="BY">Belarus</option>
			<option value="BE">Belgium</option>
			<option value="BZ">Belize</option>
			<option value="BJ">Benin</option>
			<option value="BM">Bermuda</option>
			<option value="BT">Bhutan</option>

			<option value="BO">Bolivia</option>
			<option value="BA">Bosnia And Herzegovina</option>
			<option value="BW">Botswana</option>
			<option value="BV">Bouvet Island</option>
			<option value="BR">Brazil</option>
			<option value="IO">British Indian Ocean Territory</option>
			<option value="BN">Brunei Darussalam</option>
			<option value="BG">Bulgaria</option>
			<option value="BF">Burkina Faso</option>

			<option value="BI">Burundi</option>
			<option value="KH">Cambodia</option>
			<option value="CM">Cameroon</option>
			<option value="CA">Canada</option>
			<option value="CV">Cape Verde</option>
			<option value="KY">Cayman Islands</option>
			<option value="CF">Central African Republic</option>
			<option value="TD">Chad</option>
			<option value="CL">Chile</option>

			<option value="CN">China</option>
			<option value="CX">Christmas Island</option>
			<option value="CC">Cocos (Keeling) Islands</option>
			<option value="CO">Colombia</option>
			<option value="KM">Comoros</option>
			<option value="CG">Congo</option>
			<option value="CD">Congo, The Democratic Republic Of The</option>
			<option value="CK">Cook Islands</option>
			<option value="CR">Costa Rica</option>

			<option value="CI">Cote D'Ivoire</option>
			<option value="HR">Croatia</option>
			<option value="CU">Cuba</option>
			<option value="CY">Cyprus</option>
			<option value="CZ">Czech Republic</option>
			<option value="DK">Denmark</option>
			<option value="DJ">Djibouti</option>
			<option value="DM">Dominica</option>
			<option value="DO">Dominican Republic</option>

			<option value="EC">Ecuador</option>
			<option value="EG">Egypt</option>
			<option value="SV">El Salvador</option>
			<option value="GQ">Equatorial Guinea</option>
			<option value="ER">Eritrea</option>
			<option value="EE">Estonia</option>
			<option value="ET">Ethiopia</option>
			<option value="FK">Falkland Islands (Malvinas)</option>
			<option value="FO">Faroe Islands</option>

			<option value="FJ">Fiji</option>
			<option value="FI">Finland</option>
			<option value="FR">France</option>
			<option value="GF">French Guiana</option>
			<option value="PF">French Polynesia</option>
			<option value="TF">French Southern Territories</option>
			<option value="GA">Gabon</option>
			<option value="GM">Gambia</option>
			<option value="GE">Georgia</option>

			<option value="DE">Germany</option>
			<option value="GH">Ghana</option>
			<option value="GI">Gibraltar</option>
			<option value="GR">Greece</option>
			<option value="GL">Greenland</option>
			<option value="GD">Grenada</option>
			<option value="GP">Guadeloupe</option>
			<option value="GU">Guam</option>
			<option value="GT">Guatemala</option>

			<option value="GN">Guinea</option>
			<option value="GW">Guinea-Bissau</option>
			<option value="GY">Guyana</option>
			<option value="HT">Haiti</option>
			<option value="HM">Heard Island and McDonald Islands</option>
			<option value="VA">Holy See (Vatican City State)</option>
			<option value="HN">Honduras</option>
			<option value="HK">Hong Kong</option>
			<option value="HU">Hungary</option>

			<option value="IS">Iceland</option>
			<option value="IN">India</option>
			<option value="ID">Indonesia</option>
			<option value="IR">Iran, Islamic Republic Of</option>
			<option value="IQ">Iraq</option>
			<option value="IE">Ireland</option>
			<option value="IL">Israel</option>
			<option value="IT">Italy</option>
			<option value="JM">Jamaica</option>

			<option value="JP">Japan</option>
			<option value="JO">Jordan</option>
			<option value="KZ">Kazakhstan</option>
			<option value="KE">Kenya</option>
			<option value="KI">Kiribati</option>
			<option value="KP">Korea, Democratic People's Republic Of</option>
			<option value="KR">Korea, Republic Of</option>
			<option value="KW">Kuwait</option>
			<option value="KG">Kyrgyzstan</option>

			<option value="LA">Lao People's Democratic Republic</option>
			<option value="LV">Latvia</option>
			<option value="LB">Lebanon</option>
			<option value="LS">Lesotho</option>
			<option value="LR">Liberia</option>
			<option value="LY">Libyan Arab Jamahiriya</option>
			<option value="LI">Liechtenstein</option>
			<option value="LT">Lithuania</option>
			<option value="LU">Luxembourg</option>

			<option value="MO">Macao</option>
			<option value="MK">Macedonia, The Former Yugoslav Republic Of</option>
			<option value="MG">Madagascar</option>
			<option value="MW">Malawi</option>
			<option value="MY">Malaysia</option>
			<option value="MV">Maldives</option>
			<option value="ML">Mali</option>
			<option value="MT">Malta</option>
			<option value="MH">Marshall Islands</option>

			<option value="MQ">Martinique</option>
			<option value="MR">Mauritania</option>
			<option value="MU">Mauritius</option>
			<option value="YT">Mayotte</option>
			<option value="MX">Mexico</option>
			<option value="FM">Micronesia, Federated States Of</option>
			<option value="MD">Moldova, Republic Of</option>
			<option value="MC">Monaco</option>
			<option value="MN">Mongolia</option>

			<option value="ME">Montenegro</option>
			<option value="MS">Montserrat</option>
			<option value="MA">Morocco</option>
			<option value="MZ">Mozambique</option>
			<option value="MM">Myanmar</option>
			<option value="NA">Namibia</option>
			<option value="NR">Nauru</option>
			<option value="NP">Nepal</option>
			<option value="NL">Netherlands</option>

			<option value="AN">Netherlands Antilles</option>
			<option value="NC">New Caledonia</option>
			<option value="NZ">New Zealand</option>
			<option value="NI">Nicaragua</option>
			<option value="NE">Niger</option>
			<option value="NG">Nigeria</option>
			<option value="NU">Niue</option>
			<option value="NF">Norfolk Island</option>
			<option value="MP">Northern Mariana Islands</option>

			<option value="NO">Norway</option>
			<option value="OM">Oman</option>
			<option value="PK">Pakistan</option>
			<option value="PW">Palau</option>
			<option value="PS">Palestinian Territory, Occupied</option>
			<option value="PA">Panama</option>
			<option value="PG">Papua New Guinea</option>
			<option value="PY">Paraguay</option>
			<option value="PE">Peru</option>

			<option value="PH">Philippines</option>
			<option value="PN">Pitcairn</option>
			<option value="PL">Poland</option>
			<option value="PT">Portugal</option>
			<option value="PR">Puerto Rico</option>
			<option value="QA">Qatar</option>
			<option value="RE">Reunion</option>
			<option value="RO">Romania</option>
			<option value="RU">Russian Federation</option>

			<option value="RW">Rwanda</option>
			<option value="SH">Saint Helena</option>
			<option value="KN">Saint Kitts And Nevis</option>
			<option value="LC">Saint Lucia</option>
			<option value="PM">Saint Pierre And Miquelon</option>
			<option value="VC">Saint Vincent And The Grenadines</option>
			<option value="WS">Samoa</option>
			<option value="SM">San Marino</option>
			<option value="ST">Sao Tome And Principe</option>

			<option value="SA">Saudi Arabia</option>
			<option value="SN">Senegal</option>
			<option value="RS">Serbia</option>
			<option value="SC">Seychelles</option>
			<option value="SL">Sierra Leone</option>
			<option value="SG">Singapore</option>
			<option value="SK">Slovakia</option>
			<option value="SI">Slovenia</option>
			<option value="SB">Solomon Islands</option>

			<option value="SO">Somalia</option>
			<option value="ZA">South Africa</option>
			<option value="GS">South Georgia And The South Sandwich Islands</option>
			<option value="ES">Spain</option>
			<option value="LK">Sri Lanka</option>
			<option value="SD">Sudan</option>
			<option value="SR">Suriname</option>
			<option value="SJ">Svalbard And Jan Mayen</option>
			<option value="SZ">Swaziland</option>

			<option value="SE">Sweden</option>
			<option value="CH">Switzerland</option>
			<option value="SY">Syrian Arab Republic</option>
			<option value="TW">Taiwan</option>
			<option value="TJ">Tajikistan</option>
			<option value="TZ">Tanzania, United Republic Of</option>
			<option value="TH">Thailand</option>
			<option value="TL">Timor-leste</option>
			<option value="TG">Togo</option>

			<option value="TK">Tokelau</option>
			<option value="TO">Tonga</option>
			<option value="TT">Trinidad And Tobago</option>
			<option value="TN">Tunisia</option>
			<option value="TR">Turkey</option>
			<option value="TM">Turkmenistan</option>
			<option value="TC">Turks And Caicos Islands</option>
			<option value="TV">Tuvalu</option>
			<option value="UG">Uganda</option>

			<option value="UA">Ukraine</option>
			<option value="AE">United Arab Emirates</option>
			<option value="GB">United Kingdom</option>
			<option value="UM">United States Minor Outlying Islands</option>
			<option value="UY">Uruguay</option>
			<option value="UZ">Uzbekistan</option>
			<option value="VU">Vanuatu</option>
			<option value="VE">Venezuela</option>
			<option value="VN">Viet Nam</option>

			<option value="VG">Virgin Islands, British</option>
			<option value="VI">Virgin Islands, U.S.</option>
			<option value="WF">Wallis And Futuna</option>
			<option value="EH">Western Sahara</option>
			<option value="YE">Yemen</option>
			<option value="ZM">Zambia</option>
			<option value="ZW">Zimbabwe</option>			
		</select>
		<br />
		
		<label for="language">Language:</label>
		<select name="language" id="language">
			<option value="">-- None --</option>
			<option value="EN">English</option>
			<option value="AB">Abkhazian</option>
			<option value="AA">Afar</option>
			<option value="AF">Afrikaans</option>
			<option value="SQ">Albanian</option>

			<option value="AM">Amharic</option>
			<option value="AR">Arabic</option>
			<option value="HY">Armenian</option>
			<option value="AS">Assamese</option>
			<option value="AY">Aymara</option>
			<option value="AZ">Azerbaijani</option>
			<option value="BA">Bashkir</option>
			<option value="EU">Basque</option>
			<option value="BN">Bengali</option>

			<option value="DZ">Bhutani</option>
			<option value="BH">Bihari</option>
			<option value="BI">Bislama</option>
			<option value="BR">Breton</option>
			<option value="BG">Bulgarian</option>
			<option value="MY">Burmese</option>
			<option value="BE">Byelorussian</option>
			<option value="KM">Cambodian</option>
			<option value="CA">Catalan</option>

			<option value="ZH_TW">Chinese (traditional)</option>
			<option value="ZH_CN">Chinese (simplified)</option>
			<option value="CO">Corsican</option>
			<option value="HR">Croatian</option>
			<option value="CS">Czech</option>
			<option value="DA">Danish</option>
			<option value="NL">Dutch</option>
			<option value="EO">Esperanto</option>
			<option value="ET">Estonian</option>

			<option value="FO">Faeroese</option>
			<option value="FJ">Fiji</option>
			<option value="FI">Finnish</option>
			<option value="FR">French</option>
			<option value="FY">Frisian</option>
			<option value="GD">Gaelic</option>
			<option value="GL">Galician</option>
			<option value="KA">Georgian</option>
			<option value="DE">German</option>

			<option value="EL">Greek</option>
			<option value="KL">Greenlandic</option>
			<option value="GN">Guarani</option>
			<option value="GU">Gujarati</option>
			<option value="HA">Hausa</option>
			<option value="IW">Hebrew</option>
			<option value="HI">Hindi</option>
			<option value="HU">Hungarian</option>
			<option value="IS">Icelandic</option>

			<option value="IN">Indonesian</option>
			<option value="IA">Interlingua</option>
			<option value="IE">Interlingue</option>
			<option value="IK">Inupiak</option>
			<option value="GA">Irish</option>
			<option value="IT">Italian</option>
			<option value="JA">Japanese</option>
			<option value="JW">Javanese</option>
			<option value="KN">Kannada</option>

			<option value="KS">Kashmiri</option>
			<option value="KK">Kazakh</option>
			<option value="RW">Kinyarwanda</option>
			<option value="KY">Kirghiz</option>
			<option value="RN">Kirundi</option>
			<option value="KO">Korean</option>
			<option value="KU">Kurdish</option>
			<option value="LO">Laothian</option>
			<option value="LA">Latin</option>

			<option value="LV">Latvian</option>
			<option value="LN">Lingala</option>
			<option value="LT">Lithuanian</option>
			<option value="MK">Macedonian</option>
			<option value="MG">Malagasy</option>
			<option value="MS">Malay</option>
			<option value="ML">Malayalam</option>
			<option value="MT">Maltese</option>
			<option value="MI">Maori</option>

			<option value="MR">Marathi</option>
			<option value="MO">Moldavian</option>
			<option value="MN">Mongolian</option>
			<option value="NA">Nauru</option>
			<option value="NE">Nepali</option>
			<option value="NO">Norwegian</option>
			<option value="OC">Occitan</option>
			<option value="OR">Oriya</option>
			<option value="OM">Oromo</option>

			<option value="PS">Pashto</option>
			<option value="FA">Persian</option>
			<option value="PL">Polish</option>
			<option value="PT">Portuguese</option>
			<option value="PT_BR">Português (Brasil)</option>
			<option value="PA">Punjabi</option>
			<option value="QU">Quechua</option>
			<option value="RM">Rhaeto-Romance</option>
			<option value="RO">Romanian</option>

			<option value="RU">Russian</option>
			<option value="SM">Samoan</option>
			<option value="SG">Sangro</option>
			<option value="SA">Sanskrit</option>
			<option value="SR">Serbian</option>
			<option value="SH">Serbo-Croatian</option>
			<option value="ST">Sesotho</option>
			<option value="TN">Setswana</option>
			<option value="SN">Shona</option>

			<option value="SD">Sindhi</option>
			<option value="SI">Singhalese</option>
			<option value="SS">Siswati</option>
			<option value="SK">Slovak</option>
			<option value="SL">Slovenian</option>
			<option value="SO">Somali</option>
			<option value="ES">Spanish</option>
			<option value="SU">Sudanese</option>
			<option value="SW">Swahili</option>

			<option value="SV">Swedish</option>
			<option value="TL">Tagalog</option>
			<option value="TG">Tajik</option>
			<option value="TA">Tamil</option>
			<option value="TT">Tatar</option>
			<option value="TE">Telugu</option>
			<option value="TH">Thai</option>
			<option value="BO">Tibetan</option>
			<option value="TI">Tigrinya</option>

			<option value="TO">Tonga</option>
			<option value="TS">Tsonga</option>
			<option value="TR">Turkish</option>
			<option value="TK">Turkmen</option>
			<option value="TW">Twi</option>
			<option value="UK">Ukrainian</option>
			<option value="UR">Urdu</option>
			<option value="UZ">Uzbek</option>
			<option value="VI">Vietnamese</option>

			<option value="VO">Volapuk</option>
			<option value="CY">Welsh</option>
			<option value="WO">Wolof</option>
			<option value="XH">Xhosa</option>
			<option value="JI">Yiddish</option>
			<option value="YO">Yoruba</option>
			<option value="ZU">Zulu</option>
		</select>
		<br />
		
		<label for="timezone">Timezone:</label>
		<select name="timezone" id="timezone">
			<option value="">-- None --</option>
			<option value="Europe/London"%s>Europe/London</option>
			<option value="Africa/Abidjan"%s>Africa/Abidjan</option>
			<option value="Africa/Accra"%s>Africa/Accra</option>
			<option value="Africa/Addis_Ababa"%s>Africa/Addis_Ababa</option>
			<option value="Africa/Algiers"%s>Africa/Algiers</option>
			<option value="Africa/Asmera"%s>Africa/Asmera</option>

			<option value="Africa/Bamako"%s>Africa/Bamako</option>
			<option value="Africa/Bangui"%s>Africa/Bangui</option>
			<option value="Africa/Banjul"%s>Africa/Banjul</option>
			<option value="Africa/Bissau"%s>Africa/Bissau</option>
			<option value="Africa/Blantyre"%s>Africa/Blantyre</option>
			<option value="Africa/Brazzaville"%s>Africa/Brazzaville</option>
			<option value="Africa/Bujumbura"%s>Africa/Bujumbura</option>
			<option value="Africa/Cairo"%s>Africa/Cairo</option>
			<option value="Africa/Casablanca"%s>Africa/Casablanca</option>

			<option value="Africa/Ceuta"%s>Africa/Ceuta</option>
			<option value="Africa/Conakry"%s>Africa/Conakry</option>
			<option value="Africa/Dakar"%s>Africa/Dakar</option>
			<option value="Africa/Dar_es_Salaam"%s>Africa/Dar_es_Salaam</option>
			<option value="Africa/Djibouti"%s>Africa/Djibouti</option>
			<option value="Africa/Douala"%s>Africa/Douala</option>
			<option value="Africa/El_Aaiun"%s>Africa/El_Aaiun</option>
			<option value="Africa/Freetown"%s>Africa/Freetown</option>
			<option value="Africa/Gaborone"%s>Africa/Gaborone</option>

			<option value="Africa/Harare"%s>Africa/Harare</option>
			<option value="Africa/Johannesburg"%s>Africa/Johannesburg</option>
			<option value="Africa/Kampala"%s>Africa/Kampala</option>
			<option value="Africa/Khartoum"%s>Africa/Khartoum</option>
			<option value="Africa/Kigali"%s>Africa/Kigali</option>
			<option value="Africa/Kinshasa"%s>Africa/Kinshasa</option>
			<option value="Africa/Lagos"%s>Africa/Lagos</option>
			<option value="Africa/Libreville"%s>Africa/Libreville</option>
			<option value="Africa/Lome"%s>Africa/Lome</option>

			<option value="Africa/Luanda"%s>Africa/Luanda</option>
			<option value="Africa/Lubumbashi"%s>Africa/Lubumbashi</option>
			<option value="Africa/Lusaka"%s>Africa/Lusaka</option>
			<option value="Africa/Malabo"%s>Africa/Malabo</option>
			<option value="Africa/Maputo"%s>Africa/Maputo</option>
			<option value="Africa/Maseru"%s>Africa/Maseru</option>
			<option value="Africa/Mbabane"%s>Africa/Mbabane</option>
			<option value="Africa/Mogadishu"%s>Africa/Mogadishu</option>
			<option value="Africa/Monrovia"%s>Africa/Monrovia</option>

			<option value="Africa/Nairobi"%s>Africa/Nairobi</option>
			<option value="Africa/Ndjamena"%s>Africa/Ndjamena</option>
			<option value="Africa/Niamey"%s>Africa/Niamey</option>
			<option value="Africa/Nouakchott"%s>Africa/Nouakchott</option>
			<option value="Africa/Ouagadougou"%s>Africa/Ouagadougou</option>
			<option value="Africa/Porto"%s>Africa/Porto</option>
			<option value="Africa/Sao_Tome"%s>Africa/Sao_Tome</option>
			<option value="Africa/Tripoli"%s>Africa/Tripoli</option>
			<option value="Africa/Tunis"%s>Africa/Tunis</option>

			<option value="Africa/Windhoek"%s>Africa/Windhoek</option>
			<option value="America/Adak"%s>America/Adak</option>
			<option value="America/Anchorage"%s>America/Anchorage</option>
			<option value="America/Anguilla"%s>America/Anguilla</option>
			<option value="America/Antigua"%s>America/Antigua</option>
			<option value="America/Araguaina"%s>America/Araguaina</option>
			<option value="America/Argentina/Buenos_Aires"%s>America/Argentina/Buenos_Aires</option>
			<option value="America/Argentina/Catamarca"%s>America/Argentina/Catamarca</option>
			<option value="America/Argentina/Cordoba"%s>America/Argentina/Cordoba</option>

			<option value="America/Argentina/Jujuy"%s>America/Argentina/Jujuy</option>
			<option value="America/Argentina/La_Rioja"%s>America/Argentina/La_Rioja</option>
			<option value="America/Argentina/Mendoza"%s>America/Argentina/Mendoza</option>
			<option value="America/Argentina/Rio_Gallegos"%s>America/Argentina/Rio_Gallegos</option>
			<option value="America/Argentina/San_Juan"%s>America/Argentina/San_Juan</option>
			<option value="America/Argentina/Tucuman"%s>America/Argentina/Tucuman</option>
			<option value="America/Argentina/Ushuaia"%s>America/Argentina/Ushuaia</option>
			<option value="America/Aruba"%s>America/Aruba</option>
			<option value="America/Asuncion"%s>America/Asuncion</option>

			<option value="America/Bahia"%s>America/Bahia</option>
			<option value="America/Barbados"%s>America/Barbados</option>
			<option value="America/Belem"%s>America/Belem</option>
			<option value="America/Belize"%s>America/Belize</option>
			<option value="America/Boa_Vista"%s>America/Boa_Vista</option>
			<option value="America/Bogota"%s>America/Bogota</option>
			<option value="America/Boise"%s>America/Boise</option>
			<option value="America/Cambridge_Bay"%s>America/Cambridge_Bay</option>
			<option value="America/Campo_Grande"%s>America/Campo_Grande</option>

			<option value="America/Cancun"%s>America/Cancun</option>
			<option value="America/Caracas"%s>America/Caracas</option>
			<option value="America/Cayenne"%s>America/Cayenne</option>
			<option value="America/Cayman"%s>America/Cayman</option>
			<option value="America/Chicago"%s>America/Chicago</option>
			<option value="America/Chihuahua"%s>America/Chihuahua</option>
			<option value="America/Coral_Harbour"%s>America/Coral_Harbour</option>
			<option value="America/Costa_Rica"%s>America/Costa_Rica</option>
			<option value="America/Cuiaba"%s>America/Cuiaba</option>

			<option value="America/Curacao"%s>America/Curacao</option>
			<option value="America/Danmarkshavn"%s>America/Danmarkshavn</option>
			<option value="America/Dawson"%s>America/Dawson</option>
			<option value="America/Dawson_Creek"%s>America/Dawson_Creek</option>
			<option value="America/Denver"%s>America/Denver</option>
			<option value="America/Detroit"%s>America/Detroit</option>
			<option value="America/Dominica"%s>America/Dominica</option>
			<option value="America/Edmonton"%s>America/Edmonton</option>
			<option value="America/Eirunepe"%s>America/Eirunepe</option>

			<option value="America/El_Salvador"%s>America/El_Salvador</option>
			<option value="America/Fortaleza"%s>America/Fortaleza</option>
			<option value="America/Glace_Bay"%s>America/Glace_Bay</option>
			<option value="America/Godthab"%s>America/Godthab</option>
			<option value="America/Goose_Bay"%s>America/Goose_Bay</option>
			<option value="America/Grand_Turk"%s>America/Grand_Turk</option>
			<option value="America/Grenada"%s>America/Grenada</option>
			<option value="America/Guadeloupe"%s>America/Guadeloupe</option>
			<option value="America/Guatemala"%s>America/Guatemala</option>

			<option value="America/Guayaquil"%s>America/Guayaquil</option>
			<option value="America/Guyana"%s>America/Guyana</option>
			<option value="America/Halifax"%s>America/Halifax</option>
			<option value="America/Havana"%s>America/Havana</option>
			<option value="America/Hermosillo"%s>America/Hermosillo</option>
			<option value="America/Indiana/Indianapolis"%s>America/Indiana/Indianapolis</option>
			<option value="America/Indiana/Knox"%s>America/Indiana/Knox</option>
			<option value="America/Indiana/Marengo"%s>America/Indiana/Marengo</option>
			<option value="America/Indiana/Petersburg"%s>America/Indiana/Petersburg</option>

			<option value="America/Indiana/Vevay"%s>America/Indiana/Vevay</option>
			<option value="America/Indiana/Vincennes"%s>America/Indiana/Vincennes</option>
			<option value="America/Inuvik"%s>America/Inuvik</option>
			<option value="America/Iqaluit"%s>America/Iqaluit</option>
			<option value="America/Jamaica"%s>America/Jamaica</option>
			<option value="America/Juneau"%s>America/Juneau</option>
			<option value="America/Kentucky/Louisville"%s>America/Kentucky/Louisville</option>
			<option value="America/Kentucky/Monticello"%s>America/Kentucky/Monticello</option>
			<option value="America/La_Paz"%s>America/La_Paz</option>

			<option value="America/Lima"%s>America/Lima</option>
			<option value="America/Los_Angeles"%s>America/Los_Angeles</option>
			<option value="America/Maceio"%s>America/Maceio</option>
			<option value="America/Managua"%s>America/Managua</option>
			<option value="America/Manaus"%s>America/Manaus</option>
			<option value="America/Martinique"%s>America/Martinique</option>
			<option value="America/Mazatlan"%s>America/Mazatlan</option>
			<option value="America/Menominee"%s>America/Menominee</option>
			<option value="America/Merida"%s>America/Merida</option>

			<option value="America/Mexico_City"%s>America/Mexico_City</option>
			<option value="America/Miquelon"%s>America/Miquelon</option>
			<option value="America/Moncton"%s>America/Moncton</option>
			<option value="America/Monterrey"%s>America/Monterrey</option>
			<option value="America/Montevideo"%s>America/Montevideo</option>
			<option value="America/Montreal"%s>America/Montreal</option>
			<option value="America/Montserrat"%s>America/Montserrat</option>
			<option value="America/Nassau"%s>America/Nassau</option>
			<option value="America/New_York"%s>America/New_York</option>

			<option value="America/Nipigon"%s>America/Nipigon</option>
			<option value="America/Nome"%s>America/Nome</option>
			<option value="America/Noronha"%s>America/Noronha</option>
			<option value="America/North_Dakota/Center"%s>America/North_Dakota/Center</option>
			<option value="America/Panama"%s>America/Panama</option>
			<option value="America/Pangnirtung"%s>America/Pangnirtung</option>
			<option value="America/Paramaribo"%s>America/Paramaribo</option>
			<option value="America/Phoenix"%s>America/Phoenix</option>
			<option value="America/Port"%s>America/Port</option>

			<option value="America/Port_of_Spain"%s>America/Port_of_Spain</option>
			<option value="America/Porto_Velho"%s>America/Porto_Velho</option>
			<option value="America/Puerto_Rico"%s>America/Puerto_Rico</option>
			<option value="America/Rainy_River"%s>America/Rainy_River</option>
			<option value="America/Rankin_Inlet"%s>America/Rankin_Inlet</option>
			<option value="America/Recife"%s>America/Recife</option>
			<option value="America/Regina"%s>America/Regina</option>
			<option value="America/Rio_Branco"%s>America/Rio_Branco</option>
			<option value="America/Santiago"%s>America/Santiago</option>

			<option value="America/Santo_Domingo"%s>America/Santo_Domingo</option>
			<option value="America/Sao_Paulo"%s>America/Sao_Paulo</option>
			<option value="America/Scoresbysund"%s>America/Scoresbysund</option>
			<option value="America/Shiprock"%s>America/Shiprock</option>
			<option value="America/St_Johns"%s>America/St_Johns</option>
			<option value="America/St_Kitts"%s>America/St_Kitts</option>
			<option value="America/St_Lucia"%s>America/St_Lucia</option>
			<option value="America/St_Thomas"%s>America/St_Thomas</option>
			<option value="America/St_Vincent"%s>America/St_Vincent</option>

			<option value="America/Swift_Current"%s>America/Swift_Current</option>
			<option value="America/Tegucigalpa"%s>America/Tegucigalpa</option>
			<option value="America/Thule"%s>America/Thule</option>
			<option value="America/Thunder_Bay"%s>America/Thunder_Bay</option>
			<option value="America/Tijuana"%s>America/Tijuana</option>
			<option value="America/Toronto"%s>America/Toronto</option>
			<option value="America/Tortola"%s>America/Tortola</option>
			<option value="America/Vancouver"%s>America/Vancouver</option>
			<option value="America/Whitehorse"%s>America/Whitehorse</option>

			<option value="America/Winnipeg"%s>America/Winnipeg</option>
			<option value="America/Yakutat"%s>America/Yakutat</option>
			<option value="America/Yellowknife"%s>America/Yellowknife</option>
			<option value="Antarctica/Casey"%s>Antarctica/Casey</option>
			<option value="Antarctica/Davis"%s>Antarctica/Davis</option>
			<option value="Antarctica/DumontDUrville"%s>Antarctica/DumontDUrville</option>
			<option value="Antarctica/Mawson"%s>Antarctica/Mawson</option>
			<option value="Antarctica/McMurdo"%s>Antarctica/McMurdo</option>
			<option value="Antarctica/Palmer"%s>Antarctica/Palmer</option>

			<option value="Antarctica/Rothera"%s>Antarctica/Rothera</option>
			<option value="Antarctica/South_Pole"%s>Antarctica/South_Pole</option>
			<option value="Antarctica/Syowa"%s>Antarctica/Syowa</option>
			<option value="Antarctica/Vostok"%s>Antarctica/Vostok</option>
			<option value="Arctic/Longyearbyen"%s>Arctic/Longyearbyen</option>
			<option value="Asia/Aden"%s>Asia/Aden</option>
			<option value="Asia/Almaty"%s>Asia/Almaty</option>
			<option value="Asia/Amman"%s>Asia/Amman</option>
			<option value="Asia/Anadyr"%s>Asia/Anadyr</option>

			<option value="Asia/Aqtau"%s>Asia/Aqtau</option>
			<option value="Asia/Aqtobe"%s>Asia/Aqtobe</option>
			<option value="Asia/Ashgabat"%s>Asia/Ashgabat</option>
			<option value="Asia/Baghdad"%s>Asia/Baghdad</option>
			<option value="Asia/Bahrain"%s>Asia/Bahrain</option>
			<option value="Asia/Baku"%s>Asia/Baku</option>
			<option value="Asia/Bangkok"%s>Asia/Bangkok</option>
			<option value="Asia/Beirut"%s>Asia/Beirut</option>
			<option value="Asia/Bishkek"%s>Asia/Bishkek</option>

			<option value="Asia/Brunei"%s>Asia/Brunei</option>
			<option value="Asia/Calcutta"%s>Asia/Calcutta</option>
			<option value="Asia/Choibalsan"%s>Asia/Choibalsan</option>
			<option value="Asia/Chongqing"%s>Asia/Chongqing</option>
			<option value="Asia/Colombo"%s>Asia/Colombo</option>
			<option value="Asia/Damascus"%s>Asia/Damascus</option>
			<option value="Asia/Dhaka"%s>Asia/Dhaka</option>
			<option value="Asia/Dili"%s>Asia/Dili</option>
			<option value="Asia/Dubai"%s>Asia/Dubai</option>

			<option value="Asia/Dushanbe"%s>Asia/Dushanbe</option>
			<option value="Asia/Gaza"%s>Asia/Gaza</option>
			<option value="Asia/Harbin"%s>Asia/Harbin</option>
			<option value="Asia/Hong_Kong"%s>Asia/Hong_Kong</option>
			<option value="Asia/Hovd"%s>Asia/Hovd</option>
			<option value="Asia/Irkutsk"%s>Asia/Irkutsk</option>
			<option value="Asia/Jakarta"%s>Asia/Jakarta</option>
			<option value="Asia/Jayapura"%s>Asia/Jayapura</option>
			<option value="Asia/Jerusalem"%s>Asia/Jerusalem</option>

			<option value="Asia/Kabul"%s>Asia/Kabul</option>
			<option value="Asia/Kamchatka"%s>Asia/Kamchatka</option>
			<option value="Asia/Karachi"%s>Asia/Karachi</option>
			<option value="Asia/Kashgar"%s>Asia/Kashgar</option>
			<option value="Asia/Katmandu"%s>Asia/Katmandu</option>
			<option value="Asia/Krasnoyarsk"%s>Asia/Krasnoyarsk</option>
			<option value="Asia/Kuala_Lumpur"%s>Asia/Kuala_Lumpur</option>
			<option value="Asia/Kuching"%s>Asia/Kuching</option>
			<option value="Asia/Kuwait"%s>Asia/Kuwait</option>

			<option value="Asia/Macau"%s>Asia/Macau</option>
			<option value="Asia/Magadan"%s>Asia/Magadan</option>
			<option value="Asia/Makassar"%s>Asia/Makassar</option>
			<option value="Asia/Manila"%s>Asia/Manila</option>
			<option value="Asia/Muscat"%s>Asia/Muscat</option>
			<option value="Asia/Nicosia"%s>Asia/Nicosia</option>
			<option value="Asia/Novosibirsk"%s>Asia/Novosibirsk</option>
			<option value="Asia/Omsk"%s>Asia/Omsk</option>
			<option value="Asia/Oral"%s>Asia/Oral</option>

			<option value="Asia/Phnom_Penh"%s>Asia/Phnom_Penh</option>
			<option value="Asia/Pontianak"%s>Asia/Pontianak</option>
			<option value="Asia/Pyongyang"%s>Asia/Pyongyang</option>
			<option value="Asia/Qatar"%s>Asia/Qatar</option>
			<option value="Asia/Qyzylorda"%s>Asia/Qyzylorda</option>
			<option value="Asia/Rangoon"%s>Asia/Rangoon</option>
			<option value="Asia/Riyadh"%s>Asia/Riyadh</option>
			<option value="Asia/Saigon"%s>Asia/Saigon</option>
			<option value="Asia/Sakhalin"%s>Asia/Sakhalin</option>

			<option value="Asia/Samarkand"%s>Asia/Samarkand</option>
			<option value="Asia/Seoul"%s>Asia/Seoul</option>
			<option value="Asia/Shanghai"%s>Asia/Shanghai</option>
			<option value="Asia/Singapore"%s>Asia/Singapore</option>
			<option value="Asia/Taipei"%s>Asia/Taipei</option>
			<option value="Asia/Tashkent"%s>Asia/Tashkent</option>
			<option value="Asia/Tbilisi"%s>Asia/Tbilisi</option>
			<option value="Asia/Tehran"%s>Asia/Tehran</option>
			<option value="Asia/Thimphu"%s>Asia/Thimphu</option>

			<option value="Asia/Tokyo"%s>Asia/Tokyo</option>
			<option value="Asia/Ulaanbaatar"%s>Asia/Ulaanbaatar</option>
			<option value="Asia/Urumqi"%s>Asia/Urumqi</option>
			<option value="Asia/Vientiane"%s>Asia/Vientiane</option>
			<option value="Asia/Vladivostok"%s>Asia/Vladivostok</option>
			<option value="Asia/Yakutsk"%s>Asia/Yakutsk</option>
			<option value="Asia/Yekaterinburg"%s>Asia/Yekaterinburg</option>
			<option value="Asia/Yerevan"%s>Asia/Yerevan</option>
			<option value="Atlantic/Azores"%s>Atlantic/Azores</option>

			<option value="Atlantic/Bermuda"%s>Atlantic/Bermuda</option>
			<option value="Atlantic/Canary"%s>Atlantic/Canary</option>
			<option value="Atlantic/Cape_Verde"%s>Atlantic/Cape_Verde</option>
			<option value="Atlantic/Faeroe"%s>Atlantic/Faeroe</option>
			<option value="Atlantic/Jan_Mayen"%s>Atlantic/Jan_Mayen</option>
			<option value="Atlantic/Madeira"%s>Atlantic/Madeira</option>
			<option value="Atlantic/Reykjavik"%s>Atlantic/Reykjavik</option>
			<option value="Atlantic/South_Georgia"%s>Atlantic/South_Georgia</option>
			<option value="Atlantic/St_Helena"%s>Atlantic/St_Helena</option>

			<option value="Atlantic/Stanley"%s>Atlantic/Stanley</option>
			<option value="Australia/Adelaide"%s>Australia/Adelaide</option>
			<option value="Australia/Brisbane"%s>Australia/Brisbane</option>
			<option value="Australia/Broken_Hill"%s>Australia/Broken_Hill</option>
			<option value="Australia/Currie"%s>Australia/Currie</option>
			<option value="Australia/Darwin"%s>Australia/Darwin</option>
			<option value="Australia/Hobart"%s>Australia/Hobart</option>
			<option value="Australia/Lindeman"%s>Australia/Lindeman</option>
			<option value="Australia/Lord_Howe"%s>Australia/Lord_Howe</option>

			<option value="Australia/Melbourne"%s>Australia/Melbourne</option>
			<option value="Australia/Perth"%s>Australia/Perth</option>
			<option value="Australia/Sydney"%s>Australia/Sydney</option>
			<option value="Europe/Amsterdam"%s>Europe/Amsterdam</option>
			<option value="Europe/Andorra"%s>Europe/Andorra</option>
			<option value="Europe/Athens"%s>Europe/Athens</option>
			<option value="Europe/Belgrade"%s>Europe/Belgrade</option>
			<option value="Europe/Berlin"%s>Europe/Berlin</option>
			<option value="Europe/Bratislava"%s>Europe/Bratislava</option>

			<option value="Europe/Brussels"%s>Europe/Brussels</option>
			<option value="Europe/Bucharest"%s>Europe/Bucharest</option>
			<option value="Europe/Budapest"%s>Europe/Budapest</option>
			<option value="Europe/Chisinau"%s>Europe/Chisinau</option>
			<option value="Europe/Copenhagen"%s>Europe/Copenhagen</option>
			<option value="Europe/Dublin"%s>Europe/Dublin</option>
			<option value="Europe/Gibraltar"%s>Europe/Gibraltar</option>
			<option value="Europe/Helsinki"%s>Europe/Helsinki</option>
			<option value="Europe/Istanbul"%s>Europe/Istanbul</option>

			<option value="Europe/Kaliningrad"%s>Europe/Kaliningrad</option>
			<option value="Europe/Kiev"%s>Europe/Kiev</option>
			<option value="Europe/Lisbon"%s>Europe/Lisbon</option>
			<option value="Europe/Ljubljana"%s>Europe/Ljubljana</option>
			<option value="Europe/London"%s>Europe/London</option>
			<option value="Europe/Luxembourg"%s>Europe/Luxembourg</option>
			<option value="Europe/Madrid"%s>Europe/Madrid</option>
			<option value="Europe/Malta"%s>Europe/Malta</option>
			<option value="Europe/Mariehamn"%s>Europe/Mariehamn</option>

			<option value="Europe/Minsk"%s>Europe/Minsk</option>
			<option value="Europe/Monaco"%s>Europe/Monaco</option>
			<option value="Europe/Moscow"%s>Europe/Moscow</option>
			<option value="Europe/Oslo"%s>Europe/Oslo</option>
			<option value="Europe/Paris"%s>Europe/Paris</option>
			<option value="Europe/Prague"%s>Europe/Prague</option>
			<option value="Europe/Riga"%s>Europe/Riga</option>
			<option value="Europe/Rome"%s>Europe/Rome</option>
			<option value="Europe/Samara"%s>Europe/Samara</option>

			<option value="Europe/San_Marino"%s>Europe/San_Marino</option>
			<option value="Europe/Sarajevo"%s>Europe/Sarajevo</option>
			<option value="Europe/Simferopol"%s>Europe/Simferopol</option>
			<option value="Europe/Skopje"%s>Europe/Skopje</option>
			<option value="Europe/Sofia"%s>Europe/Sofia</option>
			<option value="Europe/Stockholm"%s>Europe/Stockholm</option>
			<option value="Europe/Tallinn"%s>Europe/Tallinn</option>
			<option value="Europe/Tirane"%s>Europe/Tirane</option>
			<option value="Europe/Uzhgorod"%s>Europe/Uzhgorod</option>

			<option value="Europe/Vaduz"%s>Europe/Vaduz</option>
			<option value="Europe/Vatican"%s>Europe/Vatican</option>
			<option value="Europe/Vienna"%s>Europe/Vienna</option>
			<option value="Europe/Vilnius"%s>Europe/Vilnius</option>
			<option value="Europe/Warsaw"%s>Europe/Warsaw</option>
			<option value="Europe/Zagreb"%s>Europe/Zagreb</option>
			<option value="Europe/Zaporozhye"%s>Europe/Zaporozhye</option>
			<option value="Europe/Zurich"%s>Europe/Zurich</option>
			<option value="Indian/Antananarivo"%s>Indian/Antananarivo</option>

			<option value="Indian/Chagos"%s>Indian/Chagos</option>
			<option value="Indian/Christmas"%s>Indian/Christmas</option>
			<option value="Indian/Cocos"%s>Indian/Cocos</option>
			<option value="Indian/Comoro"%s>Indian/Comoro</option>
			<option value="Indian/Kerguelen"%s>Indian/Kerguelen</option>
			<option value="Indian/Mahe"%s>Indian/Mahe</option>
			<option value="Indian/Maldives"%s>Indian/Maldives</option>
			<option value="Indian/Mauritius"%s>Indian/Mauritius</option>
			<option value="Indian/Mayotte"%s>Indian/Mayotte</option>

			<option value="Indian/Reunion"%s>Indian/Reunion</option>
			<option value="Pacific/Apia"%s>Pacific/Apia</option>
			<option value="Pacific/Auckland"%s>Pacific/Auckland</option>
			<option value="Pacific/Chatham"%s>Pacific/Chatham</option>
			<option value="Pacific/Easter"%s>Pacific/Easter</option>
			<option value="Pacific/Efate"%s>Pacific/Efate</option>
			<option value="Pacific/Enderbury"%s>Pacific/Enderbury</option>
			<option value="Pacific/Fakaofo"%s>Pacific/Fakaofo</option>
			<option value="Pacific/Fiji"%s>Pacific/Fiji</option>

			<option value="Pacific/Funafuti"%s>Pacific/Funafuti</option>
			<option value="Pacific/Galapagos"%s>Pacific/Galapagos</option>
			<option value="Pacific/Gambier"%s>Pacific/Gambier</option>
			<option value="Pacific/Guadalcanal"%s>Pacific/Guadalcanal</option>
			<option value="Pacific/Guam"%s>Pacific/Guam</option>
			<option value="Pacific/Honolulu"%s>Pacific/Honolulu</option>
			<option value="Pacific/Johnston"%s>Pacific/Johnston</option>
			<option value="Pacific/Kiritimati"%s>Pacific/Kiritimati</option>
			<option value="Pacific/Kosrae"%s>Pacific/Kosrae</option>

			<option value="Pacific/Kwajalein"%s>Pacific/Kwajalein</option>
			<option value="Pacific/Majuro"%s>Pacific/Majuro</option>
			<option value="Pacific/Marquesas"%s>Pacific/Marquesas</option>
			<option value="Pacific/Midway"%s>Pacific/Midway</option>
			<option value="Pacific/Nauru"%s>Pacific/Nauru</option>
			<option value="Pacific/Niue"%s>Pacific/Niue</option>
			<option value="Pacific/Norfolk"%s>Pacific/Norfolk</option>
			<option value="Pacific/Noumea"%s>Pacific/Noumea</option>
			<option value="Pacific/Pago_Pago"%s>Pacific/Pago_Pago</option>

			<option value="Pacific/Palau"%s>Pacific/Palau</option>
			<option value="Pacific/Pitcairn"%s>Pacific/Pitcairn</option>
			<option value="Pacific/Ponape"%s>Pacific/Ponape</option>
			<option value="Pacific/Port_Moresby"%s>Pacific/Port_Moresby</option>
			<option value="Pacific/Rarotonga"%s>Pacific/Rarotonga</option>
			<option value="Pacific/Saipan"%s>Pacific/Saipan</option>
			<option value="Pacific/Tahiti"%s>Pacific/Tahiti</option>
			<option value="Pacific/Tarawa"%s>Pacific/Tarawa</option>
			<option value="Pacific/Tongatapu"%s>Pacific/Tongatapu</option>

			<option value="Pacific/Truk"%s>Pacific/Truk</option>
			<option value="Pacific/Wake"%s>Pacific/Wake</option>
			<option value="Pacific/Wallis"%s>Pacific/Wallis</option>
		</select>
		<br />
		
		<input type="submit" class="submit" value="Create account &rarr;" />
	</form>	
