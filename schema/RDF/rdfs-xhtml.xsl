<?xml version="1.0" encoding="UTF-8"?>
<!--
	# A somewhat generic RFDS to XHTML presentation conversion (0.3).

	# (c) 2003-2004 Morten Frederiksen
	# License: http://www.gnu.org/licenses/gpl
-->
<!DOCTYPE xsl:stylesheet [
	<!ENTITY foaf 'http://xmlns.com/foaf/0.1/'>
	<!ENTITY dcterms 'http://purl.org/dc/terms/'>
	<!ENTITY dc 'http://purl.org/dc/elements/1.1/'>
	<!ENTITY cyc 'http://opencyc.sourceforge.net/daml/cyc.daml#'>
	<!ENTITY owl 'http://www.w3.org/2002/07/owl#'>
	<!ENTITY daml 'http://www.daml.org/2001/03/daml+oil#'>
	<!ENTITY rdfs 'http://www.w3.org/2000/01/rdf-schema#'>
	<!ENTITY rdf 'http://www.w3.org/1999/02/22-rdf-syntax-ns#'>
]>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml" xmlns:foaf="http://xmlns.com/foaf/0.1/" xmlns:owl="http://www.w3.org/2002/07/owl#" xmlns:daml="http://www.daml.org/2001/03/daml+oil#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" exclude-result-prefixes="owl daml foaf rdf rdfs dc dcterms #default" version="1.0">
<xsl:output method="xml" indent="no" omit-xml-declaration="yes" encoding="utf-8"/>

<xsl:param name="embed" select="false()"/>
<xsl:param name="uri" select="'?'"/>
<xsl:param name="language" select="'da'"/>

<xsl:template match="/">
	<xsl:choose>
		<xsl:when test="not($embed)">
			<html>
			<head>
				<title>RDF Schema</title>
				<meta http-equiv="Content-Type" content="text/xhtml+xml; charset=utf-8"/>
				<style type="text/css">
body { font-family:"Trebuchet MS","Bitstream Vera Sans",verdana,lucida,arial,helvetica,sans-serif; background-color: f0f0f0 }
p { margin: 0; padding: 0 }
p.meta { font-size: 80% }
table.schema { width: 100% }
.schema h1+p { margin: 1em 0 }
.schema h4 { margin: 0 }
.schema h3 { padding-top: 1em }
.schema th { text-align: left; vertical-align: top }
.schema td { vertical-align: top }
.schema .details th { text-align: right; font-size: 80%; font-style: italic; vertical-align: top }
.schema .details td { font-size: 80% }
img { border: none; }
a , a:link{
background-color:transparent;
color:black;
text-decoration:none;
padding: 0 2px;
}
a:hover, #content a:hover { 
text-decoration:none; 
-moz-border-radius-bottomleft:5px;
-moz-border-radius-bottomright:5px;
-moz-border-radius-topleft:5px;
-moz-border-radius-topright:5px;
-moz-box-shadow:0 0 2px black;
}
				</style>
			</head>
			<body>
				<xsl:apply-templates select="//rdf:RDF[1]"/>
			</body>
			</html>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="//rdf:RDF[1]"/>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:if test="not(//rdf:RDF)">
		<xsl:message terminate="yes">RDF not found</xsl:message>
	</xsl:if>
</xsl:template>

<xsl:template match="rdf:RDF">
	<xsl:variable name="nodelang">
		<xsl:choose>
			<xsl:when test="@xml:lang">
				<xsl:value-of select="@xml:lang"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="'en'"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:variable name="ns">
		<xsl:choose>
			<xsl:when test="owl:Ontology[@rdf:about!='']">
				<xsl:apply-templates mode="schema-namespace" select="owl:Ontology[@rdf:about!=''][1]"/>
			</xsl:when>
			<xsl:when test="*[@rdf:about=$uri or @rdf:about=concat($uri,'#') or @rdf:about='' or @about=$uri or @about=concat($uri,'#') or @about='']">
				<xsl:apply-templates mode="schema-namespace" select="*[@rdf:about=$uri or @rdf:about=concat($uri,'#') or @rdf:about='' or @about=$uri or @about=concat($uri,'#') or @about='']"/>
			</xsl:when>
			<xsl:when test="rdf:Description">
				<xsl:apply-templates mode="schema-namespace" select="rdf:Description[1]"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$uri"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<div class="schema">
		<xsl:choose>
			<xsl:when test="*[@rdf:about=$ns or @rdf:about=$uri or @rdf:about=concat($uri,'#') or @rdf:about='' or @about=$uri or @about=concat($uri,'#') or @about='']">
				<xsl:apply-templates mode="description" select="*[@rdf:about=$ns or @rdf:about=$uri or @rdf:about=concat($uri,'#') or @rdf:about='' or @about=$uri or @about=concat($uri,'#') or @about='']">
					<xsl:with-param name="ns" select="$ns"/>
					<xsl:with-param name="lang" select="$nodelang"/>
				</xsl:apply-templates>
			</xsl:when>
			<xsl:when test="owl:Ontology">
				<xsl:apply-templates mode="description" select="owl:Ontology[1]">
					<xsl:with-param name="ns" select="$ns"/>
					<xsl:with-param name="lang" select="$nodelang"/>
				</xsl:apply-templates>
			</xsl:when>
			<xsl:when test="rdf:Description">
				<xsl:apply-templates mode="description" select="rdf:Description[1]">
					<xsl:with-param name="ns" select="$ns"/>
					<xsl:with-param name="lang" select="$nodelang"/>
				</xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates mode="schema-title" select=".">
					<xsl:with-param name="lang" select="$nodelang"/>
				</xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</div>
	<xsl:apply-templates mode="schema" select=".">
		<xsl:with-param name="ns" select="$ns"/>
		<xsl:with-param name="lang" select="$nodelang"/>
	</xsl:apply-templates>
