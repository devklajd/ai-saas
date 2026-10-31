/**
 * Road Aid Europe Theme JavaScript
 */

(function() {
    'use strict';

    // Get data from localized script
    const EMERGENCY_PHONE = roadAidData?.emergencyPhone || "+35544527122";
    const WHATSAPP_PHONE  = roadAidData?.whatsappPhone || "355694053525";
    const BUSINESS_EMAIL  = roadAidData?.businessEmail || "info@roadaideurope.com";
    const AJAX_URL        = roadAidData?.ajaxUrl || "/wp-admin/admin-ajax.php";
    const AJAX_NONCE      = roadAidData?.nonce || "";

    const enc = encodeURIComponent;
    let lastCtaTrack = { key: "", t: 0 };

    function trackCta(type, source, label) {
        const key = String(type) + "|" + String(source || "");
        const now = Date.now();
        if (lastCtaTrack.key === key && now - lastCtaTrack.t < 1500) {
            return;
        }
        lastCtaTrack = { key: key, t: now };

        try {
            const data = new FormData();
            data.append("action", "road_aid_track_cta");
            data.append("nonce", AJAX_NONCE);
            data.append("type", type);
            data.append("source", source || "");
            data.append("label", (label || "").toString().trim().slice(0, 120));
            data.append("page", window.location.pathname + window.location.search);

            if (navigator.sendBeacon) {
                navigator.sendBeacon(AJAX_URL, data);
            } else {
                fetch(AJAX_URL, {
                    method: "POST",
                    body: data,
                    credentials: "same-origin",
                    keepalive: true,
                }).catch(function () {});
            }
        } catch (err) {
            // Tracking must never block CTA navigation.
        }
    }

    function ctaSourceFromEl(el) {
        if (!el) return "unknown";
        if (el.id) return el.id;
        if (el.dataset && el.dataset.ctaSource) return el.dataset.ctaSource;
        const cls = (el.className || "").toString().trim().replace(/\s+/g, ".");
        return cls || el.tagName.toLowerCase();
    }

    function isWhatsAppHref(href) {
        return /(?:api\.)?whatsapp\.com|wa\.me/i.test(href || "");
    }

    function openWhatsApp(message, source) {
        trackCta("whatsapp", source || "openWhatsApp", "WhatsApp");
        const url = `https://wa.me/${WHATSAPP_PHONE}?text=${enc(message)}`;
        window.open(url, "_blank");
    }

    // Capture Call / WhatsApp link clicks site-wide (including homepage CTAs).
    document.addEventListener(
        "click",
        function (e) {
            const a = e.target && e.target.closest ? e.target.closest("a") : null;
            if (!a) return;
            const href = a.getAttribute("href") || "";
            const label = (a.textContent || "").replace(/\s+/g, " ").trim();
            const source = ctaSourceFromEl(a);
            if (href.indexOf("tel:") === 0) {
                trackCta("call", source, label || "Call");
            } else if (isWhatsAppHref(href)) {
                trackCta("whatsapp", source, label || "WhatsApp");
            }
        },
        true
    );

    // Mobile menu toggle with animation
    const hamburger = document.getElementById("hamburger");
    const mobileMenu = document.getElementById("mobileMenu");
    if (hamburger && mobileMenu) {
        hamburger.addEventListener("click", () => {
            const expanded = hamburger.getAttribute("aria-expanded") === "true";
            hamburger.setAttribute("aria-expanded", String(!expanded));
            mobileMenu.classList.toggle("show");
        });
    }

    // Language switcher toggle
    document.querySelectorAll('[data-lang-switcher]').forEach(switcher => {
        const btn = switcher.querySelector('button');
        if (!btn) return;
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            document.querySelectorAll('[data-lang-switcher]').forEach(s => {
                if (s !== switcher) s.classList.remove('open');
            });
            switcher.classList.toggle('open');
            btn.setAttribute('aria-expanded', switcher.classList.contains('open'));
        });
    });
    document.addEventListener('click', () => {
        document.querySelectorAll('[data-lang-switcher]').forEach(s => {
            s.classList.remove('open');
            const b = s.querySelector('button');
            if (b) b.setAttribute('aria-expanded', 'false');
        });
    });

    // Scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements for scroll animations
    document.addEventListener('DOMContentLoaded', () => {
        const animatedElements = document.querySelectorAll('.service, .card, .stat, .section');
        animatedElements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            el.style.transitionDelay = `${Math.min(index * 0.1, 0.5)}s`;
            observer.observe(el);
        });

        // Europe / Albania SVG maps: dot per region, click shows label (data-coverage or name)
        let mapTooltipEl = null;
        function showMapRegionTooltip(label, mapSvg, cx, cy, clientX, clientY) {
            if (mapTooltipEl) mapTooltipEl.remove();
            mapTooltipEl = document.createElement('div');
            mapTooltipEl.className = 'europe-map-tooltip';
            mapTooltipEl.textContent = label;
            document.body.appendChild(mapTooltipEl);
            const rect = mapTooltipEl.getBoundingClientRect();
            let wx = typeof clientX === 'number' ? clientX : window.innerWidth / 2;
            let wy = typeof clientY === 'number' ? clientY - 40 : window.innerHeight / 2 - 30;
            if (mapSvg && typeof cx === 'number' && typeof cy === 'number') {
                try {
                    const pt = mapSvg.createSVGPoint();
                    pt.x = cx;
                    pt.y = cy;
                    const svgP = pt.matrixTransform(mapSvg.getScreenCTM());
                    wx = svgP.x;
                    wy = svgP.y - 40;
                } catch (e) { /* use client coords */ }
            }
            mapTooltipEl.style.left = Math.max(8, Math.min(wx - rect.width / 2, window.innerWidth - rect.width - 8)) + 'px';
            mapTooltipEl.style.top = Math.max(8, wy) + 'px';

            const hide = () => {
                if (mapTooltipEl) {
                    mapTooltipEl.remove();
                    mapTooltipEl = null;
                }
                document.removeEventListener('click', hide);
            };
            setTimeout(() => document.addEventListener('click', hide), 0);
        }

        function initInteractiveMapSvg(mapSvg) {
            const paths = mapSvg.querySelectorAll('path[id][name]');
            const dotsGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            dotsGroup.setAttribute('class', 'europe-map-dots');

            paths.forEach((path) => {
                try {
                    const bbox = path.getBBox();
                    const cx = bbox.x + bbox.width / 2;
                    const cy = bbox.y + bbox.height / 2;
                    const label = path.getAttribute('data-coverage') || path.getAttribute('name') || path.getAttribute('id') || '';
                    if (!label || bbox.width < 2 || bbox.height < 2) return;

                    const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    circle.setAttribute('class', 'map-dot country-dot');
                    circle.setAttribute('cx', String(cx));
                    circle.setAttribute('cy', String(cy));
                    circle.setAttribute('r', '6');
                    circle.setAttribute('data-country-name', label);
                    circle.setAttribute('role', 'button');
                    circle.setAttribute('aria-label', label);

                    circle.addEventListener('click', (e) => {
                        e.stopPropagation();
                        showMapRegionTooltip(label, mapSvg, cx, cy, e.clientX, e.clientY);
                    });

                    path.style.cursor = 'pointer';
                    path.addEventListener('click', (e) => {
                        e.stopPropagation();
                        showMapRegionTooltip(label, mapSvg, cx, cy, e.clientX, e.clientY);
                    });

                    dotsGroup.appendChild(circle);
                } catch (err) {
                    // skip path if getBBox fails
                }
            });

            mapSvg.appendChild(dotsGroup);
        }

        document.querySelectorAll('.europe-map-svg, .albania-map-svg').forEach(initInteractiveMapSvg);

        // Blog slider: prev/next and dots
        const blogTrack = document.getElementById('blogSliderTrack');
        const blogDots = document.getElementById('blogSliderDots');
        if (blogTrack && blogDots) {
            const slides = blogTrack.querySelectorAll('.blog-slide');
            const prevBtn = document.querySelector('.blog-slider-prev');
            const nextBtn = document.querySelector('.blog-slider-next');
            const slideWidth = () => {
                const first = slides[0];
                return first ? first.offsetWidth + 24 : 344; // 24 = gap
            };
            const scrollToIndex = (index) => {
                const w = slideWidth();
                const maxScroll = blogTrack.scrollWidth - blogTrack.clientWidth;
                blogTrack.scrollTo({ left: Math.min(index * w, maxScroll), behavior: 'smooth' });
            };
            const updateDots = () => {
                const w = slideWidth();
                const scroll = blogTrack.scrollLeft;
                const index = Math.round(scroll / w);
                blogDots.querySelectorAll('.blog-slider-dot').forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                });
            };
            const numSlides = slides.length;
            for (let i = 0; i < numSlides; i++) {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'blog-slider-dot' + (i === 0 ? ' active' : '');
                dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
                dot.addEventListener('click', () => scrollToIndex(i));
                blogDots.appendChild(dot);
            }
            if (prevBtn) prevBtn.addEventListener('click', () => {
                const w = slideWidth();
                blogTrack.scrollBy({ left: -w, behavior: 'smooth' });
            });
            if (nextBtn) nextBtn.addEventListener('click', () => {
                const w = slideWidth();
                blogTrack.scrollBy({ left: w, behavior: 'smooth' });
            });
            blogTrack.addEventListener('scroll', () => { updateDots(); });
        }
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href.length > 1) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Update year in footer
    const yearEl = document.getElementById("year");
    if (yearEl) {
        yearEl.textContent = new Date().getFullYear();
    }

    // Update sticky call button
    const stickyCall = document.getElementById("stickyCall");
    if (stickyCall) {
        stickyCall.setAttribute("href", `tel:${EMERGENCY_PHONE}`);
    }

    // Need Help Now button
    const needHelpNow = document.getElementById("needHelpNowBtn");
    if (needHelpNow) {
        needHelpNow.addEventListener("click", (e) => {
            trackCta("call", "needHelpNowBtn", "Need Help Now");
            setTimeout(() => {
                window.location.href = `tel:${EMERGENCY_PHONE}`;
            }, 350);
        });
    }

    // WhatsApp form handlers
    const sendWhatsApp = document.getElementById("sendWhatsApp");
    if (sendWhatsApp) {
        sendWhatsApp.addEventListener("click", () => {
            const requestType = document.getElementById("requestType")?.value || "-";
            const serviceNeeded = document.getElementById("serviceNeeded")?.value || "-";
            const location = document.getElementById("location")?.value || "-";
            const vehicleInfo = document.getElementById("vehicleInfo")?.value || "-";
            const phone = document.getElementById("phone")?.value || "-";
            const isDrivable = document.getElementById("isDrivable")?.value || "-";
            const details = document.getElementById("details")?.value || "-";

            const msg = `ROAD AID EUROPE - ASSISTANCE REQUEST
Type: ${requestType}
Service: ${serviceNeeded}
Location: ${location}
Vehicle: ${vehicleInfo}
Caller Phone: ${phone}
Drivable: ${isDrivable}
Details: ${details}`;

            openWhatsApp(msg, "sendWhatsApp");
        });
    }

    // Sticky WhatsApp button
    const stickyWhatsApp = document.getElementById("stickyWhatsApp");
    if (stickyWhatsApp) {
        stickyWhatsApp.addEventListener("click", () => {
            openWhatsApp("Hello Road Aid Europe. I need roadside assistance. ", "stickyWhatsApp");
        });
    }

    // Emergency section WhatsApp button
    const emergencyWhatsApp = document.getElementById("emergencyWhatsApp");
    if (emergencyWhatsApp) {
        emergencyWhatsApp.addEventListener("click", () => {
            openWhatsApp("URGENT — I need roadside assistance now.", "emergencyWhatsApp");
        });
    }

    // Business WhatsApp button
    const bizWhatsApp = document.getElementById("bizWhatsApp");
    if (bizWhatsApp) {
        bizWhatsApp.addEventListener("click", () => {
            openWhatsApp("Hello Road Aid Europe. I want to discuss a business partnership.", "bizWhatsApp");
        });
    }

    // Form submissions
    const assistanceForm = document.getElementById("assistanceForm");
    if (assistanceForm) {
        assistanceForm.addEventListener("submit", (e) => {
            e.preventDefault();
            alert("Request captured. Use WhatsApp for fastest response.");
        });
    }

    const providerForm = document.getElementById("providerForm");
    if (providerForm) {
        providerForm.addEventListener("submit", (e) => {
            e.preventDefault();
            alert("Application captured. In production this should email your team.");
        });
    }

    // Corporate WhatsApp
    const corpWhatsAppBtn = document.getElementById("corpWhatsAppBtn");
    if (corpWhatsAppBtn) {
        corpWhatsAppBtn.addEventListener("click", () => {
            const company = document.getElementById("corpCompany")?.value || "-";
            const type = document.getElementById("corpType")?.value || "-";
            const contact = document.getElementById("corpContact")?.value || "-";
            const phone = document.getElementById("corpPhone")?.value || "-";
            const email = document.getElementById("corpEmail")?.value || "-";
            const fleet = document.getElementById("corpFleet")?.value || "-";
            const areas = document.getElementById("corpAreas")?.value || "-";
            const notes = document.getElementById("corpNotes")?.value || "-";
            
            openWhatsApp(`ROAD AID EUROPE - CORPORATE ACCOUNT REGISTRATION\nCompany: ${company}\nType: ${type}\nContact: ${contact}\nPhone/WhatsApp: ${phone}\nEmail: ${email}\nFleet size: ${fleet}\nCoverage areas: ${areas}\nNotes: ${notes}`, "corpWhatsAppBtn");
        });
    }

    // Corporate form
    const corporateRegisterForm = document.getElementById("corporateRegisterForm");
    if (corporateRegisterForm) {
        corporateRegisterForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const company = document.getElementById("corpCompany")?.value;
            const type = document.getElementById("corpType")?.value;
            const contact = document.getElementById("corpContact")?.value;
            const phone = document.getElementById("corpPhone")?.value;
            const email = document.getElementById("corpEmail")?.value;
            const fleet = document.getElementById("corpFleet")?.value || "-";
            const areas = document.getElementById("corpAreas")?.value || "-";
            const notes = document.getElementById("corpNotes")?.value || "-";
            
            const subject = enc("Corporate Account Registration - Road Aid Europe");
            const body = enc(`Company: ${company}\nType: ${type}\nContact: ${contact}\nPhone: ${phone}\nEmail: ${email}\nFleet size: ${fleet}\nCoverage areas: ${areas}\nNotes: ${notes}\n`);
            window.location.href = `mailto:${BUSINESS_EMAIL}?subject=${subject}&body=${body}`;
        });
    }

    // Contact form
    const contactForm = document.getElementById("contactForm");
    if (contactForm) {
        contactForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const name = document.getElementById("cName")?.value;
            const email = document.getElementById("cEmail")?.value;
            const msg = document.getElementById("cMessage")?.value;

            const subject = enc("Website Contact - Road Aid Europe");
            const body = enc(`Name: ${name}\nEmail: ${email}\n\nMessage:\n${msg}`);
            window.location.href = `mailto:${BUSINESS_EMAIL}?subject=${subject}&body=${body}`;
        });
    }

})();

