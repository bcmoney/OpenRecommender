<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>OpenRecommender - OpenTok video conferencing</title>
  <script src="http://staging.tokbox.com/v0.91/js/TB.min.js"></script>
</head>
<body>
  <div id="myPublisherDiv"></div>  
  <script type="text/javascript">
    var apiKey = '9801282';
    var sessionId = '1_MX4xMjMyMDgxfjcwLjQyLjQ3Ljc4fjIwMTEtMTItMTQgMDA6NDg6NDQuMDc2MjcwKzAwOjAwfjAuOTIxMzQzMzc4NTE0fg';
    var token = 'devtoken';
    
    TB.setLogLevel(TB.DEBUG);    
 
    var session = TB.initSession(sessionId);     
    session.addEventListener('sessionConnected', sessionConnectedHandler);
    session.addEventListener('streamCreated', streamCreatedHandler);     
    session.connect(apiKey, token);
 
    var publisher;
 
    function sessionConnectedHandler(event) {
      publisher = session.publish('myPublisherDiv');       
      subscribeToStreams(event.streams); // Subscribe to streams that were in the session when we connected
    }
     
    function streamCreatedHandler(event) {      
      subscribeToStreams(event.streams); // Subscribe to any new streams that are created
    }
     
    function subscribeToStreams(streams) {
      for (var i = 0; i < streams.length; i++) {
        // Make sure we don't subscribe to ourself
        if (streams[i].connection.connectionId == session.connection.connectionId) {
          return;
        }
 
        // Create the div to put the subscriber element in to
        var div = document.createElement('div');
        div.setAttribute('id', 'stream' + streams[i].streamId);
        document.body.appendChild(div);                                   
        session.subscribe(streams[i], div.id); // Subscribe to the stream
      }
    }
  </script>  
</body>
</html>