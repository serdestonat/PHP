<?php
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
function enqueue_parent_styles() {
wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

add_shortcode( 'asearch', 'asearch_func' );
function asearch_func( $atts ) {
    $atts = shortcode_atts( array(
        'source' => 'page,post,product',
        'image' => 'true'
    ), $atts, 'asearch' );
static $asearch_first_call = 1;
$source = $atts["source"];
$image = $atts["image"];
$sForam = '<div class="search_bar">
    <form class="asearch" id="asearch'.$asearch_first_call.'" action="/" method="get" autocomplete="off">
    <select id="category-filter">
        <option value="">All Categories</option>
        <option value="clothing">Clothing</option>
        <option value="hoodies">Hoodies</option>
		<option value="t-shirts">T-shirts</option>
		<option value="music">Music</option>
		<option value="albums">Albums</option>
		<option value="singles">Singles</option>
		<option value="posters">Posters</option>
    </select>
        <input type="text" name="s" placeholder="Search ..." id="keyword" class="input_search" onkeyup="searchFetch(this)"><button id="mybtn">🔍</button>
    </form><div class="search_result" id="datafetch" style="display: none;">
        <ul>
            <li>Please wait..</li>
        </ul>
    </div></div>';
$java = '<script>
function searchFetch(e) {
var datafetch = e.parentElement.nextSibling
if (e.value.trim().length > 0) { datafetch.style.display = "block"; } else { datafetch.style.display = "none"; }
const searchForm = e.parentElement;	
e.nextSibling.value = "Please wait..."
var formdata'.$asearch_first_call.' = new FormData(searchForm);
formdata'.$asearch_first_call.'.append("source", "'.$source.'") 
formdata'.$asearch_first_call.'.append("image", "'.$image.'") 
formdata'.$asearch_first_call.'.append("action", "asearch") 
formdata'.$asearch_first_call.'.append("category", document.getElementById("category-filter").value);
AjaxAsearch(formdata'.$asearch_first_call.',e) 
}
async function AjaxAsearch(formdata,e) {
  const url = "'.admin_url("admin-ajax.php").'?action=asearch";
  const response = await fetch(url, {
      method: "POST",
      body: formdata,
  });
  const data = await response.text();
if (data){	e.parentElement.nextSibling.innerHTML = data}else  {
e.parentElement.nextSibling.innerHTML = `<ul><a href="#"><li>Sorry, nothing found</li></a></ul>`
}}	
document.addEventListener("click", function(e) { if (document.activeElement.classList.contains("input_search") == false ) { [...document.querySelectorAll("div.search_result")].forEach(e => e.style.display = "none") } else {if  (e.target.value.trim().length > 0) { e.target.parentElement.nextSibling.style.display = "block"}} })
</script>';
$css = '<style>

form.asearch {
    display: flex;
    flex-wrap: nowrap;
    border: 1px solid #d6d6d6;
    border-radius: 25px;
    padding: 5px 5px 5px 5px;
}

form.asearch button#mybtn {
    order: 3;
    border-radius: 25px;
    padding: 5px;
    cursor: pointer;
    background: none;
}

form.asearch input#keyword {
    flex-grow: 1; /* Allows input to expand and fill available space */
    border: none !important;
    border-radius: 25px;
    padding: 10px 5px; /* Ensure consistent padding with select */
    padding-left: 10px;
    font-size: 14px; /* Consistent font size */
    outline: none; /* Remove the default outline on focus */
    box-shadow: none; /* Remove any box shadow */
}

form.asearch select#category-filter {
    order: 2; /* Ensures the select is on the right */
    margin-left: auto; /* Pushes the dropdown to the right */
    border-radius: 25px;
    background: white;
    padding: 10px 5px; /* Adjust padding to vertically align text */
    padding-left: 15px;
    font-size: 14px; /* Match font size to input elements */
    color: #333; /* Optional: Sets the text color */
    cursor: pointer;
}

div#datafetch {
    background: white;
    z-index: 10;
    position: absolute;
    max-height: 425px;
    overflow: auto;
    box-shadow: 0px 15px 15px #00000036;
    right: 0;
    left: 0;
    top: 50px;
}

div.search_bar {
    width: 600px!important;
    max-width: 90%!important;
    position: relative;
    border: none;
}

div.search_result ul {
    padding: 13px 0px 0px 0px;
    list-style: none;
    margin: auto;
}

div.search_result ul a {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    margin-bottom: 5px;
    text-decoration: none;
}

div.search_result ul a:hover {
    background-color: #f3f3f3;
}

div.search_result ul a li {
    display: flex;
    margin: 0px;
    padding: 5px 0px;
    padding-inline-start: 18px;
    color: #3f3f3f;
    font-weight: bold;
    width: 100%;
    align-items: center;
}

div.search_result ul a li .product-image {
    height: 60px;
    padding: 0px 5px;
    margin-right: 10px;
    flex-shrink: 0;
}

div.search_result ul a li .product-details {
    display: flex;
    flex-direction: column;
}

div.search_result ul a li .product-title {
    font-weight: bold;
}

div.search_result ul a li .product-description {
    color: #045cb4;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
    text-overflow: ellipsis;
}

.asearch input#keyword {
    width: 100%;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}

</style>';
if ( $asearch_first_call == 1 ){	
	 $asearch_first_call++;
	 return "{$sForam}{$java}{$css}"; } elseif  ( $asearch_first_call > 1 ) {
		$asearch_first_call++;
		return "{$sForam}"; }}

add_action('wp_ajax_asearch' , 'asearch');
add_action('wp_ajax_nopriv_asearch','asearch');
function asearch(){
    $s = esc_attr($_POST['s']);
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $args = array(
        'posts_per_page' => 10,
        's' => $s,
        'post_type' => explode(",", esc_attr($_POST['source'])),
    );

    if (!empty($category)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $category,
            ),
        );
    }
    $the_query = new WP_Query( $args );
    if ($the_query->have_posts()) :
        echo '<ul>';
        while ($the_query->have_posts()): $the_query->the_post(); ?>
            <a href="<?php echo esc_url(post_permalink()); ?>">
                <li>
                    <?php
                    $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'single-post-thumbnail');
                    if ($image[0] && trim(esc_attr($_POST['image'])) == "true") { ?>
                        <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" class="product-image">
                    <?php } ?>
                    <div class="product-details">
                        <span class="product-title"><?php the_title(); ?></span>
                        <span class="product-description"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></span>
                    </div>
                </li>
            </a>
        <?php endwhile;
        echo '</ul>';
        wp_reset_postdata();
    endif;
    die();
}
?>