</xsl:template>

<xsl:template mode="description" match="rdf:RDF/*">
	<xsl:param name="ns" select="'./'"/>
	<xsl:param name="lang" select="'en'"/>
	<xsl:variable name="nodelang">
		<xsl:choose>
			<xsl:when test="@xml:lang">
				<xsl:value-of select="@xml:lang"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$lang"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:apply-templates mode="schema-title" select=".">
		<xsl:with-param name="lang" select="$nodelang"/>
	</xsl:apply-templates>
	<xsl:choose>
		<xsl:when test="dc:description|dcterms:abstract">
			<p>
				<xsl:apply-templates mode="value" select="dc:description|dcterms:abstract">
					<xsl:with-param name="lang" select="$nodelang"/>
				</xsl:apply-templates>
			</p>
		</xsl:when>
		<xsl:when test="rdfs:comment">
			<p>
				<xsl:apply-templates mode="value" select="rdfs:comment">
					<xsl:with-param name="lang" select="$nodelang"/>
				</xsl:apply-templates>
			</p>
		</xsl:when>
	</xsl:choose>
	<xsl:apply-templates mode="meta" select="*">
		<xsl:with-param name="lang" select="$nodelang"/>
		<xsl:with-param name="ns" select="$ns"/>
	</xsl:apply-templates>
</xsl:template>

<xsl:template mode="schema-title" match="*">
	<xsl:param name="lang" select="'en'"/>
	<h1>
		<xsl:choose>
			<xsl:when test="dc:title">
				<xsl:apply-templates mode="value" select="dc:title">
					<xsl:with-param name="lang" select="$lang"/>
				</xsl:apply-templates>
			</xsl:when>
			<xsl:when test="rdfs:label">
				<xsl:apply-templates mode="value" select="rdfs:label">
					<xsl:with-param name="lang" select="$lang"/>
				</xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>RDF Schema</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</h1>
</xsl:template>

<xsl:template mode="meta" priority="0.9" match="dc:title|dc:description|dcterms:abstract">
</xsl:template>

<xsl:template mode="meta" priority="0.2" match="*[@rdf:resource or @resource]">
	<xsl:param name="ns" select="'./'"/>
	<p class="meta">
		<xsl:apply-templates mode="label" select="."/>
		<xsl:apply-templates mode="value" select=".">
			<xsl:with-param name="ns" select="$ns"/>
		</xsl:apply-templates>
	</p>
</xsl:template>

<xsl:template mode="meta" priority="0.1" match="*">
	<xsl:param name="ns" select="'./'"/>
	<p class="meta">
		<xsl:apply-templates mode="label" select="."/>
		<xsl:choose>
			<xsl:when test="foaf:Person or foaf:Organization">
				<xsl:apply-templates mode="foaf" select="foaf:*"/>
			</xsl:when>
			<xsl:when test="@rdf:parseType='Resource' and foaf:*">
				<xsl:apply-templates mode="foaf" select="."/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates mode="meta" select="*|@rdf:resource|@resource|@rdf:value|@value|text()">
					<xsl:with-param name="ns" select="$ns"/>
				</xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</p>
</xsl:template>

