<?php
/*
 * #Id: ExpatParser.php,v 1.7 2003/04/12 13:55:59 purestorm Exp #
 * $Id: SaxParser.php,v 1.3 2006/02/22 08:48:27 who Exp $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://binarycloud.com/phing/>.
 */


/**
 * This class is a wrapper for the PHP's internal expat parser.
 *
 * It takes an XML file represented by a abstract path name, and starts
 * parsing the file and calling the different "trap" methods inherited from
 * the AbstractParser class.
 *
 * Those methods then invoke the represenatative methods in the registered
 * handler classes.
 *
 * Modified by Yannick Lecaillez (Adaptation for phpMVC Framework)
 *		  - Replace FileReader class by filename/fopen.
 *		  - Remove Exception
 *		  - Allow parsing String
 *
 * @author	  Andreas Aderhold <andi@binarycloud.com>
 * @author	  Yannick Lecaillez <yl@seasonfive.com>
 * @copyright © 2001,2002 THYRELL. All rights reserved
 * @version   $Revision: 1.3 $ $Date: 2006/02/22 08:48:27 $
 * @public
 * @package   phing.parser
 *
 *  * Modified by Yannick Lecaillez (Adaptation for phpMVC Framework)
 *                - Replace FileReader class by filename/fopen.
 *                - Remove Exception
 *                - Allow parsing String
 */

class ExpatParser extends AbstractSAXParser {

    var $parser = null;
    var $file   = null;
    var $buffer = 4096;
    var $error_string = "";
    var $line = 0;
    // var $location = null;

    /**
     * Constructs a new ExpatParser object.
     *
     * The constructor accepts a File object that represents the filename
     * for the file to be parsed. It sets up php's internal expat parser
     * and options.
     *
     * @param  object  the File object that represents the XML file
     * @throws RuntimeException if the given argument is not a File object
     * @public
     *
     * Modifier remplace FileReader by filename
     */
    function ExpatParser($xmlFile=NULL) {

        $this->file   = $xmlFile;		// new File($xmlFile->getAbsolutePath());
        $this->parser = xml_parser_create();
        $this->buffer = 4096;
	// $this->location = new Location();
	// xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser,"startElementHandler","endElementHandler");
        xml_set_character_data_handler($this->parser,"characterDataHandler");
    }

    /**
     * Override PHP's parser default settings, created in the constructor.
     *
     * @param  string  the option to set
     * @throws mixed   the value to set
     * @return boolean true if the option could be set, otherwise false
     * @public
     */
    function parserSetOption($opt, $val) {
        return xml_parser_set_option($this->parser, $opt, $val);
    }

    /**
     * Returns the location object of the current parsed element. It describes
     * the location of the element within the XML file (line, char)
     *
     * @return object  the location of the current parser
     * @access public
     */
/*    function &getLocation() {
        return $this->location;
    } */

    /**
     * Starts the parsing process.
     *
     * @param  string  the option to set
     * @return int     1 if the parsing succeeded
     * @throws ExpatParserException if something gone wrong during parsing
     * @throws IOException if XML file can not be accessed
     * @public
     */
    function parse($filename=null)
    {
    	if ( $filename !== null )
		$this->filename = $filename;

	if ( is_file($this->filename) ) {
		$fp = fopen($this->filename, "r");
		if ( !is_resource($fp) ) {
			return 0;
		}
		
		while ($data = fread($fp, $this->buffer)) {
			// update the location
			/* $this->location = new Location(
                                  $this->file->getAbsolutePath(),
                                  xml_get_current_line_number($this->parser),
                                  xml_get_current_column_number($this->parser)
                              ); */
			
			if (!xml_parse($this->parser, $data, feof($fp))) {
				$error = xml_error_string(xml_get_error_code($this->parser));
				$line = xml_get_current_line_number($this->parser);
			        xml_parser_free($this->parser);
				die("Error: ".$this->error_string." on line ".$this->line);
			}
		}
		
	} elseif ( is_string($this->filename) ) {
		$data = $this->filename;

		if(!xml_parse($this->parser, $data, true)) {
			$this->error_string = xml_error_string(xml_get_error_code($this->parser));
			$this->line = xml_get_current_line_number($this->parser);
		        xml_parser_free($this->parser);
			die("Error: ".$this->error_string." on line ".$this->line);
		}
	
	} else {
		return 0;
	}

        xml_parser_free($this->parser);
	
        return 1;
    }
}
/*
 * Local Variables:
 * mode: php
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
?>
