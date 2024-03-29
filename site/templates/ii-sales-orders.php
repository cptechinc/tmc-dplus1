<?php
	include_once('./ii-include.php');

	if ($itemquery->count()) {
		$page->title = "$itemID Sales Orders";

		$module_json = $modules->get('JsonDataFiles');
		$json = $module_json->get_file(session_id(), $page->jsoncode);

		if ($module_json->file_exists(session_id(), $page->jsoncode)) {
			$session->salesorderstry = 0;
			$module_formatter = $modules->get('IiSalesOrders');
			$module_formatter->init_formatter();
			$document_management = $modules->get('DocumentManagement');

			$refreshurl = $page->get_itemsalesordersURL($itemID);
			$page->body .= $config->twig->render('items/ii/ii-links.twig', ['page' => $page, 'itemID' => $itemID, 'lastmodified' => $module_json->file_modified(session_id(), $page->jsoncode), 'refreshurl' => $refreshurl]);
			$page->body .= $config->twig->render('items/ii/sales-orders/sales-orders.twig', ['page' => $page, 'itemID' => $itemID, 'json' => $json, 'module_formatter' => $module_formatter, 'blueprint' => $module_formatter->get_tableblueprint(), 'document_management' => $document_management]);
		} else {
			if ($session->salesorderstry > 3) {
				$page->headline = $page->title = "Sales Orders File could not be loaded";
				$page->body = $config->twig->render('util/error-page.twig', ['title' => $page->title, 'msg' => $module_json->get_error()]);
			} else {
				$session->salesorderstry++;
				$session->redirect($page->get_itemsalesordersURL($itemID));
			}
		}
	}

	if ($page->print) {
		$page->show_title = true;
		include __DIR__ . "/blank-page.php";
	} else {
		include __DIR__ . "/basic-page.php";
	}
