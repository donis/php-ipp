<?php
/*
 *Example of use of BasicIPP, with Exceptions handling
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


error_reporting(E_ALL|E_STRICT);

/*
*
* SETTINGS, YOU ARE PLEASED TO PLAY WITH THEM
*
*/
require_once('printipp/BasicIPP.php'); 

echo memory_get_usage  (false),"\n";
echo memory_get_usage  (true),"\n";

$debug = 3; // 0 to 4 (less verbose)
$host="localhost";
$printer="/printers/Parallel_Port_1";
$port=631;
$logfile="/tmp/phpprintipp";
$username="www-data";
$password=false; // set it if you need authentication to print
$handle_http_exceptions=false;
$handle_ipp_exceptions=false;
$mediatype="text/plain";
/*
END OF SETTINGS
*/


//
$ipp = new BasicIPP(); 
$ipp->with_exceptions = $handle_ipp_exceptions;
$ipp->handle_http_exceptions = $handle_http_exceptions;
$ipp->setHost($host);
  // various tests for ipv6 and SSL you can enable instead
	//$ipp->setHost("ip6-localhost");
	//$ipp->setHost("127.0.0.1");
	//$ipp->setHost("::1");
	//$ipp->ssl = 1;
	$ipp->setPort($port);
	
	//$ipp->setPort("65537"); // uncomment to generate http error


// set the value to your printer
$ipp->setPrinterURI($printer); 

//$ipp->setPrinterURI("/printers/this_one_not_exists"); // uncomment to generate error


$ipp->debug_level = $debug; // Debugging
$ipp->setLog($logfile,'file',$debug); // logging
$ipp->setUserName($username); // setting user name for server
if ($username && $password)
 $ipp->setAuthentication($username,$password);

/* printing an utf-8 file */
$ipp->setDocumentName("testfile with UTF-8 characters");
$ipp->setCharset('utf-8');
$ipp->setMimeMediaType($mediatype);
$ipp->setData("./test-utf8.txt");//Path to file.

//$ipp->setMimeMediaType('text/foobar'); // uncomment to generate error

$ipp->setAttribute('number-up',1); // pages per sheet
$ipp->setSides(1); // by default: 2 = two-sided-long-edge // other choices: 1 = one-sided // 2CE = two-sided-short-edge
try
{
  try
  {
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
if ($debug)
  print $ipp->getDebug(); // display debugging output
?>

<?php
echo memory_get_usage  (false),"\n";
echo memory_get_usage  (true),"\n";

?>

END "EXCEPTIONS" TEST
<?php exit (0) ?>
