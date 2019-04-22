/**
 * Theme name: Ammi
 * ammi-theme.js v1.0.0
 */

/**
 * Table of Contents:
 *
 * 1.0 - Theme data: string -> boolean
 * 2.0 - Show main sidebar (hidden right sidebar)
 * 3.0 - Show drop-down search form
 * 4.0 - Superfish menu
 * 5.0 - Mobile menu
 * 6.0 - owlCarousel: Gallery post format
 * 7.0 - magnificPopup
 * 8.0 - "Back to top" button
 * 9.0 - Posts slider widget (owlCarousel)
 */

jQuery.noConflict()(function($) {
  $(document).ready(function() {
    'use strict';

    /**
     * 1.0 - Theme data: string -> boolean
     * ----------------------------------------------------
     */

    ammiData.toTopButton = (ammiData.toTopButton === 'true') ? true : false;


    /**
     * 2.0 - Show main sidebar (hidden right sidebar)
     * ----------------------------------------------------
     */

    function showMainSidebar() {
      var
        $globalContainer = $('#bwp-global-container'),
        $showSidebarButton = $('#bwp-show-main-sidebar');

      // "show sidebar" button - click
      $showSidebarButton.on('click', function() {

        // if the sidebar is closed
        if ($globalContainer.hasClass('bwp-sidebar-close')) {
          // open sidebar
          $globalContainer.removeClass('bwp-sidebar-close').addClass('bwp-sidebar-open');
          // "show sidebar" button - add "bwp-active" class
          $showSidebarButton.addClass('bwp-active');
        } else {
          // close sidebar
          $globalContainer.removeClass('bwp-sidebar-open').addClass('bwp-sidebar-close');
          // "show sidebar" button - remove "bwp-active" class
          $showSidebarButton.removeClass('bwp-active');
        }

        return false;
      });
    }

    // start "showMainSidebar" function
    showMainSidebar();


    /**
     * 3.0 - Show drop-down search form
     * ----------------------------------------------------
     */

    function showDropdownSearchForm() {
      var
        $searchButton = $('#bwp-show-dropdown-search'),
        $searchButtonIcon = $('#bwp-show-dropdown-search i'),
        $searchForm = $('#bwp-dropdown-search');

      // search button - click
      $searchButton.on('click', function() {

        // if the search form is hidden
        if ($searchForm.hasClass('bwp-search-hidden')) {

          // search button - add "bwp-active" class
          $searchButton.addClass('bwp-active');

          // add "close" icon
          $searchButtonIcon.hide().attr('class', 'fa fa-times').fadeIn(200);

          // show search form
          $searchForm.css('display', 'block');
          if ($searchForm.hasClass('bwpSlideDown')) {
            $searchForm.removeClass('bwpSlideDown');
          }
          $searchForm.removeClass('bwp-search-hidden').addClass('bwpSlideUp');

          // search field - focus
          if ($(window).width() > 768) {
            $('#bwp-dropdown-search .bwp-search-field').focus();
          }

        } else {

          // search button - remove "bwp-active" class
          $searchButton.removeClass('bwp-active');

          // add "search" icon
          $searchButtonIcon.hide().attr('class', 'fa fa-search').fadeIn(200);

          // hide search form
          $searchForm.removeClass('bwpSlideUp').addClass('bwpSlideDown bwp-search-hidden');
          setTimeout(function() {
            $searchForm.attr('style', ''); // remove "display: block;" style
          }, 200);

        }

        return false;
      });
    }

    // start "showDropdownSearchForm" function
    showDropdownSearchForm();


    /**
     * 4.0 - Superfish menu
     * ----------------------------------------------------
     */

    $('ul.sf-menu').superfish({
      delay: 400,
      animation: {opacity: 'show', marginTop: '0'},
      animationOut: {opacity: 'hide', marginTop: '15'},
      speed: 'fast'
    });


    /**
     * 5.0 - Mobile menu
     * ----------------------------------------------------
     */

    function showMobileMenu() {
      var
        $menuIcon = $('#bwp-sm-menu-icon'),
        $menuContainer = $('#bwp-dropdown-sm-menu');

      // menu icon - click
      $menuIcon.on('click', function() {

        // if the menu is hidden
        if ($menuContainer.hasClass('bwp-sm-menu-hidden')) {

          // show menu
          $menuContainer.css('display', 'block');
          if ($menuContainer.hasClass('bwpSlideDown')) {
            $menuContainer.removeClass('bwpSlideDown');
          }
          $menuContainer.removeClass('bwp-sm-menu-hidden').addClass('bwpSlideUp');

          // menu icon - add "bwp-active" class
          $menuIcon.addClass('bwp-active');

        } else {

          // hide menu
          $menuContainer.removeClass('bwpSlideUp').addClass('bwpSlideDown bwp-sm-menu-hidden');
          setTimeout(function() {
            $menuContainer.attr('style', ''); // remove "display: block;" style
          }, 200);

          // menu icon - remove "bwp-active" class
          $menuIcon.removeClass('bwp-active');

        }

        return false;
      });
    }

    // start "showMobileMenu" function
    showMobileMenu();


    /**
     * 6.0 - owlCarousel: Gallery post format
     * ----------------------------------------------------
     */

    $('.bwp-post-carousel').owlCarousel({
      singleItem: true,
      slideSpeed : 600,
      paginationSpeed: 600,
      rewindSpeed: 1000,
      autoPlay: false, // 5000
      stopOnHover: true,
      navigation : true,
      navigationText: ["<i class='fa fa-caret-left'></i>","<i class='fa fa-caret-right'></i>"],
      pagination: true,
    });


    /**
     * 7.0 - magnificPopup
     * ----------------------------------------------------
     */

    // popup-image
    function popupImage() {
      $('.bwp-popup-image').magnificPopup({
        type: 'image',

        closeOnContentClick: true,
        closeMarkup: '<button title="%title%" type="button" class="mfp-close bwp-mfp-close-button"></button>',

        fixedContentPos: true,
        fixedBgPos: true,
        overflowY: 'auto',

        removalDelay: 300,
        mainClass: 'bwp-popup-slide-in',
        callbacks: {
          beforeOpen: function() {
            this.container.data('scrollTop', parseInt($(window).scrollTop()));
          },
          afterClose: function(){
            $('html, body').scrollTop(this.container.data('scrollTop'));
          },
        }
      });
    }

    // popup-gallery
    function popupGallery() {
      $('.bwp-popup-gallery').each(function() {
        $(this).magnificPopup({
          delegate: 'a.bwp-popup-gallery-item',
          type: 'image',
          gallery: {
            enabled: true,
            navigateByImgClick: true,
            arrowMarkup: '<button title="%title%" type="button" class="bwp-mfp-arrow bwp-mfp-arrow-%dir%"></button>',
            tPrev: 'Previous',
            tNext: 'Next',
            tCounter: '<span>%curr% / %total%</span>'
          },

          closeMarkup: '<button title="%title%" type="button" class="mfp-close bwp-mfp-close-button"></button>',

          fixedContentPos: true,
          fixedBgPos: true,
          overflowY: 'auto',

          removalDelay: 300,
          mainClass: 'bwp-popup-slide-in',
          callbacks: {
            beforeOpen: function() {
              this.container.data('scrollTop', parseInt($(window).scrollTop()));
            },
            afterClose: function(){
              $('html, body').scrollTop(this.container.data('scrollTop'));
            },
          }
        });
      });
    }

    // popup-video / popup-audio
    function popupIframe() {
      $('.bwp-popup-video, .bwp-popup-audio').magnificPopup({
        type: 'inline',

        closeMarkup: '<button title="%title%" type="button" class="mfp-close bwp-mfp-close-button"></button>',

        fixedContentPos: true,
        fixedBgPos: true,
        overflowY: 'auto',

        midClick: true,
        preloader: false,
        removalDelay: 300,
        mainClass: 'bwp-popup-slide-in',
        callbacks: {
          beforeOpen: function() {
            this.container.data('scrollTop', parseInt($(window).scrollTop()));
          },
          afterClose: function(){
            $('html, body').scrollTop(this.container.data('scrollTop'));
          },
        }
      });
    }

    // start magnificPopup
    popupImage();
    popupGallery();
    popupIframe();


    /**
     * 8.0 - "Back to top" button
     * ----------------------------------------------------
     */

    function backToTopButton() {
      var
        $scrollTopBtn = $('#bwp-scroll-top');

      // first hide the button
      if ($scrollTopBtn.hasClass('bwp-visible-button')) {
        $scrollTopBtn.removeClass('bwp-visible-button');
      }

      // show button when scrolling
      $(window).scroll(function() {
        if ($(window).scrollTop() > 1000) {
          $scrollTopBtn.addClass('bwp-visible-button');
        }	else {
          $scrollTopBtn.removeClass('bwp-visible-button');
        }
      });

      // click on the button
      $scrollTopBtn.on('click', function() {
        $('html:not(:animated), body:not(:animated)').animate({scrollTop: 0}, 0);
        return false;
      });
    }

    // start "backToTopButton" function
    if (ammiData.toTopButton) {
      backToTopButton();
    }


    /**
     * 9.0 - Posts slider widget (owlCarousel)
     * ----------------------------------------------------
     */

    $('.widget-bwp-posts-slider').owlCarousel({
      singleItem: true,
      slideSpeed : 600,
      rewindSpeed: 1000,
      autoHeight: true,
      autoPlay: false, // 5000
      stopOnHover: true,
      navigation : true,
      navigationText: ["<i class='fa fa-caret-left'></i>","<i class='fa fa-caret-right'></i>"],
      pagination: false,
    });


  });
});
