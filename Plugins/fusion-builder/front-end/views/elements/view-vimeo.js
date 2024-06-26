/* eslint no-useless-escape: 0 */
var FusionPageBuilder = FusionPageBuilder || {};

( function() {

	jQuery( document ).ready( function() {

		// Vimeo Element View.
		FusionPageBuilder.fusion_vimeo = FusionPageBuilder.ElementView.extend( {

			/**
			 * Runs after view DOM is patched.
			 *
			 * @since 2.0
			 * @return {void}
			 */
			afterPatch: function() {

				this._refreshJs();
			},

			/**
			 * Modify template attributes.
			 *
			 * @since 2.0
			 * @param {Object} atts - The attributes.
			 * @return {Object}
			 */
			filterTemplateAtts: function( atts ) {
				var attributes = {};

				// Validate values.
				this.validateValues( atts.values, atts.params );

				// Create attribute objects
				attributes.attr            = this.buildAttr( atts.values );
				attributes.title_attribute = ! _.isEmpty( atts.values.title_attribute ) ? atts.values.title_attribute : 'Vimeo video player ' + this.model.get( 'cid' );
				attributes.values          = atts.values;

				return attributes;
			},

			/**
			 * Modifies the values.
			 *
			 * @since 2.0
			 * @param {Object} values - The values object.
			 * @return {void}
			 */
			validateValues: function( values, params ) {

				// Make sure only the video ID is passed to the iFrame.
				var regExp = /(?:https?:\/\/)?(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/,
					match = values.id.match( regExp );

				if ( match && 2 < match.length ) {
					values.id = match[ 3 ];
				}

				// Make videos 16:9 by default, values.width already set to params.width.
				if ( 'undefined' !== typeof params.width && '' !== params.width && ( 'undefined' === typeof params.height || '' === params.height ) ) {
					values.height = Math.round( parseInt( params.width ) * 9 / 16 );
				}

				// values.height already set to params.height.
				if ( 'undefined' !== typeof params.height && '' !== params.height && ( 'undefined' === typeof params.width || '' === params.width ) ) {
					values.width = Math.round( parseInt( params.height ) * 16 / 9 );
				}

				let autoplay = ( 'true' == values.autoplay || 'yes' === values.autoplay ) ? 'autoplay=1' : 'autoplay=0';

				if ( 'undefined' !== typeof values.start_time && '' !== values.start_time ) {
					if ( -1 === values.api_params.indexOf( '#L=' ) ) {
						const dateObject = new Date( values.start_time * 1000 ),
							hours        = dateObject.getUTCHours(),
							minutes      = dateObject.getUTCMinutes(),
							seconds      = dateObject.getSeconds();

						autoplay += '#t=' + String( hours ).padStart( 2, '0' ) + 'h' + String( minutes ).padStart( 2, '0' ) + 'm' + String( seconds ).padStart( 2, '0' ) + 's';
					}
				}

				values.api_params = autoplay + values.api_params;

				values.height = _.fusionValidateAttrValue( values.height, '' );
				values.width  = _.fusionValidateAttrValue( values.width, '' );

				values.margin_bottom = _.fusionValidateAttrValue( values.margin_bottom, 'px' );
				values.margin_top    = _.fusionValidateAttrValue( values.margin_top, 'px' );
			},

			/**
			 * Builds attributes.
			 *
			 * @since 2.0
			 * @param {Object} values - The values object.
			 * @return {Object}
			 */
			buildAttr: function( values ) {

				// Attributes.
				var attrVimeo = _.fusionVisibilityAtts( values.hide_on_mobile, {
					class: 'fusion-video fusion-vimeo',
					style: this.getStyleVars( values )
				} );

				if ( 'yes' === values.center ) {
					attrVimeo[ 'class' ] += ' center-video';
				}

				if ( '' !== values.alignment ) {
					attrVimeo[ 'class' ] += ' fusion-align' + values.alignment;
				}

				if ( 'true' == values.autoplay || 'yes' === values.autoplay ) {
					attrVimeo[ 'data-autoplay' ] = '1';
				}

				if ( '' !== values[ 'class' ] ) {
					attrVimeo[ 'class' ] += ' ' + values[ 'class' ];
				}

				if ( '' !== values.css_id ) {
					attrVimeo.id = values.css_id;
				}

				return attrVimeo;
			},

			getStyleVars: function( values ) {
				var cssVars,
					customCssVars = {};
				this.values = values;

				cssVars = [
					'margin_top',
					'margin_bottom'
				];

				if ( 'yes' !== values.center ) {
					customCssVars[ 'max-width' ]  = values.width + 'px';
					customCssVars[ 'max-height' ] = values.height + 'px';
				}

				if ( '' !== values.alignment ) {
					customCssVars.width = '100%';
				}

				return this.getCssVarsForOptions( cssVars ) + this.getCustomCssVars( customCssVars );
			}

		} );
	} );
}( jQuery ) );
