<html>
  <head>
    <title>OpenSocial PHP Client Library Examples</title>
  </head>
  <body>
    <h1>OpenSocial PHP Client Library Examples</h1>
    <p>The following examples show how to use the client library with live OpenSocial containers.  You can find the source for
       each in the <code>examples</code> directory in the client library distribution.</p>
    <p><strong>Note:</strong> Since these examples use live OpenSocial data, you may see error messages reported, depending on
       whether the site requested has implemented the feature demonstrated in the example.</p>
    <p><strong>Note:</strong> Some tests use 3-legged OAuth, meaning that you may be redirected to a page to enter your
       social network credentials.  Once you enter your credentials, you will be redirected back to the sample, and
       the sample will run with your own data from the social network.  The OpenSocial client libraries do <u>not</u>
       have access to your password, and do not store the information returned by the queries for these examples.</p>
    <h2>List of samples</h2>
    <ul>
      <li><a href="listFriends.php">People</a></li>
      <li><a href="activities.php">Activities</a></li>
      <li><a href="appData.php">App Data</a></li>
      <li><a href="messages.php">Messages</a></li>
      <li><a href="listMethods.php">system.listMethods</a> (only supported on 0.9 based sites)</li>
      <li><a href="albums.php">Albums</a> (only supported on 0.9 based sites)</li>
      <li><a href="mediaItems.php">MediaItems</a> (only supported on 0.9 based sites)</li>
      <li><a href="groups.php">Groups</a> (only supported on 0.9 based sites)</li>
      
    </ul>
    
    <h2>MySpace Extensions</h2>
    <ul>
        <li><a href="statusmood.php">StatusMood</a></li>
        <li><a href="notifications.php">Notifications</a></li>
        <li><a href="profileComments.php">Profile Comments</a></li>
    </ul>
  </body>
</html>
