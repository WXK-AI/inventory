<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$vendorDetailsSearchSql = 'SELECT * FROM vendor';
	$vendorDetailsSearchStatement = $conn->prepare($vendorDetailsSearchSql);
	$vendorDetailsSearchStatement->execute();

	$output = '<table id="vendorReportsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
				<thead>
					<tr>
						<th>Vendor ID</th>
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
	while($row = $vendorDetailsSearchStatement->fetch(PDO::FETCH_ASSOC)){
		$safeRow = array_map(function($value){
			return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
		}, $row);

		$output .= '<tr>' .
						'<td>' . $safeRow['vendorID'] . '</td>' .
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
	
	$vendorDetailsSearchStatement->closeCursor();
	
	$output .= '</tbody>
					<tfoot>
						<tr>
							<th>Vendor ID</th>
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
