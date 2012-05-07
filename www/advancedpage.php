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

$printer_uri="/printers/Parallel_Port_1"; // may be ipp://host:port/path/to/printer

$ipp_server="localhost";
$ssl=false;
//$ssl=true;
$port=false;
//$port=631;
$server_type = "unknown";
$unix = false;
//$unix="/var/run/cups/cups.sock"; // note this is the default

$user="guest"; // if you want to cancel job,
              // CUPS default policy (at least in Debian)
              // needs a user name from the system ("require @user @owner")
              // Note: replace "the system" by "/etc/cups/passwd.md5" if
              // your setup is "AuthType Digest"
$password=false;
$askpass = true;

$banners=array(); // [CUPS] array("banner-before","banner-after");
//$banners=array('acctxt');
//$banners=array('accps');
//$banners=array('standard');
$startbanner = 'txtbanner';

$conffiles = array(
    "/usr/local/etc/printipp/www",
    "/etc/printipp/www",
    "C:\\php\\etc\\printipp\\www"
    );

$class_path = false; // either false (=> auto or conffile)
                    //  or path to CupsPrintIPP.php or ExtendedPrintIPP.php                    
///////////////////////////////////////
// end of settings
///////////////////////////////////////

foreach ($conffiles as $conffile)
if (($conffile !== false) && file_exists($conffile))
{
    $conf = fopen($conffile,'r');
if (isset($conf))
 while ($line = fgetcsv( $conf, 1024, '=', '"' ))
 {
  if ((!empty($line[0])) && (strstr(trim($line[0]),'#') !== 1))
  {
    $var = trim($line[0]);
    $res = trim($line[1]);
    switch ($res)
    {
      case 'false':
        $$var = false;
        break;
      case 'true':
        $$var = true;
        break;
      case 'NULL':
        $$var = NULL;
        break;
      default:
        $$var = $res;
        break;
    }
    if ($var == 'paths' && $paths !== false)
    {
      $paths = array();
      list ($paths["root"],$paths["admin"],$paths["printers"],$paths["jobs"]) = explode(",",$res,4);
    }
  }
}
}
if (isset($server_type) && $server_type == 'CUPS' && empty($class_path))
  $class_path="printipp/CupsPrintIPP.php";
elseif (empty($class_path))
  $class_path="printipp/ExtendedPrintIPP.php";


header("Content-Type: text/plain");
require_once($class_path);
if ($server_type == 'CUPS')
  $ipp = new CupsPrintIPP();
else
  $ipp = new ExtendedPrintIPP();

$ipp->setHost($ipp_server);
if ($port && (intval($port) != 1))
  $ipp->setPort(intval($port));
if ($ssl)
  $ipp->ssl = true;
if ($unix !== false) // if we use unix sockets instead of http
  $ipp->setUnix($unix); // $unix = "/path/to/socket"

if (isset($_REQUEST['submit'])||isset($_REQUEST['cancel']))
{
    if (isset($_REQUEST['user']))
      $user = substr($_REQUEST['user'],0,80);
    if (isset($_REQUEST['password']))
      $password = substr($_REQUEST['password'],0,80);
    $error_forbidden = false;
    if (($askpass) && empty($password))
      $error_forbidden = true;
}


if ($user && ($password !== false))
  $ipp->setAuthentication($user,$password);

if ($user)
  $ipp->setUserName($user);

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

$banner_checked = "";
if (isset($_REQUEST['banner']))
{
  $banners = array($startbanner);
  $banner_checked = 'checked="checked"';
}

