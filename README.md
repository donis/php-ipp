php-ipp
=======

A clone of http://www.nongnu.org/phpprintipp/

Here is a new PHP class, that needs at least PHP5.
This class implements Internet Printing Protocol on client side.

COPYRIGHT
    Copyright (C) 2005-2008, Thomas Harding.

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

    thomas.harding@laposte.net
    Thomas Harding, 1 rue Raymond Vannier, 45000 ORLEANS, FRANCE

STATUS
    Currently, it is able to print on an IPP server, parses server's response,
    is able to cancel jobs, and do all REQUIRED and OPTIONAL operations from
    RFC2911.

    There is also a CUPS specific class, which performs CUPS specific
    operations.


LIMITATIONS
    - SSL/TLS works, but the integrated http backend is not capable of
      "upgrade".

DOCUMENTATION
    Complete documentation is in "./documentation", or in ./html,
    depending you use tarball or Debian package.

ORIGIN
    It is inspired by "PrintSendLPR", by Mick Sear, but there is no more code
    of it in. If you search for a very basic print client, uses it :)
    PrintSendLPR is available at http://www.phpclasses.org (license: LGPL).

TESTS
    Usage examples are given in "TEST_PrintIPP.php".

    You will find complete examples in ./examples or ./testfiles.

    Test _data_ files are distributed under GPL, but the aboves
    are under BSD License.

Enjoy !
--
Thomas Harding.


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/donis/php-ipp/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

