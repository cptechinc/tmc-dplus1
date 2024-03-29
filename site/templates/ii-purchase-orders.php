<?php
	include_once('./ii-include.php');

	if ($itemquery->count()) {
		$page->title = "$itemID Purchase Orders";

		$module_json = $modules->get('JsonDataFiles');
		$json = $module_json->get_file(session_id(), $page->jsoncode);

		if ($module_json->file_exists(session_id(), $page->jsoncode)) {
			$session->purchaseorderstry = 0;
			$module_formatter = $modules->get('IiPurchaseOrders');
			$module_formatter->init_formatter();

			$page->body .= $config->twig->render('items/ii/ii-links.twig', ['page' => $page, 'itemID' => $itemID]);

			$page->body .= $config->twig->render('items/ii/purchase-orders/purchase-orders.twig', ['page' => $page, 'itemID' => $itemID, 'json' => $json, 'module_formatter' => $module_formatter, 'blueprint' => $module_formatter->get_tableblueprint()]);
		} else {
			if ($session->purchaseorderstry > 3) {
				$page->headline = $page->title = "Purchase Orders File could not be loaded";
				$page->body = $config->twig->render('util/error-page.twig', ['title' => $page->title, 'msg' => $module_json->get_error()]);
			} else {
				$session->purchaseorderstry++;
				$session->redirect($page->get_itempurchaseordersURL($itemID));
			}
		}
	}

	if ($page->print) {
		$page->show_title = true;
		include __DIR__ . "/blank-page.php";
	} else {
		include __DIR__ . "/basic-page.php";
	}
