<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/upload/MultiPartUploadFileHandler.php,v 1.4 2006/02/22 08:19:54 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 08:19:54 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2003-2006 John C.Wildenauer.  All rights reserved.
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
* This is a wrapper class that provides methods for handling file uploading.
* MultiPartUploadFileHandler currently uses the PEAR::HTTP_Upload package to
* provide the core classes and methods needed to provide the file upload 
* functionality
*
* <pre>
*  File Upload Notes: 
*  ===================================
*  PHP file upload errors: (see below)
*  ~~~~~~~~~~~~~~~~~~~~
*  0 => 'File successfully uploaded', 
*  1 => 'Uploaded file exceeds the allowed size',
*  2 => 'Uploaded file exceeds the allowed size',
*  3 => 'File was only partially uploaded, please try again',
*  4 => 'No file was received. Upload again.'
*
*  Warning:
*  ~~~~~~~~~~~~~~~~~~~~
*  The MAX_FILE_SIZE is advisory to the browser. It is easy to circumvent
*  this maximum. So don't count on it that the browser obeys your wish!
*  The PHP-settings for maximum-size, however, cannot be fooled. But you 
*  should add MAX_FILE_SIZE anyway as it saves users the trouble to wait
*  for a big file being transfered only to find out that it was too big
*  afterwards.
*  The MAX_FILE_SIZE hidden field must precede the file input field and
*  its value is the maximum filesize accepted. The value is in bytes
*  
*  Related Configurations Note: 
*  ~~~~~~~~~~~~~~~~~~~~
*  See also: file_uploads, upload_max_filesize, upload_tmp_dir, and 
*  post_max_size directives in php.ini
*
*  File Upload Error Messages:
*  ~~~~~~~~~~~~~~~~~~~~
*  The error code associated with this file upload. ['error'] was added in PHP 4.2.0 
*  Note: These became PHP constants in PHP 4.3.0 
*     UPLOAD_ERR_OK
*        Value: 0; There is no error, the file uploaded with success. 
*     UPLOAD_ERR_INI_SIZE
*        Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini. 
*     UPLOAD_ERR_FORM_SIZE
*        Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form. 
*     UPLOAD_ERR_PARTIAL
*        Value: 3; The uploaded file was only partially uploaded. 
*     UPLOAD_ERR_NO_FILE
*        Value: 4; No file was uploaded. 
*
*  Uploading multiple files:
*  ~~~~~~~~~~~~~~~~~~~~
*  &lt;form action="file-upload.php" method="post" enctype="multipart/form-data"&gt;
*     &lt;input name="userfile[]" type="file"&gt;
*     &lt;input name="userfile[]" type="file"&gt;
*     &lt;input type="submit" value="Send files"&gt;
*  &lt;/form&gt;
*
*  $_FILES['userfile']['name'][0] == "myFile1.png"
*  $_FILES['userfile']['name'][1] == "myFile2.png"
*
*  Ref: PHP Manual/Features/Chapter 18. Handling file uploads
* </pre>
*
* <pre>
* Tech note: 
* PEAR core class is pre-included in the global prepend file
* PEAR::HTTP_Upload class is pre-included in the global prepend file
* 
* HTTP_Upload error languages: (default is 'en' English)
*     ['es', 'en', 'de', 'nl', 'fr', 'it']
* </pre>
* 
* @author John C Wildenauer<br>
*  Credits: Mike Schachter - Jakarta Struts /upload/DiskMultipartRequestHandler
* @version $Revision: 1.4 $
* @public
*/
class MultiPartUploadFileHandler {

	// ----- Properties ----------------------------------------------------- //

	/**
	* Logging class
	* @type Log
	*/
	var $log = NULL;

	/**
	* An array of UploadedFile objects representing the form files uploaded
	*@type array
	*/
	var $fileElements = array();

	/**
	* The PEAR::HTTP_Upload class reference
	* @type HTTP_Upload
	*/
	var $uploader = NULL;


	// ----- Constructor ---------------------------------------------------- //

	/**
	* MultiPartUploadFileHandler implementation that uses the PEAR::HTTP_Upload class.
	*
	* <p>Implementation note:<br>
	* PEAR::HTTP_Upload class uses  global $HTTP_POST_FILES, $HTTP_SERVER_VARS.<br>
	* So parameters "&$request" and, "$config" are not used in this implementation.</p>
	* 
	* @param HttpServerRequest	The HTTP request
	* @param ApplicationConfig	The ApplicationConfig instance
	* @param string				The HTTP_Upload error language (default is 'en' English)
	*									['es', 'en', 'de', 'nl', 'fr', 'it'].
	* @returns void
	* @public
	*/
	function MultiPartUploadFileHandler(&$request, $config=NULL, $lang='en') {

		$this->log	= new PhpMVC_Log();
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isTraceEnabled'	, False);

		// Using PEAR HTTP_Upload class library in this implementation
		$this->uploader =& new HTTP_Upload($lang);

	} 


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Returns a reference to the PEAR::HTTP_Upload class
	* 
	* @returns HTTP_Upload
	* @public
	*/
	function &getUploader() {

		 return $this->uploader;

	}


