<?php	
	// Connect to db
	$conn = new mysqli('localhost', 'root', '', 'review_site');
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	// Validate the id from the params
	$article_id;
	if(isset($_GET['aid']) && is_numeric($_GET['aid']) && intval($_GET['aid']) > 0){
		$article_id = intval($_GET["aid"]);
	}
	else {
		// If id is invalid, redirect to error page and stop execution to prevent potential sql injection
		$conn->close();
		header("location:error.php");
		exit();
	}
	
	// Get current article
	$stmt = $conn->prepare("
		SELECT A.id, T.name AS media_type, A.author, A.title, A.content,
			DATE_FORMAT(A.date, '%Y-%m-%d') AS date, A.date AS datetime,
			A.image_link
		FROM `articles` A
			LEFT JOIN `media_types` T ON T.id = A.type_id
		WHERE A.id = ?
	");
	$stmt->bind_param("i", $article_id);
	$stmt->execute();
	$results = $stmt->get_result();
	
	if ( !$results || $results->num_rows == 0 ) {
		echo "Article not found.";    
	}
	else {
		$article = $results->fetch_assoc();
        
        // Get comments for article
        $stmt = $conn->prepare("
            SELECT id, reply_to_id, author, content,
                DATE_FORMAT(date, '%Y-%m-%d') AS date, date AS datetime
            FROM `comments`
            WHERE article_id = ?
        ");
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $comments = $stmt->get_result();
		
		$page_title = $article["title"];
		$page_ident = "article";
		include('../html/header.php'); 
?>
<div id="article_content" class="body-content padded-body">
	<div class="main-content">
		<div class="title"><?php echo $article["title"] ?></div>
		<div class="author">Written by <?php echo $article["author"] ?></div>
		<div class="media-type">Type: <?php echo $article["media_type"] ?></div>
		<div class="date" title="<?php echo $article["datetime"] ?>">Published on <?php echo $article["date"] ?></div>
		<hr>
		<div class="content">
			<img alt="Media Logo" src="../images/<?php echo $article["image_link"] ?>" class="article-image">
			<span><?php echo $article["content"] ?></span>
		</div>
        <h3>Comments</h3>
        <div class="comment-container">
            <?php
                if ( $comments->num_rows > 0 ) {
                    // TODO: Display comments
                }
                else {
            ?>
                    <span>No comments</span>
            <?php
                }
            ?>
        </div>
	</div>
</div>
<?php 
		include('../html/footer.php');
	}
	$conn->close();
?>