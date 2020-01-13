<?php /*
Contributors: inventivogermany
Plugin Name:  Popular Posts | inventivo
Plugin URI:   https://www.inventivo.de/wordpress-agentur/wordpress-plugins
Description:  Display popular posts as a widget
Version:      0.0.1
Author:       Nils Harder
Author URI:   https://www.inventivo.de
Tags: popular posts, posts display, widget
Requires at least: 3.0
Tested up to: 5.2.2
Stable tag: 0.0.1
Text Domain: inventivo-popular-posts
Domain Path: /languages
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (! defined('ABSPATH')) {
    exit;
}

class InventivoPopularPosts extends WP_Widget
{
    public function __construct()
    {
        add_action('widgets_init', array($this,'register_popular_posts'));
        add_action('init', array($this,'register_css'));
        add_action('wp_footer', array($this,'print_css'));

        parent::__construct(
            'inventivo_popular_posts_widget',
            __('inventivo: Popular Posts', 'inventivo-popular-posts'),
            array( 'description' => __( 'Add your popular posts as a widget.', 'inventivo-popular-posts' ), )
        );
    }

    /*-----------------------------------------------------------------------------------*/
    /*	POPULAR POSTS WIDGET
    /*-----------------------------------------------------------------------------------*/

    public function register_popular_posts()
    {
        register_widget( 'InventivoPopularPosts' );
    }

    public function widget($args, $instance)
    {
        global $load_css;
        $load_css = true;
        extract($args);



        $title = apply_filters('widget_title', $instance['title']);

        echo $before_widget;

        if($title) {
            echo  $before_title.$title.$after_title;
        } ?>

        <ul class="popular-posts-list">
            <?php
            $widget_query = new WP_Query(
                array(
                    'post_type' => 'post',
                    'orderby' => 'comment_count',
                    'order' => 'DESC',
                    'posts_per_page' => $instance['amount']
                )
            );
            if( $widget_query->have_posts() ) : while ( $widget_query->have_posts() ): $widget_query->the_post();
                ?>

                <li>
                    <div class="post-image">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('thumbnail'); ?>
                        </a>
                    </div>

                    <div class="meta">
                        <span class="h5"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
                        <em>
                            <?php the_time( get_option('date_format') ); ?>
                            <a href="<?php comments_link(); ?>"><?php comments_number( '0','1','%' ); ?> <i class="fa fa-comment"></i></a>
                        </em>
                    </div>
                </li>

            <?php
            endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </ul>

        <?php echo $after_widget;
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        if( is_numeric($new_instance['amount']) ){
            $instance['amount'] = $new_instance['amount'];
        } else {
            $new_instance['amount'] = '3';
        }

        return $instance;
    }

    public function form($instance)
    {
        $defaults = array('title' => 'Popular Posts', 'amount' => '3');
        $instance = wp_parse_args((array) $instance, $defaults); ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('amount'); ?>">Amount of Posts:</label>
            <input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('amount'); ?>" name="<?php echo $this->get_field_name('amount'); ?>" value="<?php echo $instance['amount']; ?>" />
        </p>
        <?php
    }

    public function register_css()
    {
        wp_register_style('inventivo-popular-posts', plugins_url( 'public/css/inventivo-popular-posts.css', __FILE__ ));
    }

    public function print_css()
    {
        global $load_css;
        // CSS nur laden, wenn shortcode vorhanden ist
        if (!$load_css) {
            return;
        }
        wp_print_styles('inventivo-popular-posts');
    }
}

$InventivoPopularPosts = new InventivoPopularPosts();

