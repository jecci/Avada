<?php
/**
 * Underscore.js template
 *
 * @package fusion-builder
 * @since 2.0
 */

?>
<script type="text/html" id="tmpl-fusion_vimeo-shortcode">
<# if ( ! values.id ) { #>
	<div class="fusion-builder-placeholder">{{{ fusionBuilderText.video_placeholder }}}</div>
<# } else { #>
	<div {{{ _.fusionGetAttributes( attr ) }}}>
		<div class="video-shortcode">
			<iframe title="{{ title_attribute }}" src="https://player.vimeo.com/video/{{ values.id }}?{{ values.api_params }}" width="{{ values.width }}" height="{{ values.height }}" allowfullscreen></iframe>
		</div>
	</div>
<# } #>
</script>
