(function( $ ) {
   'use strict';

   $(document).ready( function() {

      // ----- Menu Ordering -----

      // Initialize sortable elements for parent menu items: https://api.jqueryui.com/sortable/
      $('#custom-admin-menu').sortable({
         items: '> li',
         opacity: 0.6,
         placeholder: 'sortable-placeholder',
         tolerance: 'pointer',
         revert: 250
      });

      // Get the default/current menu order
      let menuOrder = $('#custom-admin-menu').sortable("toArray").toString();

      // Set hidden input value for saving in options
      document.getElementById('admin_site_enhancements[custom_menu_order]').value = menuOrder;

      // Save custom order into a comma-separated string, triggerred after each drag and drop of menu item
      // https://api.jqueryui.com/sortable/#event-update
      // https://api.jqueryui.com/sortable/#method-toArray
      $('#custom-admin-menu').on( 'sortupdate', function( event, ui) {

         // Get the updated menu order
         let menuOrder = $('#custom-admin-menu').sortable("toArray").toString();

         // Set hidden input value for saving in options
         document.getElementById('admin_site_enhancements[custom_menu_order]').value = menuOrder;

      });

      /*! <fs_premium_only> */
      // Prepare variables to store submenu items order
      var submenuSortableId = '',
          submenuOrder = {}; // New object to hold values of submenu items order

      // Initialize sortable elements for submenu items: https://api.jqueryui.com/sortable/
      $('.submenu-sortable').each(function() {
         submenuSortableId = $(this).attr('id');

         $(this).sortable({
            containment: $(this),
            items: '> li',
            opacity: 0.6,
            placeholder: 'submenu-sortable-placeholder',
            tolerance: 'pointer',
            revert: 250
         });
      });

      // Store current submenus items order for saving to options
      $('.submenu-sortable').each(function() {
         submenuSortableId = $(this).attr('id');

         // Get the default/current menu order
         submenuOrder[submenuSortableId] = $(this).sortable("toArray").toString();

         // Set hidden input value for saving in options
         document.getElementById('admin_site_enhancements[custom_submenus_order]').value = JSON.stringify(submenuOrder);
      });

      // Update submenus items order for saving to options
      $('.submenu-sortable').on('sortupdate', function( event, ui) {
         submenuSortableId = $(this).attr('id');
         submenuOrder[submenuSortableId] = $(this).sortable("toArray").toString();
         document.getElementById('admin_site_enhancements[custom_submenus_order]').value = JSON.stringify(submenuOrder);
      });
      
      // Toggle submenu items
      $('.submenu-toggle').click(function() {
         $(this).children('.arrow-right').toggleClass('rotate-down');
         $(this).parents('.menu-item').find('.submenu-wrapper').toggle();
      });

      // Prepare constant to store IDs of menu items that will be hidden
      var hiddenMenuByRoleInput = document.getElementById('admin_site_enhancements[custom_menu_always_hidden]');
      if ( hiddenMenuByRoleInput && hiddenMenuByRoleInput.value ) {
         var menuAlwaysHidden = JSON.parse(hiddenMenuByRoleInput.value); // object
      } else {
         var menuAlwaysHidden = {}; // object
      }

      // Initialize object to store hidden menus and set check mark of 'Hide' checkbox
      $('.parent-menu-hide-checkbox-prm').each(function() {
         var menuId = $(this).data('menu-item-id');
         var menuTitle = $(this).data('menu-item-title');
         var menuIdOri = $(this).data('menu-item-id-ori'); // original, untransformed ID
         var menuUrlFragment = $(this).data('menu-url-fragment');
         if (typeof menuAlwaysHidden[menuId] === 'undefined') {
            menuAlwaysHidden[menuId] = {};
         }
         if (typeof menuAlwaysHidden[menuId]['menu_title'] === 'undefined') {
            menuAlwaysHidden[menuId]['menu_title'] = menuTitle;
         }
         if (typeof menuAlwaysHidden[menuId]['original_menu_id'] === 'undefined') {
            menuAlwaysHidden[menuId]['original_menu_id'] = menuIdOri;         
         }
         if (typeof menuAlwaysHidden[menuId]['hide_by_toggle'] === 'undefined') {
            menuAlwaysHidden[menuId]['hide_by_toggle'] = false;         
         }
         if (typeof menuAlwaysHidden[menuId]['always_hide'] === 'undefined') {
            menuAlwaysHidden[menuId]['always_hide'] = false;         
         }
         if (typeof menuAlwaysHidden[menuId]['always_hide_for'] === 'undefined') {
            menuAlwaysHidden[menuId]['always_hide_for'] = '';         
         }
         if (typeof menuAlwaysHidden[menuId]['which_roles'] === 'undefined') {
            menuAlwaysHidden[menuId]['which_roles'] = [];         
         }
         if ( $('#hide-until-toggled-for-'+menuId).is(':checked') || $('hide-by-role-for-'+menuId).is(':checked') ) {
            $(this).prop('checked', true);            
         }
         if (typeof menuAlwaysHidden[menuId]['menu_url_fragment'] === 'undefined') {
            menuAlwaysHidden[menuId]['menu_url_fragment'] = menuUrlFragment;
         }
         document.getElementById('admin_site_enhancements[custom_menu_always_hidden]').value = JSON.stringify(menuAlwaysHidden);
      });

      // Toggle options panel for hiding parent menu items
      $('.parent-menu-hide-checkbox-prm').click(function() {
         var menuId = $(this).data('menu-item-id'); // may contain transformed ID
         if ($(this).is(':checked')) {
            $('#options-for-'+menuId).show();
            $('#all-selected-roles-options-for-'+menuId).show();
         } else {
            $('#options-for-'+menuId).hide();
            $('#hide-until-toggled-for-'+menuId).prop('checked', false);
            $('#hide-by-role-for-'+menuId).prop('checked', false);
            $('#all-selected-roles-radio-for-'+menuId).hide();
            $('#hide-for-roles-'+menuId).hide();
            $('#menu-required-capability-for-'+menuId).hide();
            menuAlwaysHidden[menuId]['hide_by_toggle'] = false;
            menuAlwaysHidden[menuId]['always_hide'] = false;
         }
         if ( $('#options-for-'+menuId).is(':visible') ) {
            $(this).parent().next('.options-toggle').children('.arrow-right').addClass('rotate-down');
         } else {
            $(this).parent().next('.options-toggle').children('.arrow-right').removeClass('rotate-down');            
         }
         document.getElementById('admin_site_enhancements[custom_menu_always_hidden]').value = JSON.stringify(menuAlwaysHidden);
      });

      $('.hide-until-toggled-checkbox').click(function() {
         var menuId = $(this).data('menu-item-id'); // may contain transformed ID
         if ($(this).is(':checked')) {
            menuAlwaysHidden[menuId]['hide_by_toggle'] = true;
            $('#hide-status-for-'+menuId).prop('checked',true);
         } else {
            menuAlwaysHidden[menuId]['hide_by_toggle'] = false;
            if (!$('#hide-by-role-for-'+menuId).is(':checked')) {
               $('#hide-status-for-'+menuId).prop('checked',false);
               // delete menuAlwaysHidden[menuId];
            }
         }
         document.getElementById('admin_site_enhancements[custom_menu_always_hidden]').value = JSON.stringify(menuAlwaysHidden);
      });

      $('.options-toggle').click(function() {
         $(this).children('.arrow-right').toggleClass('rotate-down');
         var menuId = $(this).data('menu-item-id');
         $('#options-for-'+menuId).toggle();
      });
      
      // Set checked status of 'Always Hide for user role(s)' checkbox and show sub-options accordingly
      $('.hide-by-role-checkbox').each(function() {
         var menuId = $(this).data('menu-item-id');
         if ( $(this).is(':checked') ) {
            $('#hide-status-for-'+menuId).prop('checked', true);
            $('#all-selected-roles-options-for-'+menuId).show();
            $('#all-selected-roles-radio-for-'+menuId).show();
            if ($('#all-roles-except-for-'+menuId).is(':checked') || $('#selected-roles-for-'+menuId).is(':checked')) {
               $('#hide-for-roles-'+menuId).show();
               $('#menu-required-capability-for-'+menuId).show();
            }
         }
      });
      
      // Toggle options to always hide
      $('.hide-by-role-checkbox').click(function() {
         var menuId = $(this).data('menu-item-id');
         $('#hide-status-for-'+menuId).prop('checked', true);
         $('#hide-until-toggled-for-'+menuId).prop('checked', false);
         menuAlwaysHidden[menuId]['hide_by_toggle'] = false;
         if (typeof menuAlwaysHidden[menuId] === 'undefined') {
            menuAlwaysHidden[menuId] = {};
         }
         if ($(this).is(':checked')) {
            menuAlwaysHidden[menuId]['always_hide'] = true;
            $('#all-selected-roles-radio-for-'+menuId).show();
            if ($('#all-roles-except-for-'+menuId).is(':checked') || $('#selected-roles-for-'+menuId).is(':checked')) {
               $('#hide-for-roles-'+menuId).show();
               $('#menu-required-capability-for-'+menuId).show();menuAlwaysHidden
               if ($('#selected-roles-for-'+menuId).is(':checked')) {
                  menuAlwaysHidden[menuId]['always_hide_for'] = 'selected-roles';               
               } else if ($('#all-roles-except-for-'+menuId).is(':checked')) {
                  menuAlwaysHidden[menuId]['always_hide_for'] = 'all-roles-except';                                 
               }

            } else if ($('#all-roles-for-'+menuId).is(':checked')) {
               menuAlwaysHidden[menuId]['always_hide_for'] = 'all-roles';            
            }
         } else {
            menuAlwaysHidden[menuId]['always_hide'] = false;
            menuAlwaysHidden[menuId]['always_hide_for'] = '';
            menuAlwaysHidden[menuId]['which_roles'] = [];
            $('#all-selected-roles-radio-for-'+menuId).hide();
            $('#hide-for-roles-'+menuId).hide();
            $('#menu-required-capability-for-'+menuId).hide();
            if (!$('#hide-until-toggled-for-'+menuId).is(':checked')) {
               $('#hide-status-for-'+menuId).prop('checked',false);
            }
         }
         document.getElementById('admin_site_enhancements[custom_menu_always_hidden]').value = JSON.stringify(menuAlwaysHidden);
      });

      // Toggle role selection checkboxes
      $('.all-selected-roles-radios').change(function() {
         var menuId = $(this).data('menu-item-id');
         if (this.value == 'all-roles-except' || this.value == 'selected-roles') {
            $('#hide-for-roles-'+menuId).show();
            $('#menu-required-capability-for-'+menuId).show();
            if (this.value == 'all-roles-except') {
               menuAlwaysHidden[menuId]['always_hide_for'] = 'all-roles-except';            
            } else if (this.value == 'selected-roles') {
               menuAlwaysHidden[menuId]['always_hide_for'] = 'selected-roles';                           
            }
         } else if ( this.value == 'all-roles' ) {
            menuAlwaysHidden[menuId]['always_hide_for'] = 'all-roles';
            menuAlwaysHidden[menuId]['which_roles'] = [];
            $('#hide-until-toggled-for-'+menuId).prop('checked',false);
            menuAlwaysHidden[menuId]['hide_by_toggle'] = false;
            $('#hide-for-roles-'+menuId).hide();
            $('#menu-required-capability-for-'+menuId).hide();
         }
         document.getElementById('admin_site_enhancements[custom_menu_always_hidden]').value = JSON.stringify(menuAlwaysHidden);
      });

      // Store role checkboxes selection
      $('.role-checkbox').click(function() {
         var menuId = $(this).parent().parent().data('menu-item-id');
         var roleSlug = $(this).data('role');
         if ( ! menuAlwaysHidden[menuId]['which_roles'] ) {
            menuAlwaysHidden[menuId]['which_roles'] = []; // initialize array          
         }
         if ($(this).is(':checked')) {
            menuAlwaysHidden[menuId]['which_roles'].push(roleSlug);            
         } else {
            const index = menuAlwaysHidden[menuId]['which_roles'].indexOf(roleSlug);
            menuAlwaysHidden[menuId]['which_roles'].splice(index,1);
         }
         document.getElementById('admin_site_enhancements[custom_menu_always_hidden]').value = JSON.stringify(menuAlwaysHidden);
      });
      /*! </fs_premium_only> */

      // ----- Parent Menu Item Hiding -----

      // Prepare constant to store IDs of menu items that will be hidden
      if ( document.getElementById('admin_site_enhancements[custom_menu_hidden]') != null ) {
         var hiddenMenuItems = document.getElementById('admin_site_enhancements[custom_menu_hidden]').value.split(","); // array
      } else {
         var hiddenMenuItems = []; // array
      }


      // Detect which menu items are being checked. Ref: https://stackoverflow.com/a/3871602
      Array.from(document.getElementsByClassName('parent-menu-hide-checkbox')).forEach(function(item,index,array) {

         item.addEventListener('click', event => {

            if (event.target.checked) {

               // Add ID of menu item to array
               hiddenMenuItems.push(event.target.dataset.menuItemId);
               
            } else {

               // Remove ID of menu item from array
               const start = hiddenMenuItems.indexOf(event.target.dataset.menuItemId);
               const deleteCount = 1;
               hiddenMenuItems.splice(start, deleteCount);

            }

            // Set hidden input value
            document.getElementById('admin_site_enhancements[custom_menu_hidden]').value = hiddenMenuItems;

         });

      });

      // Clicking on header save button
      $('.asenha-save-button').click( function(e) {

         e.preventDefault();

         // Prepare variable to store ID-Title pairs of menu items
         var customMenuTitles = []; // empty array

         // Initialize other variables
         var menuItemId = '';
         var customTitle = '';

         // Save default/custom title values. Ref: https://stackoverflow.com/a/3871602
         Array.from(document.getElementsByClassName('menu-item-custom-title')).forEach(function(item,index,array) {

            menuItemId = item.dataset.menuItemId;
            customTitle = item.value;
            customMenuTitles.push(menuItemId + '__' + customTitle);            

         });

         // Set hidden input value
         document.getElementById('admin_site_enhancements[custom_menu_titles]').value = customMenuTitles;

      });

   });

})( jQuery );