	/**
	* Build an array of UploadedFile objects representing the form files 
	* uploaded, here the keys are the input names of the files and the 
	* values are UploadedFile objects.
	*
	* @param boolean		True to list all file slots, even empty file slots
	* @returns array
	* @public
	*/
	function makeFileElements($listAll=True) {

		// Using PEAR HTTP_Upload class library in this implementation
		$upload =& $this->getUploader();
		$pFiles = $upload->getFiles();	// PEAR file objects

		// Reset the fileElements array
		$this->resetFileElements();

		// Repack the PEAR_Upload file objects to our UploadedFile type.
		foreach($pFiles as $pFile) {

			// Set $listAll to True to build a list of all file slots, even if no
			// file was selected.
			if( $pFile->isValid() || $listAll ) {

				// Note: 
				// All filename keys are strings. ("file[0]", "file[test]", "namedFile")
				$fileNameKey	= $pFile->getProp('form_name');
				$fileNameOrig	= $pFile->getProp('real');		// real uploaded file name
				$fileNameNew	= $pFile->getProp('name');		// initially same as 'real'
				$fileNameTmp	= $pFile->getProp('tmp_name');// '/tmp/php391.tmp'
				$fileSize		= $pFile->getProp('size');
				$fileType		= $pFile->getProp('type');		// 'image/png'
				$fileError		= $pFile->getProp('error');

				$oFile = NULL;
				$oFile = new UploadedFile($fileNameTmp);
				$oFile->setFileNameKey($fileNameKey);
				$oFile->setFileNameOrig($fileNameOrig);
				$oFile->setFileNameNew($fileNameNew);
				$oFile->setFilePath($fileNameTmp);
				$oFile->setFileSize($fileSize);
				$oFile->setContentType($fileType);
				$oFile->setFileError($fileError);

				$this->fileElements[$fileNameKey] = $oFile;

			}
		}
	}


	/**
	* Retrieves all the UploadFileForm input elements of the request.
	* Returns an array where the keys are the input names of the files and 
	* the values are FormFile objects.
	*
	* <p>If the fileElements array is empty, call makeFileElements() to
	*		build the array.
	* 
	* @see UploadFileForm
	* @returns array
	* @public
	*/
	function &getFileElements() {

		if( count($this->fileElements) == 0 ) {
			$listAll=True;
			$this->makeFileElements($listAll);
		}

		 return $this->fileElements;

	}


	/**
	* Reset the FileElement array
	* 
	* @returns void
	* @public
	*/
	function resetFileElements() {

		 $this->fileElements = array();

	}


	/**
	* Move an uploaded file to another location.
	*
	* <p>Returns Filename on success or Pear_Error object on error.</p>
	* 
	* @param		string	The destination directory
	* @param    bool		$overwrite Overwrite if destination file exists?
	* @param		string	The input form file name. Note that the form input elements:
	*                    &lt;input type='file' name='file[]'&gt; are referenced as 
	*							strings. Eg: ["namedFile", "file[0]", "file[1]", "file[test]"]
	*		
	* @returns mixed
	* @public
	*/
	function moveFile($destDir, $overwrite=True, $fileName) {

		// Using PEAR HTTP_Upload class library in this implementation
		$upload =& $this->getUploader();
		$pFile = $upload->getFiles($fileName);	// PEAR file object

		$moveError = ''; // True on success or Pear_Error object on error

		// Only move valid files
		if( $pFile->isValid() ) {
			$moveError = $pFile->moveTo($destDir, $overwrite);
		}

		return $moveError;
	}


	/**
	* Move the uploaded files to another location.
	*
	* <p>Returns an array of operation status (error) messages. Each array
	* element represents the return value of the native PEAR HTTP_Upload
	* moveTo() operation: Filename on success or Pear_Error object on error.</p>
	* 
	* @param		string	The destination directory
	* @param    bool		$overwrite Overwrite if destination file exists?
	* @returns array
	* @public
	*/
	function moveFiles($destDir, $overwrite=True) {

		// Using PEAR HTTP_Upload class library in this implementation
		$upload =& $this->getUploader();
		$pFiles = $upload->getFiles();	// PEAR file objects

		$moveErrors = array(); // True on success or Pear_Error object on error

		foreach($pFiles as $pFile) {

			// Only move valid files
			if( $pFile->isValid() ) {
				$moveErrors[] = $pFile->moveTo($destDir, $overwrite);
			}
		}

		return $moveErrors;
	}


	/**
	* Gets the maximum post data size in bytes from the string representation
	* in the controller element of the XML configuration file.
	*
	* <p>The string can be expressed as a number followed by a "K", "M", or "G", 
	* which are interpreted to mean kilobytes, megabytes, or gigabytes, 
	* respectively. For example: the string "250M" would be converted to the 
	* integer 262144000 (250*1024*1024).
	*                 
	* <p>Returns an integer derived from the string file size parameter.
	* @param   string	The string representation of the maximum file size.
	* @returns int
	* @public
	*/
	function getMaxSize($stringSize) {

		$multiplier = 1;
  
		if( strtoupper(substr($stringSize, -1)) == "K" ) {
			$multiplier = 1024;
			$stringSize = substr($stringSize, 0, -1);  // drops the last character	
		}
		if( strtoupper(substr($stringSize, -1)) == "M" ) {
			$multiplier = 1024*1024;
			$stringSize = substr($stringSize, 0, -1);
		}
		elseif( strtoupper(substr($stringSize, -1)) == "G" ) {
			$multiplier = 1024*1024*1024;
			$stringSize = substr($stringSize, 0, -1);
		}
		
		// Convert the string to integer
		$intSize = (int)$stringSize;
		
		return $intSize * $multiplier;
	}

}
?>