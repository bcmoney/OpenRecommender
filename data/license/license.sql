CREATE TABLE `license` (
      `license_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,                        
	  `license_code` VARCHAR(60) DEFAULT NULL,
	  `license_name` VARCHAR(60) DEFAULT NULL,
	  `license_link` varchar(255) DEFAULT NULL  
);

-- Specification/Documentation Licenses --
INSERT INTO license VALUES (0, 'FreeBSD','FreeBSD Documentation License','http://www.freebsd.org/copyright/freebsd-doc-license.html');
INSERT INTO license VALUES (0, 'GFDL','GNU Free Documentation License','http://www.gnu.org/copyleft/fdl.html');
INSERT INTO license VALUES (0, 'GSFDL','GNU Simpler Free Documentation License','http://gplv3.fsf.org/sfdl-dd1.txt');
INSERT INTO license VALUES (0, 'IETF TLP','Internet Engineering Task Force - Trust Legal Provisions (TLP) Documents','http://trustee.ietf.org/license-info/');
INSERT INTO license VALUES (0, 'IEE','IEEE Publication Usage License','http://www.ieee.org/publications_standards/publications/subscriptions/info/licensing.html');
INSERT INTO license VALUES (0, 'OWFa','Open Web Foundation','http://www.openwebfoundation.org/legal/the-owf-1-0-agreements/owf-contributor-license-agreement-1-0---copyright-and-patent');
INSERT INTO license VALUES (0, 'W3C-D','W3C Document License','http://www.w3.org/Consortium/Legal/2002/copyright-documents-20021231');

-- Content Licenses --
-- http://en.wikipedia.org/wiki/List_of_free_content_licenses --
-- http://creativecommons.org/licenses/ --
INSERT INTO license VALUES (0, 'AgainstDRM','Against DRM license','http://www.freecreations.org/Against_DRM2.html');
INSERT INTO license VALUES (0, 'CC BY','Creative Commons: Attribution alone','http://creativecommons.org/licenses/by/3.0/');
INSERT INTO license VALUES (0, 'CC BY-NC','Creative Commons: Attribution + Noncommercial','http://creativecommons.org/licenses/by-nc/3.0/');
INSERT INTO license VALUES (0, 'CC BY-ND','Creative Commons: Attribution + NoDerivatives','http://creativecommons.org/licenses/by-nd/3.0/');
INSERT INTO license VALUES (0, 'CC BY-SA','Creative Commons: Attribution + ShareAlike','http://creativecommons.org/licenses/by-sa/3.0/');
INSERT INTO license VALUES (0, 'CC BY-NC-ND','Creative Commons: Attribution + Noncommercial + NoDerivatives','http://creativecommons.org/licenses/by-nc-nd/3.0/');
INSERT INTO license VALUES (0, 'CC BY-NC-SA','Creative Commons: Attribution + Noncommercial + ShareAlike','http://creativecommons.org/licenses/by-nc-sa/3.0/');
INSERT INTO license VALUES (0, 'CC Zero','Creative Commons 0: Public Domain','http://creativecommons.org/publicdomain/zero/1.0/');
INSERT INTO license VALUES (0, 'DSL','Design Science License','http://www.gnu.org/licenses/dsl.html');
INSERT INTO license VALUES (0, 'DRL','Dominion Rules Licence','http://en.wikipedia.org/wiki/Dominion_Rules_Licence');
INSERT INTO license VALUES (0, 'EUPL','European Union Public Licence','http://www.osor.eu/eupl/european-union-public-licence-eupl-v.1.1');
INSERT INTO license VALUES (0, 'FAL','Free Art License','http://artlibre.org/licence/lal/en');
INSERT INTO license VALUES (0, 'GPL','GNU General Public License','http://www.gnu.org/licenses/gpl.html');
INSERT INTO license VALUES (0, 'MirOS (C)','MirOS Licence for Content','http://www.opensource.org/licenses/miros.html');
INSERT INTO license VALUES (0, 'OpenGov', 'Open Government License for Public Information', 'http://www.nationalarchives.gov.uk/doc/open-government-licence/');
INSERT INTO license VALUES (0, 'OPL','Open Publication License','http://www.opencontent.org/openpub/');
INSERT INTO license VALUES (0, 'TAPR','TAPR Open Hardware License','http://www.tapr.org/TAPR_Open_Hardware_License_v1.0.txt');

-- Software Licenses: OpenSource --
-- http://en.wikipedia.org/wiki/Comparison_of_free_software_licences --
-- http://www.opensource.org/licenses/alphabetical --
--  http://www.opensource.org/licenses/<LICENSE_ID> --
--                  LICENSE_ID,License Name
INSERT INTO license VALUES (0, 'afl-3.0','Academic Free License 3.0 (AFL 3.0)','http://www.opensource.org/licenses/afl-3.0');
INSERT INTO license VALUES (0, 'agpl-v3','Affero GNU Public License','http://www.opensource.org/licenses/agpl-v3');
INSERT INTO license VALUES (0, 'apl-1.0','Adaptive Public License','http://www.opensource.org/licenses/apl-1.0');
INSERT INTO license VALUES (0, 'apache2.0','Apache License, 2.0','http://www.opensource.org/licenses/apache2.0');
INSERT INTO license VALUES (0, 'apsl-2.0','Apple Public Source License','http://www.opensource.org/licenses/apsl-2.0');
INSERT INTO license VALUES (0, 'artistic-license-2.0','Artistic license 2.0','http://www.opensource.org/licenses/artistic-license-2.0');
INSERT INTO license VALUES (0, 'attribution','Attribution Assurance Licenses','http://www.opensource.org/licenses/attribution');
INSERT INTO license VALUES (0, 'bsd-license','BSD licenses (New and Simplified)','http://www.opensource.org/licenses/bsd-license');
INSERT INTO license VALUES (0, 'bsl1.0','Boost Software License (BSL1.0)','http://www.opensource.org/licenses/bsl1.0');
INSERT INTO license VALUES (0, 'ca-tosl1.1','Computer Associates Trusted Open Source License 1.1','http://www.opensource.org/licenses/ca-tosl1.1');
INSERT INTO license VALUES (0, 'cddl1','Common Development and Distribution License','http://www.opensource.org/licenses/cddl1');
INSERT INTO license VALUES (0, 'cpal_1.0','Common Public Attribution License 1.0 (CPAL)','http://www.opensource.org/licenses/cpal_1.0');
INSERT INTO license VALUES (0, 'cuaoffice','CUA Office Public License Version 1.0','http://www.opensource.org/licenses/cuaoffice');
INSERT INTO license VALUES (0, 'eudatagrid','EU DataGrid Software License','http://www.opensource.org/licenses/eudatagrid');
INSERT INTO license VALUES (0, 'eclipse-1.0','Eclipse Public License','http://www.opensource.org/licenses/eclipse-1.0');
INSERT INTO license VALUES (0, 'ecl2','Educational Community License, Version 2.0','http://www.opensource.org/licenses/ecl2');
INSERT INTO license VALUES (0, 'ver2_eiffel','Eiffel Forum License V2.0','http://www.opensource.org/licenses/ver2_eiffel');
INSERT INTO license VALUES (0, 'entessa','Entessa Public License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'eupl-v.1.1','European Union Public License','http://ec.europa.eu/idabc/eupl');
INSERT INTO license VALUES (0, 'fair','Fair License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'frameworx','Frameworx License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'gpl-2.0','GNU General Public License version 2.0 (GPLv2)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'gpl-3.0','GNU General Public License version 3.0 (GPLv3)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'lgpl-2.1','GNU Library or "Lesser" General Public License version 2.1 (LGPLv2.1)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'lgpl-3.0','GNU Library or "Lesser" General Public License version 3.0 (LGPLv3)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'historical','Historical Permission Notice and Disclaimer','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'ibmpl','IBM Public License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'ipafont','IPA Font License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'isc-license','ISC License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'lppl','LaTeX Project Public License (LPPL)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'lucent1.02','Lucent Public License Version 1.02','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'miros','MirOS Licence','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'ms-pl','Microsoft Public License (Ms-PL)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'ms-rl','Microsoft Reciprocal License (Ms-RL)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'mit-license','MIT license','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'motosoto','Motosoto License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'mozilla1.1','Mozilla Public License 1.1 (MPL)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'multics','Multics License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'nasa1.3','NASA Open Source Agreement 1.3','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'ntp-license','NTP License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'naumen','Naumen Public License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'nethack','Nethack General Public License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'nokia','Nokia Open Source License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'NOSL3.0','Non-Profit Open Software License 3.0 (Non-Profit OSL 3.0)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'oclc2','OCLC Research Public License 2.0','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'openfont','Open Font License 1.1 (OFL 1.1)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'opengroup','Open Group Test Suite License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'osl-3.0','Open Software License 3.0 (OSL 3.0)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'php','PHP License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'postgresql','The PostgreSQL License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'pythonpl','Python license (CNRI Python License)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'PythonSoftFoundation','Python Software Foundation License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'qtpl','Qt Public License (QPL)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'real','RealNetworks Public Source License V1.0','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'rpl1.5','Reciprocal Public License 1.5 (RPL1.5)','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'ricohpl','Ricoh Source Code Public License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'simpl-2.0','Simple Public License 2.0','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'sleepycat','Sleepycat License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'sunpublic','Sun Public License','http://www.opensource.org/licenses/');
INSERT INTO license VALUES (0, 'sybase','Sybase Open Watcom Public License 1.0','http://www.opensource.org/licenses/sybase');
INSERT INTO license VALUES (0, 'UoI-NCSA','University of Illinois/NCSA Open Source License','http://www.opensource.org/licenses/UoI-NCSA');
INSERT INTO license VALUES (0, 'vovidapl','Vovida Software License v. 1.0','http://www.opensource.org/licenses/vovidapl');
INSERT INTO license VALUES (0, 'W3C','W3C Software License','http://www.w3.org/Consortium/Legal/2002/copyright-software-20021231');
INSERT INTO license VALUES (0, 'wxwindows','wxWindows Library License','http://www.opensource.org/licenses/wxwindows');
INSERT INTO license VALUES (0, 'xnet','X.Net License','http://www.opensource.org/licenses/xnet');
INSERT INTO license VALUES (0, 'zpl','Zope Public License','http://www.opensource.org/licenses/zpl');
INSERT INTO license VALUES (0, 'zlib-license','zlib/libpng license','http://www.opensource.org/licenses/zlib-license');