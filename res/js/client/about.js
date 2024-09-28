$(document).ready(function() {
    function p(p) {
        $(p.target).prev(".toggle-submenu").find(".more-less").toggleClass("fa-plus-square fa-minus-square")
    }
    $("#mobile_menu").click(function(p) {
            $("#sidebar_menu, #mobile_menu").toggleClass("active"),
                $("#sidebar_menu_bg").addClass("active"),
                $("#search-toggle, #search, #header").removeClass("active"),
                $("body").toggleClass("body-hidden")
        }),
        $(".toggle-sidebar, #sidebar_menu_bg").click(function(p) {
            $("#sidebar_menu, #mobile_menu, #sidebar_menu_bg, #search-toggle, #search, #header").removeClass("active"),
                $("body").removeClass("body-hidden")
        }),
        $("#search-toggle").click(function(p) {
            $("#search, #search-toggle, #sidebar_menu_bg, #header").toggleClass("active"),
                $("body").toggleClass("body-hidden")
        }),
        $("#sidebar_subs_genre, #sidebar_subs_country").on("hidden.bs.collapse", p),
        $("#sidebar_subs_genre, #sidebar_subs_country").on("shown.bs.collapse", p)
});


function redirect(p) {
    p.redirect && (location.href = p.redirect)
}

function watch(p, s) {
    $(".detail_page").hasClass("watch_page") && (s.preventDefault(),
        s = $(p).attr("href"),
        p = $(p).attr("data-linkid"),
        $(".link-item").removeClass("active"),
        history.pushState({}, "", s),
        get_source(p))
}

function watch2(p) {
    var s;
    if ($(".detail_page").hasClass("watch_page"))
        return s = $(p).attr("href"),
            p = $(p).attr("data-linkid"),
            $(".link-item").removeClass("active"),
            history.pushState({}, "", s),
            get_source(p),
            !1
}


$(document).ready(function() {
    $("#text-home-expand").click(function(p) {
            $(".text-home").toggleClass("thm-expand")
        }),
        $(".detail-extend-toggle").click(function(p) {
            $(".detail-extend").toggleClass("active")
        }),
        $(".header_menu-list> .nav-item").bind("mouseover", function() {
            $(this).find(".header_menu-sub").css("display", "block")
        }),
        $(".header_menu-list> .nav-item").bind("mouseout", function() {
            $(this).find(".header_menu-sub").css("display", "none")
        }),
        $("#turn-off-light").click(function(p) {
            $("#mask-overlay, #turn-off-light, .watching_player-area").toggleClass("active")
        }),
        $("#mask-overlay").click(function(p) {
            $("#mask-overlay, #turn-off-light, .watching_player-area").removeClass("active")
        });
    var p = !0,
        s = ($(".search-suggest").mouseover(function() {
                p = !1
            }),
            $(".search-suggest").mouseout(function() {
                p = !0
            }),
            null);
    $("input[name=keyword]").keyup(function() {
            null != s && clearTimeout(s),
                s = setTimeout(function() {
                    s = null;
                    var p = $("input[name=keyword]").val().trim();
                    1 < p.length ? $.post("/ajax/search", {
                        keyword: p
                    }, function(p) {
                        $(".search-suggest").html(p),
                            $(".search-suggest").css({
                                display: "flex"
                            })
                    }) : $(".search-suggest").hide()
                }, 600)
        }),
        $("input[name=keyword]").blur(function() {
            p && $(".search-suggest").hide()
        }),
        $("input[name=keyword]").focus(function() {
            "" !== $(".search-suggest").html() && $(".search-suggest").css({
                display: "flex"
            })
        }),
        $(".goto-seasons").click(function() {
            $("html, body").animate({
                scrollTop: $("#content-episodes").offset().top - 30
            }, 1e3)
        }),
        $(".goto-comments").click(function() {
            $("html, body").animate({
                scrollTop: $("#film_comments").offset().top - 30
            }, 1e3)
        }),
        $(".btn-filter").click(function() {
            var p = [],
                s = [],
                e = ($(".genre-ids:checked").each(function() {
                        p.push($(this).val())
                    }),
                    $(".country-ids:checked").each(function() {
                        s.push($(this).val())
                    }),
                    p = 0 < p.length ? p.join("-") : "all",
                    s = 0 < s.length ? s.join("-") : "all",
                    $("input[name=release_year]:checked").val() || $("select[name=release_year]").val()),
                x = $("input[name=quality]:checked").val() || $("select[name=quality]").val(),
                l = $("input[name=type]:checked").val() || $("select[name=type]").val();
            window.location.href = "/filter?type=" + l + "&quality=" + x + "&release_year=" + e + "&genre=" + p + "&country=" + s
        }),
        0 < $("#site-notice").length && void 0 === Cookies.get("_s_notice") && $.get("/ajax/notice", function(p) {
            p.status && ($("#site-notice").html(p.html),
                "every_12h" === p.frequency && Cookies.set("_s_notice", 1, {
                    expires: new Date((new Date).getTime() + 432e5)
                }),
                "every_24h" === p.frequency) && Cookies.set("_s_notice", 1, {
                expires: new Date((new Date).getTime() + 864e5)
            })
        }),
        $.get("/ajax/banners?page=" + currPage, function(p) {
            p.status && Object.entries(p.banners).forEach(function(p) {
                0 < $("#hgiks-" + p[0]).length && "" !== p[1] && "null" !== p[1] && null !== p[1] && (postscribe("#hgiks-" + p[0], p[1]),
                    $("#hgiks-" + p[0]).show())
            })
        }),
        $.get("/ajax/banner/vpn", function(p) {
            p.status && ($("#vpn-top").html(p.html),
                $("#vpn-top").show())
        }),
        $("#modaltrailer").on("shown.bs.modal", function() {
            $("#iframe-trailer").attr("src", $("#iframe-trailer").attr("data-src"))
        }),
        $("#modaltrailer").on("hide.bs.modal", function() {
            $("#iframe-trailer").attr("src", "")
        })
});


