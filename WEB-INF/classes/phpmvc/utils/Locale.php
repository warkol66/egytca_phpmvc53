<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/Locale.php,v 1.3 2006/02/22 08:16:06 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:16:06 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2002-2006 John C.Wildenauer.  All rights reserved.
*
* This file is part of the php.MVC Web applications framework
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.

* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.

* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/**
* i18n locales
* <p>Locales represent a specific country and culture. Classes which can be
* passed a Locale object tailor their information for a given locale. For
* instance, currency number formatting is handled differently for the USA
* and France.
*
* <p>Locales are made up of a language code, a country code, and an optional
* set of variant strings. Language codes are represented by
* www.ics.uci.edu/pub/ietf/http/related/iso639.txt
* ISO 639:1988 w/ additions from ISO 639/RA Newsletter No. 1/1989
* and a decision of the Advisory Committee of ISO/TC39 on August 8, 1997.
*
* <p>Country codes are represented by
* www.chemie.fu-berlin.de/diverse/doc/ISO_3166.html ISO 3166. Variant strings 
* are vendor and browser specific.
* Standard variant strings include "POSIX" for POSIX, "WIN" for MS-Windows, and
* "MAC" for Macintosh. When there is more than one variant string, they must
* be separated by an underscore (U+005F).
*
* <p>The default locale is determined by the values of the system properties
* user.language, user.region, and user.variant, defaulting to "en".
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Jochen Hoenicke (GNU Classpath)<br>
*  Paul Fisher(GNU Classpath)<br>
*  Eric Blake <ebb9@email.byu.edu>(GNU Classpath)
* @version $Revision: 1.3 $
* @public
*/
class Locale {

	// ----- Instance Variables --------------------------------------------- //

	/** Locale which represents the English language.
	* @private
	* @type Locale
	*/
	var $ENGLISH	= NULL; // new Locale("en")

	/** Locale which represents the French language.
	* @private
	* @type Locale
	*/
	var $FRENCH		= NULL; // new Locale("fr");

	 /** Locale which represents the German language.
	* @private
	* @type Locale
	*/
	var $GERMAN		= NULL; // new Locale("de");

	/** Locale which represents the Italian language.
	* @private
	* @type Locale
	*/
	var $ITALIAN	= NULL; // new Locale("it");

	/** Locale which represents the Japanese language.
	* @private
	* @type Locale
	*/
	var $JAPANESE	= NULL; // new Locale("ja");

	/** Locale which represents the Korean language.
	* @private
	* @type Locale
	*/
	var $KOREAN		= NULL; // new Locale("ko");

	/** Locale which represents the Chinese language.
	* @private
	* @type Locale
	*/
	var $CHINESE	= NULL; // new Locale("zh");

	/** Locale which represents the Chinese language as used in China.
	* @private
	* @type Locale
	*/
	var $SIMPLIFIED_CHINESE = NULL; // new Locale("zh", "CN");

	/**
	* Locale which represents the Chinese language as used in Taiwan.
	* Same as TAIWAN Locale.
	* @private
	* @type Locale
	*/
	var $TRADITIONAL_CHINESE = NULL; // new Locale("zh", "TW");

	/** Locale which represents France.
	* @private
	* @type Locale
	*/
	var $FRANCE		= NULL; // new Locale("fr", "FR");

	/** Locale which represents Germany.
	* @private
	* @type Locale
	*/
	var $GERMANY	= NULL; // new Locale("de", "DE");

	/** Locale which represents Italy.
	* @private
	* @type Locale
	*/
	var $ITALY		= NULL; // new Locale("it", "IT");

	/** Locale which represents Japan.
	* @private
	* @type Locale
	*/
	var $JAPAN		= NULL; // new Locale("ja", "JP");

	/** Locale which represents Korea.
	* @private
	* @type Locale
	*/
	var $KOREA		= NULL; // new Locale("ko", "KR");

