<html>
  <head>
   <title>hello from {{ ansible_hostname }}</title>
   <style type="text/css">
     body {
       font-family: 'Helvetica', sans-serif;
     }
   </style>
  </head>
  <body>
  <h1>hello from {{ ansible_hostname }}!</h1>
  
  <!-- cows are the best at delivering good news -->
  <?php
    $output = shell_exec('echo "if you can see this, your secure apache webserver was installed successfully!" | cowsay');
    echo "<p><pre>$output</pre></p>";
  ?>


  <!-- let's just reassure the user that regular https is working --> 
  <p style="color:green;">https seems to be working!</p>


  <!-- is php enabled? -->
  <?php echo '<p style="color:green;">php seems to be working!</p>'; ?>


  <!-- is ssl enabled? -->
  <?php 
    if ($_SERVER['HTTPS']) { 
      echo '<p style="color:green;">looks like tls is enabled, too!  nice one.</p>'; 
    } else {
      echo '<p style="color:red;">uh oh, tls is not enabled!</p>';
    } 
  ?>


  <!-- has http port 80 been disabled? -->
  <?php
    $host       = 'localhost';
    $http_port  = '80';
    $connection = @fsockopen($host,$http_port);
    
    if (is_resource($connection)) {
      echo '<p style="color:red;">uh oh, looks like port 80 on the server is open...</p>';
    } else {
      echo '<p style="color:green;">looks like port 80 on the server is closed! great!</p>';
    }
  ?>

  </body>
</html>
