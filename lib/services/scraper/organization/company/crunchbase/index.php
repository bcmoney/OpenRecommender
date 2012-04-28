<?php

include "Crunchbase.class.php";


$company = (!empty($_REQUEST['company'])) ? $_REQUEST['company'] : 'bcmoney';

$crunchbase = new Crunchbase();

$crunchbase->getCompany($company);

?>
