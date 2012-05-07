<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 syntax=php: */
/*
 *Example of use of PrintIPP
 *
 * Copyright(C) 2008 Thomas Harding
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 * 
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Thomas Harding nor the names of its
 *       contributors may be used to endorse or promote products derived from
 *       this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
 *   mailto:thomas.harding@laposte.net
 *
 */



///////////////////////////////////////
// S E T T I N G S
///////////////////////////////////////

error_reporting(E_ALL|E_STRICT);

putenv("TZ=GMT"); // get rid of stupids php warnings rules on date

$class_path="printipp/CupsPrintIPP.php";

$printer_uri="/printers/Parallel_Port_1"; // may be ipp://host:port/path/to/printer
$printer_uri="/printers/reseauipp"; // may be ipp://host:port/path/to/printer

$ipp_server="localhost";
$ssl=false;
//$ssl=true;
$port=false;
//$port=631;

$unix = false;
//$unix = true; // see below for use of unix socket
//$ipp_server="/var/run/cups/cups.sock"; // note this is the default

$user="guest"; // if you want to cancel job,
              // CUPS default policy (at least in Debian)
              // needs a user name from the system ("require @user @owner")
              // Note: replace "the system" by "/etc/cups/passwd.md5" if
              // your setup is "AuthType Digest"
//$user="test";

// beware to not set password if yo ask for
$password="";// need depends on your server policy
$askpass = true;

$banners=array(); // [CUPS] array("banner-before","banner-after");
//$banners=array('acctxt');
//$banners=array('accps');
//$banners=array('standard');

///////////////////////////////////////
// end of settings
///////////////////////////////////////

header("Content-Type: text/plain");
require_once($class_path);

function copyright() {

echo <<<FIN

Copyright (C) 2008 Thomas Harding <thomas.harding@laposte.net>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. Neither the name of his copyright holder nor the names of its
   contributors may be used to endorse or promote products derived from
   this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS ``AS IS'' AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED.  IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

FIN;
}

if (isset($_REQUEST['copyright']))
{
  copyright();
  exit(0);
}

//header("Content-Type: application/xhtml+xml");
header("Content-Type: text/html");

echo <<<FIN
<?xml version="1.0" encoding="utf-8"?>
FIN;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <meta name="Author" content="Thomas Harding" />
    <link rev="made" href="mailto:thomas.harding@laposte.net" />
    <style type="text/css">
    /* <![CDATA[ */
    
    body
    {
      font-family: sans-serif;
      padding: 5em;
      padding-top: 1em;
      background-color: #3e813b;
    }

    h1
    {
      color: #3b5e8a;
      padding: 1em;
      border-radius: 10px;
      -moz-border-radius: 10px;
       background-color: #4ea24a;
    }
    
    #logo
    {
      margin-bottom: -0.75em;
      margin-top: -0.5em;
    }

    div.form
    {
      padding: 3em;
      border-radius: 10px;
      -moz-border-radius: 10px;
      background-color: #76a06b;
      margin-bottom: 1em;
    }
    
    div.form div {
      padding: 10px;
    }

    div.form label
    { 
      color: #404075;
      font-style: bold;
      margin-right: 10px;
    }
    

    div.warning
    {
      text-align: center;
      color: red;
      background: #9da556;
      font-size: large;
      font-style: bold;
      padding: 2em;
      margin-bottom: 1em;
      border-radius: 10px;
      -moz-border-radius: 10px;
    }

    div.moreinfo
    {
      text-align: left;
      color: black;
      background: #9da556;
      padding: 2em;
      border-radius: 10px;
      -moz-border-radius: 10px;
    }

    div.moreinfo h2
    {
      color: #1f1ff5;
      font-size: large;
      font-style: bold;
    }

    #result
    {
      text-align: center;
      color: #1f1ff5;
      background: #9da556;
      font-size: large;
      font-style: bold;
      padding: 2em;
      border-radius: 10px;
      -moz-border-radius: 10px;
      margin-bottom: 1em;
    }

    p.footer
    {
      margin-bottom: 1em;
      text-align: center;
      font-family: sans-serif;
      font-size: 60%;
      color: black;
      background-color: #AAAAFF;
    }
 
    /* ]]> */
    </style>
    <title>PHP PrintIPP test page</title>
  </head>
  <body>
  <h1>PHP PrintIPP test page 
      <img
          id="logo"
          src="printipp-logo.png"
          alt=""
          /></h1>

<?php
  
  $printing = true;
  //
  // FORM HANDLING
  //

  if (!isset($_REQUEST['submit'])) $printing = false;

  // job-billing
  if (isset($_REQUEST['submit']))
  {
    $job_billing = $_REQUEST['job-billing'];
    $job_billing = stripslashes($job_billing);
    $job_billing = preg_replace("#[^\w.: ()\/&@]#um"," ",$job_billing);
    $job_billing = substr($job_billing,0,20);
  }
  
  if (!isset($job_billing) || ($job_billing == ""))
    $job_billing = "php PrintIPP";


  if (isset($_REQUEST['submit']))
    if  (is_uploaded_file($_FILES['file']['tmp_name']))
    {
        $file = $_FILES['file']['tmp_name'];
    }
    else
    {
      if ($printing)
        echo '<div class="warning">No file provided</div>',"\n";
      $printing = false;
    }

  if (isset($_REQUEST['submit']))
    switch ($_REQUEST["format"])
    {
      case "ostream":
        $format="";
        break;
      case "html":
        $format="text/html";
        break;
      case "plain":
        $format="text/plain";
        break;
      case "shell":
        $format="application/x-shell";
        break;
      case "perl":
        $format="application/x-perl";
        break;
      case "raw":
        $format="application/vnd.cups-raw";
        break;
      default:
        if ($printing)
          echo '  <div class="warning">You maybe jocking?</div>',"\n";
        $printing = false;
        break;
    }
  if (isset($_REQUEST['submit']))
  {
    if (isset($_REQUEST['user']))
      $user = substr($_REQUEST['user'],0,80);
    if (isset($_REQUEST['password']))
      $password = substr($_REQUEST['password'],0,80);
  }
