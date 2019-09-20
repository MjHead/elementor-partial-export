<div class="">
	<h3 class="cs-vui-title">Export Elementor Content</h3>
	<cx-vui-repeater
		button-label="Add new export group"
		button-style="accent"
		button-size="mini"
		v-model="exportGroups"
		@add-new-item="addNewGroup"
	>
		<cx-vui-repeater-item
			v-for="( group, index ) in exportGroups"
			:title="exportGroups[ index ].name"
			:collapsed="isCollapsed( group )"
			:index="index"
			@clone-item="cloneGroup( $event )"
			@delete-item="deleteGroup( $event )"
			:key="'group_' + index"
		>
			<cx-vui-input
				label="Group Name"
				description="Set export group name to break up content into logical parts"
				:wrapper-css="[ 'equalwidth' ]"
				:size="'fullwidth'"
				:value="exportGroups[ index ].name"
				@input="setGroupProp( index, 'name', $event )"
			></cx-vui-input>
			<cx-vui-f-select
				label="Select Posts"
				description="Set posts to export into current group"
				:wrapper-css="[ 'equalwidth' ]"
				:remote="true"
				:remote-callback="getPosts"
				:size="'fullwidth'"
				:multiple="true"
				:value="exportGroups[ index ].posts"
				@input="setGroupProp( index, 'posts', $event )"
			></cx-vui-f-select>
		</cx-vui-repeater-item>
	</cx-vui-repeater>
	<div class="cx-vui-hr"></div>
	<cx-vui-button
		:button-style="'accent'"
		@click="goToExport"
	>
		<svg slot="label" width="22" height="19" viewBox="0 0 22 19" style="margin: -2px 10px 0 -5px;" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 14H3C1.89543 14 1 14.8954 1 16C1 17.1046 1.89543 18 3 18H19C20.1046 18 21 17.1046 21 16C21 14.8954 20.1046 14 19 14Z" stroke="white" stroke-width="2"/><path d="M11.7071 0.292893C11.3166 -0.0976311 10.6834 -0.0976311 10.2929 0.292893L3.92893 6.65685C3.53841 7.04738 3.53841 7.68054 3.92893 8.07107C4.31946 8.46159 4.95262 8.46159 5.34315 8.07107L11 2.41421L16.6569 8.07107C17.0474 8.46159 17.6805 8.46159 18.0711 8.07107C18.4616 7.68054 18.4616 7.04738 18.0711 6.65685L11.7071 0.292893ZM12 15V1H10V15H12Z" fill="white"/></svg>
		<span slot="label">Export</span>
	</cx-vui-button>
</div>