<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$itemDetailsSearchSql = 'SELECT * FROM item';
	$itemDetailsSearchStatement = $conn->prepare($itemDetailsSearchSql);
	$itemDetailsSearchStatement->execute();
	
	$output = '<table id="itemDetailsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
				<thead>
					<tr>
						<th>Product ID</th>
						<th>Item Number</th>
						<th>Item Name</th>
						<th>Discount %</th>
						<th>Stock</th>
						<th>Unit Price</th>
						<th>Status</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>';
	
	// Create table rows from the selected data
	while($row = $itemDetailsSearchStatement->fetch(PDO::FETCH_ASSOC)){
		$productID = htmlspecialchars($row['productID'], ENT_QUOTES, 'UTF-8');
		$itemNumber = htmlspecialchars($row['itemNumber'], ENT_QUOTES, 'UTF-8');
		$itemName = htmlspecialchars($row['itemName'], ENT_QUOTES, 'UTF-8');
		$discount = htmlspecialchars($row['discount'], ENT_QUOTES, 'UTF-8');
		$stock = htmlspecialchars($row['stock'], ENT_QUOTES, 'UTF-8');
		$unitPrice = htmlspecialchars($row['unitPrice'], ENT_QUOTES, 'UTF-8');
		$status = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');
		$description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
		
		$output .= '<tr>' .
						'<td>' . $productID . '</td>' .
						'<td>' . $itemNumber . '</td>' .
						'<td><a href="#" class="itemDetailsHover" data-toggle="popover" id="' . $productID . '">' . $itemName . '</a></td>' .
						'<td>' . $discount . '</td>' .
						'<td>' . $stock . '</td>' .
						'<td>' . $unitPrice . '</td>' .
						'<td>' . $status . '</td>' .
						'<td>' . $description . '</td>' .
					'</tr>';
	}
	
	$itemDetailsSearchStatement->closeCursor();
	
	$output .= '</tbody>
					<tfoot>
						<tr>
							<th>Product ID</th>
							<th>Item Number</th>
							<th>Item Name</th>
							<th>Discount %</th>
							<th>Stock</th>
							<th>Unit Price</th>
							<th>Status</th>
							<th>Description</th>
						</tr>
					</tfoot>
				</table>';
	echo $output;
?>
