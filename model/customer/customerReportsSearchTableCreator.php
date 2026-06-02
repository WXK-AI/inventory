<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$customerDetailsSearchSql = 'SELECT * FROM customer';
	$customerDetailsSearchStatement = $conn->prepare($customerDetailsSearchSql);
	$customerDetailsSearchStatement->execute();

	$output = '<table id="customerReportsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
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
		$customerID = htmlspecialchars($row['customerID'], ENT_QUOTES, 'UTF-8');
		$fullName = htmlspecialchars($row['fullName'], ENT_QUOTES, 'UTF-8');
		$email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');
		$mobile = htmlspecialchars($row['mobile'], ENT_QUOTES, 'UTF-8');
		$phone2 = htmlspecialchars($row['phone2'], ENT_QUOTES, 'UTF-8');
		$address = htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8');
		$address2 = htmlspecialchars($row['address2'], ENT_QUOTES, 'UTF-8');
		$city = htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8');
		$district = htmlspecialchars($row['district'], ENT_QUOTES, 'UTF-8');
		$status = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');
		$output .= '<tr>' .
						'<td>' . $customerID . '</td>' .
						'<td>' . $fullName . '</td>' .
						'<td>' . $email . '</td>' .
						'<td>' . $mobile . '</td>' .
						'<td>' . $phone2 . '</td>' .
						'<td>' . $address . '</td>' .
						'<td>' . $address2 . '</td>' .
						'<td>' . $city . '</td>' .
						'<td>' . $district . '</td>' .
						'<td>' . $status . '</td>' .
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
