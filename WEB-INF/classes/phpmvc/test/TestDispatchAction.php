<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/TestDispatchAction.php,v 1.3 2006/02/22 08:53:57 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:53:57 $
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
* TestDispatchAction 
* 
* @author John C. Wildenauer
* @version $Revision: 1.3 $
*/
class TestDispatchAction extends DispatchAction {

	/**
	* addToCart
	* 
	* @param ActionMapping		The ActionMapping used to select this instance
	* @param ActionForm			The ActionForm for this request
	* @param HttpRequestBase	The HTTP request we are processing
	* @param HttpResponseBase	The HTTP response we are creating
	*
	* @public
	* @return ForwardConfig
	*/
	function addToCart($mapping, $form, &$request, &$response) {

		// Activate logging in the DispatchAction constructor
		$trace = $this->log->getLog('isTraceEnabled');
		if($trace) {
			$this->log->trace('Start: TestDispatchAction->addToCart(...)'.
									'['.__LINE__.']');
		}

		// Do add
		$cart = $itemID = $qnty = NULL;
		$cart = $request->getAttribute('cart');
		$itemID = $request->getAttribute('itemID');
		$qnty = $request->getAttribute('qnty');

		// Something wrong !!!, return the "failure" URI
		// <action ... <forward name = "failure" path="stdLogon.php" ... /> />
		if($cart == NULL || $itemID == NULL || $qnty == NULL )
			return $mapping->findForwardConfig('failure');

		$cart[$itemID] += $qnty;
		$request->setAttribute('cart', $cart);

		// Looks good
		// Forward control to the specified "success" URI
		return $mapping->findForwardConfig('success');

	}


	/**
	* subtractFromCart
	* 
	* @param ActionMapping		The ActionMapping used to select this instance
	* @param ActionForm			The ActionForm for this request
	* @param HttpRequestBase	The HTTP request we are processing
	* @param HttpResponseBase	The HTTP response we are creating
	*
	* @public
	* @return ForwardConfig
	*/
	function subtractFromCart($mapping, $form, &$request, &$response) {

		// Activate logging in the DispatchAction constructor
		$trace = $this->log->getLog('isTraceEnabled');
		if($trace) {
			$this->log->trace('Start: TestDispatchAction->subtractFromCart(...)'.
									'['.__LINE__.']');
		}

		// Do add
		$cart = $itemID = $qnty = NULL;
		$cart = $request->getAttribute('cart');
		$itemID = $request->getAttribute('itemID');
		$qnty = $request->getAttribute('qnty');

		// Something wrong !!!, return the "failure" URI
		// <action ... <forward name = "failure" path="stdLogon.php" ... /> />
		if($cart == NULL || $itemID == NULL || $qnty == NULL )
			return $mapping->findForwardConfig('failure');

		$cart[$itemID] -= $qnty;
		$request->setAttribute('cart', $cart);

		// Looks good
		// Forward control to the specified "success" URI
		return $mapping->findForwardConfig('success');

	}
}
?>