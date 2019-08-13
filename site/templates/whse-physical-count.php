<?php
	$whsesession = WhsesessionQuery::create()->findOneBySessionid(session_id());
	$warehouse   = WarehouseQuery::create()->findOneByWhseid($whsesession->whseid);

	$page->formurl = $page->parent('template=warehouse-menu')->child('template=redir')->url;

	if ($input->get->scan) {
		$scan = $input->get->text('scan');
		$page->scan = $scan;
		$page->title = "Find Item Inquiry for $scan";
		$page->body = $config->twig->render('warehouse/inventory/physical-count/physical-count-form.twig', ['page' => $page, 'scan' => $scan]);
	} else {
		$page->body = $config->twig->render('warehouse/inventory/physical-count/item-search-form.twig', ['page' => $page]);
	}

	include __DIR__ . "/basic-page.php";
