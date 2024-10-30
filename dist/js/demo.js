/**
 * SAJID Created Google Review Page Menu
 * ------------------
 */
 
jQuery.noConflict();
 
jQuery(function () {
  'use strict';


  jQuery('[data-toggle="control-sidebar"]').controlSidebar();
  jQuery('[data-toggle="push-menu"]').pushMenu();

  var $pushMenu       = jQuery('[data-toggle="push-menu"]').data('lte.pushmenu');
  var $controlSidebar = jQuery('[data-toggle="control-sidebar"]').data('lte.controlsidebar');
  var $layout         = jQuery('body').data('lte.layout');

  
  function get(name) {
    if (typeof (Storage) !== 'undefined') {
      return localStorage.getItem(name);
    } else {
      window.alert('Please use a modern browser to properly view this template!');
    }
  }

  function store(name, val) {
    if (typeof (Storage) !== 'undefined') {
      localStorage.setItem(name, val);
    } else {
      window.alert('Please use a modern browser to properly view this template!');
    }
  }

  /**
   * Toggles layout classes
   *
   * @param String cls the layout class to toggle
   * @returns void
   */
  function changeLayout(cls) {
    jQuery('body').toggleClass(cls)
    $layout.fixSidebar()
    if (jQuery('body').hasClass('fixed') && cls == 'fixed') {
      $pushMenu.expandOnHover()
      $layout.activate()
    }
    $controlSidebar.fix()
  }

  /**
   * Replaces the old skin with the new skin
   * @param String cls the new skin class
   * @returns Boolean false to prevent link's default action
   */
  function changeSkin(cls) {
    jQuery.each(mySkins, function (i) {
      jQuery('body').removeClass(mySkins[i])
    })

    jQuery('body').addClass(cls)
    store('skin', cls)
    return false
  }

  /**
   * Retrieve default settings and apply them to the template
   *
   * @returns void
   */
  function setup() {
    var tmp = get('skin')
    if (tmp && $.inArray(tmp, mySkins))
      changeSkin(tmp)

    // Add the change skin listener
    jQuery('[data-skin]').on('click', function (e) {
      if (jQuery(this).hasClass('knob'))
        return
      e.preventDefault()
      changeSkin(jQuery(this).data('skin'))
    })

    // Add the layout manager
    jQuery('[data-layout]').on('click', function () {
      changeLayout(jQuery(this).data('layout'))
    })

    jQuery('[data-controlsidebar]').on('click', function () {
      changeLayout(jQuery(this).data('controlsidebar'))
      var slide = !$controlSidebar.options.slide

      $controlSidebar.options.slide = slide
      if (!slide)
        jQuery('.control-sidebar').removeClass('control-sidebar-open')
    })

    jQuery('[data-sidebarskin="toggle"]').on('click', function () {
      var $sidebar = $('.control-sidebar')
      if ($sidebar.hasClass('control-sidebar-dark')) {
        $sidebar.removeClass('control-sidebar-dark')
        $sidebar.addClass('control-sidebar-light')
      } else {
        $sidebar.removeClass('control-sidebar-light')
        $sidebar.addClass('control-sidebar-dark')
      }
    })

    jQuery('[data-enable="expandOnHover"]').on('click', function () {
      jQuery(this).attr('disabled', true)
      $pushMenu.expandOnHover()
      if (!jQuery('body').hasClass('sidebar-collapse'))
        jQuery('[data-layout="sidebar-collapse"]').click()
    })

    //  Reset options
    if (jQuery('body').hasClass('fixed')) {
      jQuery('[data-layout="fixed"]').attr('checked', 'checked')
    }
    if (jQuery('body').hasClass('layout-boxed')) {
      jQuery('[data-layout="layout-boxed"]').attr('checked', 'checked')
    }
    if (jQuery('body').hasClass('sidebar-collapse')) {
      jQuery('[data-layout="sidebar-collapse"]').attr('checked', 'checked')
    }

  }

  // Create the new tab
  var $tabPane = jQuery('<div />', {
    'id'   : 'control-sidebar-theme-demo-options-tab',
    'class': 'tab-pane active'
  })

  // Create the tab button
  var $tabButton = jQuery('<li />', { 'class': 'active' })
    .html('<a href=\'#control-sidebar-theme-demo-options-tab\' data-toggle=\'tab\'>'
      + '<i class="fa fa-wrench"></i>'
      + '</a>')

  // Add the tab button to the right sidebar tabs
  jQuery('[href="#control-sidebar-home-tab"]')
    .parent()
    .before($tabButton)

  // Create the menu
  var $demoSettings = jQuery('<div />')

  // Layout options
  $demoSettings.append(
    '<h4 class="control-sidebar-heading">'
    + 'Google Reviews'
    + '</h4>'
    + '<p data-toggle="control-sidebar">'
    + '<img src="" class="close-icon-img" style="width: 40px; float: right; position: relative; top: -48px; cursor: pointer;" />'
    + '</p>'
    // Fixed layout
    + '<div class="form-group">'
    +'<div id="google-reviews">'
    +' <div id="map-plug" style="position: relative; overflow: hidden;">'
    +'<div style="height: 100%; width: 100%; position: absolute; top: 0px; left: 0px; background-color: rgb(229, 227, 223);">'
    +' <div class="gm-err-container">'
    +'<div class="gm-err-content">'
    + '<label class="control-sidebar-subheading">'
    + '<input type="checkbox"data-layout="fixed"class="pull-right"/> '
    + '<p style="align:center;">Google Reviews</p>'
    + '</label>'
    + '<p>Activate the fixed layout. You can\'t use fixed and boxed layouts together</p>'
    +' <div class="gm-err-icon">'
    +' <img src="https://maps.gstatic.com/mapfiles/api-3/images/icon_error.png" draggable="false" style="-webkit-user-select: none;">'
    +' </div>'
    +' <div class="gm-err-title">Sorry! Something went wrong.'
    +'</div>'
    +' <div class="gm-err-message">This page did not load Google Maps correctly. See the JavaScript console for technical details.'
    +'</div>'
    +'</div>'
    +'</div>'
    +'</div>'
    +'</div>'
    +'</div>'
    + '</div>'
    +'	<a href="#0" style="" data-toggle="control-sidebar" class="cd-btn js-cd-panel-trigger" data-panel="main">Close</a>'
  );
  
  $tabPane.append($demoSettings);
  jQuery('#control-sidebar-home-tab').after($tabPane);

  setup();

  jQuery('[data-toggle="tooltip"]').tooltip();
});