	/**
	* Locale which represents China.
	* Same as SIMPLIFIED_CHINESE Locale.
	* @private
	* @type Locale
	*/
	var $CHINA		= NULL; // SIMPLIFIED_CHINESE;

	/**
	* Locale which represents the People's Republic of China.
	* Same as CHINA Locale.
	* @private
	* @type Locale
	*/
	var $PRC			= NULL; // CHINA;

	/**
	* Locale which represents Taiwan.
	* Same as TRADITIONAL_CHINESE Locale.
	* @private
	* @type Locale
	*/
	var $TAIWAN		= NULL; // TRADITIONAL_CHINESE;

	/** Locale which represents the United Kingdom.
	* @private
	* @type Locale
	*/
	var $UK			= NULL; // new Locale("en", "GB");

	/** Locale which represents the United States.
	* @private
	* @type Locale
	*/
	var $US			= NULL; // new Locale("en", "US");

	/** Locale which represents the English speaking portion of Canada.
	* @private
	* @type Locale
	*/
	var $CANADA		= NULL; // new Locale("en", "CA");

	/** Locale which represents the French speaking portion of Canada.
	* @private
	* @type Locale
	*/
	var $CANADA_FRENCH = NULL; // new Locale("fr", "CA");

	/**
	* The language code, as returned by getLanguage().
	* @access private
	* @type string
	*/
	var $language;

	/**
	* The country code, as returned by getCountry().
	* @private
	* @type string
	*/
	var $country;

	/**
	* The variant code, as returned by getVariant().
	* @private
	* @type string
	*/
	var $variant;

	/**
	* This is the cached hashcode. When writing to stream, we write -1.
	* @private
	* @type int
	*/
	#var $hashcode;

	/**
	* The default AppServer locale. 
	* Except for during bootstrapping, this should never be null.
	* Note the logic in the main constructor, to detect when
	* bootstrapping has completed.
	* @private
	* @type Locale
	*/
	var $defaultLocale = NULL; // new Locale(lang, country, variant)


	// ----- Private Methods ---------------------------------------------- //

	/**
	* Convert new iso639 codes to the old ones.
	*
	* @param string	The language to check
	* @private
	* @returns string
	*/
	function convertLanguage($language) {

		// CHECK THIS

		if($language == '')
			return $language;

		$language = strtolower(language);

		$index = strpos("he,id,yi", $language); // find first match position
		if($index > 0)
			return substr("iw,in,ji", $index, $index + 2);

		return $language;

	}


	// ----- Constructors --------------------------------------------------- //

	/**
	* Creates a new locale for the given language and country.
	*
	* @param string	Lowercase two-letter ISO-639 A2 language code ("en")
	* @param string	Uppercase two-letter ISO-3166 A2 contry code ("AU")
	* @param string	Vendor and browser specific variant
	* @param Locale	The default AppServer locale.
	*/
	function Locale($language='', $country='', $variant='', $defaultLocale=NULL) {

		// !!!!!!!!!!!
		// During bootstrap, we already know the strings being passed in are
		// the correct capitalization, and not null. We can't call
		// String.toUpperCase during this time, since that depends on the
		// default locale.

		// Note1: To setup the AppServer default locale only, use
		// parameter $defaultLocale=NULL. This will be the AppServer default
		// locale object.
		// Note2: To setup a user locale. use the $language, $country and 
		// $variant parameters as required. And be sure to pass the AppServer
		// $defaultLocale object.

		// Convert new iso639 codes to the old ones !!!!!
		// $this->language= $this->convertLanguage($language);

		$this->defaultLocale = $defaultLocale;
		$this->language= strtolower($language);
		$this->country	= strtoupper($country);
		$this->variant	= strtoupper($variant);

		#$hashcode = language.hashCode() ^ country.hashCode() ^ variant.hashCode();

	}

	/**
	* Returns the default Locale. The default locale is generally once set
	* on start up and then never changed. Normally you should use this locale
	* for everywhere you need a locale. The initial setting matches the
	* default locale, the user has chosen.
	*
	* @public
	* @returns Locale
	*/
	function getDefault() {
		return $this->defaultLocale;
	}

