<?php

class WCIO_Editor {

	private $contact_form;
	private $panels = array();

	public function __construct( WCIO_ContactForm $contact_form ) {
		$this->contact_form = $contact_form;
	}

	public function add_panel( $id, $title, $callback ) {
		if ( wpcf7_is_name( $id ) ) {
			$this->panels[$id] = array(
				'title' => $title,
				'callback' => $callback );
		}
	}

	public function display() {
		if ( empty( $this->panels ) ) {
			return;
		}

		echo '<ul id="contact-form-editor-tabs">';

		foreach ( $this->panels as $id => $panel ) {
			echo sprintf( '<li id="%1$s-tab"><a href="#%1$s">%2$s</a></li>',
				esc_attr( $id ), esc_html( $panel['title'] ) );
		}

		echo '</ul>';

		foreach ( $this->panels as $id => $panel ) {
			echo sprintf( '<div class="contact-form-editor-panel" id="%1$s">',
				esc_attr( $id ) );
			call_user_func( $panel['callback'], $this->contact_form );
			echo '</div>';
		}
	}
}

function wpcf7_editor_panel_form( $post ) {
?>
<h2><?php echo esc_html( __( 'Form', 'contact-form-7' ) ); ?></h2>

<?php
	$tag_generator = WCIO_TagGenerator::get_instance();
	$tag_generator->print_buttons();
?>

<textarea id="wpcf7-form" name="wpcf7-form" cols="100" rows="24" class="large-text code"><?php echo esc_textarea( $post->prop( 'form' ) ); ?></textarea>
<?php
}

function wpcf7_editor_panel_mail( $post ) {
	wpcf7_editor_box_mail( $post );

	echo '<br class="clear" />';

	wpcf7_editor_box_mail( $post, array(
		'id' => 'wpcf7-mail-2',
		'name' => 'mail_2',
		'title' => __( 'Mail (2)', 'contact-form-7' ),
		'use' => __( 'Use Mail (2)', 'contact-form-7' ) ) );
}

function wpcf7_editor_box_mail( $post, $args = '' ) {
	$args = wp_parse_args( $args, array(
		'id' => 'wpcf7-mail',
		'name' => 'mail',
		'title' => __( 'Mail', 'contact-form-7' ),
		'use' => null ) );

	$id = esc_attr( $args['id'] );

	$mail = wp_parse_args( $post->prop( $args['name'] ), array(
		'active' => false, 'recipient' => '', 'sender' => '',
		'subject' => '', 'body' => '', 'additional_headers' => '',
		'attachments' => '', 'use_html' => false, 'exclude_blank' => false ) );

	$do_validate = wpcf7_validate_configuration();

?>
<div class="contact-form-editor-box-mail" id="<?php echo $id; ?>">
<h2><?php echo esc_html( $args['title'] ); ?></h2>

<?php
	if ( ! empty( $args['use'] ) ) :
?>
<label for="<?php echo $id; ?>-active"><input type="checkbox" id="<?php echo $id; ?>-active" name="<?php echo $id; ?>-active" class="toggle-form-table" value="1"<?php echo ( $mail['active'] ) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html( $args['use'] ); ?></label>
<p class="description"><?php echo esc_html( __( "Mail (2) is an additional mail template often used as an autoresponder.", 'contact-form-7' ) ); ?></p>
<?php
	endif;
?>

<fieldset>
<legend><?php echo esc_html( __( "In the following fields, you can use these mail-tags:", 'contact-form-7' ) ); ?><br />
<?php $post->suggest_mail_tags( $args['name'] ); ?></legend>
<table class="form-table">
<tbody>
	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-recipient"><?php echo esc_html( __( 'To', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<?php $config_error = $post->config_error(
			sprintf( '%s.recipient', $args['name'] ) ); ?>
		<input type="text" id="<?php echo $id; ?>-recipient" name="<?php echo $id; ?>-recipient" class="large-text code" size="70" value="<?php echo esc_attr( $mail['recipient'] ); ?>"<?php if ( $do_validate && $config_error ) { echo ' aria-invalid="true"'; } ?> />
		<?php if ( $do_validate && $config_error ) {
			echo sprintf( '<br /><span role="alert" class="config-error">%s</span>', $config_error );
		} ?>
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-sender"><?php echo esc_html( __( 'From', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<?php $config_error = $post->config_error(
			sprintf( '%s.sender', $args['name'] ) ); ?>
		<input type="text" id="<?php echo $id; ?>-sender" name="<?php echo $id; ?>-sender" class="large-text code" size="70" value="<?php echo esc_attr( $mail['sender'] ); ?>"<?php if ( $do_validate && $config_error ) { echo ' aria-invalid="true"'; } ?> />
		<?php if ( $do_validate && $config_error ) {
			echo sprintf( '<br /><span role="alert" class="config-error">%s</span>', $config_error );
		} ?>
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-subject"><?php echo esc_html( __( 'Subject', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<?php $config_error = $post->config_error(
			sprintf( '%s.subject', $args['name'] ) ); ?>
		<input type="text" id="<?php echo $id; ?>-subject" name="<?php echo $id; ?>-subject" class="large-text code" size="70" value="<?php echo esc_attr( $mail['subject'] ); ?>"<?php if ( $do_validate && $config_error ) { echo ' aria-invalid="true"'; } ?> />
		<?php if ( $do_validate && $config_error ) {
			echo sprintf( '<br /><span role="alert" class="config-error">%s</span>', $config_error );
		} ?>
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-additional-headers"><?php echo esc_html( __( 'Additional Headers', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<?php $config_error = $post->config_error(
			sprintf( '%s.additional_headers', $args['name'] ) ); ?>
		<textarea id="<?php echo $id; ?>-additional-headers" name="<?php echo $id; ?>-additional-headers" cols="100" rows="4" class="large-text code"<?php if ( $do_validate && $config_error ) { echo ' aria-invalid="true"'; } ?>><?php echo esc_textarea( $mail['additional_headers'] ); ?></textarea>
		<?php if ( $do_validate && $config_error ) {
			echo sprintf( '<br /><span role="alert" class="config-error">%s</span>', $config_error );
		} ?>
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-body"><?php echo esc_html( __( 'Message Body', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<?php $config_error = $post->config_error(
			sprintf( '%s.body', $args['name'] ) ); ?>
		<textarea id="<?php echo $id; ?>-body" name="<?php echo $id; ?>-body" cols="100" rows="18" class="large-text code"<?php if ( $do_validate && $config_error ) { echo ' aria-invalid="true"'; } ?>><?php echo esc_textarea( $mail['body'] ); ?></textarea>
		<?php if ( $do_validate && $config_error ) {
			echo sprintf( '<br /><span role="alert" class="config-error">%s</span>', $config_error );
		} ?>

		<p><label for="<?php echo $id; ?>-exclude-blank"><input type="checkbox" id="<?php echo $id; ?>-exclude-blank" name="<?php echo $id; ?>-exclude-blank" value="1"<?php echo ( ! empty( $mail['exclude_blank'] ) ) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html( __( 'Exclude lines with blank mail-tags from output', 'contact-form-7' ) ); ?></label></p>

		<p><label for="<?php echo $id; ?>-use-html"><input type="checkbox" id="<?php echo $id; ?>-use-html" name="<?php echo $id; ?>-use-html" value="1"<?php echo ( $mail['use_html'] ) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html( __( 'Use HTML content type', 'contact-form-7' ) ); ?></label></p>
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-attachments"><?php echo esc_html( __( 'File Attachments', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<textarea id="<?php echo $id; ?>-attachments" name="<?php echo $id; ?>-attachments" cols="100" rows="4" class="large-text code"><?php echo esc_textarea( $mail['attachments'] ); ?></textarea>
	</td>
	</tr>
