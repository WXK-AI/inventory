<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$customerDetailsSearchSql = 'SELECT * FROM customer';
	$customerDetailsSearchStatement = $conn->prepare($customerDetailsSearchSql);
	$customerDetailsSearchStatement->execute();

	$output = '<table id="customerDetailsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
				<thead>
					<tr>
						<th>Customer ID</th>
						<th>Full Name</th>
						<th>Email</th>
						<th>Mobile</th>
						<th>Phone 2</th>
						<th>Address</th>
						<th>Address 2</th>
						<th>City</th>
						<th>District</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>';
	
	// Create table rows from the selected data
	while($row = $customerDetailsSearchStatement->fetch(PDO::FETCH_ASSOC)){
		$safeRow = array_map(function($value){
			return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
		}, $row);

		$output .= '<tr>' .
						'<td>' . $safeRow['customerID'] . '</td>' .
						'<td>' . $safeRow['fullName'] . '</td>' .
						'<td>' . $safeRow['email'] . '</td>' .
						'<td>' . $safeRow['mobile'] . '</td>' .
						'<td>' . $safeRow['phone2'] . '</td>' .
						'<td>' . $safeRow['address'] . '</td>' .
						'<td>' . $safeRow['address2'] . '</td>' .
						'<td>' . $safeRow['city'] . '</td>' .
						'<td>' . $safeRow['district'] . '</td>' .
						'<td>' . $safeRow['status'] . '</td>' .
					'</tr>';
	}
	
	$customerDetailsSearchStatement->closeCursor();
	
	$output .= '</tbody>
					<tfoot>
						<tr>
							<th>Customer ID</th>
							<th>Full Name</th>
							<th>Email</th>
							<th>Mobile</th>
							<th>Phone 2</th>
							<th>Address</th>
							<th>Address 2</th>
							<th>City</th>
							<th>District</th>
							<th>Status</th>
						</tr>
					</tfoot>
				</table>';
	echo $output;
?>