	/**
	* Changes the default locale. Normally only called on program start up.
	* Note that this doesn't change the locale for other programs. This has
	* a security check,
	* <code>PropertyPermission("user.language", "write")</code>, because of
	* its potential impact to running code.
	*
	* @param Locale	The new default locale
	* @public
	* @returns void
	*/
	function setDefault($newLocale) {

		if($newLocale == NULL){
			return 'NullPointerException';
		}

		$this->defaultLocale = $newLocale;

	}

	/**
	* Returns the list of available locales.
	*
	* @public
	* @returns array
	*/
	function getAvailableLocales() {

		/* I only return those for which localized language
		* or country information exists.
		* XXX - remove hard coded list, and implement more locales (Sun's JDK 1.4
		* has 148 installed locales!).
		*/
		#return new Locale[] {ENGLISH, FRENCH, GERMAN, new Locale("ga", "")};

	}

	/**
	* Returns a String array of all 2-letter uppercase country codes as defined
	* in ISO 3166.
	*
	* @public
	* @returns array
	*/
	function getISOCountries() {

		return array(
			"AD", "AE", "AF", "AG", "AI", "AL", "AM", "AN", "AO", "AQ", "AR", "AS",
			"AT", "AU", "AW", "AZ", "BA", "BB", "BD", "BE", "BF", "BG", "BH", "BI",
			"BJ", "BM", "BN", "BO", "BR", "BS", "BT", "BV", "BW", "BY", "BZ", "CA",
			"CC", "CF", "CG", "CH", "CI", "CK", "CL", "CM", "CN", "CO", "CR", "CU",
			"CV", "CX", "CY", "CZ", "DE", "DJ", "DK", "DM", "DO", "DZ", "EC", "EE",
			"EG", "EH", "ER", "ES", "ET", "FI", "FJ", "FK", "FM", "FO", "FR", "FX",
			"GA", "GB", "GD", "GE", "GF", "GH", "GI", "GL", "GM", "GN", "GP", "GQ",
			"GR", "GS", "GT", "GU", "GW", "GY", "HK", "HM", "HN", "HR", "HT", "HU",
			"ID", "IE", "IL", "IN", "IO", "IQ", "IR", "IS", "IT", "JM", "JO", "JP",
			"KE", "KG", "KH", "KI", "KM", "KN", "KP", "KR", "KW", "KY", "KZ", "LA",
			"LB", "LC", "LI", "LK", "LR", "LS", "LT", "LU", "LV", "LY", "MA", "MC",
			"MD", "MG", "MH", "MK", "ML", "MM", "MN", "MO", "MP", "MQ", "MR", "MS",
			"MT", "MU", "MV", "MW", "MX", "MY", "MZ", "NA", "NC", "NE", "NF", "NG",
			"NI", "NL", "NO", "NP", "NR", "NU", "NZ", "OM", "PA", "PE", "PF", "PG",
			"PH", "PK", "PL", "PM", "PN", "PR", "PT", "PW", "PY", "QA", "RE", "RO",
			"RU", "RW", "SA", "SB", "SC", "SD", "SE", "SG", "SH", "SI", "SJ", "SK",
			"SL", "SM", "SN", "SO", "SR", "ST", "SV", "SY", "SZ", "TC", "TD", "TF",
			"TG", "TH", "TJ", "TK", "TM", "TN", "TO", "TP", "TR", "TT", "TV", "TW",
			"TZ", "UA", "UG", "UM", "US", "UY", "UZ", "VA", "VC", "VE", "VG", "VI",
			"VN", "VU", "WF", "WS", "YE", "YT", "YU", "ZA", "ZM", "ZR", "ZW"
	 	);
	}

