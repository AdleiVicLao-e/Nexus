function showThankYouMessage(event) {
    event.preventDefault();

    const today = new Date().toISOString().split('T')[0];

    const formData = new FormData(event.target);
    formData.append("date", today);
    formData.append("quality_presentation", document.querySelector('input[name="exhibits"]:checked').value);
    formData.append("cleanliness_ambiance", document.querySelector('input[name="cleanliness"]:checked').value);
    formData.append("staff_service", document.querySelector('input[name="staff"]:checked').value);
    formData.append("overall_experience", document.querySelector('input[name="experience"]:checked').value);
    formData.append("comments", document.getElementById("comments").value);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../include/addFeedback.php", true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = xhr.responseText;

            if (response.includes("success")) {
                document.getElementById("overlay-message-success").innerText = "Thank you! Your feedback has been successfully submitted.";
                document.getElementById("overlay-success").style.display = "block";

                event.target.reset();

                setTimeout(function () {
                    window.location.href = "../../feedback.php";
                }, 2000);
            } else {
                document.getElementById("overlay-message-error").innerText = "There was an error submitting your feedback. Please try again."; // Set error message
                document.getElementById("overlay-error").style.display = "block";
            }
        } else {
            document.getElementById("overlay-message-error").innerText = "Error with the request. Please try again."; // Set error message
            document.getElementById("overlay-error").style.display = "block";
        }
    };
    xhr.send(formData);
}