<xsl:template mode="foaf" match="foaf:Person|foaf:person|foaf:Organization|*[foaf:*]">
	<xsl:if test="rdfs:seeAlso[1]/@rdf:resource">
		<xsl:apply-templates mode="explorer" select="rdfs:seeAlso[1]"/>
		<xsl:value-of select="' '"/>
	</xsl:if>
	<xsl:choose>
		<xsl:when test="foaf:homepage[1]/@rdf:resource">
			<a href="http://weborganics.co.uk//stylesheets/%7Bfoaf:homepage%5B1%5D/@rdf:resource%7D">
				<xsl:value-of select="foaf:name|@foaf:name"/>
			</a>
		</xsl:when>
		<xsl:when test="foaf:weblog[1]/@rdf:resource">
			<a href="http://weborganics.co.uk//stylesheets/%7Bfoaf:weblog%5B1%5D/@rdf:resource%7D">
				<xsl:value-of select="foaf:name|@foaf:name"/>
			</a>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="foaf:name|@foaf:name"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template mode="explorer" match="rdfs:seeAlso">
	<a>
		<xsl:attribute name="href">
			<xsl:value-of select="'http://xml.mfd-consult.dk/foaf/explorer/?foaf='"/>
			<xsl:call-template name="ampify">
				<xsl:with-param name="text" select="@rdf:resource"/>
			</xsl:call-template>
		</xsl:attribute>
		<xsl:attribute name="title">
			<xsl:value-of select="'FoaF Explorer: '"/>
			<xsl:value-of select="../foaf:name|../@foaf:name"/>
		</xsl:attribute>
		<xsl:element name="img">
			<xsl:attribute name="style">
				<xsl:value-of select="'vertical-align: bottom'"/>
			</xsl:attribute>
			<xsl:attribute name="src">
				<xsl:value-of select="'/stylesheets/foaf-explorer.16.png'"/>
			</xsl:attribute>
			<xsl:attribute name="alt">
				<xsl:value-of select="'[FoaF]'"/>
			</xsl:attribute>
		</xsl:element>
	</a>
</xsl:template>

<xsl:template mode="label" match="*">
	<em title="{namespace-uri()}{local-name()}">
		<xsl:value-of select="local-name()"/>
		<xsl:value-of select="': '"/>
	</em>
</xsl:template>

<xsl:template mode="schema-namespace" match="*">
	<xsl:choose>
		<xsl:when test="string-length(@rdf:about)!=0 and @rdf:about!='#' or string-length(@about)!=0 and @about!='#'">
			<xsl:value-of select="@rdf:about|@about"/>
		</xsl:when>
		<xsl:when test="string-length(@xml:base)!=0">
			<xsl:value-of select="@xml:base"/>
			<xsl:if test="not(contains(@xml:base,'#')) and substring(@xml:base,string-length(@xml:base),1)!='/'">
				<xsl:text>#</xsl:text>
			</xsl:if>
		</xsl:when>
		<xsl:when test="string-length(../@xml:base)!=0">
			<xsl:value-of select="../@xml:base"/>
			<xsl:if test="not(contains(../@xml:base,'#')) and substring(../@xml:base,string-length(../@xml:base),1)!='/'">
				<xsl:text>#</xsl:text>
			</xsl:if>
		</xsl:when>
		<xsl:when test="string-length($uri)!=0">
			<xsl:value-of select="$uri"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="'?'"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template mode="icon" match="*">
	<xsl:param name="ns" select="'./'"/>
	<a href="http://weborganics.co.uk//stylesheets/%7B$ns%7D">
		<xsl:element name="img">
			<xsl:attribute name="src">
				<xsl:value-of select="'/stylesheets/rdf-schema.png'"/>
			</xsl:attribute>
			<xsl:attribute name="style">
				<xsl:value-of select="'float: right; margin: 0.2em; padding: 0'"/>
			</xsl:attribute>
			<xsl:attribute name="alt">
				<xsl:value-of select="'[RDF Schema]'"/>
			</xsl:attribute>
		</xsl:element>
	</a>
</xsl:template>