	/**
	* Returns a String array of all 2-letter lowercase language codes as defined
	* in ISO 639 (both old and new variant).
	*
	* @public
	* @returns array
	*/
	function getISOLanguages() {

		return array(
			"aa", "ab", "af", "am", "ar", "as", "ay", "az", "ba", "be", "bg", "bh",
			"bi", "bn", "bo", "br", "ca", "co", "cs", "cy", "da", "de", "dz", "el",
			"en", "eo", "es", "et", "eu", "fa", "fi", "fj", "fo", "fr", "fy", "ga",
			"gd", "gl", "gn", "gu", "ha", "he", "hi", "hr", "hu", "hy", "ia", "id",
			"ie", "ik", "in", "is", "it", "iu", "iw", "ja", "ji", "jw", "ka", "kk",
			"kl", "km", "kn", "ko", "ks", "ku", "ky", "la", "ln", "lo", "lt", "lv",
			"mg", "mi", "mk", "ml", "mn", "mo", "mr", "ms", "mt", "my", "na", "ne",
			"nl", "no", "oc", "om", "or", "pa", "pl", "ps", "pt", "qu", "rm", "rn",
			"ro", "ru", "rw", "sa", "sd", "sg", "sh", "si", "sk", "sl", "sm", "sn",
			"so", "sq", "sr", "ss", "st", "su", "sv", "sw", "ta", "te", "tg", "th",
			"ti", "tk", "tl", "tn", "to", "tr", "ts", "tt", "tw", "ug", "uk", "ur",
			"uz", "vi", "vo", "wo", "xh", "yi", "yo", "za", "zh", "zu"
		);

	}

	/**
	* Returns the language code of this locale ("en"), or an empty String. Some
	* language codes have changed as ISO 639 has evolved; this returns the old
	* name, even if you built the locale with the new one.
	* 
	*
	* @public
	* @returns string
	*/
	function getLanguage() {
		return $this->language;
	}

	/**
	* Returns the country code of this locale ("AU"), or an empty String.
	*
	* @public
	* @returns string
	*/
	function getCountry() {
		return $this->country;
	}

	/**
	* Returns the variant code of this locale, or an empty String.
	*
	* @public
	* @returns string
	*/
	function getVariant() {
		return $this->variant;
	}

	/**
	* Gets the string representation of the current locale. This consists of
	* the language, the country, and the variant, separated by an underscore.
	* The variant is listed only if there is a language or country. Examples:
	* "en", "de_DE", "_GB", "en_US_WIN", "de__POSIX", "fr__MAC".
	*
	* @public
	* @returns string
	* @see getDisplayName(var)
	*/
	function toString() {

		// We need a language and/or a country to proceed
		if ( (strlen($this->language) == 0) && (strlen($this->country) == 0) )
			return '';

		$localeStr = '';

		// JCW: Modified this method output
		$l_ = '';
		$c_ = '';
		if(strlen($this->language) != 0) {
			$localeStr .= $this->language;
			$l_ = '_';
		}

		if(strlen($this->country) != 0) {
			$localeStr .= $l_;
			$localeStr .= $this->country;
			$c_ = '_';
		}

		if(strlen($this->variant) != 0)
			$localeStr .= $c_;
			$localeStr .= $this->variant;

		return $localeStr;

	}

	/**
	* Returns the three-letter ISO language abbrevation of this locale.
	*
	* @public
	* @returns string
	*/
	function getISO3Language() {
		//
	}

	/**
	* Returns the three-letter ISO country abbrevation of the locale.
	*
	* @public
	* @returns string
	*/
	function getISO3Country() {
		//
	}

	/**
	* Gets the country name suitable for display to the user, formatted
	* for the default locale.  This has the same effect as
	* <pre>
	* getDisplayLanguage(Locale::getDefault());
	* </pre>
	*
	* <p>Returns the language name of this locale localized to the default 
	* locale, with the ISO code as backup
	*
	* @public
	* @returns string
	*/
	#function getDisplayLanguage() {
	#	return getDisplayLanguage($this->defaultLocale);
	#}

