<?php
/**
 * Underscore.js template
 *
 * @package fusion-builder
 * @since 2.0
 */

?>
<script type="text/html" id="tmpl-fusion_imageframe-shortcode">
<#
if ( 'undefined' !== typeof values.element_content && ! _.isEmpty( values.element_content ) ) {

	if ( 'magnify' === values.hover_type && 'yes' !== values.lightbox ){
		var html = '<span ' + _.fusionGetAttributes( attr ) + ' ' + _.fusionGetAttributes( imageMagnify ) + '>' + values.element_content + '</span>';
	} else if ( 'scroll' === values.hover_type ) {
		var html = '<span ' + _.fusionGetAttributes( attr ) + ' ' + _.fusionGetAttributes( imageScroll ) + '>' + values.element_content + '</span>';
	} else {
		var html = '<span ' + _.fusionGetAttributes( attr ) + '>' + values.element_content + '</span>';
	}

	if ( 'bottomshadow' === values.style_type ) {
		html += '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 0 600 28" preserveAspectRatio="none"><g clip-path="url(#a)"><mask id="b" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="600" height="28"><path d="M0 0h600v28H0V0Z" fill="#fff"/></mask><g filter="url(#c)" mask="url(#b)"><path d="M16.439-18.667h567.123v30.8S438.961-8.4 300-8.4C161.04-8.4 16.438 12.133 16.438 12.133v-30.8Z" fill="#000"/></g></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h600v28H0z"/></clipPath><filter id="c" x="5.438" y="-29.667" width="589.123" height="52.8" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape"/><feGaussianBlur stdDeviation="5.5" result="effect1_foregroundBlur_3983_183"/></filter></defs></svg>';
	}

	if ( ( 'liftup' === values.hover_type && -1 !== jQuery.inArray( values.caption_style, [ 'off', 'above', 'below' ] ) ) || 'bottomshadow' === values.style_type ) {
		html = '<div class="' + liftupClasses + '">' + liftupStyles + html + '</div>';
	}

	if ( -1 !== jQuery.inArray( values.caption_style, [ 'off', 'above', 'below' ] ) && 'undefined' !== typeof captionHtml ) {
		html = 'above' === values.caption_style ? captionHtml + html : html + captionHtml;
	}

	if ( '' !== values.max_width && '' !== values.aspect_ratio ) {
		html = '<div style="display:inline-block; max-width:100%; width:'+ _.fusionGetValueWithUnit( values.max_width ) +';">' + html + '</div>';
	}

	if ( 'center' === values.align && ! isFlex ) {
		html = '<div class="imageframe-align-center">' + html + '</div>';
	}

	html = '<div ' + _.fusionGetAttributes( responsiveAttr ) + '>' + html + '</div>';

	#>

	<# if ( '' !== filter_style_block ) { #>
		{{{ filter_style_block }}}
	<# } #>

	<# if ( stickyStyles ) { #>
		{{{ stickyStyles }}}
	<# } #>

	{{{ html }}}
<# } else { #>
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 560"><path fill="#EAECEF" d="M0 0h1024v560H0z"/><g fill-rule="evenodd" clip-rule="evenodd"><path fill="#BBC0C4" d="M378.9 432L630.2 97.4c9.4-12.5 28.3-12.6 37.7 0l221.8 294.2c12.5 16.6.7 40.4-20.1 40.4H378.9z"/><path fill="#CED3D6" d="M135 430.8l153.7-185.9c10-12.1 28.6-12.1 38.7 0L515.8 472H154.3c-21.2 0-32.9-24.8-19.3-41.2z"/><circle fill="#FFF" cx="429" cy="165.4" r="55.5"/></g></svg>
<# } #>
</script>