<xsl:template mode="schema" match="rdf:RDF">
	<xsl:param name="ns" select="'./'"/>
	<xsl:param name="lang" select="'en'"/>
	<xsl:variable name="nodelang" select="$lang"/>
	<table class="schema">
		<tr>
			<th colspan="3">
				<xsl:if test="string-length($ns)!=0">
					<xsl:apply-templates mode="icon" select=".">
						<xsl:with-param name="ns" select="$ns"/>
					</xsl:apply-templates>
				</xsl:if>
				<h2>
					<xsl:text>Namespace: </xsl:text>
					<xsl:value-of select="$ns"/>
				</h2>
			</th>
		</tr>
		<xsl:variable name="classes">
			<xsl:apply-templates mode="type" select="rdfs:Class|daml:Class|owl:Class|*[      rdf:type/@rdf:resource='http://www.w3.org/2000/01/rdf-schema#Class'      or rdf:type/@resource='http://www.w3.org/2000/01/rdf-schema#Class'      or rdf:type/@rdf:resource='http://www.daml.org/2001/03/daml+oil#Class'      or rdf:type/@resource='http://www.daml.org/2001/03/daml+oil#Class'      or starts-with(@rdf:about,$ns)      and string-length(@rdf:about) &gt; string-length($ns)      and rdfs:subClassOf]">
				<xsl:with-param name="lang" select="$nodelang"/>
				<xsl:with-param name="ns" select="$ns"/>
			</xsl:apply-templates>
		</xsl:variable>
		<xsl:if test="string-length($classes)!=0">
			<tr>
				<th colspan="3">
					<h3>
						<xsl:text>Classes</xsl:text>
					</h3>
				</th>
			</tr>
			<xsl:apply-templates mode="type" select="rdfs:Class|daml:Class|owl:Class|*[      rdf:type/@rdf:resource='http://www.w3.org/2000/01/rdf-schema#Class'      or rdf:type/@resource='http://www.w3.org/2000/01/rdf-schema#Class'      or rdf:type/@rdf:resource='http://www.daml.org/2001/03/daml+oil#Class'      or rdf:type/@resource='http://www.daml.org/2001/03/daml+oil#Class'      or starts-with(@rdf:about,$ns)      and string-length(@rdf:about) &gt; string-length($ns)      and rdfs:subClassOf]">
				<xsl:with-param name="lang" select="$nodelang"/>
				<xsl:with-param name="ns" select="$ns"/>
			</xsl:apply-templates>
		</xsl:if>
		<xsl:variable name="properties">
			<xsl:apply-templates mode="type" select="rdf:Property|daml:Property|owl:ObjectProperty|owl:DatatypeProperty|*[      rdf:type/@rdf:resource='http://www.w3.org/1999/02/22-rdf-syntax-ns#Property'      or rdf:type/@resource='http://www.w3.org/1999/02/22-rdf-syntax-ns#Property'      or rdf:type/@rdf:resource='http://www.daml.org/2001/03/daml+oil#Property'      or rdf:type/@resource='http://www.daml.org/2001/03/daml+oil#Property'      or rdf:type/@rdf:resource='http://www.w3.org/2002/07/owl#ObjectProperty'      or rdf:type/@resource='http://www.w3.org/2002/07/owl#ObjectProperty'      or rdf:type/@rdf:resource='http://www.w3.org/2002/07/owl#DatatypeProperty'      or rdf:type/@resource='http://www.w3.org/2002/07/owl#DatatypeProperty'      or starts-with(@rdf:about,$ns)      and string-length(@rdf:about) &gt; string-length($ns)      and (rdfs:subPropertyOf or rdfs:domain or rdfs:range)]">
				<xsl:with-param name="lang" select="$nodelang"/>
				<xsl:with-param name="ns" select="$ns"/>
			</xsl:apply-templates>
		</xsl:variable>
		<xsl:if test="string-length($properties)">
			<tr>
				<th colspan="3">
					<h3>
						<xsl:text>Properties</xsl:text>
					</h3>
				</th>
			</tr>
			<xsl:apply-templates mode="type" select="rdf:Property|daml:Property|owl:ObjectProperty|owl:DatatypeProperty|*[      rdf:type/@rdf:resource='http://www.w3.org/1999/02/22-rdf-syntax-ns#Property'      or rdf:type/@resource='http://www.w3.org/1999/02/22-rdf-syntax-ns#Property'      or rdf:type/@rdf:resource='http://www.daml.org/2001/03/daml+oil#Property'      or rdf:type/@resource='http://www.daml.org/2001/03/daml+oil#Property'      or rdf:type/@rdf:resource='http://www.w3.org/2002/07/owl#ObjectProperty'      or rdf:type/@resource='http://www.w3.org/2002/07/owl#ObjectProperty'      or rdf:type/@rdf:resource='http://www.w3.org/2002/07/owl#DatatypeProperty'      or rdf:type/@resource='http://www.w3.org/2002/07/owl#DatatypeProperty'      or starts-with(@rdf:about,$ns)      and string-length(@rdf:about) &gt; string-length($ns)      and (rdfs:subPropertyOf or rdfs:domain or rdfs:range)]">
				<xsl:with-param name="lang" select="$nodelang"/>
				<xsl:with-param name="ns" select="$ns"/>
			</xsl:apply-templates>
		</xsl:if>
	</table>
</xsl:template>

