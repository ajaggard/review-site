<?php
	// Get articles from db
	
	// Connect to db
	$conn = new mysqli('localhost', 'root', '', 'review_site');
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
		
	// Get featured articles
	$featured_results = $conn->query("
		SELECT A.id, T.name AS media_type, A.author, A.title, 
			DATE_FORMAT(A.date, '%Y-%m-%d') AS date, A.date AS datetime,
			A.image_link, A.featured
		FROM `articles` A
			LEFT JOIN `media_types` T ON T.id = A.type_id
		WHERE A.featured = 1
		ORDER BY A.date DESC
		LIMIT 3
	");
	// Get non-featured (and older featured) articles
	$normal_results = $conn->query("
		(
			SELECT A.id, T.name AS media_type, A.author, A.title, 
				DATE_FORMAT(A.date, '%Y-%m-%d') AS date, A.date AS datetime, 
				A.image_link, A.featured
			FROM `articles` A
				LEFT JOIN `media_types` T ON T.id = A.type_id
			WHERE A.featured = 1
			ORDER BY A.date DESC
			LIMIT 3, 5
		)	
		UNION
		(
			SELECT A.id, T.name AS media_type, A.author, A.title, 
				DATE_FORMAT(A.date, '%Y-%m-%d') AS date, A.date AS datetime, 
				A.image_link, A.featured
			FROM `articles` A 
				LEFT JOIN `media_types` T ON T.id = A.type_id
			WHERE A.featured = 0
			ORDER BY A.date DESC
			LIMIT 5
		)	
		ORDER BY datetime DESC
		LIMIT 5
	");
	if ( !$normal_results || $normal_results->num_rows == 0 ) {
		echo "No normal articles found.";    
	}
	else if ( !$featured_results || $featured_results->num_rows == 0 ) {
		echo "No featured articles found.";    
	}
	else {
		$page_title = "My Reviews";
		$page_ident = "home";
		include('../html/header.php'); 
?>
<div id="home_content" class="body-content">
	<div id="feature_slider">
		<?php 
			# Display featured articles as banner
			# TODO: Make banner show 3 and swipe between them
			$num_slides = 0;
			while( $row = $featured_results->fetch_assoc() ) {
				$num_slides++;
		?>
				<a href="../html/article.php?aid=<?php echo $row["id"] ?>" class="feature-banner" 
					style="background-image: url('../images/<?php echo $row["image_link"] ?>')">
					<div class="feature-title">
						<?php echo $row["title"] ?>
					</div>
				</a>
		<?php
			}
		?>
		<div class="switcher">
			<?php
				for ($i = 0; $i < $num_slides; $i++) {
			?>
					<span class="switch-button" data-slide_idx="<?php echo $i ?>"></span>
			<?php
				}
			?>
		</div>
	</div>
	<div id="recent_articles" class="padded-body">
		<?php
			# Show normal articles
			while( $row = $normal_results->fetch_assoc() ){
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
	</div>
</div>
<?php 
		include('../html/footer.php');
	}
	$conn->close();
?>