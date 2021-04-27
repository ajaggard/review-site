<?php
	// Get articles from db
	$conn = new mysqli('localhost', 'root', '', 'review_site');
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$search_term = '';
	if( isset($_GET['search']) ) {
		$search_term = $_GET['search'];
	}
	
	# Get page number for calculations
	$results_per_page = 5;
	$page = 0;
	if(isset($_GET['page']) && is_numeric($_GET['page'])){
		$page = intval($_GET["page"]) - 1;
	}
	else if (isset($_GET['page'])) {
		// If id is invalid, redirect to error page and stop execution to prevent potential sql injection
		$conn->close();
		header("location:error.php");
		exit();
	}
	$page_start = $page * $results_per_page;
	
	$results;
	
	// Get total number of results
	$stmt = $conn->prepare("
		SELECT COUNT(*)
		FROM `articles`
		WHERE title LIKE CONCAT('%',?,'%') OR 
			author LIKE CONCAT('%',?,'%')
	");
	$stmt->bind_param("ss", $search_term, $search_term);
	$stmt->execute();
	$results = $stmt->get_result();
	$result_count = $results->fetch_row()[0] ?? false;
	
	// Get results (potentially filtered)
	$stmt = $conn->prepare("
		SELECT A.id, T.name AS media_type, A.author, A.title, 
			DATE_FORMAT(A.date, '%Y-%m-%d') AS date, A.date AS datetime, 
			A.image_link
		FROM `articles` A 
			LEFT JOIN `media_types` T ON T.id = A.type_id
		WHERE A.title LIKE CONCAT('%',?,'%') OR 
			A.author LIKE CONCAT('%',?,'%')
		ORDER BY A.date DESC
		LIMIT ?,?
	");
	$stmt->bind_param("ssii", $search_term, $search_term, $page_start, $results_per_page);
	$stmt->execute();
	$results = $stmt->get_result();
	
	if ( !$results ) {
		echo "An error has occurred.";
	}
	else {
		$page_title = "Review Archive";
		$page_ident = "archive";
		include('../html/header.php'); 
?>
<div id="archive_content" class="body-content padded-body">
	<form class="search-bar" action="../html/archive.php">
		<input class="search-box" name="search" value="<?php echo $search_term ?>" placeholder="Search"><input class="submit-button" type="submit" value="Go">
	</form>
	<div class="search-results">
		<?php
			if ( $results->num_rows == 0 ) {
		?>
				<div class="notice">No results found.</div>
		<?php
			}
			else {
				$showing_message = "Showing " . $page_start + 1 . " to " .
					($page_start + $results_per_page < $result_count ? $page_start + $results_per_page : $result_count) .
					" of " . $result_count . " result(s).";
		?>
				<div class="notice"><?php echo $showing_message ?></div>
		<?php
				# Show resulting articles
				while( $row = $results->fetch_assoc() ){
					$article_link = "../html/article.php?aid=" . $row["id"];
		?>
					<div class="article-entry">
						<a href="<?php echo $article_link ?>" style="background-image: url('../images/<?php echo $row["image_link"] ?>')"></a>
						<div class="text-link">
							<a class="title" href="<?php echo $article_link ?>"><?php echo $row["title"] ?></a>
							<div class="author">By <?php echo $row["author"] ?></div>
							<div class="date" title="<?php echo $row["datetime"] ?>"><?php echo $row["date"] ?></div>
							<div class="media-type"><?php echo $row["media_type"] ?></div>
						</div>
					</div>
		<?php
				}
		?>
				<div class="pager">
		<?php
						$total_pages = ceil($result_count / $results_per_page);
						for ($i = 1; $i <= $total_pages; $i++) {
		?>
							<a href="../html/archive.php?page=<?php echo $i; if ( $search_term ) { echo "&search=" . urlencode($search_term); } ?>" class="page-button"><?php echo $i ?></a>
		<?php
						}
		?>
				</div>
		<?php
			}
		?>
	</div>
</div>
<?php 
		include('../html/footer.php');
	}
	$conn->close();
?>