<xsl:template mode="type" match="*">
	<xsl:param name="ns" select="'./'"/>
	<xsl:param name="lang" select="'en'"/>
	<xsl:variable name="nodelang">
		<xsl:choose>
			<xsl:when test="@xml:lang">
				<xsl:value-of select="@xml:lang"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$lang"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:variable name="id">
		<xsl:apply-templates mode="id" select=".">
			<xsl:with-param name="ns" select="$ns"/>
		</xsl:apply-templates>
	</xsl:variable>
	<tr>
		<th rowspan="{count(*)-count(rdfs:label|rdfs:comment|rdf:type[     @rdf:resource='http://www.w3.org/2000/01/rdf-schema#Class'     or @resource='http://www.w3.org/2000/01/rdf-schema#Class'     or @rdf:resource='http://www.daml.org/2001/03/daml+oil#Class'     or @resource='http://www.daml.org/2001/03/daml+oil#Class'     or @rdf:resource='http://www.w3.org/2000/01/rdf-schema#Property'     or @resource='http://www.w3.org/2000/01/rdf-schema#Property'     or @rdf:resource='http://www.daml.org/2001/03/daml+oil#Property'     or @resource='http://www.daml.org/2001/03/daml+oil#Property'     or @rdf:resource='http://www.w3.org/2002/07/owl#ObjectProperty'     or @resource='http://www.w3.org/2002/07/owl#ObjectProperty'     or @rdf:resource='http://www.w3.org/2002/07/owl#DatatypeProperty'     or @resource='http://www.w3.org/2002/07/owl#DatatypeProperty'     or starts-with(@rdf:about,$ns)     and string-length(@rdf:about) &gt; string-length($ns)     and (rdfs:subClassOf or rdfs:subPropertyOf or rdfs:domain or rdfs:range)]) + 1}">
			<h4>
				<a name="{$id}">
					<xsl:choose>
						<xsl:when test="rdfs:label|@rdfs:label">
							<xsl:apply-templates mode="value" select="rdfs:label|@rdfs:label">
								<xsl:with-param name="lang" select="$nodelang"/>
							</xsl:apply-templates>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="$id"/>
						</xsl:otherwise>
					</xsl:choose>
				</a>
			</h4>
			<span style="font-size: 80%;">
				<xsl:value-of select="'['"/>
				<xsl:if test="not(starts-with($id,'http:')) and substring($ns,string-length($ns))!='/' and substring($ns,string-length($ns))!='#'">
					<xsl:value-of select="'#'"/>
				</xsl:if>
				<xsl:value-of select="$id"/>
				<xsl:value-of select="']'"/>
			</span>
		</th>
		<td colspan="2">
			<xsl:apply-templates mode="value" select="rdfs:comment|@rdfs:comment">
				<xsl:with-param name="lang" select="$nodelang"/>
			</xsl:apply-templates>
		</td>
	</tr>
	<xsl:apply-templates mode="details" select="*">
		<xsl:with-param name="lang" select="$nodelang"/>
		<xsl:with-param name="ns" select="$ns"/>
	</xsl:apply-templates>
</xsl:template>

<xsl:template mode="id" match="*">
	<xsl:param name="ns" select="'./'"/>
	<xsl:choose>
		<xsl:when test="@rdf:ID">
			<xsl:value-of select="@rdf:ID"/>
		</xsl:when>
		<xsl:when test="@ID">
			<xsl:value-of select="@ID"/>
		</xsl:when>
		<xsl:when test="@rdf:about and starts-with(@rdf:about,concat($ns,'#'))">
			<xsl:value-of select="substring-after(@rdf:about,concat($ns,'#'))"/>
		</xsl:when>
		<xsl:when test="@about and starts-with(@about,concat($ns,'#'))">
			<xsl:value-of select="substring-after(@about,concat($ns,'#'))"/>
		</xsl:when>
		<xsl:when test="@rdf:about and starts-with(@rdf:about,$ns)">
			<xsl:value-of select="substring-after(@rdf:about,$ns)"/>
		</xsl:when>
		<xsl:when test="@about and starts-with(@about,$ns)">
			<xsl:value-of select="substring-after(@about,$ns)"/>
		</xsl:when>
		<xsl:when test="@rdf:about and starts-with(@rdf:about,'http:')">
			<xsl:value-of select="@rdf:about"/>
		</xsl:when>
		<xsl:when test="@about and starts-with(@about,'http:')">
			<xsl:value-of select="@about"/>
		</xsl:when>
		<xsl:when test="@rdf:about and starts-with(@rdf:about,'.')">
			<xsl:call-template name="basepath">
				<xsl:with-param name="path" select="$ns"/>
			</xsl:call-template>
			<xsl:value-of select="@rdf:about"/>
		</xsl:when>
		<xsl:when test="@about and starts-with(@about,'.')">
			<xsl:call-template name="basepath">
				<xsl:with-param name="path" select="$ns"/>
			</xsl:call-template>
			<xsl:value-of select="@about"/>
		</xsl:when>
		<xsl:when test="@rdf:about and not(contains(@rdf:about,'/'))">
			<xsl:value-of select="@rdf:about"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$ns"/>
			<xsl:value-of select="@rdf:about|@about"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template mode="details" match="rdfs:label|rdfs:comment|rdf:type[     @rdf:resource='http://www.w3.org/2000/01/rdf-schema#Class'     or @resource='http://www.w3.org/2000/01/rdf-schema#Class'     or @rdf:resource='http://www.daml.org/2001/03/daml+oil#Class'     or @resource='http://www.daml.org/2001/03/daml+oil#Class'     or @rdf:resource='http://www.w3.org/2000/01/rdf-schema#Property'     or @resource='http://www.w3.org/2000/01/rdf-schema#Property'     or @rdf:resource='http://www.daml.org/2001/03/daml+oil#Property'     or @resource='http://www.daml.org/2001/03/daml+oil#Property'     or @rdf:resource='http://www.w3.org/2002/07/owl#ObjectProperty'     or @resource='http://www.w3.org/2002/07/owl#ObjectProperty'     or @rdf:resource='http://www.w3.org/2002/07/owl#DatatypeProperty'     or @resource='http://www.w3.org/2002/07/owl#DatatypeProperty']">
