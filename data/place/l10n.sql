-- INFO --
--- Geonames ---
--- Wikipedia ---
--- WikiTravel ---
--- TripAdvisor ---

-- MAPS --
--- OpenStreetMap ---
--- Google Maps ---
--- Yahoo! Maps ---
--- Bing Maps ---
--- MapQuest ---

---- GeoHack: http://toolserver.org/~geohack/geohack.php?pagename=Black_Diamond,_Alberta&params=50_41_18_N_114_13_57_W_type:city(1900)_region:CA-AB ----
---- GeoNames: http://www.geonames.org/search.html?q=<PLACE> ----

-- FLIGHTS --
--- Expedia ---
--- Kayak ---
--- CheapoAir ---
--- Orbitz ---

-- TRAINS --
--- CNrail ---
--- SkyTrain (BC) ---
--- AM Track ---
--- Hyperdia ---

-- HOTELS --
--- Expedia ---
--- Rakuten Travel ---
--- Yahoo! Travel ---
--- AOL Travel ---
--- LateRooms ---
--- hotel.com ---



-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 11, 2011 at 11:46 AM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `openrecommender`
--

-- --------------------------------------------------------

--
-- Table structure for table `l10n`
--

CREATE TABLE IF NOT EXISTS `l10n` (
  `l10n_id` int(3) NOT NULL AUTO_INCREMENT,
  `l10n_code` char(2) DEFAULT NULL,
  `l10n_name` varchar(50) DEFAULT NULL,
  `l10n_locale` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`l10n_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=256 ;

--
-- Dumping data for table `l10n`
--

INSERT INTO `l10n` (`l10n_id`, `l10n_code`, `l10n_name`, `l10n_locale`) VALUES
(1, 'AC', 'saint_helena', 'Ascension Island'),
(2, 'AD', 'andorra', 'Andorra'),
(3, 'AE', 'united_arab_emirates', 'United Arab Emirates'),
(4, 'AF', 'afghanistan', 'Afghanistan'),
(5, 'AG', 'antigua_and_barbuda', 'Antigua and Barbuda'),
(6, 'AI', 'anguilla', 'Anguilla'),
(7, 'AL', 'albania', 'Albania'),
(8, 'AM', 'armenia', 'Armenia'),
(9, 'AN', 'netherlands_antilles', 'Netherlands Antilles'),
(10, 'AO', 'angola', 'Angola'),
(11, 'AQ', 'antarctica', 'Antarctica'),
(12, 'AR', 'argentina', 'Argentina'),
(13, 'AS', 'american_samoa', 'American Samoa'),
(14, 'AT', 'austria', 'Austria'),
(15, 'AU', 'australia', 'Australia'),
(16, 'AW', 'aruba', 'Aruba'),
(17, 'AX', 'finland', 'Aland Islands'),
(18, 'AZ', 'azerbaijan', 'Azerbaijan'),
(19, 'BA', 'bosnia_and_herzegovina', 'Bosnia and Herzegovina'),
(20, 'BB', 'barbados', 'Barbados'),
(21, 'BD', 'bangladesh', 'Bangladesh'),
(22, 'BE', 'belgium', 'Belgium'),
(23, 'BF', 'burkina_faso', 'Burkina Faso'),
(24, 'BG', 'bulgaria', 'Bulgaria'),
(25, 'BH', 'bahrain', 'Bahrain'),
(26, 'BI', 'burundi', 'Burundi'),
(27, 'BJ', 'benin', 'Benin'),
(28, 'BM', 'bermuda', 'Bermuda'),
(29, 'BN', 'brunei', 'Brunei Darussalam'),
(30, 'BO', 'bolivia', 'Bolivia'),
(31, 'BR', 'brazil', 'Brazil'),
(32, 'BS', 'bahamas_the', 'Bahamas'),
(33, 'BT', 'bhutan', 'Bhutan'),
(34, 'BV', 'bouvet_island', 'Bouvet Island'),
(35, 'BW', 'botswana', 'Botswana'),
(36, 'BY', 'belarus', 'Belarus'),
(37, 'BZ', 'belize', 'Belize'),
(38, 'CA', 'canada', 'Canada'),
(39, 'CC', 'cocos_', 'Cocos (Keeling) Islands'),
(40, 'CD', 'congo_democratic_republic_of_the', 'Congo, Democratic Republic'),
(41, 'CF', 'central_african_republic', 'Central African Republic'),
(42, 'CG', 'congo_republic_of_the', 'Congo'),
(43, 'CH', 'switzerland', 'Switzerland'),
(44, 'CI', 'cote_divoire', 'Cote D`Ivoire (Ivory Coast)'),
(45, 'CK', 'cook_islands', 'Cook Islands'),
(46, 'CL', 'chile', 'Chile'),
(47, 'CM', 'cameroon', 'Cameroon'),
(48, 'CN', 'china', 'China'),
(49, 'CO', 'colombia', 'Colombia'),
(50, 'CR', 'costa_rica', 'Costa Rica'),
(51, 'CS', 'czech_republic', 'Czechoslovakia (former)'),
(52, 'CU', 'cuba', 'Cuba'),
(53, 'CV', 'cape_verde', 'Cape Verde'),
(54, 'CX', 'christmas_island', 'Christmas Island'),
(55, 'CY', 'cyprus', 'Cyprus'),
(56, 'CZ', 'czech_republic', 'Czech Republic'),
(57, 'DE', 'germany', 'Germany'),
(58, 'DJ', 'djibouti', 'Djibouti'),
(59, 'DK', 'denmark', 'Denmark'),
(60, 'DM', 'dominica', 'Dominica'),
(61, 'DO', 'dominican_republic', 'Dominican Republic'),
(62, 'DZ', 'algeria', 'Algeria'),
(63, 'EC', 'ecuador', 'Ecuador'),
(64, 'EE', 'estonia', 'Estonia'),
(65, 'EG', 'egypt', 'Egypt'),
(66, 'EH', 'western_sahara', 'Western Sahara'),
(67, 'ER', 'eritrea', 'Eritrea'),
(68, 'ES', 'spain', 'Spain'),
(69, 'ET', 'ethiopia', 'Ethiopia'),
(70, 'EU', 'european_union', 'European Union'),
(71, 'FI', 'finland', 'Finland'),
(72, 'FJ', 'fiji', 'Fiji'),
(73, 'FK', 'falkland_islands_', 'Falkland Islands (Malvinas)'),
(74, 'FM', 'micronesia_federated_states_of', 'Micronesia'),
(75, 'FO', 'faroe_islands', 'Faroe Islands'),
(76, 'FR', 'france', 'France'),
(77, 'FX', 'france', 'France, Metropolitan'),
(78, 'GA', 'gabon', 'Gabon'),
(79, 'GB', 'united_kingdom', 'Great Britain (UK)'),
(80, 'GD', 'grenada', 'Grenada'),
(81, 'GE', 'georgia', 'Georgia'),
(82, 'GF', 'french_guiana', 'French Guiana'),
(83, 'GG', 'guernsey', 'Guernsey'),
(84, 'GH', 'ghana', 'Ghana'),
(85, 'GI', 'gibraltar', 'Gibraltar'),
(86, 'GL', 'greenland', 'Greenland'),
(87, 'GM', 'gambia_the', 'Gambia'),
(88, 'GN', 'guinea', 'Guinea'),
(89, 'GP', 'guadeloupe', 'Guadeloupe'),
(90, 'GQ', 'equatorial_guinea', 'Equatorial Guinea'),
(91, 'GR', 'greece', 'Greece'),
(92, 'GS', 'south_georgia_and_the_south_sandwich_islands', 'S. Georgia and S. Sandwich Isls.'),
(93, 'GT', 'guatemala', 'Guatemala'),
(94, 'GU', 'guam', 'Guam'),
(95, 'GW', 'guineabissau', 'Guinea-Bissau'),
(96, 'GY', 'guyana', 'Guyana'),
(97, 'HK', 'hong_kong', 'Hong Kong'),
(98, 'HM', 'heard_island_and_mcdonald_islands', 'Heard and McDonald Islands'),
(99, 'HN', 'honduras', 'Honduras'),
(100, 'HR', 'croatia', 'Croatia (Hrvatska)'),
(101, 'HT', 'haiti', 'Haiti'),
(102, 'HU', 'hungary', 'Hungary'),
(103, 'ID', 'indonesia', 'Indonesia'),
(104, 'IE', 'ireland', 'Ireland'),
(105, 'IL', 'israel', 'Israel'),
(106, 'IM', 'united_kingdom', 'Isle of Man'),
(107, 'IN', 'india', 'India'),
(108, 'IO', 'british_indian_ocean_territory', 'British Indian Ocean Territory'),
(109, 'IQ', 'iraq', 'Iraq'),
(110, 'IR', 'iran', 'Iran'),
(111, 'IS', 'iceland', 'Iceland'),
(112, 'IT', 'italy', 'Italy'),
(113, 'JE', 'united_kingdom', 'Jersey'),
(114, 'JM', 'jamaica', 'Jamaica'),
(115, 'JO', 'jordan', 'Jordan'),
(116, 'JP', 'japan', 'Japan'),
(117, 'KE', 'kenya', 'Kenya'),
(118, 'KG', 'kyrgyzstan', 'Kyrgyzstan'),
(119, 'KH', 'cambodia', 'Cambodia'),
(120, 'KI', 'kiribati', 'Kiribati'),
(121, 'KM', 'comoros', 'Comoros'),
(122, 'KN', 'saint_kitts_and_nevis', 'Saint Kitts and Nevis'),
(123, 'KP', 'korea_north', 'Korea (North)'),
(124, 'KR', 'korea_south', 'Korea (South)'),
(125, 'KW', 'kuwait', 'Kuwait'),
(126, 'KY', 'cayman_islands', 'Cayman Islands'),
(127, 'KZ', 'kazakhstan', 'Kazakhstan'),
(128, 'LA', 'laos', 'Laos'),
(129, 'LB', 'lebanon', 'Lebanon'),
(130, 'LC', 'saint_lucia', 'Saint Lucia'),
(131, 'LI', 'liechtenstein', 'Liechtenstein'),
(132, 'LK', 'sri_lanka', 'Sri Lanka'),
(133, 'LR', 'liberia', 'Liberia'),
(134, 'LS', 'lesotho', 'Lesotho'),
(135, 'LT', 'lithuania', 'Lithuania'),
(136, 'LU', 'luxembourg', 'Luxembourg'),
(137, 'LV', 'latvia', 'Latvia'),
(138, 'LY', 'libya', 'Libya'),
(139, 'MA', 'morocco', 'Morocco'),
(140, 'MC', 'monaco', 'Monaco'),
(141, 'MD', 'moldova', 'Moldova'),
(142, 'ME', 'montenegro', 'Montenegro'),
(143, 'MF', 'saint_martin', 'Saint Martin'),
(144, 'MG', 'madagascar', 'Madagascar'),
(145, 'MH', 'marshall_islands', 'Marshall Islands'),
(146, 'MK', 'macedonia_the_former_yugoslav_republic_of', 'F.Y.R.O.M. (Macedonia)'),
(147, 'ML', 'mali', 'Mali'),
(148, 'MM', 'burma', 'Myanmar'),
(149, 'MN', 'mongolia', 'Mongolia'),
(150, 'MO', 'macau', 'Macau'),
(151, 'MP', 'northern_mariana_islands', 'Northern Mariana Islands'),
(152, 'MQ', 'martinique', 'Martinique'),
(153, 'MR', 'mauritania', 'Mauritania'),
(154, 'MS', 'montserrat', 'Montserrat'),
(155, 'MT', 'malta', 'Malta'),
(156, 'MU', 'mauritius', 'Mauritius'),
(157, 'MV', 'maldives', 'Maldives'),
(158, 'MW', 'malawi', 'Malawi'),
(159, 'MX', 'mexico', 'Mexico'),
(160, 'MY', 'malaysia', 'Malaysia'),
(161, 'MZ', 'mozambique', 'Mozambique'),
(162, 'NA', 'namibia', 'Namibia'),
(163, 'NC', 'new_caledonia', 'New Caledonia'),
(164, 'NE', 'niger', 'Niger'),
(165, 'NF', 'norfolk_island', 'Norfolk Island'),
(166, 'NG', 'nigeria', 'Nigeria'),
(167, 'NI', 'nicaragua', 'Nicaragua'),
(168, 'NL', 'netherlands', 'Netherlands'),
(169, 'NO', 'norway', 'Norway'),
(170, 'NP', 'nepal', 'Nepal'),
(171, 'NR', 'nauru', 'Nauru'),
(172, 'NT', 'kuwait', 'Neutral Zone'),
(173, 'NU', 'niue', 'Niue'),
(174, 'NZ', 'new_zealand', 'New Zealand (Aotearoa)'),
(175, 'OM', 'oman', 'Oman'),
(176, 'PA', 'panama', 'Panama'),
(177, 'PE', 'peru', 'Peru'),
(178, 'PF', 'french_polynesia', 'French Polynesia'),
(179, 'PG', 'papua_new_guinea', 'Papua New Guinea'),
(180, 'PH', 'philippines', 'Philippines'),
(181, 'PK', 'pakistan', 'Pakistan'),
(182, 'PL', 'poland', 'Poland'),
(183, 'PM', 'saint_pierre_and_miquelon', 'St. Pierre and Miquelon'),
(184, 'PN', 'pitcairn_islands', 'Pitcairn'),
(185, 'PR', 'puerto_rico', 'Puerto Rico'),
(186, 'PS', 'west_bank', 'Palestinian Territory, Occupied'),
(187, 'PT', 'portugal', 'Portugal'),
(188, 'PW', 'palau', 'Palau'),
(189, 'PY', 'paraguay', 'Paraguay'),
(190, 'QA', 'qatar', 'Qatar'),
(191, 'RE', 'reunion', 'Reunion'),
(192, 'RS', 'serbia', 'Serbia'),
(193, 'RO', 'romania', 'Romania'),
(194, 'RU', 'russia', 'Russian Federation'),
(195, 'RW', 'rwanda', 'Rwanda'),
(196, 'SA', 'saudi_arabia', 'Saudi Arabia'),
(197, 'SB', 'solomon_islands', 'Solomon Islands'),
(198, 'SC', 'seychelles', 'Seychelles'),
(199, 'SD', 'sudan', 'Sudan'),
(200, 'SE', 'sweden', 'Sweden'),
(201, 'SG', 'singapore', 'Singapore'),
(202, 'SH', 'saint_helena', 'St. Helena'),
(203, 'SI', 'slovenia', 'Slovenia'),
(204, 'SJ', 'svalbard', 'Svalbard &amp; Jan Mayen Islands'),
(205, 'SK', 'slovakia', 'Slovak Republic'),
(206, 'SL', 'sierra_leone', 'Sierra Leone'),
(207, 'SM', 'italy', 'San Marino'),
(208, 'SN', 'senegal', 'Senegal'),
(209, 'SO', 'somalia', 'Somalia'),
(210, 'SR', 'suriname', 'Suriname'),
(211, 'ST', 'sao_tome_and_principe', 'Sao Tome and Principe'),
(212, 'SU', 'soviet_union_former', 'USSR (former)'),
(213, 'SV', 'el_salvador', 'El Salvador'),
(214, 'SY', 'syria', 'Syria'),
(215, 'SZ', 'swaziland', 'Swaziland'),
(216, 'TC', 'turks_and_caicos_islands', 'Turks and Caicos Islands'),
(217, 'TD', 'chad', 'Chad'),
(218, 'TF', 'french_southern_and_antarctic_lands', 'French Southern Territories'),
(219, 'TG', 'togo', 'Togo'),
(220, 'TH', 'thailand', 'Thailand'),
(221, 'TJ', 'tajikistan', 'Tajikistan'),
(222, 'TK', 'tokelau', 'Tokelau'),
(223, 'TM', 'turkmenistan', 'Turkmenistan'),
(224, 'TN', 'tunisia', 'Tunisia'),
(225, 'TO', 'tonga', 'Tonga'),
(226, 'TP', 'east_timor', 'East Timor'),
(227, 'TR', 'turkey', 'Turkey'),
(228, 'TT', 'trinidad_and_tobago', 'Trinidad and Tobago'),
(229, 'TV', 'tuvalu', 'Tuvalu'),
(230, 'TW', 'taiwan', 'Taiwan'),
(231, 'TZ', 'tanzania', 'Tanzania'),
(232, 'UA', 'ukraine', 'Ukraine'),
(233, 'UG', 'uganda', 'Uganda'),
(234, 'UK', 'united_kingdom', 'United Kingdom'),
(235, 'UM', 'united_states', 'US Minor Outlying Islands'),
(236, 'US', 'united_states', 'United States'),
(237, 'UY', 'uruguay', 'Uruguay'),
(238, 'UZ', 'uzbekistan', 'Uzbekistan'),
(239, 'VA', 'holy_see_', 'Vatican City State (Holy See)'),
(240, 'VC', 'saint_vincent_and_the_grenadines', 'Saint Vincent &amp; the Grenadines'),
(241, 'VE', 'venezuela', 'Venezuela'),
(242, 'VG', 'british_virgin_islands', 'British Virgin Islands '),
(243, 'VI', 'virgin_islands', 'Virgin Islands (U.S.)'),
(244, 'VN', 'vietnam', 'Viet Nam'),
(245, 'VU', 'vanuatu', 'Vanuatu'),
(246, 'WF', 'wallis_and_futuna', 'Wallis and Futuna Islands'),
(247, 'WS', 'samoa', 'Samoa'),
(248, 'XK', 'kosovo', 'Kosovo'),
(249, 'YE', 'yemen', 'Yemen'),
(250, 'YT', 'france', 'Mayotte'),
(251, 'YU', 'yugoslavia_former', 'Serbia and Montenegro (former Yugoslavia)'),
(252, 'ZA', 'south_africa', 'South Africa'),
(253, 'ZM', 'zambia', 'Zambia'),
(254, 'ZR', 'congo_democratic_republic_of_the', 'Democratic Republic of the Congo (former Zaire)'),
(255, 'ZW', 'zimbabwe', 'Zimbabwe');