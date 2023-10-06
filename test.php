function getObjectsPhoto($curr_page = 1, $type = "", $htmlFormat = "")
	{
		$showAll = isset($_GET['showAll']) ? $_GET['showAll'] : "";
		$html = "";
		$page = $curr_page;
		$size = $_POST['size'];
		$createdByids = $_POST['publisherId'] ?? "";
		$deskIds = $_POST['deskId'] ?? "";
		$workState = $_POST['workstate'] ?? "";
		$publishedFrom = "";
		$publishedTo = "";
		$category = $_POST['category'] ?? "";
		$order = isset($_POST['order']) ? $_POST['order'] : "";

		if ($order == 'asc' || $order == 'desc') {
			$orderBy = "created";
		} else {
			$orderBy = "";
		}

		// print_r($mine);
		// die;

		if ($showAll) {

			$from = 1;
			// if ($htmlFormat == 'list' || $htmlFormat == '') {
			// 	$size = 10;
			// } else if($htmlFormat == 'grid'){
			// 	$size = 20;
			// }
		} else {
			if ($htmlFormat == 'list' || $htmlFormat == '') {
				if ($page == 1) {
					$from = (int)$page;
				} else {
					$from = ((int)$page * (int)10) - (int)9;
					// print_r($page);
					// die;
				}
				// $size = 10;
			} else if ($htmlFormat == 'grid') {
				if ($page == 1) {
					$from = (int)$page;
				} else {
					$from = ((int)$page * (int)20) - (int)19;
				}
				// $size = 20;
			}
		}

		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		// print_r($size);
		// die;
		$imageType = $_POST['imageType'] ?? "";
		$dateFrom = $_POST['startDate'] ?? "";
		$dateTo = $_POST['endDate'] ?? "";

		if ($this->session->userdata("is_login") == TRUE) {
			$curl = curl_init();
			$fields = array(
				'From' => $from,
				'Workstate' => $workState,
				'CreatedByIds' => $createdByids,
				'PublishedFrom' => $publishedFrom,
				'PublishedTo' => $publishedTo,
				'Categories' => $category,
				'OrderDirection' => $order,
				'OrderBy' => $orderBy,
				//'CreatedBy' => $mine,
				'Keyword' => urldecode($keyword),
				'Size' => $size,
				'Type' => $type,
				'ImageType' => $imageType,
				'CreatedFrom' => $dateFrom,
				'CreatedTo' => $dateTo,
				'DeskIds' => $deskIds
			);

			print_r(json_encode($fields));
			// die;

			curl_setopt_array($curl, array(
				CURLOPT_URL => INR_API . "My/NewsfeedContents",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HEADER => true,
				CURLOPT_POSTFIELDS => json_encode($fields),
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->session->userdata("token"),
					"Content-Type: application/json"
				),
			));

			$response = curl_exec($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$body = substr($response, $header_size);
			$err = curl_error($curl);

			$result = curl_exec($curl);
			curl_close($curl);
			$content = '';
			if ($htmlFormat == 'list' || $htmlFormat == '') {
				$content = $this->setContent($type, $body, $page, $order, $keyword, $htmlFormat, $imageType);
				// echo $content;
			} else if ($htmlFormat == 'grid') {
				$content = $this->setContentgrid($type, $body, $page, $order, $keyword, $htmlFormat, $imageType);
				// print_r($content);
				// die;
				// echo $content;
			}
			echo $content;
		}
	}