</xsl:template>

<xsl:template mode="details" match="*">
	<xsl:param name="ns" select="'./'"/>
	<xsl:param name="lang" select="'en'"/>
	<xsl:variable name="nodelang">
		<xsl:choose>
			<xsl:when test="@xml:lang">
				<xsl:value-of select="@xml:lang"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$lang"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<tr class="details">
		<th>
			<span title="{concat(namespace-uri(),local-name())}">
				<xsl:value-of select="local-name()"/>
			</span>
			<xsl:value-of select="':'"/>
		</th>
		<td>
			<xsl:apply-templates mode="value" select=".">
				<xsl:with-param name="lang" select="$nodelang"/>
				<xsl:with-param name="ns" select="$ns"/>
			</xsl:apply-templates>
		</td>
	</tr>
</xsl:template>

<xsl:template mode="value" match="*[@rdf:resource|@resource|@rdf:about|@about]">
	<xsl:param name="ns" select="'./'"/>
	<xsl:apply-templates mode="rdf-image" select=".">
		<xsl:with-param name="ns" select="$ns"/>
	</xsl:apply-templates>
	<xsl:variable name="res">
		<xsl:apply-templates mode="uri" select="@rdf:resource|@resource|@rdf:about|@about">
			<xsl:with-param name="ns" select="$ns"/>
		</xsl:apply-templates>
	</xsl:variable>
	<a href="http://weborganics.co.uk//stylesheets/%7B$res%7D">
		<xsl:value-of select="$res"/>
	</a>
</xsl:template>

<xsl:template mode="value" match="@*">
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template mode="value" match="*">
	<xsl:param name="lang" select="'en'"/>
	<xsl:variable name="nodelang">
		<xsl:choose>
			<xsl:when test="@xml:lang">
				<xsl:value-of select="@xml:lang"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$lang"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:variable name="nsname" select="concat(namespace-uri(),local-name())"/>
	<xsl:if test="namespace-uri()='http://opencyc.sourceforge.net/daml/cyc.daml#'">
		<xsl:text>From </xsl:text>
		<a href="http://www.cyc.com/cycdoc/vocab/vocab-toc.html">
			<xsl:text>OpenCyc</xsl:text>
		</a>
		<xsl:text>: </xsl:text>
	</xsl:if>
	<xsl:choose>
		<xsl:when test="count(../*[concat(namespace-uri(),local-name())=$nsname])=1 or $language=$nodelang">
			<xsl:value-of select=".|@rdf:value|@value"/>
		</xsl:when>
		<xsl:when test="count(../*[concat(namespace-uri(),local-name())=$nsname and (not(@xml:lang) or @xml:lang=$language)])=1">
		</xsl:when>
		<xsl:when test="starts-with($nodelang,'en')">
			<xsl:value-of select=".|@rdf:value|@value"/>
		</xsl:when>
		<xsl:when test="count(../*[concat(namespace-uri(),local-name())=$nsname and (@xml:lang and starts-with(@xml:lang,'en') or starts-with($lang,'en'))])=1">
		</xsl:when>
		<xsl:when test="position()=1">
			<xsl:value-of select=".|@rdf:value|@value"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template mode="value" match="*[@rdf:parseType='Resource']">
	<xsl:param name="ns" select="'./'"/>
	<dl>
		<dd>
			<dl>
				<xsl:apply-templates mode="intersection-property" select="*">
					<xsl:with-param name="ns" select="$ns"/>
				</xsl:apply-templates>
			</dl>
		</dd>
	</dl>
</xsl:template>

