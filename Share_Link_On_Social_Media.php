<?php
/**
 * @package Share_Link_On_Social_Media
 * @version 1.0
 */
/*
Plugin Name: ScriptHere's Share Links On Social Media with Rich Snippets.
Plugin URI: https://github.com/blogscripthere/custom_registration_fields
Description: Simple Open Graph Tags and Twitter Cards to WordPress posts and pages.
Author: Narendra Padala
Author URI: https://in.linkedin.com/in/narendrapadala
Text Domain: shot
Version: 1.0
Last Updated: 16/12/2017
*/

/**
 * get post data for social markups.
 */
function get_markup_data_callback($tags ='open_graph'){
    //init
    $post_data = array();
    //set default image here
    $default_image ="http://yoursite.com/images/default.png";
    //check
    if (is_single() || is_page()) {

        global $post;
        //check
        if(get_the_post_thumbnail($post->ID, 'thumbnail')) {
            $thumb_id = get_post_thumbnail_id($post->ID);
            $thumb_object = get_post($thumb_id);
            $image = $thumb_object->guid;
        } else {
            // set default image
            $image = $default_image;
        }
        //set post details
        $post_data['title']=$post->post_title;
        $post_data['type']=($tags =='open_graph') ? "article":"summary";
        $post_data['image']=$image;
        $post_data['url']=get_permalink($post->ID);
        //get author
        $author_id=$post->post_author;
        $author = the_author_meta( 'user_nicename' , $author_id );
        if(empty($author)){
            //set default
            $author ='admin';
        }
        //set author
        $post_data['author'] =$author;
        //post excerpt as description
        $description = strip_tags($post->post_excerpt);
        //check
        if(empty($description)){
            //set
            $description = substr(strip_tags($post->post_content),0,200) . '...';
        }
        //set description
        $post_data['description']=$description;


    }else{
        //set site details
        $post_data['type']=($tags =='open_graph') ? "website":"summary";
        $post_data['title']= get_bloginfo('name');
        $post_data['image']=$default_image;
        $post_data['url']=site_url();
        $post_data['description']=get_bloginfo('description');
        $post_data['author']=get_bloginfo('name');
    }
    //set
    $post_data['site_name']= get_bloginfo('name');

    //return
    return $post_data;
}


/**
 * open graph tags markup.
 */
function opengraph_markup_callback(){
    //get markup data
    $data= get_markup_data_callback('open_graph');
    //extract
    extract($data);
    //init
    $html ="";
    //check
    if(is_single() || is_page()){
        $html .='<meta property="og:title" content="'.$title.'" />';
    }else{
        $html .='<meta property="og:site_name" content="'.$site_name.'"  />';
    }
    //set
    $html .='   <meta property="og:type" content="'.$type.'" />
                <meta property="og:image" content="'.$image.'" />
                <meta property="og:url" content="'.$url.'"  />
                <meta property="og:description" content="'.$description.'" />';

    //display
    print $html;
}

/**
 * twitter card tags markup
 */
function twittercard_markup_callback(){
    //get markup data
    $data= get_markup_data_callback('twitter_card');
    //extract
    extract($data);
    //init
    $html ="";
    //init
    $html .= '  <meta name="twitter:card" value="'.$type.'" />
                <meta name="twitter:title" content="'.$title.'" />
                <meta name="twitter:description" content="'.$description.'" />
                <meta name="twitter:image" content="'.$image.'" />
                <meta name="twitter:site" content="@'.$author.'">
                <meta name="twitter:creator" content="@'.$author.'">';
    //display
    print $html;
}

/**
 * hook open graph tags markup.
 */
add_action('wp_head', 'opengraph_markup_callback');

/**
 * hook twitter card tags markup
 */
add_action('wp_head', 'twittercard_markup_callback');