!(function($) {
    "use strict";
  
    // Smooth scroll for the navigation menu and links with .scrollto classes
    $(document).on('click', '.nav-menu a, .mobile-nav a, .scrollto', function(e) {
      if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
        e.preventDefault();
        var target = $(this.hash);
        if (target.length) {
  
          var scrollto = target.offset().top;
  
          if ($('#header').length) {
            scrollto -= $('#header').outerHeight();
          }
  
          if ($(this).attr("href") == '#header') {
            scrollto = 0;
          }
  
          $('html, body').animate({
            scrollTop: scrollto
          }, 1500, 'easeInOutExpo');
  
          if ($(this).parents('.nav-menu, .mobile-nav').length) {
            $('.nav-menu .active, .mobile-nav .active').removeClass('active');
            $(this).closest('li').addClass('active');
          }
  
          if ($('body').hasClass('mobile-nav-active')) {
            $('body').removeClass('mobile-nav-active');
            $('.mobile-nav-toggle i').toggleClass('icofont-navigation-menu icofont-close');
            $('.mobile-nav-overly').fadeOut();
          }
          return false;
        }
      }
    });
  
    // Mobile Navigation
    if ($('.nav-menu').length) {
      var $mobile_nav = $('.nav-menu').clone().prop({
        class: 'mobile-nav d-lg-none'
      });
      $('body').append($mobile_nav);
      $('body').prepend('<button type="button" class="mobile-nav-toggle d-lg-none"><i class="icofont-navigation-menu"></i></button>');
      $('body').append('<div class="mobile-nav-overly"></div>');
  
      $(document).on('click', '.mobile-nav-toggle', function(e) {
        $('body').toggleClass('mobile-nav-active');
        $('.mobile-nav-toggle i').toggleClass('icofont-navigation-menu icofont-close');
        $('.mobile-nav-overly').toggle();
      });
  
      $(document).on('click', '.mobile-nav .drop-down > a', function(e) {
        e.preventDefault();
        $(this).next().slideToggle(300);
        $(this).parent().toggleClass('active');
      });
  
      $(document).click(function(e) {
        var container = $(".mobile-nav, .mobile-nav-toggle");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
          if ($('body').hasClass('mobile-nav-active')) {
            $('body').removeClass('mobile-nav-active');
            $('.mobile-nav-toggle i').toggleClass('icofont-navigation-menu icofont-close');
            $('.mobile-nav-overly').fadeOut();
          }
        }
      });
    } else if ($(".mobile-nav, .mobile-nav-toggle").length) {
      $(".mobile-nav, .mobile-nav-toggle").hide();
    }
  
    // Back to top button
    $(window).scroll(function() {
      if ($(this).scrollTop() > 100) {
        $('.back-to-top').fadeIn('slow');
      } else {
        $('.back-to-top').fadeOut('slow');
      }
    });
  
    $('.back-to-top').click(function() {
      $('html, body').animate({
        scrollTop: 0
      }, 1500, 'easeInOutExpo');
      return false;
    });
  
    // Porfolio isotope and filter
    $(window).on('load', function() {
      var portfolioIsotope = $('.portfolio-container').isotope({
        itemSelector: '.portfolio-item',
        layoutMode: 'fitRows'
      });
  
      $('#portfolio-flters li').on('click', function() {
        $("#portfolio-flters li").removeClass('filter-active');
        $(this).addClass('filter-active');
  
        portfolioIsotope.isotope({
          filter: $(this).data('filter')
        });
      });
  
      // Initiate venobox (lightbox feature used in portofilo)
      $(document).ready(function() {
        $('.venobox').venobox();
      });
    });
  
    // Clients carousel (uses the Owl Carousel library)
    $(".clients-carousel").owlCarousel({
      autoplay: true,
      dots: true,
      loop: true,
      responsive: {
        0: {
          items: 2
        },
        768: {
          items: 4
        },
        900: {
          items: 6
        }
      }
    });
  
    // Initi AOS
    AOS.init({
      duration: 800,
      easing: "ease-in-out"
    });
  
  })(jQuery);

  