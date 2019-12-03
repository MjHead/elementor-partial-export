(function () {

	"use strict";

	Vue.component( 'epex-main', {
		template: '#epex_main',
		data: function() {
			return {
				exportGroups: [],
				exportOptions: '',
			};
		},
		methods: {
			buildQuery: function( params ) {
				return Object.keys( params ).map(function( key ) {
					return key + '=' + params[ key ];
				}).join( '&' );
			},
			getQueryArgs: function() {

				var result = {};

				this.exportGroups.forEach( function( group ) {
					result[ group.name ] = group.posts.join( ',' ) + '|' + group.meta;
				} );

				if ( this.exportOptions ) {
					result['options_to_export'] = this.exportOptions;
				}

				return result;

			},
			goToExport: function() {
				window.location = EPEXConfig.export_content + '&' + this.buildQuery( this.getQueryArgs() );
			},
			getPosts: function( query, ids ) {

				if ( ids.length ) {
					ids = ids.join( ',' );
				}

				return wp.apiFetch( {
					method: 'get',
					url: EPEXConfig.get_posts + '&' + this.buildQuery( {
						query: query,
						ids: ids,
					} )
				} );

			},
			addNewGroup: function() {

				var newGroup = {
					name: null,
					posts: [],
					collapsed: false,
				};

				this.exportGroups.push( newGroup );

			},
			cloneGroup: function( index ) {

				var group    = this.exportGroups[ index ],
					newGroup = {
						name: group.name + '-copy',
						posts: group.posts,
					};

				this.exportGroups.push( newGroup );

			},
			deleteGroup: function( index ) {
				this.exportGroups.splice( index, 1 );
			},
			setGroupProp: function( index, key, value ) {
				var group = this.exportGroups[ index ];
				group[ key ] = value;
				this.exportGroups.splice( index, 1, group );

			},
			isCollapsed: function( object ) {

				if ( undefined === object.collapsed || true === object.collapsed ) {
					return true;
				} else {
					return false;
				}

			},
		}
	} );

	new Vue({
		el: '#epex_app',
	});

})();
