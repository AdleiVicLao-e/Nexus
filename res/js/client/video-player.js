function changeServer() {
    var p;
    1 == movie.type ? $("#watch-" + watchId).parent().next().find("a").click() : (p = $("#watch-" + watchId).attr("data-number"),
    $(".item-server.active").parent().next().find("a").click(),
    setTimeout(function() {
        $(".episodes-content .tab-pane.active").find("a[data-number=" + p + "]").click()
    }, 1e3))
}
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
var is_login = !1
  , movie = ($.get("/ajax/login-state", function(p) {
    is_login = p.is_login,
    $("#user-slot, #header_login").html(p.html),
    is_login && $("#header").addClass("header-logged")
}),
$.get("/ajax/verify?domain=" + window.location.hostname, function(p) {
    p.status || (window.location.href = p.redirectTo)
}),
{
    id: $(".detail_page-watch").attr("data-id"),
    type: $(".detail_page-watch").attr("data-type")
})
  , is_process = !1;
function check_login() {
    return !!is_login || ($("#modallogin").modal("show"),
    !1)
}
function redirect(p) {
    p.redirect && (location.href = p.redirect)
}
function vote_info() {
    $.get("/ajax/vote_info/" + movie.id, function(p) {
        $("#block-rating").html(p)
    })
}
function vote_submit(s, e) {
    $("#vote-loading").show(),
    "undefined" != typeof recaptcha_site_key ? grecaptcha.execute(recaptcha_site_key, {
        action: "vote_movie"
    }).then(function(p) {
        $.post("/ajax/vote/" + s, {
            state: e,
            token: p
        }, function(p) {
            $("#block-rating").html(p)
        })
    }) : $.post("/ajax/vote/" + s, {
        state: e
    }, function(p) {
        $("#block-rating").html(p)
    })
}
function like(p) {
    check_login() && vote_submit(p, 1)
}
function dislike(p) {
    check_login() && vote_submit(p, 0)
}
function get_episodes() {
    2 == movie.type ? $.get("/ajax/tv/seasons/" + movie.id, function(p) {
        $("#content-episodes").html(p)
    }) : $.get("/ajax/movie/episodes/" + movie.id, function(p) {
        $("#content-episodes").html(p)
    })
}
function check_favorite() {
    $.get("/ajax/check_favorite/" + movie.id, function(p) {
        $("#favorite-state").html(p)
    })
}
function submit_add_favorite(p) {
    is_process = !0,
    $.post("/ajax/favorite", {
        movie_id: movie.id,
        token: p
    }, function(p) {
        redirect(p),
        is_process = !1,
        $("#favorite-state").html(p.html),
        $("#fav-message").html(p.message),
        $("#alert-fav").show(),
        setTimeout(function() {
            $("#alert-fav").hide()
        }, 3e3)
    })
}
function add_favorite() {
    is_process || check_login() && ("undefined" != typeof recaptcha_site_key ? grecaptcha.execute(recaptcha_site_key, {
        action: "add_favorite"
    }).then(function(p) {
        submit_add_favorite(p)
    }) : submit_add_favorite(""))
}
function remove_favorite(p) {
    is_process || check_login() && confirm("Are you sure remove this movie from favorite?") && (is_process = !0,
    $.post("/ajax/remove_favorite", {
        id: p
    }, function(p) {
        is_process = !1,
        redirect(p),
        location.reload()
    }))
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
function showModalRegister() {
    $("#modallogin").modal("show"),
    $(".auth-tab").removeClass("active show"),
    $("#modal-tab-register").addClass("active show")
}
function showModalLogin() {
    $("#modallogin").modal("show"),
    $(".auth-tab").removeClass("active show"),
    $("#modal-tab-login").addClass("active show")
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
    var p = !0
      , s = ($(".search-suggest").mouseover(function() {
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
        var p = []
          , s = []
          , e = ($(".genre-ids:checked").each(function() {
            p.push($(this).val())
        }),
        $(".country-ids:checked").each(function() {
            s.push($(this).val())
        }),
        p = 0 < p.length ? p.join("-") : "all",
        s = 0 < s.length ? s.join("-") : "all",
        $("input[name=release_year]:checked").val() || $("select[name=release_year]").val())
          , x = $("input[name=quality]:checked").val() || $("select[name=quality]").val()
          , l = $("input[name=type]:checked").val() || $("select[name=type]").val();
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
var app = new Vue({
    el: "#app",
    data: {
        name: "",
        email: "",
        password: "",
        confirm_password: "",
        new_password: "",
        remember: !1,
        error_login: !1,
        error_register: !1,
        error_forgot: !1,
        error_profile: !1,
        error_message: "",
        keyword: "",
        is_process: !1
    },
    methods: {
        show_login: function() {
            this.error_login = !1,
            $(".auth-tab").removeClass("active show"),
            $("#modal-tab-login").addClass("active show")
        },
        show_register: function() {
            this.error_register = !1,
            $(".auth-tab").removeClass("active show"),
            $("#modal-tab-register").addClass("active show")
        },
        show_forgot: function() {
            this.error_forgot = !1,
            $(".mlt-item").removeClass("active"),
            $(".auth-tab").removeClass("active show"),
            $("#modal-tab-forgot").addClass("active show")
        },
        login: function(p) {
            var s = this;
            s.email && s.password && (this.is_process || ($("#login-loading").show(),
            this.is_process = !0,
            $.post("/ajax/login", {
                email: this.email,
                password: this.password,
                remember: this.remember
            }, function(p) {
                p.status ? (is_login = p.is_login,
                $("#user-slot, #header_login").html(p.html),
                $("#modallogin").modal("hide")) : (s.error_login = !0,
                s.error_message = p.message),
                s.is_process = !1,
                $("#login-loading").hide()
            }))),
            p.preventDefault()
        },
        forgot_password: function(p) {
            p.preventDefault()
        },
        register: function(p) {
            var s = this;
            this.password !== this.confirm_password ? (this.error_register = !0,
            this.error_message = "Confirm password is invalid") : this.is_process || ($("#register-loading").show(),
            this.is_process = !0,
            "undefined" != typeof recaptcha_site_key ? grecaptcha.execute(recaptcha_site_key, {
                action: "register"
            }).then(function(p) {
                s.submit_register(p)
            }) : s.submit_register("")),
            p.preventDefault()
        },
        submit_register: function(p) {
            var s = this;
            $.post("/ajax/register", {
                email: this.email,
                password: this.password,
                name: this.name,
                token: p
            }, function(p) {
                p.status ? (is_login = p.is_login,
                $("#user-slot").html(p.html),
                $("#modallogin").modal("hide")) : (s.error_register = !0,
                s.error_message = p.message),
                s.is_process = !1,
                $("#register-loading").hide()
            })
        },
        update_profile: function(p) {
            var s = this;
            this.is_process || ($("#update-profile-loading").show(),
            this.is_process = !0,
            $.post("/ajax/update_profile", {
                name: $("#pro5-name").val(),
                password: this.password,
                new_password: this.new_password
            }, function(p) {
                p.status ? location.reload() : (s.error_profile = !0,
                s.error_message = p.message),
                s.is_process = !1
            })),
            p.preventDefault()
        },
        search: function(p) {
            var s;
            "" !== this.keyword && (s = this.keyword.replace(/\W-/g, "").replace(/['"]+/g, "").split(" ").join("-").toLowerCase(),
            location.href = "/search/" + s),
            p.preventDefault()
        }
    }
})
  , _0x173302 = (!function(p, s) {
    var e, x;
    "object" == typeof exports && "undefined" != typeof module ? module.exports = s() : "function" == typeof define && define.amd ? define(s) : (p = p || self,
    e = p.Cookies,
    (x = p.Cookies = s()).noConflict = function() {
        return p.Cookies = e,
        x
    }
    )
}(this, function() {
    "use strict";
    function h(p) {
        for (var s = 1; s < arguments.length; s++) {
            var e, x = arguments[s];
            for (e in x)
                p[e] = x[e]
        }
        return p
    }
    var i = {
        read: function(p) {
            return p.replace(/(%[\dA-F]{2})+/gi, decodeURIComponent)
        },
        write: function(p) {
            return encodeURIComponent(p).replace(/%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g, decodeURIComponent)
        }
    };
    return function s(o, t) {
        function e(p, s, e) {
            if ("undefined" != typeof document) {
                "number" == typeof (e = h({}, t, e)).expires && (e.expires = new Date(Date.now() + 864e5 * e.expires)),
                e.expires && (e.expires = e.expires.toUTCString()),
                p = encodeURIComponent(p).replace(/%(2[346B]|5E|60|7C)/g, decodeURIComponent).replace(/[()]/g, escape),
                s = o.write(s, p);
                var x, l = "";
                for (x in e)
                    e[x] && (l += "; " + x,
                    !0 !== e[x]) && (l += "=" + e[x].split(";")[0]);
                return document.cookie = p + "=" + s + l
            }
        }
        return Object.create({
            set: e,
            get: function(p) {
                if ("undefined" != typeof document && (!arguments.length || p)) {
                    for (var s = document.cookie ? document.cookie.split("; ") : [], e = {}, x = 0; x < s.length; x++) {
                        var l = s[x].split("=")
                          , t = l.slice(1).join("=");
                        '"' === t[0] && (t = t.slice(1, -1));
                        try {
                            var h = i.read(l[0]);
                            if (e[h] = o.read(t, h),
                            p === h)
                                break
                        } catch (p) {}
                    }
                    return p ? e[p] : e
                }
            },
            remove: function(p, s) {
                e(p, "", h({}, s, {
                    expires: -1
                }))
            },
            withAttributes: function(p) {
                return s(this.converter, h({}, this.attributes, p))
            },
            withConverter: function(p) {
                return s(h({}, this.converter, p), this.attributes)
            }
        }, {
            attributes: {
                value: Object.freeze(t)
            },
            converter: {
                value: Object.freeze(o)
            }
        })
    }(i, {
        path: "/"
    })
}),
!function(p) {
    (jQuery.browser = jQuery.browser || {}).mobile = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(p) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(p.substr(0, 4))
}(navigator.userAgent || navigator.vendor || window.opera),
function() {
    var x = !0;
    return function(s, e) {
        var p = x ? function() {
            var p;
            if (e)
                return p = e.apply(s, arguments),
                e = null,
                p
        }
        : function() {}
        ;
        return x = !1,
        p
    }
}())
  , css = (!function() {
    _0x173302(this, function() {
        var p = new RegExp("function *\\( *\\)")
          , s = new RegExp("\\+\\+ *(?:[a-zA-Z_$][0-9a-zA-Z_$]*)","i")
          , e = _0x39426c("init");
        p.test(e + "chain") && s.test(e + "input") ? _0x39426c() : e("0")
    })()
}(),
"text-shadow: -1px -1px hsl(0,100%,50%), 1px 1px hsl(5.4, 100%, 50%), 3px 2px hsl(10.8, 100%, 50%), 5px 3px hsl(16.2, 100%, 50%), 7px 4px hsl(21.6, 100%, 50%), 9px 5px hsl(27, 100%, 50%), 11px 6px hsl(32.4, 100%, 50%), 13px 7px hsl(37.8, 100%, 50%), 14px 8px hsl(43.2, 100%, 50%), 16px 9px hsl(48.6, 100%, 50%), 18px 10px hsl(54, 100%, 50%), 20px 11px hsl(59.4, 100%, 50%), 22px 12px hsl(64.8, 100%, 50%), 23px 13px hsl(70.2, 100%, 50%), 25px 14px hsl(75.6, 100%, 50%), 27px 15px hsl(81, 100%, 50%), 28px 16px hsl(86.4, 100%, 50%), 30px 17px hsl(91.8, 100%, 50%), 32px 18px hsl(97.2, 100%, 50%), 33px 19px hsl(102.6, 100%, 50%), 35px 20px hsl(108, 100%, 50%), 36px 21px hsl(113.4, 100%, 50%), 38px 22px hsl(118.8, 100%, 50%), 39px 23px hsl(124.2, 100%, 50%), 41px 24px hsl(129.6, 100%, 50%), 42px 25px hsl(135, 100%, 50%), 43px 26px hsl(140.4, 100%, 50%), 45px 27px hsl(145.8, 100%, 50%), 46px 28px hsl(151.2, 100%, 50%), 47px 29px hsl(156.6, 100%, 50%), 48px 30px hsl(162, 100%, 50%), 49px 31px hsl(167.4, 100%, 50%), 50px 32px hsl(172.8, 100%, 50%), 51px 33px hsl(178.2, 100%, 50%), 52px 34px hsl(183.6, 100%, 50%), 53px 35px hsl(189, 100%, 50%), 54px 36px hsl(194.4, 100%, 50%), 55px 37px hsl(199.8, 100%, 50%), 55px 38px hsl(205.2, 100%, 50%), 56px 39px hsl(210.6, 100%, 50%), 57px 40px hsl(216, 100%, 50%), 57px 41px hsl(221.4, 100%, 50%), 58px 42px hsl(226.8, 100%, 50%), 58px 43px hsl(232.2, 100%, 50%), 58px 44px hsl(237.6, 100%, 50%), 59px 45px hsl(243, 100%, 50%), 59px 46px hsl(248.4, 100%, 50%), 59px 47px hsl(253.8, 100%, 50%), 59px 48px hsl(259.2, 100%, 50%), 59px 49px hsl(264.6, 100%, 50%), 60px 50px hsl(270, 100%, 50%), 59px 51px hsl(275.4, 100%, 50%), 59px 52px hsl(280.8, 100%, 50%), 59px 53px hsl(286.2, 100%, 50%), 59px 54px hsl(291.6, 100%, 50%), 59px 55px hsl(297, 100%, 50%), 58px 56px hsl(302.4, 100%, 50%), 58px 57px hsl(307.8, 100%, 50%), 58px 58px hsl(313.2, 100%, 50%), 57px 59px hsl(318.6, 100%, 50%), 57px 60px hsl(324, 100%, 50%), 56px 61px hsl(329.4, 100%, 50%), 55px 62px hsl(334.8, 100%, 50%), 55px 63px hsl(340.2, 100%, 50%), 54px 64px hsl(345.6, 100%, 50%), 53px 65px hsl(351, 100%, 50%), 52px 66px hsl(356.4, 100%, 50%), 51px 67px hsl(361.8, 100%, 50%), 50px 68px hsl(367.2, 100%, 50%), 49px 69px hsl(372.6, 100%, 50%), 48px 70px hsl(378, 100%, 50%), 47px 71px hsl(383.4, 100%, 50%), 46px 72px hsl(388.8, 100%, 50%), 45px 73px hsl(394.2, 100%, 50%), 43px 74px hsl(399.6, 100%, 50%), 42px 75px hsl(405, 100%, 50%), 41px 76px hsl(410.4, 100%, 50%), 39px 77px hsl(415.8, 100%, 50%), 38px 78px hsl(421.2, 100%, 50%), 36px 79px hsl(426.6, 100%, 50%), 35px 80px hsl(432, 100%, 50%), 33px 81px hsl(437.4, 100%, 50%), 32px 82px hsl(442.8, 100%, 50%), 30px 83px hsl(448.2, 100%, 50%), 28px 84px hsl(453.6, 100%, 50%), 27px 85px hsl(459, 100%, 50%), 25px 86px hsl(464.4, 100%, 50%), 23px 87px hsl(469.8, 100%, 50%), 22px 88px hsl(475.2, 100%, 50%), 20px 89px hsl(480.6, 100%, 50%), 18px 90px hsl(486, 100%, 50%), 16px 91px hsl(491.4, 100%, 50%), 14px 92px hsl(496.8, 100%, 50%), 13px 93px hsl(502.2, 100%, 50%), 11px 94px hsl(507.6, 100%, 50%), 9px 95px hsl(513, 100%, 50%), 7px 96px hsl(518.4, 100%, 50%), 5px 97px hsl(523.8, 100%, 50%), 3px 98px hsl(529.2, 100%, 50%), 1px 99px hsl(534.6, 100%, 50%), 7px 100px hsl(540, 100%, 50%), -1px 101px hsl(545.4, 100%, 50%), -3px 102px hsl(550.8, 100%, 50%), -5px 103px hsl(556.2, 100%, 50%), -7px 104px hsl(561.6, 100%, 50%), -9px 105px hsl(567, 100%, 50%), -11px 106px hsl(572.4, 100%, 50%), -13px 107px hsl(577.8, 100%, 50%), -14px 108px hsl(583.2, 100%, 50%), -16px 109px hsl(588.6, 100%, 50%), -18px 110px hsl(594, 100%, 50%), -20px 111px hsl(599.4, 100%, 50%), -22px 112px hsl(604.8, 100%, 50%), -23px 113px hsl(610.2, 100%, 50%), -25px 114px hsl(615.6, 100%, 50%), -27px 115px hsl(621, 100%, 50%), -28px 116px hsl(626.4, 100%, 50%), -30px 117px hsl(631.8, 100%, 50%), -32px 118px hsl(637.2, 100%, 50%), -33px 119px hsl(642.6, 100%, 50%), -35px 120px hsl(648, 100%, 50%), -36px 121px hsl(653.4, 100%, 50%), -38px 122px hsl(658.8, 100%, 50%), -39px 123px hsl(664.2, 100%, 50%), -41px 124px hsl(669.6, 100%, 50%), -42px 125px hsl(675, 100%, 50%), -43px 126px hsl(680.4, 100%, 50%), -45px 127px hsl(685.8, 100%, 50%), -46px 128px hsl(691.2, 100%, 50%), -47px 129px hsl(696.6, 100%, 50%), -48px 130px hsl(702, 100%, 50%), -49px 131px hsl(707.4, 100%, 50%), -50px 132px hsl(712.8, 100%, 50%), -51px 133px hsl(718.2, 100%, 50%), -52px 134px hsl(723.6, 100%, 50%), -53px 135px hsl(729, 100%, 50%), -54px 136px hsl(734.4, 100%, 50%), -55px 137px hsl(739.8, 100%, 50%), -55px 138px hsl(745.2, 100%, 50%), -56px 139px hsl(750.6, 100%, 50%), -57px 140px hsl(756, 100%, 50%), -57px 141px hsl(761.4, 100%, 50%), -58px 142px hsl(766.8, 100%, 50%), -58px 143px hsl(772.2, 100%, 50%), -58px 144px hsl(777.6, 100%, 50%), -59px 145px hsl(783, 100%, 50%), -59px 146px hsl(788.4, 100%, 50%), -59px 147px hsl(793.8, 100%, 50%), -59px 148px hsl(799.2, 100%, 50%), -59px 149px hsl(804.6, 100%, 50%), -60px 150px hsl(810, 100%, 50%), -59px 151px hsl(815.4, 100%, 50%), -59px 152px hsl(820.8, 100%, 50%), -59px 153px hsl(826.2, 100%, 50%), -59px 154px hsl(831.6, 100%, 50%), -59px 155px hsl(837, 100%, 50%), -58px 156px hsl(842.4, 100%, 50%), -58px 157px hsl(847.8, 100%, 50%), -58px 158px hsl(853.2, 100%, 50%), -57px 159px hsl(858.6, 100%, 50%), -57px 160px hsl(864, 100%, 50%), -56px 161px hsl(869.4, 100%, 50%), -55px 162px hsl(874.8, 100%, 50%), -55px 163px hsl(880.2, 100%, 50%), -54px 164px hsl(885.6, 100%, 50%), -53px 165px hsl(891, 100%, 50%), -52px 166px hsl(896.4, 100%, 50%), -51px 167px hsl(901.8, 100%, 50%), -50px 168px hsl(907.2, 100%, 50%), -49px 169px hsl(912.6, 100%, 50%), -48px 170px hsl(918, 100%, 50%), -47px 171px hsl(923.4, 100%, 50%), -46px 172px hsl(928.8, 100%, 50%), -45px 173px hsl(934.2, 100%, 50%), -43px 174px hsl(939.6, 100%, 50%), -42px 175px hsl(945, 100%, 50%), -41px 176px hsl(950.4, 100%, 50%), -39px 177px hsl(955.8, 100%, 50%), -38px 178px hsl(961.2, 100%, 50%), -36px 179px hsl(966.6, 100%, 50%), -35px 180px hsl(972, 100%, 50%), -33px 181px hsl(977.4, 100%, 50%), -32px 182px hsl(982.8, 100%, 50%), -30px 183px hsl(988.2, 100%, 50%), -28px 184px hsl(993.6, 100%, 50%), -27px 185px hsl(999, 100%, 50%), -25px 186px hsl(1004.4, 100%, 50%), -23px 187px hsl(1009.8, 100%, 50%), -22px 188px hsl(1015.2, 100%, 50%), -20px 189px hsl(1020.6, 100%, 50%), -18px 190px hsl(1026, 100%, 50%), -16px 191px hsl(1031.4, 100%, 50%), -14px 192px hsl(1036.8, 100%, 50%), -13px 193px hsl(1042.2, 100%, 50%), -11px 194px hsl(1047.6, 100%, 50%), -9px 195px hsl(1053, 100%, 50%), -7px 196px hsl(1058.4, 100%, 50%), -5px 197px hsl(1063.8, 100%, 50%), -3px 198px hsl(1069.2, 100%, 50%), -1px 199px hsl(1074.6, 100%, 50%), -1px 200px hsl(1080, 100%, 50%), 1px 201px hsl(1085.4, 100%, 50%), 3px 202px hsl(1090.8, 100%, 50%), 5px 203px hsl(1096.2, 100%, 50%), 7px 204px hsl(1101.6, 100%, 50%), 9px 205px hsl(1107, 100%, 50%), 11px 206px hsl(1112.4, 100%, 50%), 13px 207px hsl(1117.8, 100%, 50%), 14px 208px hsl(1123.2, 100%, 50%), 16px 209px hsl(1128.6, 100%, 50%), 18px 210px hsl(1134, 100%, 50%), 20px 211px hsl(1139.4, 100%, 50%), 22px 212px hsl(1144.8, 100%, 50%), 23px 213px hsl(1150.2, 100%, 50%), 25px 214px hsl(1155.6, 100%, 50%), 27px 215px hsl(1161, 100%, 50%), 28px 216px hsl(1166.4, 100%, 50%), 30px 217px hsl(1171.8, 100%, 50%), 32px 218px hsl(1177.2, 100%, 50%), 33px 219px hsl(1182.6, 100%, 50%), 35px 220px hsl(1188, 100%, 50%), 36px 221px hsl(1193.4, 100%, 50%), 38px 222px hsl(1198.8, 100%, 50%), 39px 223px hsl(1204.2, 100%, 50%), 41px 224px hsl(1209.6, 100%, 50%), 42px 225px hsl(1215, 100%, 50%), 43px 226px hsl(1220.4, 100%, 50%), 45px 227px hsl(1225.8, 100%, 50%), 46px 228px hsl(1231.2, 100%, 50%), 47px 229px hsl(1236.6, 100%, 50%), 48px 230px hsl(1242, 100%, 50%), 49px 231px hsl(1247.4, 100%, 50%), 50px 232px hsl(1252.8, 100%, 50%), 51px 233px hsl(1258.2, 100%, 50%), 52px 234px hsl(1263.6, 100%, 50%), 53px 235px hsl(1269, 100%, 50%), 54px 236px hsl(1274.4, 100%, 50%), 55px 237px hsl(1279.8, 100%, 50%), 55px 238px hsl(1285.2, 100%, 50%), 56px 239px hsl(1290.6, 100%, 50%), 57px 240px hsl(1296, 100%, 50%), 57px 241px hsl(1301.4, 100%, 50%), 58px 242px hsl(1306.8, 100%, 50%), 58px 243px hsl(1312.2, 100%, 50%), 58px 244px hsl(1317.6, 100%, 50%), 59px 245px hsl(1323, 100%, 50%), 59px 246px hsl(1328.4, 100%, 50%), 59px 247px hsl(1333.8, 100%, 50%), 59px 248px hsl(1339.2, 100%, 50%), 59px 249px hsl(1344.6, 100%, 50%), 60px 250px hsl(1350, 100%, 50%), 59px 251px hsl(1355.4, 100%, 50%), 59px 252px hsl(1360.8, 100%, 50%), 59px 253px hsl(1366.2, 100%, 50%), 59px 254px hsl(1371.6, 100%, 50%), 59px 255px hsl(1377, 100%, 50%), 58px 256px hsl(1382.4, 100%, 50%), 58px 257px hsl(1387.8, 100%, 50%), 58px 258px hsl(1393.2, 100%, 50%), 57px 259px hsl(1398.6, 100%, 50%), 57px 260px hsl(1404, 100%, 50%), 56px 261px hsl(1409.4, 100%, 50%), 55px 262px hsl(1414.8, 100%, 50%), 55px 263px hsl(1420.2, 100%, 50%), 54px 264px hsl(1425.6, 100%, 50%), 53px 265px hsl(1431, 100%, 50%), 52px 266px hsl(1436.4, 100%, 50%), 51px 267px hsl(1441.8, 100%, 50%), 50px 268px hsl(1447.2, 100%, 50%), 49px 269px hsl(1452.6, 100%, 50%), 48px 270px hsl(1458, 100%, 50%), 47px 271px hsl(1463.4, 100%, 50%), 46px 272px hsl(1468.8, 100%, 50%), 45px 273px hsl(1474.2, 100%, 50%), 43px 274px hsl(1479.6, 100%, 50%), 42px 275px hsl(1485, 100%, 50%), 41px 276px hsl(1490.4, 100%, 50%), 39px 277px hsl(1495.8, 100%, 50%), 38px 278px hsl(1501.2, 100%, 50%), 36px 279px hsl(1506.6, 100%, 50%), 35px 280px hsl(1512, 100%, 50%), 33px 281px hsl(1517.4, 100%, 50%), 32px 282px hsl(1522.8, 100%, 50%), 30px 283px hsl(1528.2, 100%, 50%), 28px 284px hsl(1533.6, 100%, 50%), 27px 285px hsl(1539, 100%, 50%), 25px 286px hsl(1544.4, 100%, 50%), 23px 287px hsl(1549.8, 100%, 50%), 22px 288px hsl(1555.2, 100%, 50%), 20px 289px hsl(1560.6, 100%, 50%), 18px 290px hsl(1566, 100%, 50%), 16px 291px hsl(1571.4, 100%, 50%), 14px 292px hsl(1576.8, 100%, 50%), 13px 293px hsl(1582.2, 100%, 50%), 11px 294px hsl(1587.6, 100%, 50%), 9px 295px hsl(1593, 100%, 50%), 7px 296px hsl(1598.4, 100%, 50%), 5px 297px hsl(1603.8, 100%, 50%), 3px 298px hsl(1609.2, 100%, 50%), 1px 299px hsl(1614.6, 100%, 50%), 2px 300px hsl(1620, 100%, 50%), -1px 301px hsl(1625.4, 100%, 50%), -3px 302px hsl(1630.8, 100%, 50%), -5px 303px hsl(1636.2, 100%, 50%), -7px 304px hsl(1641.6, 100%, 50%), -9px 305px hsl(1647, 100%, 50%), -11px 306px hsl(1652.4, 100%, 50%), -13px 307px hsl(1657.8, 100%, 50%), -14px 308px hsl(1663.2, 100%, 50%), -16px 309px hsl(1668.6, 100%, 50%), -18px 310px hsl(1674, 100%, 50%), -20px 311px hsl(1679.4, 100%, 50%), -22px 312px hsl(1684.8, 100%, 50%), -23px 313px hsl(1690.2, 100%, 50%), -25px 314px hsl(1695.6, 100%, 50%), -27px 315px hsl(1701, 100%, 50%), -28px 316px hsl(1706.4, 100%, 50%), -30px 317px hsl(1711.8, 100%, 50%), -32px 318px hsl(1717.2, 100%, 50%), -33px 319px hsl(1722.6, 100%, 50%), -35px 320px hsl(1728, 100%, 50%), -36px 321px hsl(1733.4, 100%, 50%), -38px 322px hsl(1738.8, 100%, 50%), -39px 323px hsl(1744.2, 100%, 50%), -41px 324px hsl(1749.6, 100%, 50%), -42px 325px hsl(1755, 100%, 50%), -43px 326px hsl(1760.4, 100%, 50%), -45px 327px hsl(1765.8, 100%, 50%), -46px 328px hsl(1771.2, 100%, 50%), -47px 329px hsl(1776.6, 100%, 50%), -48px 330px hsl(1782, 100%, 50%), -49px 331px hsl(1787.4, 100%, 50%), -50px 332px hsl(1792.8, 100%, 50%), -51px 333px hsl(1798.2, 100%, 50%), -52px 334px hsl(1803.6, 100%, 50%), -53px 335px hsl(1809, 100%, 50%), -54px 336px hsl(1814.4, 100%, 50%), -55px 337px hsl(1819.8, 100%, 50%), -55px 338px hsl(1825.2, 100%, 50%), -56px 339px hsl(1830.6, 100%, 50%), -57px 340px hsl(1836, 100%, 50%), -57px 341px hsl(1841.4, 100%, 50%), -58px 342px hsl(1846.8, 100%, 50%), -58px 343px hsl(1852.2, 100%, 50%), -58px 344px hsl(1857.6, 100%, 50%), -59px 345px hsl(1863, 100%, 50%), -59px 346px hsl(1868.4, 100%, 50%), -59px 347px hsl(1873.8, 100%, 50%), -59px 348px hsl(1879.2, 100%, 50%), -59px 349px hsl(1884.6, 100%, 50%), -60px 350px hsl(1890, 100%, 50%), -59px 351px hsl(1895.4, 100%, 50%), -59px 352px hsl(1900.8, 100%, 50%), -59px 353px hsl(1906.2, 100%, 50%), -59px 354px hsl(1911.6, 100%, 50%), -59px 355px hsl(1917, 100%, 50%), -58px 356px hsl(1922.4, 100%, 50%), -58px 357px hsl(1927.8, 100%, 50%), -58px 358px hsl(1933.2, 100%, 50%), -57px 359px hsl(1938.6, 100%, 50%), -57px 360px hsl(1944, 100%, 50%), -56px 361px hsl(1949.4, 100%, 50%), -55px 362px hsl(1954.8, 100%, 50%), -55px 363px hsl(1960.2, 100%, 50%), -54px 364px hsl(1965.6, 100%, 50%), -53px 365px hsl(1971, 100%, 50%), -52px 366px hsl(1976.4, 100%, 50%), -51px 367px hsl(1981.8, 100%, 50%), -50px 368px hsl(1987.2, 100%, 50%), -49px 369px hsl(1992.6, 100%, 50%), -48px 370px hsl(1998, 100%, 50%), -47px 371px hsl(2003.4, 100%, 50%), -46px 372px hsl(2008.8, 100%, 50%), -45px 373px hsl(2014.2, 100%, 50%), -43px 374px hsl(2019.6, 100%, 50%), -42px 375px hsl(2025, 100%, 50%), -41px 376px hsl(2030.4, 100%, 50%), -39px 377px hsl(2035.8, 100%, 50%), -38px 378px hsl(2041.2, 100%, 50%), -36px 379px hsl(2046.6, 100%, 50%), -35px 380px hsl(2052, 100%, 50%), -33px 381px hsl(2057.4, 100%, 50%), -32px 382px hsl(2062.8, 100%, 50%), -30px 383px hsl(2068.2, 100%, 50%), -28px 384px hsl(2073.6, 100%, 50%), -27px 385px hsl(2079, 100%, 50%), -25px 386px hsl(2084.4, 100%, 50%), -23px 387px hsl(2089.8, 100%, 50%), -22px 388px hsl(2095.2, 100%, 50%), -20px 389px hsl(2100.6, 100%, 50%), -18px 390px hsl(2106, 100%, 50%), -16px 391px hsl(2111.4, 100%, 50%), -14px 392px hsl(2116.8, 100%, 50%), -13px 393px hsl(2122.2, 100%, 50%), -11px 394px hsl(2127.6, 100%, 50%), -9px 395px hsl(2133, 100%, 50%), -7px 396px hsl(2138.4, 100%, 50%), -5px 397px hsl(2143.8, 100%, 50%), -3px 398px hsl(2149.2, 100%, 50%), -1px 399px hsl(2154.6, 100%, 50%); font-size: 40px;");

setInterval(function() {
    _0x39426c()
}, 4e3),
console.log("%cSTOP! %s", css, "Great power comes great responsibility!");