<xsl:template mode="value" match="owl:intersectionOf[@rdf:parseType='Collection']|owl:unionOf[@rdf:parseType='Collection']">
	<xsl:param name="ns" select="'./'"/>
	<dl>
		<xsl:apply-templates mode="intersection-object" select="*">
			<xsl:with-param name="ns" select="$ns"/>
		</xsl:apply-templates>
	</dl>
</xsl:template>

<xsl:template mode="intersection-object" match="*">
	<xsl:param name="ns" select="'./'"/>
	<dt>
		<span title="{concat(namespace-uri(),local-name())}">
			<xsl:value-of select="local-name()"/>
		</span>
		<xsl:text>:</xsl:text>
	</dt>
	<dd>
		<xsl:apply-templates mode="value" select="self::node()[@*]">
			<xsl:with-param name="ns" select="$ns"/>
		</xsl:apply-templates>
		<dl>
			<xsl:apply-templates mode="intersection-property" select="*">
				<xsl:with-param name="ns" select="$ns"/>
			</xsl:apply-templates>
		</dl>
	</dd>
</xsl:template>

<xsl:template mode="intersection-property" match="@*">
	<xsl:param name="ns" select="'./'"/>
	<dt>
		<xsl:apply-templates mode="value" select="..">
			<xsl:with-param name="ns" select="$ns"/>
		</xsl:apply-templates>
	</dt>
</xsl:template>

<xsl:template mode="intersection-property" match="*[@rdf:resource|@resource|@rdf:about|@about]">
	<xsl:param name="ns" select="'./'"/>
	<dt>
		<span title="{concat(namespace-uri(),local-name())}">
			<xsl:value-of select="local-name()"/>
		</span>
		<xsl:value-of select="':'"/>
	</dt>
	<dd>
		<xsl:apply-templates mode="value" select=".">
			<xsl:with-param name="ns" select="$ns"/>
		</xsl:apply-templates>
	</dd>
</xsl:template>

<xsl:template mode="intersection-property" match="*">
	<xsl:param name="ns" select="'./'"/>
	<dt>
		<span title="{concat(namespace-uri(),local-name())}">
			<xsl:value-of select="local-name()"/>
		</span>
		<xsl:value-of select="':'"/>
	</dt>
	<dd>
		<xsl:apply-templates mode="value" select=".">
			<xsl:with-param name="ns" select="$ns"/>
		</xsl:apply-templates>
	</dd>
</xsl:template>

<xsl:template mode="rdf-image" match="rdfs:seeAlso">
	<xsl:param name="ns" select="'./'"/>
	<a>
		<xsl:attribute name="href">
			<xsl:apply-templates mode="uri" select="@rdf:resource|@resource|@rdf:about|@about">
				<xsl:with-param name="ns" select="$ns"/>
			</xsl:apply-templates>
		</xsl:attribute>
		<xsl:element name="img">
			<xsl:attribute name="style">
				<xsl:value-of select="'vertical-align: middle'"/>
			</xsl:attribute>
			<xsl:attribute name="src">
				<xsl:value-of select="'/stylesheets/rdf_flyer.16.png'"/>
			</xsl:attribute>
			<xsl:attribute name="alt">
				<xsl:value-of select="'[RDF]'"/>
			</xsl:attribute>
		</xsl:element>
	</a>
	<xsl:value-of select="' '"/>
</xsl:template>

<xsl:template mode="rdf-image" match="*[starts-with(@rdf:resource,'http://opencyc.sourceforge.net/daml/cyc.daml#') or starts-with(@rdf:about,'http://opencyc.sourceforge.net/daml/cyc.daml#')]">
</xsl:template>

<xsl:template mode="rdf-image" match="owl:imports|daml:imports|rdfs:isDefinedBy">
	<xsl:param name="ns" select="'./'"/>
	<a>
		<xsl:attribute name="href">
			<xsl:value-of select="'http://xml.mfd-consult.dk/ws/2003/01/rdfs/?rdfs='"/>
			<xsl:value-of select="@rdf:resource|@resource|@rdf:about|@about"/>
		</xsl:attribute>
		<xsl:element name="img">
			<xsl:attribute name="style">
				<xsl:value-of select="'vertical-align: middle'"/>
			</xsl:attribute>
			<xsl:attribute name="src">
				<xsl:value-of select="'/stylesheets/rdfs-explorer.16.png'"/>
			</xsl:attribute>
			<xsl:attribute name="alt">
				<xsl:value-of select="'[RDFS Explorer]'"/>
			</xsl:attribute>
		</xsl:element>
	</a>
	<xsl:value-of select="' '"/>
</xsl:template>

