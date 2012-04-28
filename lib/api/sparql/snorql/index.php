<?php
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>OpenRecommender - SPARQL Explorer</title>
	<!-- SNORQL - SPARQL Explorer -->    
    <script type="text/javascript" src="js/snorql/prototype.js"></script>
    <script type="text/javascript" src="js/snorql/scriptaculous/scriptaculous.js"></script>
    <script type="text/javascript" src="js/snorql/sparql.js"></script>
    <script type="text/javascript" src="js/snorql/namespaces.js"></script>
    <script type="text/javascript" src="js/snorql/snorql.js"></script>
	<link rel="stylesheet" type="text/css" href="css/snorql/style.css" />
  </head>
  <body class="snorql" onload="snorql.start()">
    <div id="header">
      <h1 id="title">OpenRecommender - Snorql + CodeMirror</h1>
    </div>
	<!-- Query browser -->
    <div class="section" style="float: right; width: 8em">
      <h2>Browse:</h2>
      <ul>
        <li><a class="graph-link" href="?browse=classes">Classes</a></li>
        <li><a class="graph-link" href="?browse=properties">Properties</a></li>
        <li id="browse-named-graphs-link"><a href="?browse=graphs">Named Graphs</a></li>
      </ul>
    </div>
	<!-- Graph browser -->
    <div id="default-graph-section" class="section" style="margin-right: 12em">
      <h2 style="display: inline">GRAPH:</h2>
      <p style="display: inline">
        Default graph.
        <a href="?browse=graphs">List named graphs</a>
      </p>
    </div>
	<!-- Post-processsing of Query Results -->
    <div id="named-graph-section" class="section" style="margin-right: 12em">
      <h2 style="display: inline">GRAPH:</h2>
      <p style="display: inline">
        <span id="selected-named-graph">Named graph goes here</span>.
        <a href="javascript:snorql.switchToDefaultGraph()">Switch back to default graph</a>
      </p>
    </div>
	<!-- Edit/Refine SPARQL query here -->
    <div class="section" style="margin-right: 12em">
      <h2>SPARQL:</h2>
      <pre id="prefixestext"></pre>
      <form id="queryform" action="#" method="get"><div>
        <input type="hidden" name="query" value="" id="query" />
        <input type="hidden" name="output" value="json" id="jsonoutput" disabled="disabled" />
        <input type="hidden" name="stylesheet" value="" id="stylesheet" disabled="disabled" />
        <input type="hidden" name="graph" value="" id="graph-uri" disabled="disabled" />
      </div></form>
      <div>
	    <!-- Highlight SPARQL text via CodeMirror -->
        <textarea id="querytext" name="query" rows="9" cols="80"></textarea>
		<!-- Result format selection -->
        Results:
        <select id="selectoutput" onchange="snorql.updateOutputMode()">
          <option selected="selected" value="browse">Browse</option>
          <option value="json">as JSON</option>
          <option value="xml">as XML</option>
          <option value="xslt">as XML+XSLT</option>
        </select>
		<!-- Post-processsing of Query Results -->
        <span id="xsltcontainer"><span id="xsltinput">
          XSLT stylesheet URL:
          <input id="xsltstylesheet" type="text" value="xsl/xml-to-html.xsl" size="30" />
        </span></span>
        <input type="button" value="Go!" onclick="snorql.submitQuery()" />
        <input type="button" value="Reset" onclick="snorql.resetQuery()" />
      </div>
    </div>
	<!-- Display SPARQL results here -->
    <div class="section">
      <div id="result"><span></span></div>
    </div>
	<!-- Footer -->
    <div id="footer">Powered by <a id="poweredby" href="https://github.com/kurtjx/SNORQL" target="_blank">Snorql</a> + <a href="http://codemirror.net" target="_blank">CodeMirror</a></div>
	<!-- CodeMirror - SPARQL syntax highlighter -->
	<script type="text/javascript" src="js/codemirror.js"></script>
    <link rel="stylesheet" type="text/css" href="css/codemirror.css"/>
	<script type="text/javascript">
	  var editor = CodeMirror.fromTextArea('querytext', {
		height: "250px",
		parserfile: "parsesparql.js",
		stylesheet: "css/sparqlcolors.css",
		path: "js/"
	  });
	</script>
  </body>
</html>
