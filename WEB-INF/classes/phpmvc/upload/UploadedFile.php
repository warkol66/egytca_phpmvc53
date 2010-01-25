<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/upload/UploadedFile.php,v 1.2 2006/02/22 08:58:47 who Exp $
* $Revision: 1.2 $
* $Date: 2006/02/22 08:58:47 $
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
* This class contains the properties representing an uploaded file.
*
* @author John C Wildenauer<br>
*  Credits: Jakarta Struts upload/DiskFile
* @version $Revision: 1.2 $
* @public
*/
class UploadedFile {


	// ----- Properties ----------------------------------------------------- //

	/**
	* The upload filename from the HTML form.
	* All filename keys are strings. ("file[0]", "file[test]", "namedFile")
	* @type string
	*/
	var $fileNameKey = '';

	/**
	* The file/path to the file. Initially set to the temporary file path,
	* (Eg: '/tmp/php391.tmp') but can be set to the new (moved) location.
	* @type string
	*/
	var $filePath = '';

	/**
	* The original name of the file as uploaded. for example "myPic.png"
	* @type string
	*/
	var $fileNameOrig = '';

	/**
	* The new name of the file. Initially set to the original uploaded file name. 
	* The file may be renamed after uploading.
	* @type string
	*/
	var $fileNameNew = '';

	/**
	* The content type of the file
	* @type string
	*/
	var $contentType = '';

	/**
	* The size in bytes of the file
	* @type int
	*/
	var $fileSize = 0;

	/**
	* A file operation status.
	* <p>True  = good file<br>
	*    False = bad file
	* @type boolean
	*/
	var $fileStatus = True;

	/**
	* A file operation status message
	* @type string
	*/
	var $fileStatusMsg = '';

	/**
	* A file operation error message, if any
	* @type string
	*/
	var $fileErrorMsg = '';


	// ----- Constructor ---------------------------------------------------- //

	/**
	* 
	* @param	string	The filepath to the temporary upload file
	* @public	
	* @returns void
	*/
	function UploadedFile($filePath='') {

		$this->filePath = $filePath;

	}


	// ----- Public Methods ------------------------------------------------- //

	
	/**
	* Delete the temporary file.
	* @public	
	* @returns void
	*/
	function destroy() {
		if( file_exists($this->filePath) ) {
			unlink($this->filePath);
		}    
	}


	/**
	* Set the upload filename from the HTML form.
	* All filename keys are strings. ("file[0]", "file[test]", "namedFile").
	* @param string	The filename from the HTML form.
	* @returns void
	*/
	function setFileNameKey($fileNameKey) {
		$this->fileNameKey = $fileNameKey;
	}

	/**
	* Get the upload filename as defined on the HTML form.
	* Returns the filename from the HTML form.
	* @returns string
	*/
	function getFileNameKey() {
		return $this->fileNameKey;
	}

	/**
	* Set the path/filename to this form file.
	* @param string	The path/filename for this file
	* @returns void
	*/
	function setFilePath($filePath) {
		$this->filePath = $filePath;
	}

	/**
	* Get the file path for this form file.
	* Returns A filepath to the temporary file
	* @returns string
	*/
	function getFilePath() {
		return $this->filePath;
	}

	/**
	* Set the original file name
	* @param string	The original file name
	* @returns void
	*/
	function setFileNameOrig($fileNameOrig) {
		$this->fileNameOrig = $fileNameOrig;
	}

	/**
	* Get the original file name.
	* Returns the original filename as a string
	* @returns string
	*/
	function getFileNameOrig() {
		return $this->fileNameOrig;
	}

	/**
	* Set the new file name
	* @param string	The new file name
	* @returns void
	*/
	function setFileNameNew($fileNameNew) {
		$this->fileNameNew = $fileNameNew;
	}

	/**
	* Get the new file name.
	* Returns the new filename as a string
	* @returns string
	*/
	function getFileNameNew() {
		return $this->fileNameNew;
	}

	/**
	* Set the content type
	* @param string	The content type
	* @returns void
	*/
	function setContentType($contentType) {
		$this->contentType = $contentType;
	}

	/**
	* Get the content type.
	* Returns the content type as a string
	* @returns string
	*/
	function getContentType() {
		return $this->contentType;
	}

	/**
	* Set the file size
	* @param int	The size of the file in bytes
	* @returns void
	*/
	function setFileSize($fileSize) {
		$this->fileSize = $fileSize;
	}

	/**
	* Get the file size.
	* Returns the size of this file in bytes
	* @returns int
	*/
	function getFileSize() {
		return $this->fileSize;
	}

	/**
	* Set a file operation status.
	* @param boolean	The file operation status
	* @returns void
	*/
	function setFileStatus($fileStatus) {
		$this->fileStatus = $fileStatus;
	}

	/**
	* Returns file operation status.
	* @returns string
	*/
	function getFileStatus() {
		return $this->fileStatus;
	}

	/**
	* Set a file operation status message.
	* @param string	The file operation status message
	* @returns void
	*/
	function setFileStatusMsg($fileStatusMsg) {
		$this->fileStatusMsg = $fileStatusMsg;
	}

	/**
	* Get the file operation status message.
	* Returns file operation status message string.
	* @returns string
	*/
	function getFileStatusMsg() {
		return $this->fileStatusMsg;
	}

	/**
	* Set a file operation error message.
	* @param string	The file operation error message
	* @returns void
	*/
	function setFileError($fileErrorMsg) {
		$this->fileErrorMsg = $fileErrorMsg;
	}

	/**
	* Get the file operation error message.
	* Returns file operation error message string.
	* @returns string
	*/
	function getFileError() {
		return $this->fileErrorMsg;
	}

}
?>