<?php
	$whsesession = WhsesessionQuery::create()->findOneBySessionid(session_id());
	$warehouse   = WarehouseQuery::create()->findOneByWhseid($whsesession->whseid);

	$page->formurl = $page->parent('template=warehouse-menu')->child('template=redir')->url;

	if (!$page->print && !$input->get->scan) {
		$page->body = $config->twig->render('warehouse/inventory/physical-count/item-search-form.twig', ['page' => $page]);
	}

	if ($input->get->scan) {
		$scan = $input->get->text('scan');
		$page->scan = $scan;
		$page->title = "Find Item Inquiry for $scan";
		$inventory = InvsearchQuery::create();
		$resultscount = InvsearchQuery::create()->countDistinctItemid(session_id());
		$items = InvsearchQuery::create()->findDistinctItems(session_id());
		$page->body .= $config->twig->render('warehouse/inventory/find-item/results.twig', ['page' => $page, 'resultscount' => $resultscount, 'items' => $items, 'inventory' => $inventory, 'warehouse' => $warehouse]);
	}

	include __DIR__ . "/basic-page.php";
