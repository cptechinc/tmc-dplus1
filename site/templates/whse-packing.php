<?php
	use ProcessWire\WireHttp;

	$warehousepacking = $modules->get('WarehousePacking');
	$warehousepacking->set_sessionID(session_id());
	$whsesession = $warehousepacking->get_whsesession();
	$warehouse   = $warehousepacking->get_warehouseconfig();
	$http = new WireHttp();
	$html = $modules->get('HtmlWriter');

	if ($input->get->ordn) {
		$ordn = SalesOrder::get_paddedordernumber($input->get->text('ordn'));
		$warehousepacking->set_ordn($ordn);
		$warehousepacking->init_cartoncount();

		if (SalesOrderQuery::create()->orderExists($ordn)) {
			$warehousepacking->set_ordn($ordn);

			if ($input->get->text('action') == 'add-box') {
				$warehousepacking->handle_barcodeaction($input);
				$session->redirect($page->fullURL->getUrl(), $http301 = false);
			}

			if ($input->get->box) {
				$boxnumber = $input->get->text('box');
				$page->box = $boxnumber;

				if ($input->requestMethod('POST')) {
					$warehousepacking->handle_barcodeaction($input);
					// $session->redirect($page->fullURL->getUrl(), $http301 = false);
				}
					$packed_items = WhseitempackQuery::create()->filterBySessionidOrder(session_id(), $ordn)->filterByCarton($boxnumber)->find();
					$page->body .= $config->twig->render('warehouse/packing/box-header.twig', ['page' => $page, 'warehousepacking' => $warehousepacking]);

					if ($session->packerror) {
						$page->body .= $config->twig->render('util/alert.twig', ['type' => 'danger', 'title' => "Error!", 'iconclass' => 'fa fa-warning fa-2x', 'message' => $session->packerror]);
						$page->body .= $html->div('class=form-group', '');
						$session->remove('packerror');
					}
					$page->body .= $config->twig->render('warehouse/packing/box-item-form.twig', ['page' => $page, 'warehousepacking' => $warehousepacking, 'box' => $boxnumber]);
					$page->body .= $config->twig->render('warehouse/packing/box-items.twig', ['page' => $page, 'warehousepacking' => $warehousepacking, 'packed_items' => $packed_items]);


			} else {
				//$http->get("127.0.0.1".$pages->get('template=redir, redir_file=sales-order')->url."?action=get-order-notes&ordn=$ordn&sessionID=".session_id());
				$page->body = $config->twig->render('warehouse/packing/order-notes.twig', ['page' => $page, 'notes' => $warehousepacking->get_packingnotes()]);
				$page->body .= $html->div('class=mb-3');
				$page->body .= $config->twig->render('warehouse/packing/box-list.twig', ['page' => $page, 'warehousepacking' => $warehousepacking]);
				$page->body .= $html->h3('Items');
				$page->body .= $config->twig->render('warehouse/packing/order-items.twig', ['page' => $page, 'warehousepacking' => $warehousepacking]);
			}
		} else {
			$page->body = $config->twig->render('warehouse/packing/status.twig', ['page' => $page, 'message' => "Error finding Sales Order # $ordn"]);
		}
	} else {
		$page->formurl = $page->child('template=redir')->url;
		$page->body = $config->twig->render('warehouse/packing/sales-order-form.twig', ['page' => $page]);
	}
	$config->scripts->append(hash_templatefile('scripts/lib/jquery-validate.js'));
	$config->scripts->append(hash_templatefile('scripts/warehouse/pack-order.js'));

	include __DIR__ . "/basic-page.php";
