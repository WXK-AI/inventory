<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['id'])){
		
		$productID = htmlentities($_POST['id']);
		$output = '';
		
			
		$defaultImgFolder = 'data/item_images/';
		
		// Get all item details
		$sql = 'SELECT * FROM item WHERE productID = :productID';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['productID' => $productID]);
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$safeItemNumber = htmlspecialchars($row['itemNumber'], ENT_QUOTES, 'UTF-8');
			$safeImageUrl = htmlspecialchars($row['imageURL'], ENT_QUOTES, 'UTF-8');
			$safeItemName = htmlspecialchars($row['itemName'], ENT_QUOTES, 'UTF-8');
			$safeUnitPrice = htmlspecialchars($row['unitPrice'], ENT_QUOTES, 'UTF-8');
			$safeDiscount = htmlspecialchars($row['discount'], ENT_QUOTES, 'UTF-8');
			$safeStock = htmlspecialchars($row['stock'], ENT_QUOTES, 'UTF-8');
			$output = '<p><img src="';
		
			if($row['imageURL'] === '' || $row['imageURL'] === 'imageNotAvailable.jpg'){
				$output .= 'data/item_images/imageNotAvailable.jpg" class="img-fluid"></p>';
			} else {
				$output .= 'data/item_images/' . $safeItemNumber . '/' . $safeImageUrl . '" class="img-fluid"></p>';
			}
						
			$output .= '<span><strong>Name:</strong> ' . $safeItemName . '</span><br>';
			$output .= '<span><strong>Price:</strong> ' . $safeUnitPrice . '</span><br>';
			$output .= '<span><strong>Discount:</strong> ' . $safeDiscount . ' %</span><br>';
			$output .= '<span><strong>Stock:</strong> ' . $safeStock . '</span><br>';
		}
		
		echo $output;
	}
?>
