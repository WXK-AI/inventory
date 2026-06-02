<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$itemDetailsSearchSql = 'SELECT * FROM item';
	$itemDetailsSearchStatement = $conn->prepare($itemDetailsSearchSql);
	$itemDetailsSearchStatement->execute();

	$output = '<table id="itemReportsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
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
		$safeRow = array_map(function($value){
			return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
		}, $row);

		$output .= '<tr>' .
						'<td>' . $safeRow['productID'] . '</td>' .
						'<td>' . $safeRow['itemNumber'] . '</td>' .
						//'<td>' . $safeRow['itemName'] . '</td>' .
						'<td><a href="#" class="itemDetailsHover" data-toggle="popover" id="' . $safeRow['productID'] . '">' . $safeRow['itemName'] . '</a></td>' .
						'<td>' . $safeRow['discount'] . '</td>' .
						'<td>' . $safeRow['stock'] . '</td>' .
						'<td>' . $safeRow['unitPrice'] . '</td>' .
						'<td>' . $safeRow['status'] . '</td>' .
						'<td>' . $safeRow['description'] . '</td>' .
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