?>

  <div class="form">
    <form
        id="printform"
        method="POST"
        enctype="multipart/form-data"
        accept-charset="utf-8"
        action="<?php echo $_SERVER['PHP_SELF'] ?>" >
<?php
    if ($askpass)
    echo <<<FIN
      <div>
      <label
        for="user"
        accesskey="U">USER</label>
      <input
        type="text"
        name="user"
        id="user"
        value="$user" />
      </div>
      <div>
      <label
        for="password"
        accesskey="P">PASSWORD</label>
      <input
        type="password"
        name="password"
        id="password"
        value="$password" />
      </div>

FIN;
?>
      <div>
      <label
        for="job-billing"
        accesskey="B">JOB-BILLING</label>
      <input
        type="text"
        name="job-billing"
        id="job-billing"
        value="<?php echo $job_billing ?>" />
      </div>
      <div>
      <label
        for="file"
        accesskey="F">FILE</label>
      <input
        type="file"
        name="file"
        id="file" />
      </div>
      <div>
      <label
        for="format"
        accesskey="O">FORMAT (optionnal)</label>
      <select
        name="format"
        id="format">
        <option value="ostream">guess</option>
        <option value="html">html</option>
        <option value="plain">plain text</option>
        <option value="shell">shell script</option>
        <option value="perl">perl script</option>
        <option value="raw">raw text or printer's language</option>
        <option value="x-foobar">x-foobar</option>
      </select>
      </div>
      <div>
      <input type="submit" name="submit" />
      </div>
    </form>
  </div>
<?php
if (!isset($_REQUEST['submit']))
  echo <<<FIN
      <div>
      <p class="footer">Copyright © 2008 Thomas Harding.
            <br />
            All rights reserved.
            <br />
            Copying and distribution of this program can be made under BSD License.
            see
            <a href="{$_SERVER['PHP_SELF']}?copyright=1">COPYING</a>.
      </p>
      </div>
FIN;

?>
<?
  if (!$printing)
  {
    echo "</body>\n</html>\n";
    exit(1);
  }
  //
  // let's start with IPP
  //
  $ipp = new CupsPrintIPP();

  $ipp->setHost($ipp_server);
  if ($unix)                    // if we use unix sockets
    $ipp->setUnix($ipp_server); // $ipp_server = "/path/to/socket"
  if ($port && (intval($port) != 1))
    $ipp->setPort(intval($port));
  if ($ssl)
    $ipp->ssl = true;
  if (!$printer_uri)
  {
    echo '<div class="warning">
      Please set printer uri in file ',
      $_SERVER['PHP_SELF'],
      '</div>',"\n";
    echo "</body>\n</html>\n";
    exit(1);
  }
  $ipp->setPrinterURI($printer_uri);

  if ($user && $password)
    $ipp->setAuthentication($user,$password);
  
  if ($user)
    $ipp->setUserName($user);
  
  /*
  //just to test administratives operations
  //require a user from "SystemGroup"
  echo $ipp->pausePrinter();
  echo $ipp->resumePrinter();
  */

  $ipp->setAttribute('job-sheets',$banners);
  $ipp->setAttribute('job-billing',$job_billing);

  $ipp->setData($file);
  $ipp->setDocumentName("test file printed from web page");
  if ($format)
    $ipp->setMimeMediaType($format);

  $result = $ipp->printJob();
  $job = $ipp->last_job;
  if ($job)
  {
    $attributes = array(
                        "job_originating_host_name",
                        "job_originating_user_name",
                        "job_id",
                        "job_printer_uri",
                        "job_name",
                        "job_state",
                        "job_state_reasons",
                        "time_at_creation",
                        "time_at_completed",
                        "job_media_sheets_completed",
                        "job_billing",
                        "document_name",
                        "document_format",
                        );
    $jobattributes = $ipp->getJobAttributes($job);
    /*
    echo "<pre>";
    print_r($ipp->job_attributes);
    echo "</pre>";
    */
    $job_attributes = array();
    if (isset($ipp->job_attributes) && ($jobattributes =="successfull-ok"))
    foreach ($attributes as $attribute)
    {
      if (isset ($ipp->job_attributes->$attribute))
      {
        $job_attributes[$attribute] = $ipp->job_attributes->$attribute->_value0;
        if (($attribute == 'time_at_creation') or ($attribute == 'time_at_completed'))
        {
          if ($job_attributes[$attribute] == "")
            $job_attributes[$attribute] = "unknown";
          else
            $job_attributes[$attribute] = date('Y-m-d H:i:s',intval($job_attributes[$attribute]));
        }
      }
      else
      {
        $job_attributes[$attribute] = "unknown";
      }
    }
  }
  echo '<div id="result">result of your command: ',$result,"</div>\n";
  echo '<div class="moreinfo">',"\n<h2>details</h2>\n";
  if (isset($job_attributes))
    foreach($job_attributes as $key => $value)
      printf("%s = %s<br />\n",$key,$value);
  echo "</div>\n";
  @unlink($file);

?>

  </body>
</html>
