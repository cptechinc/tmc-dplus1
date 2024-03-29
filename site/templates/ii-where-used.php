<?php
	include_once('./ii-include.php');

	if ($itemquery->count()) {
		$page->title = "$itemID Where Used";

		$module_json = $modules->get('JsonDataFiles');
		$json = $module_json->get_file(session_id(), $page->jsoncode);

		if ($module_json->file_exists(session_id(), $page->jsoncode)) {
			$session->whereusedtry = 0;
			$refreshurl = $page->get_itemwhereusedURL($itemID);
			$page->body .= $config->twig->render('items/ii/ii-links.twig', ['page' => $page, 'itemID' => $itemID, 'lastmodified' => $module_json->file_modified(session_id(), $page->jsoncode), 'refreshurl' => $refreshurl]);
			$page->body .= $config->twig->render('items/ii/where-used/where-used.twig', ['page' => $page, 'json' => $json, 'module_json' => $module_json]);
		} else {
			if ($session->whereusedtry > 3) {
				$page->headline = $page->title = "Where Used File could not be loaded";
				$page->body = $config->twig->render('util/error-page.twig', ['title' => $page->title, 'msg' => $module_json->get_error()]);
			} else {
				$session->whereusedtry++;
				$session->redirect($page->get_itemwhereusedURL($itemID));
			}
		}
	}

	if ($page->print) {
		$page->show_title = true;
		include __DIR__ . "/blank-page.php";
	} else {
		include __DIR__ . "/basic-page.php";
	}
