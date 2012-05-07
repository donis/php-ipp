<?php
/*
 * SERVER TEST, with Exceptions handling
 * Successfull at least with CUPS 1.2 and TRENDnet TE100-P1P
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

/*
*
* SETTINGS, YOU ARE PLEASED TO PLAY WITH THEM
*
*/

error_reporting(E_ALL|E_STRICT);

$debug = false; // false or 0 to 5 (5 = less verbose)

$srv="cups-local";
$srv="TE100";

$username="test";
if ($srv=="cups-local")
$password="verysecret";
if ($srv=="TE100")
$password=false; // set it if you need authentication to print

if ($srv=="cups-local")
$host="localhost";
if ($srv=="TE100")
$host="192.168.0.1";

if ($srv=="cups-local")
$printer="ipp://localhost:631/printers/reseauipp";
if ($srv=="TE100")
$printer="ipp://192.168.0.1:631/P1";

if ($srv=="cups-local")
$paths = false;
if ($srv=="TE100")
$paths = array ("root" => "/P1", "admin" => "/P1", "printers" => "/P1", "jobs" => "/P1");

$get_attributes = false;
#$get_attributes = true;

$print = false;
$print = true;

$port=631;

$logfile="/tmp/phpprintipp";
$handle_http_exceptions=false;
$handle_http_exceptions=true;
$handle_ipp_exceptions=false;
$handle_ipp_exceptions=true;
$data = "/home/tom/printpage_epson";
$mediatype="application/octet-stream";
$data = "test\r\nPortez ce whisky au vieux juge blond qui fume\r\n";//."";
$mediatype="text/plain";
/*
END OF SETTINGS
*/


if ($get_attributes)
  require_once('printipp/PrintIPP.php'); 
else
  require_once('printipp/BasicIPP.php'); 

echo memory_get_usage  (false),"\n";
echo memory_get_usage  (true),"\n";


//
if ($get_attributes)
  $ipp = new PrintIPP(); 
else
  $ipp = new BasicIPP();

$ipp->with_exceptions = $handle_ipp_exceptions;
$ipp->handle_http_exceptions = $handle_http_exceptions;
$ipp->setHost($host);
if ($paths) $ipp->paths = $paths;
$ipp->setLanguage("en-us");
  // various tests for ipv6 and SSL you can enable instead
	//$ipp->setHost("ip6-localhost");
	//$ipp->setHost("127.0.0.1");
	//$ipp->setHost("::1");
	//$ipp->ssl = 1;
	$ipp->setPort($port);
	
	//$ipp->setPort("65537"); // uncomment to generate http error


// set the value to your printer
$ipp->setPrinterURI($printer); 

$ipp->debug_level = $debug; // Debugging
$ipp->setLog($logfile,'file',$debug); // logging
$ipp->setUserName($username); // setting user name for server
if ($username && $password)
 $ipp->setAuthentication($username,$password);

/* printing an utf-8 file */
$ipp->setDocumentName("test");
$ipp->setCharset('utf-8');
$ipp->setMimeMediaType($mediatype);
$ipp->setData($data);//String or path to file.
$ipp->setAttribute("requested-attributes",
	array("copies-supported",
	      "document-format-supported",
	      "printer-is-accepting-jobs",
	      "printer-state",
	      "printer-state-reasons")
	);

try
{
  try
  {
  if ($get_attributes)
  printf (_("Get Printer Attributes: %s\n"), $ipp->getPrinterAttributes());
  var_dump ($ipp->printer_attributes);
  if ($print)
  printf(_("Job status: %s\n"), $ipp->printJob()); // Print job, display job status 
  }
  catch (httpException $e)
  {
    printf("%s\nerrno: %s\n",$e->getMessage(),$e->getErrno());
    trigger_error("I prefer to quit", E_USER_ERROR);
  }
}
catch (ippException $e)
{
  printf("%s\nerrno: %s\n",$e->getMessage(),$e->getErrno());
  trigger_error("I prefer to quit", E_USER_ERROR);
}
?>


<?php
if ($debug !== false)
  print $ipp->getDebug(); // display debugging output
?>

<?php
echo memory_get_usage  (false),"\n";
echo memory_get_usage  (true),"\n";

?>

END "SERVER" TEST
<?php exit (0) ?>