	/**
	* Gets the language name suitable for display to the user, formatted
	* for a specified locale.
	*
	* <p>Returns the language name of this locale localized to the given locale,
	* with the ISO code as backup
	*
	* @param Locale	The locale to use for formatting
	* @public
	* @returns string
	*/
	function getDisplayLanguage($locale=NULL) {

		if($locale == NULL)
			$locale = $this->defaultLocale;

		// Try 
		#ResourceBundle bundle = ResourceBundle.getBundle("gnu.java.locale.iso639", locale);
		#return bundle.getString(language);

		// Catch MissingResourceException ex
		return $this->language;

	}

	/**
	* Returns the country name of this locale localized to the
	* default locale. If the localized is not found, the ISO code
	* is returned. This has the same effect as
	* <pre>
	* getDisplayCountry(Locale::getDefault());
	* </pre>
	*
	* <p>Returns the country name of this locale localized to the given
	* locale, with the ISO code as backup
	*
	* @param Locale	The locale locale to use for formatting
	* @public
	* @returns string
	*/
	function getDisplayCountry($locale=NULL) {

		if($locale == NULL)
			return $this->getDisplayCountry($this->defaultLocale);

		// Try
		#ResourceBundle bundle = ResourceBundle.getBundle("gnu.java.locale.iso3166", locale);
		#return bundle.getString(country);

		// Catch MissingResourceException
		return $this->country;

	}

	/**
	* Returns the variant name of this locale localized to the
	* given locale. If the localized is not found, the variant code
	* itself is returned.
	*
	* @param Locale	THe locale to use for formatting
	* @public
	* @returns string
	*/
	function getDisplayVariant($locale=NULL) {

		if($locale == NULL)
			return $this->getDisplayVariant($this->defaultLocale);

		// XXX - load a bundle?
		return $this->variant;
	}

	/**
	* Gets all local components suitable for display to the user, formatted
	* for a specified locale. For the language component,
	* getDisplayLanguage(Locale) is called. For the country component,
	* getDisplayCountry(Locale) is called. For the variant set component,
	* getDisplayVariant(Locale) is called.
	*
	* <p>The returned String will be one of the following forms:<br>
	* <pre>
	* language (country, variant)
	* language (country)
	* language (variant)
	* country (variant)
	* language
	* country
	* variant
	* </pre>
	*
	* @param Locale	The locale to use for formatting
	* @public
	* @returns string
	*/
	function getDisplayName($locale=NULL) {

		if($locale == NULL)
			$locale = $this->defaultLocale;

		$result = '';
		$count = 0;
		$delimiters = array('', ' (', ','); // String[]
		if(strlen($this->language) != 0) {
			$result .= $delimiters[$count++];
			$result .= $this->getDisplayLanguage($locale);
		}
		if(strlen($this->country) != 0) {
			$result .= $delimiters[$count++];
			$result .= $this->getDisplayCountry($locale);
		}
		if(strlen($this->variant) != 0) {
			$result .= $delimiters[$count++];
			$result .= $this->getDisplayVariant($locale);
		}
		if($count > 1)
			$result .= ')';

		return $result;

	}

	/**
	* Return the hash code for this locale. The hashcode is the logical
	* xor of the hash codes of the language, the country and the variant.
	* The hash code is precomputed, since <code>Locale</code>s are often
	* used in hash tables.
	*
	* @public
	* @returns int
	*/
	function hashCode() {
		// This method is synchronized because writeObject() might reset
		// the hashcode.
		#return $this->hashcode;
	}

	/**
	* Compares two locales. To be equal, obj must be a Locale with the same
	* language, country, and variant code.
	* <p>Returns True if obj is equal to $this
	*
	* @param object	The other locale
	* @public
	* @returns boolean
	*/
	function equals($obj) {

		//  $obj instanceof Locale !!!!
		// PHP 4.2.0 ( is_a($obj, get_class($this) )
		if( get_class($obj) == get_class($this) )
			return False;

		// We should have a Locale class object
		$l = $obj; // Locale 
		return ($this->language == $l->language)
					&& ($this->country == $l->country)
					&& ($this->variant == $l->variant);
	}
}
?>