require([
    'jquery',
    'swiper'
], function ($, Swiper) {

    'use strict';

    $(document).ready(function () {

        const SwiperClass = SwiperModule.default || SwiperModule;

        // EACH slider block
        $('.silder-product').each(function (index) {

            const wrapper = $(this);

            // Unique elements inside current wrapper
            const slider = wrapper.find('.mySwiper')[0];
            const bar = wrapper.find('.progress-bar')[0];
            const dot = wrapper.find('.progress-dot')[0];
            const nextBtn = wrapper.find('.swiper-button-next')[0];
            const prevBtn = wrapper.find('.swiper-button-prev')[0];

            if (!slider) {
                return;
            }

            // INIT SWIPER
            const swiper = new SwiperClass(slider, {

                slidesPerView: 5.4,
                spaceBetween: 30,
                freeMode: true,
                grabCursor: true,


                navigation: {
                    nextEl: nextBtn,
                    prevEl: prevBtn
                },
                breakpoints: {
                320: {
                    slidesPerView: 1.2,
                    spaceBetween: 10
                },

                576: {
                    slidesPerView: 2.2,
                    spaceBetween: 15
                },

                768: {
                    slidesPerView: 3.2,
                    spaceBetween: 20
                },

                992: {
                    slidesPerView: 4.2,
                    spaceBetween: 25
                },
                1200: {
                    slidesPerView: 5.4,
                    spaceBetween: 30
                }
            }

            });

            // IF no progress bar
            if (!bar || !dot) {
                return;
            }

            // UPDATE DOT
            function updateDot(progress) {

                const max = bar.offsetWidth - dot.offsetWidth;

                dot.style.left = (progress * max) + 'px';
            }

            // SWIPER EVENTS
            swiper.on('progress', function () {

                updateDot(swiper.progress);

            });

            swiper.on('resize', function () {

                updateDot(swiper.progress);

            });

            // INITIAL
            updateDot(swiper.progress);

            // DRAG SUPPORT
            let isDragging = false;

            dot.addEventListener('mousedown', function () {

                isDragging = true;

            });

            document.addEventListener('mouseup', function () {

                isDragging = false;

            });

            document.addEventListener('mousemove', function (e) {

                if (!isDragging) {
                    return;
                }

                const rect = bar.getBoundingClientRect();

                let x = e.clientX - rect.left;

                const max = rect.width - dot.offsetWidth;

                x = Math.max(0, Math.min(x, max));

                dot.style.left = x + 'px';

                const percent = x / max;

                swiper.setProgress(percent);

            });

        });

    });

});