$printer_choice = "";
if ($server_type == 'CUPS')
{
$ipp->getPrinters();
$printer_list = array();
foreach ($ipp->available_printers AS $printer)
{
  $ipp->setPrinterURI($printer);
  $ipp->getPrinterAttributes();
  $printer_name = $ipp->printer_attributes->printer_name->_value0;
  $printer_list[$printer] = $printer_name;
}

if (count($printer_list)) {

    $printer_choice = "\n\n
                <div>
                <label
                for='printer'
                accesskey='P'>PRINTER</label>
                <select name='printer' id='printer'>\n";
    foreach($printer_list as $printer => $printer_name) {
        if (isset($_REQUEST['printer']) && $_REQUEST['printer'] == $printer) {
            $selected = "selected='1'";
            $printer_uri = $printer;
        } else
            $selected = "";
    $printer_choice .= "
                    <option value='$printer' $selected>$printer_name</option>";
    
    }
    $printer_choice .= "
                </select>
                </div>\n";
}
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
    <title>PHP PrintIPP advanced test page</title>
  </head>
  <body>
  <h1>PHP PrintIPP advanced test page 
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
  if (isset($_REQUEST['submit']) && isset($_REQUEST['job-billing']))
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
?>

  <div class="form">
    <form
        id="printform"
        method="POST"
        enctype="multipart/form-data"
        accept-charset="utf-8"
        action="<?php echo dirname($_SERVER['PHP_SELF']),'/advancedpage.php' ?>" >
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
        for="file"
        accesskey="F">FILE</label>
      <input
        type="file"
        name="file"
        id="file" />
      </div>
<?php
    if (isset($startbanner))
    echo <<<FIN
      <div>
      <label
        for="banner"
        accesskey="B">PRINT BANNER</label>
      <input 
        type="checkbox"
        name="banner"
        id="banner"
        value="1"
        $banner_checked
        />
      </div>
FIN;
?>
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
      <?php echo $printer_choice ?>
      <div>
      <input type="submit" name="submit" />
      </div>
    </form>
  </div>
<?php
if (!isset($_REQUEST['submit']) && !isset($_REQUEST['cancel']))
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
  $cancel = (isset($_REQUEST['cancel'])) ? $_REQUEST['cancel'] : false;

  if (!$printing && !$cancel)
  {
    echo "</body>\n</html>\n";
    exit(1);
  }
  if ($error_forbidden == true)
  {
  echo <<<FIN
  <div id="result">
  <p>Please set your username and password</p>
  </div>
FIN;
  exit(1);
  }

?>
  <div>
<?php
  //
  // let's start with IPP
  //
 
  if ($cancel)
  {
        $cancel = preg_replace("#[^\[\]a-zA-Z0-9/:%-_]#","",$cancel);
        $result = $ipp->cancelJob($cancel);
  }
  elseif($printing)
  {
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
 
  /*
  //just to test administratives operations
  //require a user from "SystemGroup"
  echo $ipp->pausePrinter();
  echo $ipp->resumePrinter();
  */

  $ipp->setAttribute('job-sheets',$banners);

  if ($server_type == "CUPS")
    $ipp->setAttribute('job-billing',$job_billing);

  $ipp->setData($file);
  $ipp->setDocumentName(substr(basename($_FILES['file']['name']),0,60));
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
  }
  echo <<<FIN
  </div>
  <div id="result">
  <p>result of your command: {$result}</p>
FIN;
  if ($printing && $job)
  {
    echo <<<FIN
  <p>
    <form
        id="cancelform"
        method="POST"
        accept-charset="ascii"
        action="{$_SERVER['PHP_SELF']}" >
      <input type="hidden" name="cancel" value="{$job}" />
      <input type="hidden" name="user" value="{$user}" />
      <input type="hidden" name="password" value="{$password}" />
      <input type="submit" name="foobar" value="cancel" />
    </form>
  </p>
FIN;
  }
  echo "</div>\n";
  if ($printing) {
  echo '<div class="moreinfo">',"\n<h2>details</h2>\n";
  if (isset($job_attributes))
    foreach($job_attributes as $key => $value)
      printf("%s = %s<br />\n",$key,$value);
  echo "</div>\n";
  @unlink($file);
  }

?>

  </body>
</html>
