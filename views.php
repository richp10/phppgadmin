<?php

	/**
	 * Manage views in a database
	 *
	 * $Id: views.php,v 1.2 2003/02/07 17:34:35 xzilla Exp $
	 */

	// Include application functions
	include_once('libraries/lib.inc.php');
	
	$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';
	if (!isset($msg)) $msg = '';
	$PHP_SELF = $_SERVER['PHP_SELF'];
	
	/** 
	 * Function to save after editing a view
	 */
	function doSaveEdit() {
		global $localData;
		
		$status = $localData->setView($_POST['view'], $_POST['formDefinition']);
		if ($status == 0)
			doProperties('View updated.');
		else
			doEdit('View update failed.');
	}
	
	/**
	 * Function to allow editing of a view
	 */
	function doEdit($msg = '') {
		global $data, $localData, $misc;
		global $PHP_SELF, $strName, $strDefinition, $strViews ;
		
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": {$strViews}: ", htmlspecialchars($_REQUEST['view']), ": Edit</h2>\n";
		$misc->printMsg($msg);
		
		$viewdata = &$localData->getView($_REQUEST['view']);
		
		if ($viewdata->recordCount() > 0) {
			echo "<form action=\"$PHP_SELF\" method=post>\n";
			echo "<table width=100%>\n";
			echo "<tr><th class=data>{$strName}</th></tr>\n";
			echo "<tr><td class=data1>", htmlspecialchars($viewdata->f[$data->vwFields['vwname']]), "</td></tr>\n";
			echo "<tr><th class=data>{$strDefinition}</th></tr>\n";
			echo "<tr><td class=data1><textarea style=\"width:100%;\" rows=20 cols=50 name=formDefinition wrap=virtual>", 
				htmlspecialchars($viewdata->f[$data->vwFields['vwdef']]), "</textarea></td></tr>\n";
			echo "</table>\n";
			echo "<input type=hidden name=action value=save_edit>\n";
			echo "<input type=hidden name=view value=\"", htmlspecialchars($_REQUEST['view']), "\">\n";
			echo $misc->form;
			echo "<input type=submit value=Save> <input type=reset>\n";
			echo "</form>\n";
		}
		else echo "<p>No data.</p>\n";
		
		echo "<p><a class=navlink href=\"$PHP_SELF?{$misc->href}\">Show All Views</a> |\n";
		echo "<a class=navlink href=\"$PHP_SELF?action=properties&{$misc->href}&view=", 
			urlencode($_REQUEST['view']), "\">Properties</a></p>\n";
	}
	
	/**
	 * Show read only properties for a view
	 */
	function doProperties($msg = '') {
		global $data, $localData, $misc;
		global $PHP_SELF, $strName, $strDefinition, $strViews;
	
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": {$strViews}: ", htmlspecialchars($_REQUEST['view']), ": Properties</h2>\n";
		$misc->printMsg($msg);
		
		$viewdata = &$localData->getView($_REQUEST['view']);
		
		if ($viewdata->recordCount() > 0) {
			echo "<table width=100%>\n";
			echo "<tr><th class=data>{$strName}</th></tr>\n";
			echo "<tr><td class=data1>", htmlspecialchars($viewdata->f[$data->vwFields['vwname']]), "</td></tr>\n";
			echo "<tr><th class=data>{$strDefinition}</th></tr>\n";
			echo "<tr><td class=data1>", nl2br(htmlspecialchars($viewdata->f[$data->vwFields['vwdef']])), "</td></tr>\n";
			echo "</table>\n";
		}
		else echo "<p>No data.</p>\n";
		
		echo "<p><a class=navlink href=\"$PHP_SELF?{$misc->href}\">Show All Views</a> |\n";
		echo "<a class=navlink href=\"$PHP_SELF?action=edit&{$misc->href}&view=", 
			urlencode($_REQUEST['view']), "\">Edit</a></p>\n";
	}
	
	/**
	 * Show confirmation of drop and perform actual drop
	 */
	function doDrop($confirm) {
		global $localData, $misc;
		global $PHP_SELF, $strViews;

		if ($confirm) { 
			echo "<h2>", htmlspecialchars($_REQUEST['database']), ": {$strViews}: ", htmlspecialchars($_REQUEST['view']), ": Drop</h2>\n";
			
			echo "<p>Are you sure you want to drop the view \"", htmlspecialchars($_REQUEST['view']), "\"?</p>\n";
			
			echo "<form action=\"$PHP_SELF\" method=\"post\">\n";
			echo "<input type=hidden name=action value=drop>\n";
			echo "<input type=hidden name=view value=\"", htmlspecialchars($_REQUEST['view']), "\">\n";
			echo $misc->form;
			echo "<input type=submit name=choice value=\"Yes\"> <input type=submit name=choice value=\"No\">\n";
			echo "</form>\n";
		}
		else {
			$status = $localData->dropView($_POST['view']);
			if ($status == 0)
				doDefault('View dropped.');
			else
				doDefault('View drop failed.');
		}
		
	}
	
	/**
	 * Displays a screen where they can enter a new view
	 */
	function doCreate($msg = '') {
		global $data, $localData, $misc;
		global $PHP_SELF, $strName, $strDefinition, $strViews;
		
		if (!isset($_POST['formView'])) $_POST['formView'] = '';
		if (!isset($_POST['formDefinition'])) $_POST['formDefinition'] = '';
		
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": {$strViews}: Create View</h2>\n";
		$misc->printMsg($msg);
		
		echo "<form action=\"$PHP_SELF\" method=post>\n";
		echo "<table width=100%>\n";
		echo "<tr><th class=data>{$strName}</th></tr>\n";
		echo "<tr><td class=data1><input name=formView size={$data->_maxNameLen} maxlength={$data->_maxNameLen} value=\"", 
			htmlspecialchars($_POST['formView']), "\"></td></tr>\n";
		echo "<tr><th class=data>{$strDefinition}</th></tr>\n";
		echo "<tr><td class=data1><textarea style=\"width:100%;\" rows=20 cols=50 name=formDefinition wrap=virtual>", 
			htmlspecialchars($_POST['formDefinition']), "</textarea></td></tr>\n";
		echo "</table>\n";
		echo "<input type=hidden name=action value=save_create>\n";
		echo $misc->form;
		echo "<input type=submit value=Save> <input type=reset>\n";
		echo "</form>\n";
		
		echo "<p><a class=navlink href=\"$PHP_SELF?{$misc->href}\">Show All Views</a></p>\n";
	}
	
	/**
	 * Actually creates the new view in the database
	 */
	function doSaveCreate() {
		global $localData, $strViewNeedsName, $strViewNeedsDef;
		
		// Check that they've given a name and a definition
		if ($_POST['formView'] == '') doCreate($strViewNeedsName);
		elseif ($_POST['formDefinition'] == '') doCreate($strViewNeedsDef);
		else {		 
			$status = $localData->createView($_POST['formView'], $_POST['formDefinition']);
			if ($status == 0)
				doDefault('View created.');
			else
				doCreate('View creation failed.');
		}
	}	

	/**
	 * Show default list of views in the database
	 */
	function doDefault($msg = '') {
		global $data, $localData, $misc;
		global $PHP_SELF, $strView, $strOwner, $strActions, $strNoViews, $strViews;
		global $strBrowse, $strProperties, $strDrop, $strCreateView, $strBack;
		
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": {$strViews}</h2>\n";
		$misc->printMsg($msg);
		
		$views = &$localData->getViews();
		
		if ($views->recordCount() > 0) {
			echo "<table>\n";
			echo "<tr><th class=data>{$strView}</th><th class=data>{$strOwner}</th><th colspan=4 class=data>{$strActions}</th>\n";
			$i = 0;
			while (!$views->EOF) {
				// @@@@@@@@@ FIX THIS!!!!!
				$query = urlencode("SELECT * FROM \"{$views->f[$data->vwFields['vwname']]}\"");
				$count = urlencode("SELECT COUNT(*) AS total FROM \"{$views->f[$data->vwFields['vwname']]}\"");
				$return_url = urlencode("views.php?{$misc->href}");
				$id = (($i % 2) == 0 ? '1' : '2');
				echo "<tr><td class=data{$id}>", htmlspecialchars($views->f[$data->vwFields['vwname']]), "</td>\n";
				echo "<td class=data{$id}>", htmlspecialchars($views->f[$data->vwFields['vwowner']]), "</td>\n";
				echo "<td class=opbutton{$id}><a href=\"display.php?{$misc->href}&query={$query}&count={$count}&return_url={$return_url}&return_desc={$strBack}\">{$strBrowse}</a></td>\n";
				echo "<td class=opbutton{$id}>Select</td>\n";
				echo "<td class=opbutton{$id}><a href=\"$PHP_SELF?action=properties&{$misc->href}&view=", urlencode($views->f[$data->vwFields['vwname']]), "\">{$strProperties}</a></td>\n"; 
				echo "<td class=opbutton{$id}><a href=\"$PHP_SELF?action=confirm_drop&{$misc->href}&view=", urlencode($views->f[$data->vwFields['vwname']]), "\">{$strDrop}</a></td>\n"; 
				echo "</tr>\n";
				$views->moveNext();
				$i++;
			}
			echo "</table>\n";
		}
		else {
			echo "<p>{$strNoViews}</p>\n";
		}
		
		echo "<p><a class=navlink href=\"$PHP_SELF?action=create&{$misc->href}\">{$strCreateView}</a></p>\n";

	}

	$misc->printHeader($strViews);

	switch ($action) {
		case 'save_create':
			doSaveCreate();
			break;
		case 'create':
			doCreate();
			break;
		case 'drop':
			if ($_POST['choice'] == 'Yes') doDrop(false);
			else doDefault();
			break;
		case 'confirm_drop':
			doDrop(true);
			break;			
		case 'save_edit':
			doSaveEdit();
			break;
		case 'edit':
			doEdit();
			break;
		case 'properties':
			doProperties();
			break;
		case 'browse':
			// @@ Not yet implemented
		default:
			doDefault();
			break;
	}	

	$misc->printFooter();
	
?>