<xsl:template mode="rdf-image" match="rdf:type|dcterms:references|rdfs:subPropertyOf|rdfs:subClassOf|rdfs:domain|rdfs:range|owl:equivalentClass|owl:disjointWith|owl:onProperty|owl:Class|owl:equivalentProperty|owl:complementOf|owl:inverseOf">
	<xsl:param name="ns" select="'./'"/>
	<a>
		<xsl:attribute name="href">
			<xsl:value-of select="'http://xml.mfd-consult.dk/ws/2003/01/rdfs/?rdfs='"/>
			<xsl:apply-templates mode="rdfs-uri" select="@rdf:resource|@resource|@rdf:about|@about">
				<xsl:with-param name="ns" select="$ns"/>
			</xsl:apply-templates>
		</xsl:attribute>
		<xsl:element name="img">
			<xsl:attribute name="style">
				<xsl:value-of select="'vertical-align: middle'"/>
			</xsl:attribute>
			<xsl:attribute name="src">
				<xsl:value-of select="'/stylesheets/rdfs-explorer.16.png'"/>
			</xsl:attribute>
			<xsl:attribute name="alt">
				<xsl:value-of select="'[RDFS Explorer]'"/>
			</xsl:attribute>
		</xsl:element>
	</a>
	<xsl:value-of select="' '"/>
</xsl:template>

<xsl:template mode="rdf-image" match="*">
</xsl:template>

<xsl:template mode="rdfs-uri" match="@*">
	<xsl:param name="ns" select="'./'"/>
	<xsl:choose>
		<xsl:when test="starts-with(.,'#')">
			<xsl:call-template name="ampify">
				<xsl:with-param name="text" select="$ns"/>
			</xsl:call-template>
			<xsl:value-of select="."/>
		</xsl:when>
		<xsl:when test="contains(.,'#') and string-length(substring-after(.,'#'))!=0">
			<xsl:call-template name="ampify">
				<xsl:with-param name="text" select="concat(substring-before(.,'#'),'#')"/>
			</xsl:call-template>
			<xsl:value-of select="'#'"/>
			<xsl:value-of select="substring-after(.,'#')"/>
		</xsl:when>
		<xsl:when test="contains(.,'#')">
			<xsl:call-template name="ampify">
				<xsl:with-param name="text" select="."/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:variable name="basepath">
				<xsl:call-template name="basepath">
					<xsl:with-param name="path" select="."/>
				</xsl:call-template>
			</xsl:variable>
			<xsl:call-template name="ampify">
				<xsl:with-param name="text" select="$basepath"/>
			</xsl:call-template>
			<xsl:value-of select="'#'"/>
			<xsl:value-of select="substring-after(.,$basepath)"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template mode="uri" match="@*">
	<xsl:param name="ns" select="'./'"/>
	<xsl:choose>
		<xsl:when test="contains(.,':')">
			<xsl:value-of select="."/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="basepath">
				<xsl:with-param name="path" select="$ns"/>
			</xsl:call-template>
			<xsl:value-of select="."/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="basepath">
	<xsl:param name="path" select="''"/>
	<xsl:choose>
		<xsl:when test="contains($path,'/')">
			<xsl:value-of select="substring-before($path,'/')"/>
			<xsl:value-of select="'/'"/>
			<xsl:call-template name="basepath">
				<xsl:with-param name="path" select="substring-after($path,'/')"/>
			</xsl:call-template>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template name="ampify">
	<xsl:param name="text" select="''"/>
	<xsl:choose>
		<xsl:when test="contains($text,'&amp;')">
			<xsl:call-template name="hashify">
				<xsl:with-param name="text" select="substring-before($text,'&amp;')"/>
			</xsl:call-template>
			<xsl:value-of select="'%26'"/>
			<xsl:call-template name="ampify">
				<xsl:with-param name="text" select="substring-after($text,'&amp;')"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="hashify">
				<xsl:with-param name="text" select="$text"/>
			</xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="hashify">
	<xsl:param name="text" select="''"/>
	<xsl:choose>
		<xsl:when test="contains($text,'#')">
			<xsl:call-template name="plusify">
				<xsl:with-param name="text" select="substring-before($text,'#')"/>
			</xsl:call-template>
			<xsl:value-of select="'%23'"/>
			<xsl:call-template name="hashify">
				<xsl:with-param name="text" select="substring-after($text,'#')"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="plusify">
				<xsl:with-param name="text" select="$text"/>
			</xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="plusify">
	<xsl:param name="text" select="''"/>
	<xsl:choose>
		<xsl:when test="contains($text,'+')">
			<xsl:value-of select="substring-before($text,'+')"/>
			<xsl:value-of select="'%2B'"/>
			<xsl:call-template name="plusify">
				<xsl:with-param name="text" select="substring-after($text,'+')"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$text"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>