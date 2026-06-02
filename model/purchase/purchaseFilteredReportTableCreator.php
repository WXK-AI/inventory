<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$uPrice = 0;
	$qty = 0;
	$totalPrice = 0;
	
	if(isset($_POST['startDate'])){
		$startDate = htmlentities($_POST['startDate']);
		$endDate = htmlentities($_POST['endDate']);
		
		$purchaseFilteredReportSql = 'SELECT * FROM purchase WHERE purchaseDate BETWEEN :startDate AND :endDate';
		$purchaseFilteredReportStatement = $conn->prepare($purchaseFilteredReportSql);
		$purchaseFilteredReportStatement->execute(['startDate' => $startDate, 'endDate' => $endDate]);

		$output = '<table id="purchaseFilteredReportsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
					<thead>
						<tr>
							<th>Purchase ID</th>
							<th>Item Number</th>
							<th>Purchase Date</th>
							<th>Item Name</th>
							<th>Vendor Name</th>
							<th>Vendor ID</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Total Price</th>
						</tr>
					</thead>
					<tbody>';
		
		// Create table rows from the selected data
		while($row = $purchaseFilteredReportStatement->fetch(PDO::FETCH_ASSOC)){
			$uPrice = $row['unitPrice'];
			$qty = $row['quantity'];
			$totalPrice = $uPrice * $qty;
			$safeRow = array_map(function($value){
				return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
			}, $row);
		
			$output .= '<tr>' .
							'<td>' . $safeRow['purchaseID'] . '</td>' .
							'<td>' . $safeRow['itemNumber'] . '</td>' .
							'<td>' . $safeRow['purchaseDate'] . '</td>' .
							'<td>' . $safeRow['itemName'] . '</td>' .
							'<td>' . $safeRow['vendorName'] . '</td>' .
							'<td>' . $safeRow['vendorID'] . '</td>' .
							'<td>' . $safeRow['quantity'] . '</td>' .
							'<td>' . $safeRow['unitPrice'] . '</td>' .
							'<td>' . $totalPrice . '</td>' .
						'</tr>';
		}
		
		$purchaseFilteredReportStatement->closeCursor();
		
		$output .= '</tbody>
						<tfoot>
							<tr>
								<th>Total</th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>';
		echo $output;
	}
?>

