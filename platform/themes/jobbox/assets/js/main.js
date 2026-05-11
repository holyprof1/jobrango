;(function ($) {
    'use strict'

    // Page loading
    $(window).on('load', function () {
        $('#preloader-active').fadeOut('slow')
    })
    /*-----------------
        Menu Stick
    -----------------*/
    var header = $('.sticky-bar')
    var win = $(window)
    win.on('scroll', function () {
        var scroll = win.scrollTop()
        if (scroll < 200) {
            header.removeClass('stick')
            $('.header-style-2 .categories-dropdown-active-large').removeClass('open')
            $('.header-style-2 .categories-button-active').removeClass('open')
        } else {
            header.addClass('stick')
        }
    })
    if (typeof WOW !== 'undefined') {
        /*------ Wow Active ----*/
        new WOW().init()
    }
    //sidebar sticky
    if ($('.sticky-sidebar').length) {
        $('.sticky-sidebar').theiaStickySidebar()
    }
    /*----------------------------
        Category toggle function
    ------------------------------*/
    if ($('.categories-button-active').length) {
        var searchToggle = $('.categories-button-active')
        searchToggle.on('click', function (e) {
            e.preventDefault()
            if ($(this).hasClass('open')) {
                $(this).removeClass('open')
                $(this).siblings('.categories-dropdown-active-large').removeClass('open')
            } else {
                $(this).addClass('open')
                $(this).siblings('.categories-dropdown-active-large').addClass('open')
            }
        })
    }
    /*---------------------
        Select active
    --------------------- */
    if ($('.select-active').length) {
        $('.select-active').select2()
    }
    /*---- CounterUp ----*/
    if ($('.count').length) {
        $('.count').counterUp({
            delay: 10,
            time: 2000,
        })
    }
    // Isotope active
    if ($('.grid').length) {
        $('.grid').imagesLoaded(function () {
            // init Isotope
            var $grid = $('.grid').isotope({
                itemSelector: '.grid-item',
                percentPosition: true,
                layoutMode: 'masonry',
                masonry: {
                    // use outer width of grid-sizer for columnWidth
                    columnWidth: '.grid-item',
                },
            })
        })
    }
    /*====== SidebarSearch ======*/
    function sidebarSearch() {
        var searchTrigger = $('.search-active'),
            endTriggersearch = $('.search-close'),
            container = $('.main-search-active')
        searchTrigger.on('click', function (e) {
            e.preventDefault()
            container.addClass('search-visible')
        })
        endTriggersearch.on('click', function () {
            container.removeClass('search-visible')
        })
    }
    sidebarSearch()
    /*====== Sidebar menu Active ======*/
    function mobileHeaderActive() {
        var navbarTrigger = $('.burger-icon'),
            endTrigger = $('.mobile-menu-close'),
            container = $('.mobile-header-active'),
            wrapper4 = $('body')

        if (!container.length || !navbarTrigger.length) {
            return
        }

        if (!container.attr('id')) {
            container.attr('id', 'mobile-header-drawer')
        }

        if (!wrapper4.children('.body-overlay-1').length) {
            wrapper4.prepend('<div class="body-overlay-1"></div>')
        }

        navbarTrigger.attr({
            'aria-controls': container.attr('id'),
            'aria-expanded': 'false',
        })

        var overlay = wrapper4.children('.body-overlay-1').first()
        var closeMobileHeader = function () {
            container.removeClass('sidebar-visible')
            wrapper4.removeClass('mobile-menu-active')
            navbarTrigger.removeClass('burger-close').attr('aria-expanded', 'false')
        }

        navbarTrigger.on('click', function (e) {
            e.preventDefault()
            var isOpening = !container.hasClass('sidebar-visible')

            navbarTrigger.toggleClass('burger-close', isOpening).attr('aria-expanded', isOpening ? 'true' : 'false')
            container.toggleClass('sidebar-visible', isOpening)
            wrapper4.toggleClass('mobile-menu-active', isOpening)
        })
        endTrigger.on('click', closeMobileHeader)
        overlay.on('click', closeMobileHeader)
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && container.hasClass('sidebar-visible')) {
                closeMobileHeader()
            }
        })
    }
    mobileHeaderActive()
    /*---------------------
        Mobile menu active
    ------------------------ */
    var $offCanvasNav = $('.mobile-menu'),
        $offCanvasNavSubMenu = $offCanvasNav.find('.sub-menu')
    /*Add Toggle Button With Off Canvas Sub Menu*/
    $offCanvasNavSubMenu.each(function (index) {
        var $submenu = $(this)
        var $parent = $submenu.parent()
        var submenuId = $submenu.attr('id') || 'mobile-sub-menu-' + (index + 1)

        $submenu.attr({
            id: submenuId,
            'aria-hidden': 'true',
        })

        if (! $parent.children('.menu-expand').length) {
            $parent.prepend(
                '<span class="menu-expand" role="button" tabindex="0" aria-expanded="false" aria-controls="' +
                    submenuId +
                    '"><i class="fi-rr-angle-small-down"></i></span>'
            )
        }
    })
    /*Close Off Canvas Sub Menu*/
    $offCanvasNavSubMenu.slideUp()
    /*Category Sub Menu Toggle*/
    $offCanvasNav.on('click', 'li a, li .menu-expand', function (e) {
        var $this = $(this)
        var $parent = $this.parent('li')
        var $submenu = $this.siblings('ul')

        if (
            $parent.attr('class').match(/\b(menu-item-has-children|has-children|has-sub-menu)\b/) &&
            ($this.attr('href') === '#' || $this.hasClass('menu-expand'))
        ) {
            e.preventDefault()
            if ($submenu.is(':visible')) {
                $parent.removeClass('active')
                $submenu.attr('aria-hidden', 'true').slideUp()
                $parent.children('.menu-expand').attr('aria-expanded', 'false')
            } else {
                var $siblings = $this.closest('li').siblings('li')

                $parent.addClass('active')
                $siblings.removeClass('active').find('li').removeClass('active')
                $siblings.find('ul:visible').attr('aria-hidden', 'true').slideUp()
                $siblings.find('.menu-expand').attr('aria-expanded', 'false')
                $submenu.attr('aria-hidden', 'false').slideDown()
                $parent.children('.menu-expand').attr('aria-expanded', 'true')
            }
        }
    })
    $offCanvasNav.on('keydown', '.menu-expand', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault()
            $(this).trigger('click')
        }
    })
    /*--- language currency active ----*/
    $('.mobile-language-active').on('click', function (e) {
        e.preventDefault()
        $(this).attr('aria-expanded', $('.lang-dropdown-active').is(':visible') ? 'false' : 'true')
        $('.lang-dropdown-active').slideToggle(900)
    })
    /*--- categories-button-active-2 ----*/
    $('.categories-button-active-2').on('click', function (e) {
        e.preventDefault()
        $('.categori-dropdown-active-small').slideToggle(900)
    })
    /*--- Mobile demo active ----*/
    var demo = $('.tm-demo-options-wrapper')
    $('.view-demo-btn-active').on('click', function (e) {
        e.preventDefault()
        demo.toggleClass('demo-open')
    })
    /*-----More Menu Open----*/
    $('.more_slide_open').slideUp()
    $('.more_categories').on('click', function () {
        $(this).toggleClass('show')
        $('.more_slide_open').slideToggle()
    })
    /* --- SwiperJS --- */

    $('.swiper-group-9').each(function () {
        var swiper_10_items = new Swiper(this, {
            spaceBetween: 20,
            slidesPerView: 9,
            slidesPerGroup: 2,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next-group-9',
                prevEl: '.swiper-button-prev-group-9',
            },
            autoplay: {
                delay: 10000,
            },
            breakpoints: {
                1360: {
                    slidesPerView: 9,
                },
                1199: {
                    slidesPerView: 7,
                },
                800: {
                    slidesPerView: 5,
                },
                390: {
                    slidesPerView: 4,
                },
                250: {
                    slidesPerView: 3,
                    slidesPerGroup: 1,
                    spaceBetween: 15,
                },
            },
        })
    })
    $('.swiper-group-7').each(function () {
        var swiper_10_items = new Swiper(this, {
            spaceBetween: 20,
            slidesPerView: 7,
            slidesPerGroup: 2,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next-group-7',
                prevEl: '.swiper-button-prev-group-7',
            },
            autoplay: {
                delay: 10000,
            },
            breakpoints: {
                1360: {
                    slidesPerView: 7,
                },
                1199: {
                    slidesPerView: 5,
                },
                800: {
                    slidesPerView: 4,
                },
                390: {
                    slidesPerView: 3,
                },
                250: {
                    slidesPerView: 2,
                    slidesPerGroup: 1,
                    spaceBetween: 15,
                },
            },
        })
    })
    $('.swiper-group-6').each(function () {
        var swiper_6_items = new Swiper(this, {
            spaceBetween: 30,
            slidesPerView: 6,
            slidesPerGroup: 2,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 10000,
            },
            breakpoints: {
                1360: {
                    slidesPerView: 6,
                },
                1199: {
                    slidesPerView: 5,
                },
                992: {
                    slidesPerView: 4,
                },
                600: {
                    slidesPerView: 3,
                },
                400: {
                    slidesPerView: 2,
                },
                250: {
                    slidesPerView: 1,
                    slidesPerGroup: 1,
                    spaceBetween: 15,
                },
            },
        })
    })
    $('.swiper-group-5').each(function () {
        var swiper_5_items = new Swiper(this, {
            spaceBetween: 15,
            slidesPerGroup: 3,
            slidesPerView: 5,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            autoplay: {
                delay: 10000,
            },
            breakpoints: {
                1199: {
                    slidesPerView: 5,
                },
                800: {
                    slidesPerView: 3,
                },
                475: {
                    slidesPerView: 2,
                },
                350: {
                    slidesPerView: 1,
                    slidesPerGroup: 1,
                },
                275: {
                    slidesPerView: 1,
                },
            },
        })
    })
    $('.swiper-group-4-border').each(function () {
        var swiper_4_items_border = new Swiper(this, {
            spaceBetween: 30,
            slidesPerView: 4,
            slidesPerGroup: 1,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next-border',
                prevEl: '.swiper-button-prev-border',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            autoplay: {
                delay: 10000,
            },
            breakpoints: {
                1360: {
                    slidesPerView: 4,
                },
                1199: {
                    slidesPerView: 3,
                },
                600: {
                    slidesPerView: 2,
                },
                350: {
                    slidesPerView: 1,
                },
                150: {
                    slidesPerView: 1,
                },
            },
        })
    })
    $('.swiper-group-4').each(function () {
        var swiper_3_items = new Swiper(this, {
            spaceBetween: 30,
            slidesPerView: 4,
            slidesPerGroup: 1,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next-4',
                prevEl: '.swiper-button-prev-4',
            },
            autoplay: {
                delay: 10000,
            },
            breakpoints: {
                1199: {
                    slidesPerView: 4,
                },
                800: {
                    slidesPerView: 2,
                },
                400: {
                    slidesPerView: 1,
                },
                150: {
                    slidesPerView: 1,
                },
            },
        })
    })
    $('.swiper-group-4-banner').each(function () {
        var swiper_3_items = new Swiper(this, {
            spaceBetween: 30,
            slidesPerView: 4,
            slidesPerGroup: 1,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next-4',
                prevEl: '.swiper-button-prev-4',
            },
            autoplay: {
                delay: 10000,
            },
            breakpoints: {
                1199: {
                    slidesPerView: 4,
                },
                800: {
                    slidesPerView: 4,
                },
                400: {
                    slidesPerView: 3,
                },
                150: {
                    slidesPerView: 2,
                },
            },
        })
    })
    $('.swiper-group-3').each(function () {
        var swiper_3_items = new Swiper(this, {
            spaceBetween: 30,
            slidesPerView: 3,
            slidesPerGroup: 1,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 10000,
            },
            breakpoints: {
                1199: {
                    slidesPerView: 3,
                },
                800: {
                    slidesPerView: 2,
                },
                400: {
                    slidesPerView: 1,
                },
                250: {
                    slidesPerView: 1,
                },
            },
        })
    })
    $('.swiper-group-3-explore').each(function () {
        var swiper_3_items = new Swiper(this, {
            spaceBetween: 30,
            slidesPerView: 3,
            slidesPerGroup: 1,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 10000,
            },
            breakpoints: {
                1199: {
                    slidesPerView: 3,
                },
                600: {
                    slidesPerView: 3,
                },
                350: {
                    slidesPerView: 2,
                },
                250: {
                    slidesPerView: 1,
                },
            },
        })
    })
    $('.swiper-group-2').each(function () {
        var swiper_2_items = new Swiper(this, {
            spaceBetween: 30,
            slidesPerView: 2,
            slidesPerGroup: 1,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 10000,
            },
            breakpoints: {
                1199: {
                    slidesPerView: 2,
                },
                800: {
                    slidesPerView: 1,
                },
                600: {
                    slidesPerView: 1,
                },
                400: {
                    slidesPerView: 1,
                },
                250: {
                    slidesPerView: 1,
                },
            },
        })
    })

    if ($('#testimonial-slider').length) {
        new Swiper('#testimonial-slider', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            breakpoints: {
                200: {
                    slidesPerView: 1,
                    spaceBetween: 10,
                },
                992: {
                    slidesPerView: 4,
                    spaceBetween: 20,
                },
            },
        })
    }

    if ($('#testimonial-slider-2').length) {
        new Swiper('#testimonial-slider-2', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            breakpoints: {
                200: {
                    slidesPerView: 1,
                    spaceBetween: 10,
                },
                992: {
                    slidesPerView: 4,
                    spaceBetween: 20,
                },
            },
        })
    }

    $('.swiper-group-1').each(function () {
        var swiper_1_items = new Swiper(this, {
            spaceBetween: 30,
            slidesPerView: 1,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next-1',
                prevEl: '.swiper-button-prev-1',
            },
            autoplay: {
                delay: 10000,
            },
        })
    })

    //Dropdown selected item
    $('.dropdown-menu.js-dropdown-clickable li a').on('click', function (e) {
        e.preventDefault()
        $(this)
            .parents('.dropdown')
            .find('.btn span')
            .html($(this).text() + ' <span class="caret"></span>')
        $(this).parents('.dropdown').find('.btn').val($(this).data('value'))
    })
    $('.list-tags-job .remove-tags-job').on('click', function (e) {
        e.preventDefault()
        $(this).closest('.job-tag').remove()
    })
    // Video popup
    if ($('.popup-youtube').length) {
        $('.popup-youtube').magnificPopup({
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false,
        })
    }
    // Init function billed
    checkBilled()
})(jQuery)
// Check billed
function checkBilled() {
    var checkBox = $('#cb_billed_type')
    var forMonth = $('.for-month')
    var forYear = $('.for-year')
    for (var i = 0; i < forMonth.length; i++) {
        if (checkBox.is(':checked')) {
            forYear.eq(i).addClass('display-year')
            forMonth.eq(i).removeClass('display-month')
        } else {
            forYear.eq(i).removeClass('display-year')
            forMonth.eq(i).addClass('display-month')
        }
    }
}
//Perfect Scrollbar
if (document.querySelector('.mobile-header-wrapper-inner')) {
    new PerfectScrollbar('.mobile-header-wrapper-inner')
}

$(document).on('click', '.btn-remove-avatar', function (e) {
    e.preventDefault()

    if (confirm($(this).attr('data-confirm'))) {
        $('#delete-avatar-form').submit()
    }
})
