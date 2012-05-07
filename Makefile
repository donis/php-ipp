INSTALL=/usr/bin/install
INSTALLdir = $(INSTALL) -d
DESTDIR=/usr/local/
progname=printipp
datarootdir=$(DESTDIR)$(prefix)/share/
bindir=$(DESTDIR)$(prefix)/bin
sysconfdir=$(DESTDIR)/etc

mandir=$(datarootdir)/man
docdir=$(datarootdir)/doc/$(progname)
sharedlibsdir = $(datarootdir)/php/$(progname)
bannersdir = $(datarootdir)/cups/banners
wwwdir = $(datarootdir)/$(progname)/

www_etc = $(sysconfdir)/apache2/sites-available


documentation = \
	phpprintipp.html \
	attributes.html \
	readable-attributes.html \
	CupsPrintIPP-usage.html \
	usage.html \
	rc_example \
	install.html \
	cups-attributes.html

offdoc = AUTHORS NEWS README

bin = phpprintipp

manpages = phpprintipp.1

banners = \
	txtbanner \
	psbanner

wwwpages = \
	basicpage.php \
	advancedpage.php \
	printipp-logo.png

samplescripts = \
	test_cli.php \
	test_exceptions.php \
	TEST_PrintIPP.php

sampledata = \
	COPYING \
	photo.jpg \
	printipp-logo.ps \
	printipp-logo.svg \
	README \
	test.pdf \
	test-pdf.ps \
	test.png \
	test.ps \
	test.txt \
	test-utf8-compressed.txt.gz \
	test-utf8.txt

man_MANS = $(manpages)

all: mans etc_apache_phpprintipp
	

clean:

install: install-data-local

install-data-local: install-shared install-bin \
	install-banners install-wwwpages


install-bin:
	$(INSTALLdir) $(bindir)
	for i in $(bin) ; do \
	$(INSTALL) -m 755 bin/$$i $(bindir)/$$i ;\
	done
	$(INSTALLdir) $(sysconfdir)/printipp

install-shared:
	$(INSTALLdir) $(sharedlibsdir)
	$(INSTALL) -m 644 php_classes/ExtendedPrintIPP.php \
	  $(sharedlibsdir)/ExtendedPrintIPP.php
	$(INSTALL) -m 644 php_classes/PrintIPP.php \
	  $(sharedlibsdir)/PrintIPP.php
	$(INSTALL) -m 644 php_classes/BasicIPP.php \
	  $(sharedlibsdir)/BasicIPP.php
	$(INSTALL) -m 644 php_classes/CupsPrintIPP.php \
	  $(sharedlibsdir)/CupsPrintIPP.php
	$(INSTALL) -m 644 php_classes/http_class.php \
	  $(sharedlibsdir)/http_class.php

install-doc:
	$(INSTALLdir) $(docdir)
	for i in $(documentation) ; do \
		install -m 644 documentation/$$i $(docdir)/$$i ;\
	done
	for i in $(offdoc) ; do \
	  if test -f $$i ; then \
	    install -m 644 $$i $(docdir)/$$i ;\
	  fi;\
	done


install-samples:
	$(INSTALLdir) $(docdir)/samples
	for i in $(samplescripts) ; do \
	  if test -f testfiles/$$i ; then \
	    install -m 644 testfiles/$$i $(docdir)/samples/$$i.src ;\
	  fi;\
	done
	for i in $(sampledata) ; do \
	  if test -f testfiles/$$i ; then \
	    install -m 644 testfiles/$$i $(docdir)/samples/$$i ;\
	  fi;\
	done

install-wwwpages: etc_apache_phpprintipp
	$(INSTALLdir) $(wwwdir)
	for i in $(wwwpages) ; do \
	  if test -f www/$$i ; then \
	    install -m 644 www/$$i $(wwwdir)/$$i ;\
	  fi;\
	done
	cd $(wwwdir) && rm -f index.php && ln -s advancedpage.php index.php
	$(INSTALLdir) $(www_etc)
	$(INSTALL) -m 644 etc_apache_phpprintipp \
	  $(www_etc)/phpprintipp
	$(INSTALLdir) $(sysconfdir)/printipp
	$(INSTALL) -m 644 www/www $(sysconfdir)/printipp/www \

install-banners:
	$(INSTALLdir) $(bannersdir)
	for i in $(banners) ; do \
	  if test -f banners/$$i ; then \
	    install -m 644 banners/$$i $(bannersdir)/$$i ;\
	  fi;\
	done

mans: phpprintipp.1

phpprintipp.1: documentation/phpprintipp-1.xml
	xsltproc \
	"http://docbook.sourceforge.net/release/xsl/current/manpages/docbook.xsl" \
	$<

etc_apache_phpprintipp: www/etc_apache_phpprintipp
	sed -e 's,WWWDIR,$(wwwdir),' www/etc_apache_phpprintipp > \
		etc_apache_phpprintipp