document.addEventListener('DOMContentLoaded', function () {
    // Toggle password visibility
    const togglePassword = document.getElementById('toggle-password');
    const passwordField = document.getElementById('password-field');
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function () {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    }

    function toggleForm(type) {
        const studentForm = document.getElementById("student-form");
        const visitorForm = document.getElementById("visitor-form");
        const submitButton = document.getElementById("btn-submit");

        if (studentForm && visitorForm && submitButton) {
            if (type === 'student') {
                studentForm.classList.remove("hidden");
                visitorForm.classList.add("hidden");
                submitButton.style.display = 'block';
            } else if (type === 'visitor') {
                visitorForm.classList.remove("hidden");
                studentForm.classList.add("hidden");
                submitButton.style.display = 'block';
            }
        }
    }
    
    // search-box open close res code
    const navbar = document.querySelector(".navbar");
    const searchBox = document.querySelector(".search-box .bx-search");

    if (navbar && searchBox) {
        searchBox.addEventListener("click", () => {
            navbar.classList.toggle("showInput");
            if (navbar.classList.contains("showInput")) {
                searchBox.classList.replace("bx-search", "bx-x");
            } else {
                searchBox.classList.replace("bx-x", "bx-search");
            }
        });
    }

    // sidebar open close res code
    const navLinks = document.querySelector(".nav-links");
    const menuOpenBtn = document.querySelector(".navbar .bx-menu");
    const menuCloseBtn = document.querySelector(".nav-links .bx-x");

    if (navLinks && menuOpenBtn && menuCloseBtn) {
        menuOpenBtn.onclick = function() {
            navLinks.style.left = "0";
        }
        menuCloseBtn.onclick = function() {
            navLinks.style.left = "-100%";
        }
    }

    // sidebar submenu open close res code
    const htmlcssArrow = document.querySelector(".htmlcss-arrow");
    const moreArrow = document.querySelector(".more-arrow");
    const jsArrow = document.querySelector(".res-arrow");

    if (htmlcssArrow) {
        htmlcssArrow.onclick = function() {
            navLinks.classList.toggle("show1");
        }
    }
    if (moreArrow) {
        moreArrow.onclick = function() {
            navLinks.classList.toggle("show2");
        }
    }
    if (jsArrow) {
        jsArrow.onclick = function() {
            navLinks.classList.toggle("show3");
        }
    }

    const submitButton = document.getElementById('btn-submit');
    if (submitButton) {
        submitButton.addEventListener('click', function(event) {
            event.preventDefault();

            const userName = document.getElementById('user_name').value;
            const userSchool = document.getElementById('user_school').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'include/user-db.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    window.location.href = 'homepage.html';
                } else {
                }
            };

            xhr.send(`user_name=${encodeURIComponent(userName)}&user_school=${encodeURIComponent(userSchool)}`);
        });
    }

    const carousel = document.querySelector(".carousel"); 
    const arrowBtns = document.querySelectorAll(".wrapper i"); 
    const wrapper = document.querySelector(".wrapper"); 
  
    const firstCard = carousel.querySelector(".card"); 
    const firstCardWidth = firstCard.offsetWidth; 
  
    let isDragging = false, 
        startX, 
        startScrollLeft, 
        timeoutId; 
  
    const dragStart = (e) => {  
        isDragging = true; 
        carousel.classList.add("dragging"); 
        startX = e.pageX; 
        startScrollLeft = carousel.scrollLeft; 
    }; 
  
    const dragging = (e) => { 
        if (!isDragging) return; 
      
        // Calculate the new scroll position 
        const newScrollLeft = startScrollLeft - (e.pageX - startX); 
      
        // Check if the new scroll position exceeds  
        // the carousel boundaries 
        if (newScrollLeft <= 0 || newScrollLeft >=  
            carousel.scrollWidth - carousel.offsetWidth) { 
              
            // If so, prevent further dragging 
            isDragging = false; 
            return; 
        } 
      
        // Otherwise, update the scroll position of the carousel 
        carousel.scrollLeft = newScrollLeft; 
    }; 
  
    const dragStop = () => { 
        isDragging = false;  
        carousel.classList.remove("dragging"); 
    }; 
  
    const autoPlay = () => { 
      
        // Return if window is smaller than 800 
        if (window.innerWidth < 800) return;  
          
        // Calculate the total width of all cards 
        const totalCardWidth = carousel.scrollWidth; 
          
        // Calculate the maximum scroll position 
        const maxScrollLeft = totalCardWidth - carousel.offsetWidth; 
          
        // If the carousel is at the end, stop autoplay 
        if (carousel.scrollLeft >= maxScrollLeft) return; 
          
        // Autoplay the carousel after every 2500ms 
        timeoutId = setTimeout(() =>  
            carousel.scrollLeft += firstCardWidth, 2500); 
    }; 
  
    carousel.addEventListener("mousedown", dragStart); 
    carousel.addEventListener("mousemove", dragging); 
    document.addEventListener("mouseup", dragStop); 
    wrapper.addEventListener("mouseenter", () =>  
        clearTimeout(timeoutId)); 
    wrapper.addEventListener("mouseleave", autoPlay); 
  
    // Add event listeners for the arrow buttons to  
    // scroll the carousel left and right 
    arrowBtns.forEach(btn => { 
        btn.addEventListener("click", () => { 
            carousel.scrollLeft += btn.id === "left" ?  
                -firstCardWidth : firstCardWidth; 
        }); 
    }); 

    const scanner = new Html5QrcodeScanner('reader', {
      qrbox: {
          width: 250,
          height: 250,
      },
      fps: 20,
  });

  scanner.render(onScanSuccess, onScanError);

  function onScanSuccess(result) {
      document.getElementById('result').innerHTML = `
          <h2>Success!</h2>
          <p><a href="${result}" target="_blank">${result}</a></p>
      `;
      scanner.clear();
      document.getElementById('reader').remove();
  }

  function onScanError(err) {
      console.error(err);
  }

    // Function to show or hide the custom input field for "Other" schools
function showOtherInput() {
    var select = document.getElementById("schoolSelect");
    var otherInput = document.getElementById("otherSchoolInput");
  
    if (select.value === "other") {
      otherInput.style.display = "block"; // Show the input field
    } else {
      otherInput.style.display = "none"; // Hide the input field if "Others" is not selected
    }
  }
  
  // Function to validate the visitor login form before submission
  function validateForm() {
    var name = document.getElementById("user_name").value;
    var school = document.getElementById("schoolSelect").value;
    var otherSchool = document.getElementById("otherSchoolName").value;
    var otherSchoolInput = document.getElementById("otherSchoolInput").style.display;
    var errorMessage = document.getElementById("error-message");
  
    // Check if name is filled, school is selected, and otherSchool is filled if "Others" is selected
    if (
      name.trim() === "" ||
      school === "" ||
      (school === "other" && otherSchool.trim() === "")
    ) {
      errorMessage.style.display = "block"; // Show error message
      return false; // Prevent form submission
    } else {
      errorMessage.style.display = "none"; // Hide error message
      return true; // Allow form submission
    }
  }
  
  // Add event listener for form submission to trigger validation
  document.getElementById("guest-form").onsubmit = function () {
    return validateForm();
  };
    
});

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

document.getElementById('watchVideosButton').addEventListener('click', function() {
    window.location.href = 'igorot-dances.php';
});

