<div class="wrap">
    <form method='post'>

        <h1><?php _e("WP Accessibility Helper - Attachments Control Center","wp-accessibility-helper"); ?></h1>
        <?php render_wah_header_notice(); ?>
        <br/>
        </br/>
        <?php
        $attachments = get_posts(
            array(
              'post_type'      => 'attachment',
              'posts_per_page' => 10,
              'paged'          => get_pageNumber()
            )
        );
      $data_array = array();
      foreach($attachments as $post){

        setup_postdata( $post );

        $post_link      = get_permalink($post->ID);
        $alt            = get_post_meta($post->ID, '_wp_attachment_image_alt', true);
        $edit_post_link = get_edit_post_link( $post->ID ) ? get_edit_post_link( $post->ID ): '';

        $data_array[$post->ID]['post_id']        = $post->ID;
        $data_array[$post->ID]['image']          = wp_get_attachment_image_src( $post->ID, array( 32 , 32 ) );
        $data_array[$post->ID]['post_type']      = $post->post_type;
        $data_array[$post->ID]['post_title']     = $post->post_title;
        $data_array[$post->ID]['post_alt']       = $alt;
        $data_array[$post->ID]['permalink']      = $post_link;
        $data_array[$post->ID]['edit_post_link'] = $edit_post_link;

        if( !empty( $data_array[$post->ID]['image'] ) && is_array( $data_array[$post->ID]['image'] ) )
            $data_array[$post->ID]['image'] = reset( $data_array[$post->ID]['image'] );
      }

      ?>
      <div class="main_image_control_table">

        <table id="wp-accessibility-helper-image-control-table" class="accessibility_table">
          <tr>
            <th class="id_column"><?php _e("ID","wp-accessibility-helper"); ?></th>
            <th class="preview_thumbnail"><?php _e("Thumbnail","wp-accessibility-helper"); ?></th>
            <th class="title_column"><?php _e("Title","wp-accessibility-helper"); ?></th>
            <th class="alt_column"><?php _e("Alt tag","wp-accessibility-helper"); ?></th>
            <th class="edit_post_column"><?php _e("Edit image","wp-accessibility-helper"); ?></th>
          </tr>

          <?php if($data_array) :?>
            <?php foreach($data_array as $data): ?>
            <tr data-item="<?php echo $data['post_id']; ?>">
              <td><?php echo $data['post_id']; ?></td>
              <td>
                <img class="athumb" src="<?php echo $data['image']; ?>" alt="" />
              </td>
              <td class="title_box">
                <input type="text" class="attachment_post_title" value="<?php echo $data['post_title']; ?>" />
                <a href="<?php echo $data['permalink']; ?>" target="_blank">
                  <?php _e("view","wp-accessibility-helper"); ?>
                </a>
              </td>
              <td class="alt_box">
                <input type="text" class="attachment_post_alt" value="<?php echo $data['post_alt']; ?>"
                <?php if(empty($data['post_alt']) || $data['post_alt'] ==''): ?>placeholder="<?php _e("no alt tag","wp-accessibility-helper"); ?>"<?php endif; ?> />
              </td>
              <td>
                <a class="attachmentid" data-attachmentid="<?php echo $data['post_id']; ?>"
                  href="<?php echo $data['edit_post_link']; ?>" target="_blank">
                  [<?php _e("Edit image","wp-accessibility-helper") ?>]
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>

        </table>
        <?php get_pagination( 'attachment' ); ?>
      </div>

    </form>
</div>
