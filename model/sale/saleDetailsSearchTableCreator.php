<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$uPrice = 0;
	$qty = 0;
	$totalPrice = 0;
	
	$saleDetailsSearchSql = 'SELECT * FROM sale';
	$saleDetailsSearchStatement = $conn->prepare($saleDetailsSearchSql);
	$saleDetailsSearchStatement->execute();

	$output = '<table id="saleDetailsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
				<thead>
					<tr>
						<th>Sale ID</th>
						<th>Item Number</th>
						<th>Customer ID</th>
						<th>Customer Name</th>
						<th>Item Name</th>
						<th>Sale Date</th>
						<th>Discount %</th>
						<th>Quantity</th>
						<th>Unit Price</th>
						<th>Total Price</th>
					</tr>
				</thead>
				<tbody>';
	
	// Create table rows from the selected data
	while($row = $saleDetailsSearchStatement->fetch(PDO::FETCH_ASSOC)){
		$uPrice = $row['unitPrice'];
		$qty = $row['quantity'];
		$discount = $row['discount'];
		$totalPrice = $uPrice * $qty * ((100 - $discount)/100);
		$safeRow = array_map(function($value){
			return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
		}, $row);
			
		$output .= '<tr>' .
						'<td>' . $safeRow['saleID'] . '</td>' .
						'<td>' . $safeRow['itemNumber'] . '</td>' .
						'<td>' . $safeRow['customerID'] . '</td>' .
						'<td>' . $safeRow['customerName'] . '</td>' .
						'<td>' . $safeRow['itemName'] . '</td>' .
						'<td>' . $safeRow['saleDate'] . '</td>' .
						'<td>' . $safeRow['discount'] . '</td>' .
						'<td>' . $safeRow['quantity'] . '</td>' .
						'<td>' . $safeRow['unitPrice'] . '</td>' .
						'<td>' . $totalPrice . '</td>' .
					'</tr>';
	}
	
	$saleDetailsSearchStatement->closeCursor();
	
	$output .= '</tbody>
					<tfoot>
						<tr>
							<th>Sale ID</th>
							<th>Item Number</th>
							<th>Customer ID</th>
							<th>Customer Name</th>
							<th>Item Name</th>
							<th>Sale Date</th>
							<th>Discount %</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Total Price</th>
						</tr>
					</tfoot>
				</table>';
	echo $output;
?>