</tbody>
</table>
</fieldset>
</div>
<?php
}

function wpcf7_editor_panel_messages( $post ) {
	$messages = wpcf7_messages();

	if ( ! wpcf7_use_really_simple_captcha() ) {
		unset( $messages['captcha_not_match'] );
	}

	$do_validate = wpcf7_validate_configuration();

?>
<h2><?php echo esc_html( __( 'Messages', 'contact-form-7' ) ); ?></h2>
<fieldset>
<legend><?php echo esc_html( __( 'Edit messages used in the following situations.', 'contact-form-7' ) ); ?></legend>
<?php

	foreach ( $messages as $key => $arr ) {
		$field_name = 'wpcf7-message-' . strtr( $key, '_', '-' );

		$config_error = $do_validate
			? $post->config_error( sprintf( 'messages.%s', $key ) ) : '';

?>
<p class="description">
<label for="<?php echo $field_name; ?>"><?php echo esc_html( $arr['description'] ); ?><br />
<input type="text" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" class="large-text" size="70" value="<?php echo esc_attr( $post->message( $key, false ) ); ?>"<?php echo $config_error ? ' aria-invalid="true"' : ''; ?> />
<?php
	if ( $config_error ) {
		echo sprintf( '<br /><span role="alert" class="config-error">%s</span>', $config_error );
	}
?>
</label>
</p>
<?php
	}
?>
</fieldset>
<?php
}

function wpcf7_editor_panel_additional_settings( $post ) {
	$desc_link = wpcf7_link(
		__( 'http://contactform7.com/additional-settings/', 'contact-form-7' ),
		__( 'Additional Settings', 'contact-form-7' ) );
	$description = __( "You can add customization code snippets here. For details, see %s.", 'contact-form-7' );
	$description = sprintf( esc_html( $description ), $desc_link );

?>
<h2><?php echo esc_html( __( 'Additional Settings', 'contact-form-7' ) ); ?></h2>
<fieldset>
<legend><?php echo $description; ?></legend>
<textarea id="wpcf7-additional-settings" name="wpcf7-additional-settings" cols="100" rows="8" class="large-text"><?php echo esc_textarea( $post->prop( 'additional_settings' ) ); ?></textarea>
</fieldset>
<?php
}
