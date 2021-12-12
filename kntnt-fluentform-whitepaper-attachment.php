<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Kntnt White Paper Attachments for FluentForm
 * Plugin URI:        https://www.kntnt.com/
 * Description:       Enables the custom fields `whitepaper_audience` and `whitepaper_id` of a post to be added as hidden fields of a form provided by FluentForm and embedded in the post. The values of the custom fields are used to attach a white paper to an email sent to the uer filling out the form.
 * Version:           1.0.0
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

defined( 'ABSPATH' ) || die;

// Add the custom fields `whitepaper_audience` and `whitepaper_id` of the post
// embedding the form to hidden fields of the form. To access them in FluentForm
// UI, the same hidden fields must be added ABOVE the Action Hook field.
add_action( 'kntnt-fluent-form-whitepaper', function ( $form ) {

	// Get values from post's custom fields
	$pid             = get_the_ID();
	$interest        = get_post_meta( $pid, 'whitepaper_audience', true );
	$whitepaper_id   = get_post_meta( $pid, 'whitepaper_id', true );
	$whitepaper_name = ( $whitepaper_id && ( $post = get_post( $whitepaper_id ) ) ) ? $post->post_title : '';

	// Add the custom filed values to hidden fields of the form.
	?>
    <input type="hidden" name="interest" value="<?php echo $interest; ?>" data-name="interest">
    <input type="hidden" name="whitepaper_id" value="<?php echo $whitepaper_id; ?>" data-name="whitepaper_id">
    <input type="hidden" name="whitepaper_name" value="<?php echo $whitepaper_name; ?>" data-name="whitepaper_name">
	<?php

}, 10, 1 );

// Add an attachment to an email notification sent to the user who filled out the form.
add_filter( 'fluentform_filter_email_attachments', function ( $emailAttachments, $notification, $form, $formData ) {

	if ( 'field' == $notification['sendTo']['type'] && // If the email is sent to the person who filled out the form…
	     isset( $formData['whitepaper_id'] ) && ( $whitepaper_file = get_attached_file( $formData['whitepaper_id'] ) ) ) { // …and the form contains a valid post id for an attachment…
		$emailAttachments[] = $whitepaper_file; // …attach the attachment to the email sent to the user.
	}

	return $emailAttachments;

}, 10, 4 );